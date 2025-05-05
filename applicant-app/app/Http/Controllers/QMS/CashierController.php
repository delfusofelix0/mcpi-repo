<?php

namespace App\Http\Controllers\QMS;

use App\Http\Controllers\Controller;
use App\Models\Ticket;
use App\Models\Window;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Inertia\Inertia;

class CashierController extends Controller
{
    public function dashboard()
    {
        $window = Window::with('currentTicket')->find(Auth::user()->window_id ?? 1);

        $waitingTickets = Ticket::where('status', 'waiting')
            ->orderBy('issue_time')
            ->paginate(5); // Automatically handles limit, offset, etc.

        return Inertia::render('Cashier/Dashboard', [
            'window' => $window,
            'waitingTickets' => $waitingTickets,
            'currentTicket' => $window->currentTicket,
        ]);
    }

    public function callNext(Request $request)
    {
        $window = Window::with('currentTicket')->find(Auth::user()->window_id ?? $request->window_id);

        if ($window->currentTicket) {
            return redirect()->back()->with('error', 'Please complete or skip the current ticket first.');
        }

        $nextTicket = Ticket::where('status', 'waiting')
            ->orderBy('issue_time')
            ->first();

        if (!$nextTicket) {
            return redirect()->back()->with('error', 'No waiting tickets available.');
        }

        $nextTicket->markAsServing($window);

        return redirect()->back()->with('success', 'Ticket #' . $nextTicket->ticket_number . ' called.');
    }

    public function complete(Request $request, Ticket $ticket)
    {
        if ($ticket->status !== 'serving') {
            return redirect()->back()->with('error', 'Only serving tickets can be completed.');
        }

        $ticket->markAsCompleted();

        return redirect()->back()->with('success', 'Ticket #' . $ticket->ticket_number . ' completed.');
    }

    public function skip(Request $request, Ticket $ticket)
    {
        if ($ticket->status !== 'serving') {
            return redirect()->back()->with('error', 'Only serving tickets can be skipped.');
        }

        $ticket->markAsSkipped();

        return redirect()->back()->with('success', 'Ticket #' . $ticket->ticket_number . ' skipped.');
    }
}
