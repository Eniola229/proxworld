<?php

namespace App\Http\Controllers;

use App\Models\Ticket;
use App\Models\TicketMessage;
use App\Types\TicketSenderType;
use App\Types\TicketStatus;
use App\Mail\NewTicketAlertMail;
use App\Mail\TicketCreatedMail;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;

/**
 * Support tickets + live chat. Everything the UI needs is JSON; the browser
 * polls GET /support/{ticket}/messages?after=<id> every few seconds.
 */ 
class TicketController extends Controller
{
    /** HTML for the support page, JSON ticket list for the chat component. */
    public function index(Request $request)
    {
        if ($request->expectsJson()) {
            $tickets = $request->user()->tickets()->latest('updated_at')->get();

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

    public function store(Request $request)
    {
        $data = $request->validate([
            'subject' => ['required', 'string', 'max:255'],
            'message' => ['required', 'string', 'max:5000'],
        ]);

        $ticket = Ticket::create([
            'user_id' => $request->user()->id,
            'subject' => $data['subject'],
            'status'  => TicketStatus::OPEN,
        ]);

        $message = TicketMessage::create([
            'ticket_id'   => $ticket->id,
            'sender_type' => TicketSenderType::USER,
            'message'     => $data['message'],
        ]);

        $this->notifyCreated($ticket, $message);

        if ($request->expectsJson()) {
            return response()->json(['ticket' => $this->ticketPayload($ticket->fresh())], 201);
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

    public function sendMessage(Request $request, Ticket $ticket)
    {
        abort_unless($ticket->user_id === $request->user()->id, 403);
        abort_if($ticket->isClosed(), 422, 'This ticket is closed.');

        $data = $request->validate(['message' => ['required', 'string', 'max:5000']]);

        $message = TicketMessage::create([
            'ticket_id'   => $ticket->id,
            'sender_type' => TicketSenderType::USER,
            'message'     => $data['message'],
        ]);

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

        return [
            'id'             => $t->id,
            'short_id'       => substr((string) $t->id, 0, 8),
            'subject'        => $t->subject,
            'status'         => $status,
            'is_closed'      => $t->isClosed(),
            'rating'         => $t->rating,
            'rating_comment' => $t->rating_comment,
            'can_rate'       => $t->canBeRated(),
            'created'        => $t->created_at?->format('M d, Y'),
            'updated'        => $t->updated_at?->diffForHumans(),
        ];
    }

    private function messagePayload(TicketMessage $m): array
    {
        $isAdmin = $m->sender_type !== TicketSenderType::USER;

        return [
            'id'       => $m->id,
            'is_admin' => $isAdmin,
            'name'     => $isAdmin ? ($m->senderAdmin->name ?? 'Support') : 'You',
            'message'  => $m->message,
            'time'     => $m->created_at->format('H:i'),
        ];
    }
}