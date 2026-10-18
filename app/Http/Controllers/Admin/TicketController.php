<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Ticket;
use App\Models\TicketMessage;
use App\Services\CloudinaryService;
use App\Types\TicketSenderType;
use App\Types\TicketStatus;
use App\Mail\TicketReplyMail;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;
use Illuminate\Validation\ValidationException;

/** Admin side of the same AJAX live-chat pattern as the customer TicketController. */
class TicketController extends Controller
{
    public function index(Request $request)
    {
        $status = $request->get('status', 'all');
        $mine   = $request->boolean('mine');
        $me     = $request->user('admin');

        // NOTE: needs a latestMessage() relationship on the Ticket model (see instructions).
        $tickets = Ticket::query()
            ->with(['user:id,name,email', 'latestMessage'])
            ->when($status !== 'all', fn ($q) => $q->where('status', $status))
            ->when($mine && $me, fn ($q) => $q->repliedBy($me->id))
            ->when($request->filled('search'), fn ($q) => $q->where('subject', 'like', "%{$request->search}%"))
            ->latest('updated_at')
            ->paginate(20)
            ->withQueryString();

        $stats = [
            'total'       => Ticket::count(),
            'open'        => Ticket::where('status', TicketStatus::OPEN)->count(),
            'in_progress' => Ticket::where('status', TicketStatus::IN_PROGRESS)->count(),
            'closed'      => Ticket::where('status', TicketStatus::CLOSED)->count(),
        ];

        return view('admin.support.index', compact('tickets', 'stats', 'status', 'mine'));
    }

    public function show(Request $request, Ticket $ticket)
    {
        $ticket->load('user', 'messages.senderAdmin');
        $ticket->user?->withNgnBalance();

        $adminId = $request->user('admin')?->id;

        return view('admin.support.show', [
            'ticket'   => $ticket,
            'messages' => $ticket->messages,
            'initial'  => $ticket->messages
                ->sortBy('created_at')
                ->map(fn ($m) => $this->messagePayload($m, $adminId, $ticket->user?->name))
                ->values(),
        ]);
    }

    /** Polled by the admin page. */
    public function messages(Request $request, Ticket $ticket)
    {
        $request->validate(['after' => ['nullable', 'uuid']]);

        // UUIDs aren't sortable, so the cursor is the anchor message's timestamp (>= may
        // repeat the anchor; the browser de-dupes by id).
        $query = $ticket->messages()->orderBy('created_at')->orderBy('id');

        if ($request->filled('after')) {
            $anchor = $ticket->messages()->whereKey($request->query('after'))->first();

            if ($anchor) {
                $query->where('created_at', '>=', $anchor->created_at);
            }
        }

        $adminId  = $request->user('admin')?->id;
        $customer = $ticket->user()->value('name');

        return response()->json([
            'messages' => $query->with('senderAdmin:id,name')->get()
                ->map(fn ($m) => $this->messagePayload($m, $adminId, $customer))->values(),
        ]);
    }

