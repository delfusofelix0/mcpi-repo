<?php

namespace App\Http\Controllers\QMS;

use App\Http\Controllers\Controller;
use App\Models\Ticket;
use Illuminate\Http\Request;
use Inertia\Inertia;

class TicketController extends Controller
{
    // Simple ticket generation page
    public function index()
    {
        return Inertia::render('Tickets/Generate');
    }

    // Generate a new ticket
    public function generate(Request $request)
    {
        $ticket = Ticket::create([
            'ticket_number' => Ticket::generateTicketNumber(),
            'issue_time' => now(),
            'status' => 'waiting',
        ]);

        // Return ticket info - later we'll add printing functionality
        return Inertia::render('Tickets/Issued', [
            'ticket' => $ticket,
        ]);
    }
}
