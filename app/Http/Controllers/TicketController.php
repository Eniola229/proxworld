<?php

namespace App\Http\Controllers;

use App\Models\Ticket;
use App\Models\TicketMessage;
use App\Services\CloudinaryService;
use App\Types\TicketSenderType;
use App\Types\TicketStatus;
use App\Mail\NewTicketAlertMail;
use App\Mail\TicketCreatedMail;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Str;
use Illuminate\Validation\ValidationException;

/**
 * Support tickets + live chat. Everything the UI needs is JSON; the browser
 * polls GET /support/{ticket}/messages?after=<id> every few seconds.
 *
 * Starting a chat needs no subject: the first message creates the ticket and
 * its first 20 words become the subject.
 */
class TicketController extends Controller
{
    /** HTML for the support page, JSON ticket list for the chat component. */
    public function index(Request $request)
    {
        if ($request->expectsJson()) {
            $tickets = $request->user()->tickets()
                ->with('latestMessage')
                ->latest('updated_at')
                ->get();

            return response()->json([
                'tickets' => $tickets->map(fn ($t) => $this->ticketPayload($t))->values(),
            ]);
        }

        return view('support.index', ['openTicket' => $request->query('ticket')]);
    }

    /** Old deep links (/support/{id}) open the ticket inside the support page. */
    public function show(Request $request, Ticket $ticket)
    {
        abort_unless($ticket->user_id === $request->user()->id, 403);

        return redirect()->route('support.index', ['ticket' => $ticket->id]);
    }

    /** First message of a new chat. Creates the ticket, subject comes from the message. */
    public function store(Request $request, CloudinaryService $cloudinary)
    {
        $data   = $request->validate($this->messageRules(), $this->messageErrors());
        $text   = $this->cleanText($data['message'] ?? null);
        $upload = $this->uploadAttachment($request, $cloudinary);

        $ticket = Ticket::create([
            'user_id' => $request->user()->id,
            'subject' => $this->makeSubject($text, $upload !== null),
            'status'  => TicketStatus::OPEN,
        ]);

        $message = $this->storeMessage($ticket, $text, $upload);

        $this->notifyCreated($ticket, $message);

        if ($request->expectsJson()) {
            return response()->json([
                'ticket'  => $this->ticketPayload($ticket->fresh()),
                'message' => $this->messagePayload($message),
            ], 201);
        }

        return redirect()->route('support.index', ['ticket' => $ticket->id]);
    }

    /** Polled by the chat component. */
    public function messages(Request $request, Ticket $ticket)
    {
        abort_unless($ticket->user_id === $request->user()->id, 403);

        $request->validate(['after' => ['nullable', 'uuid']]);

        // UUIDs aren't reliably sortable, so the cursor is the anchor message's
        // timestamp. Using >= can repeat the anchor; the client de-dupes by id.
        $query = $ticket->messages()->orderBy('created_at')->orderBy('id');

        if ($request->filled('after')) {
            $anchor = $ticket->messages()->whereKey($request->query('after'))->first();

            if ($anchor) {
                $query->where('created_at', '>=', $anchor->created_at);
            }
        }

        return response()->json([
            'ticket'   => $this->ticketPayload($ticket),
            'messages' => $query->with('senderAdmin:id,name')->get()
                ->map(fn ($m) => $this->messagePayload($m))->values(),
        ]);
    }

    public function sendMessage(Request $request, Ticket $ticket, CloudinaryService $cloudinary)
    {
        abort_unless($ticket->user_id === $request->user()->id, 403);
        abort_if($ticket->isClosed(), 422, 'This ticket is closed.');

        $data   = $request->validate($this->messageRules(), $this->messageErrors());
        $text   = $this->cleanText($data['message'] ?? null);
        $upload = $this->uploadAttachment($request, $cloudinary);

        $message = $this->storeMessage($ticket, $text, $upload);

        $ticket->update(['status' => TicketStatus::OPEN, 'updated_at' => now()]);

        return response()->json(['message' => $this->messagePayload($message)]);
    }

    /** Customer rates a closed ticket. One rating per ticket, no edits. */
    public function rate(Request $request, Ticket $ticket)
    {
        abort_unless($ticket->user_id === $request->user()->id, 403);
        abort_unless($ticket->isClosed(), 422, 'You can only rate a closed ticket.');

        $data = $request->validate([
            'rating'  => ['required', 'integer', 'between:1,5'],
            'comment' => ['nullable', 'string', 'max:1000'],
        ]);

        // The whereNull guard stops a double submit from overwriting the first rating.
        $saved = Ticket::whereKey($ticket->id)->whereNull('rating')->update([
            'rating'         => $data['rating'],
            'rating_comment' => $data['comment'] ?? null,
            'rated_at'       => now(),
        ]);

        abort_if($saved === 0, 422, 'This ticket has already been rated.');

        return response()->json(['ticket' => $this->ticketPayload($ticket->fresh())]);
    }

