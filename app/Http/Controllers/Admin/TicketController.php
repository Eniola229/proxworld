<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Ticket;
use App\Models\TicketMessage;
use App\Types\TicketSenderType;
use App\Types\TicketStatus;
use App\Mail\TicketReplyMail;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;

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

    public function sendMessage(Request $request, Ticket $ticket)
    {
        $data = $request->validate(['message' => ['required', 'string', 'max:5000']]);

        $admin = $request->user('admin');

        $message = TicketMessage::create([
            'ticket_id'       => $ticket->id,
            'sender_type'     => TicketSenderType::ADMIN,
            'sender_admin_id' => $admin->id,
            'message'         => $data['message'],
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

    public function destroy(Ticket $ticket)
    {
        $ticket->delete(); // cascades to ticket_messages

        return redirect()->route('admin.support.index')->with('success', 'Ticket deleted.');
    }

    /** is_admin = sent by staff. "mine" = sent by the admin looking at the page. */
    private function messagePayload(TicketMessage $m, ?string $adminId, ?string $customerName): array
    {
        $isAdmin = $m->sender_type === TicketSenderType::ADMIN;
        $mine    = $isAdmin && (string) $m->sender_admin_id === (string) $adminId;

        return [
            'id'       => $m->id,
            'is_admin' => $isAdmin,
            'name'     => $isAdmin ? ($mine ? 'You' : ($m->senderAdmin->name ?? 'Support')) : ($customerName ?: 'Customer'),
            'message'  => $m->message,
            'time'     => $m->created_at->format('M d, H:i'),
        ];
    }
}