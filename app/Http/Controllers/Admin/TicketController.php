<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Ticket;
use App\Models\TicketMessage;
use App\Types\TicketSenderType;
use App\Types\TicketStatus;
use Illuminate\Http\Request;

/** Admin side of the same AJAX live-chat pattern as the customer TicketController. */
class TicketController extends Controller
{

    public function index(Request $request)
    {
        $status = $request->get('status', 'all');

        $tickets = Ticket::query()
            ->with(['user:id,name,email', 'latestMessage'])
            ->when($status !== 'all', fn ($q) => $q->where('status', $status))
            ->when($request->filled('search'), fn ($q) => $q->where('subject', 'like', "%{$request->search}%"))
            ->latest('updated_at')
            ->paginate(20)
            ->withQueryString();

        $stats = [
            'total' => Ticket::count(),
            'open' => Ticket::where('status', TicketStatus::OPEN)->count(),
            'in_progress' => Ticket::where('status', TicketStatus::IN_PROGRESS)->count(),
            'closed' => Ticket::where('status', TicketStatus::CLOSED)->count(),
        ];

        return view('admin.support.index', compact('tickets', 'stats', 'status'));
    }
    
    public function show(Ticket $ticket)
    {
        $ticket->load('user', 'messages.senderAdmin');

        return view('admin.support.show', [
            'ticket' => $ticket,
            'messages' => $ticket->messages,
        ]);
    }

    public function messages(Request $request, Ticket $ticket)
    {
        $query = $ticket->messages();

        if ($request->filled('after')) {
            $query->where('id', '>', $request->integer('after'));
        }

        return response()->json(['messages' => $query->get()]);
    }

    public function sendMessage(Request $request, Ticket $ticket)
    {
        $data = $request->validate(['message' => ['required', 'string', 'max:5000']]);

        $message = TicketMessage::create([
            'ticket_id' => $ticket->id,
            'sender_type' => TicketSenderType::ADMIN,
            'sender_admin_id' => $request->user('admin')->id,
            'message' => $data['message'],
        ]);

        $ticket->update(['status' => TicketStatus::IN_PROGRESS, 'assigned_admin_id' => $ticket->assigned_admin_id ?? $request->user('admin')->id]);

        return response()->json(['message' => $message]);
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
}