    public function sendMessage(Request $request, Ticket $ticket, CloudinaryService $cloudinary)
    {
        $data = $request->validate([
            'message'    => ['nullable', 'string', 'max:5000', 'required_without:attachment'],
            'attachment' => ['nullable', 'file', 'mimes:jpg,jpeg,png,gif,webp,mp4,webm,mov', 'max:51200'],
        ], [
            'message.required_without' => 'Type a reply or attach an image or video.',
            'attachment.mimes'         => 'Only images (JPG, PNG, GIF, WEBP) and videos (MP4, WEBM, MOV) are allowed.',
            'attachment.max'           => 'That file is too large. The limit is 50 MB.',
        ]);

        $admin  = $request->user('admin');
        $upload = null;

        if ($file = $request->file('attachment')) {
            $isVideo = str_starts_with((string) $file->getMimeType(), 'video/');

            try {
                $result = $isVideo
                    ? $cloudinary->uploadVideo($file, 'orderer/support')
                    : $cloudinary->uploadImage($file, 'orderer/support');

                $upload = [
                    'url'       => $result['url'],
                    'public_id' => $result['public_id'],
                    'type'      => $isVideo ? 'video' : 'image',
                    'name'      => $file->getClientOriginalName(),
                ];
            } catch (\Throwable $e) {
                report($e);

                throw ValidationException::withMessages(['attachment' => 'Upload failed. Please try again.']);
            }
        }

        $text = trim((string) ($data['message'] ?? ''));

        $message = TicketMessage::create([
            'ticket_id'            => $ticket->id,
            'sender_type'          => TicketSenderType::ADMIN,
            'sender_admin_id'      => $admin->id,
            'message'              => $text === '' ? null : $text,
            'attachment_url'       => $upload['url'] ?? null,
            'attachment_type'      => $upload['type'] ?? null,
            'attachment_public_id' => $upload['public_id'] ?? null,
            'attachment_name'      => $upload['name'] ?? null,
        ]);

        $ticket->update([
            'status'            => TicketStatus::IN_PROGRESS,
            'assigned_admin_id' => $ticket->assigned_admin_id ?? $admin->id,
        ]);

        // Email the customer about the reply. Never blocks the reply itself.
        try {
            $ticket->loadMissing('user');
            Mail::to($ticket->user->email)->queue(new TicketReplyMail($ticket, $message));
        } catch (\Throwable $e) {
            report($e);
        }

        return response()->json([
            'message' => $this->messagePayload($message->load('senderAdmin:id,name'), $admin->id, $ticket->user()->value('name')),
        ]);
    }

    public function close(Ticket $ticket)
    {
        $ticket->update(['status' => TicketStatus::CLOSED, 'closed_at' => now()]);

        return back()->with('success', 'Ticket closed.');
    }

    public function reopen(Ticket $ticket)
    {
        $ticket->update(['status' => TicketStatus::OPEN, 'closed_at' => null]);

        return back()->with('success', 'Ticket reopened.');
    }

    public function updateStatus(Request $request, Ticket $ticket)
    {
        $data = $request->validate(['status' => ['required', 'in:open,in_progress,closed']]);
        $ticket->update(['status' => $data['status'], 'closed_at' => $data['status'] === TicketStatus::CLOSED ? now() : null]);

        return back()->with('success', 'Ticket status updated.');
    }

    public function destroy(Ticket $ticket, CloudinaryService $cloudinary)
    {
        // Remove uploaded media from Cloudinary first (best effort), then the rows.
        foreach ($ticket->messages()->whereNotNull('attachment_public_id')->get() as $m) {
            try {
                $cloudinary->delete($m->attachment_public_id, $m->attachment_type ?: 'image');
            } catch (\Throwable $e) {
                report($e);
            }
        }

        $ticket->delete(); // cascades to ticket_messages

        return redirect()->route('admin.support.index')->with('success', 'Ticket deleted.');
    }

    /** is_admin = sent by staff. "mine" = sent by the admin looking at the page. */
    private function messagePayload(TicketMessage $m, ?string $adminId, ?string $customerName): array
    {
        $isAdmin = $m->sender_type === TicketSenderType::ADMIN;
        $mine    = $isAdmin && (string) $m->sender_admin_id === (string) $adminId;

        return [
            'id'         => $m->id,
            'is_admin'   => $isAdmin,
            'name'       => $isAdmin ? ($mine ? 'You' : ($m->senderAdmin->name ?? 'Support')) : ($customerName ?: 'Customer'),
            'message'    => (string) $m->message,
            'attachment' => $m->attachment_url ? [
                'url'  => $m->attachment_url,
                'type' => $m->attachment_type,
                'name' => $m->attachment_name,
            ] : null,
            'time'       => $m->created_at->format('M d, H:i'),
        ];
    }
}