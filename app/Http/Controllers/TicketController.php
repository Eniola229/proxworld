<?php

namespace App\Http\Controllers;

use App\Models\Ticket;
use App\Models\TicketMessage;
use App\Types\TicketSenderType;
use App\Types\TicketStatus;
use Illuminate\Http\Request;

/**
 * Live-chat-feel support with plain AJAX polling — no websockets needed.
 * The ticket page polls GET .../messages?after=<id> every ~3s and appends
 * new rows without a reload. No attachment field anywhere — users are
 * pointed to Telegram for anything requiring images (see support.index view).
 */
class TicketController extends Controller
{
    public function index(Request $request)
    {
        return view('support.index', [
            'tickets' => $request->user()->tickets()->latest()->get(),
            'telegramSupportUrl' => \App\Models\Setting::get('social_links')['telegram_support'] ?? null,
        ]);
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
            'status' => TicketStatus::OPEN,
        ]);

        TicketMessage::create([
            'ticket_id' => $ticket->id,
            'sender_type' => TicketSenderType::USER,
            'message' => $data['message'],
        ]);

        return redirect()->route('support.index')->with('success', 'Ticket created — our team will respond shortly.');
    }

    /** Polled via fetch() every few seconds. */
    public function messages(Request $request, Ticket $ticket)
    {
        abort_unless($ticket->user_id === $request->user()->id, 403);

        $query = $ticket->messages();

        if ($request->filled('after')) {
            $query->where('id', '>', $request->integer('after'));
        }

        return response()->json(['messages' => $query->with('senderAdmin:id,name')->get()]);
    }

    public function sendMessage(Request $request, Ticket $ticket)
    {
        abort_unless($ticket->user_id === $request->user()->id, 403);
        abort_if($ticket->isClosed(), 422, 'This ticket is closed.');

        $data = $request->validate(['message' => ['required', 'string', 'max:5000']]);

        $message = TicketMessage::create([
            'ticket_id' => $ticket->id,
            'sender_type' => TicketSenderType::USER,
            'message' => $data['message'],
        ]);

        $ticket->update(['status' => TicketStatus::OPEN, 'updated_at' => now()]);

        return response()->json(['message' => $message]);
    }
}