    /* ------------------------------------------------------------------ */

    private function messageRules(): array
    {
        return [
            // Text is optional only when a file is attached.
            'message'    => ['nullable', 'string', 'max:5000', 'required_without:attachment'],
            // 50 MB cap. Make sure php.ini upload_max_filesize / post_max_size allow it.
            'attachment' => ['nullable', 'file', 'mimes:jpg,jpeg,png,gif,webp,mp4,webm,mov', 'max:51200'],
        ];
    }

    private function messageErrors(): array
    {
        return [
            'message.required_without' => 'Type a message or attach a photo or video.',
            'attachment.mimes'         => 'Only images (JPG, PNG, GIF, WEBP) and videos (MP4, WEBM, MOV) are allowed.',
            'attachment.max'           => 'That file is too large. The limit is 50 MB.',
        ];
    }

    private function cleanText(?string $text): ?string
    {
        $text = trim((string) $text);

        return $text === '' ? null : $text;
    }

    /** Subject = first 20 words of the opening message. */
    private function makeSubject(?string $text, bool $hasAttachment): string
    {
        $text = trim(preg_replace('/\s+/u', ' ', (string) $text));

        if ($text === '') {
            return $hasAttachment ? 'Photo / video attachment' : 'New conversation';
        }

        return Str::limit(Str::words($text, 20, '…'), 255, '…');
    }

    /** Uploads to Cloudinary before anything is saved, so a failed upload never leaves a half-made ticket. */
    private function uploadAttachment(Request $request, CloudinaryService $cloudinary): ?array
    {
        $file = $request->file('attachment');

        if (! $file) {
            return null;
        }

        $isVideo = str_starts_with((string) $file->getMimeType(), 'video/');

        try {
            $result = $isVideo
                ? $cloudinary->uploadVideo($file, 'orderer/support')
                : $cloudinary->uploadImage($file, 'orderer/support');

            return [
                'url'       => $result['url'],
                'public_id' => $result['public_id'],
                'type'      => $isVideo ? 'video' : 'image',
                'name'      => $file->getClientOriginalName(),
            ];
        } catch (\Throwable $e) {
            report($e);

            throw ValidationException::withMessages([
                'attachment' => 'Upload failed. Please try again.',
            ]);
        }
    }

    private function storeMessage(Ticket $ticket, ?string $text, ?array $upload): TicketMessage
    {
        return TicketMessage::create([
            'ticket_id'            => $ticket->id,
            'sender_type'          => TicketSenderType::USER,
            'message'              => $text,
            'attachment_url'       => $upload['url'] ?? null,
            'attachment_type'      => $upload['type'] ?? null,
            'attachment_public_id' => $upload['public_id'] ?? null,
            'attachment_name'      => $upload['name'] ?? null,
        ]);
    }

    /** Confirmation to the customer + optional alert to the support team. Never blocks ticket creation. */
    private function notifyCreated(Ticket $ticket, TicketMessage $message): void
    {
        try {
            $ticket->loadMissing('user');

            Mail::to($ticket->user->email)->queue(new TicketCreatedMail($ticket, $message));

            if ($to = config('services.support.notify_email')) {
                Mail::to($to)->queue(new NewTicketAlertMail($ticket, $message));
            }
        } catch (\Throwable $e) {
            report($e);
        }
    }

    private function ticketPayload(Ticket $t): array
    {
        $status = $t->status instanceof \BackedEnum ? $t->status->value : (string) $t->status;
        $last   = $t->latestMessage;

        $preview = '';
        if ($last) {
            $preview = $last->message
                ? Str::limit($last->message, 60)
                : ($last->attachment_type === 'video' ? '🎥 Video' : '📷 Photo');
        }

        return [
            'id'             => $t->id,
            'short_id'       => substr((string) $t->id, 0, 8),
            'subject'        => $t->subject,
            'status'         => $status,
            'is_closed'      => $t->isClosed(),
            'rating'         => $t->rating,
            'rating_comment' => $t->rating_comment,
            'can_rate'       => $t->canBeRated(),
            'preview'        => $preview,
            'created'        => $t->created_at?->format('M d, Y'),
            'updated'        => $t->updated_at?->diffForHumans(),
            'updated_short'  => $t->updated_at?->diffForHumans(null, true, true),
        ];
    }

    private function messagePayload(TicketMessage $m): array
    {
        $isAdmin = $m->sender_type !== TicketSenderType::USER;

        return [
            'id'         => $m->id,
            'is_admin'   => $isAdmin,
            'name'       => $isAdmin ? ($m->senderAdmin->name ?? 'Support') : 'You',
            'message'    => (string) $m->message,
            'attachment' => $m->attachment_url ? [
                'url'  => $m->attachment_url,
                'type' => $m->attachment_type,
                'name' => $m->attachment_name,
            ] : null,
            'time'       => $m->created_at->format('H:i'),
        ];
    }
}