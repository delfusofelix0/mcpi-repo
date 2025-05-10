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
        $department = $request->input('department', 'general');

        // Determine prefix based on department
        if ($department === 'registrar-gradecollege') {
            $prefix = 'GC';
        } elseif ($department === 'registrar-jhs') {
            $prefix = 'JHS';
        } elseif ($department === 'registrar-shs') {
            $prefix = 'SHS';
        } else {
            $prefix = strtoupper(substr($department, 0, 1)); // C, A, or R
        }

        // Get the current count for today for this department
        $count = Ticket::where('department', $department)
                ->whereDate('created_at', today())
                ->count() + 1;

        $ticketNumber = $prefix . str_pad($count, 3, '0', STR_PAD_LEFT); // RG001, RJ001, RS001, C001, A001, etc.

        // Create the ticket
        $ticket = Ticket::create([
            'ticket_number' => $ticketNumber,
            'department' => $department,
            'status' => 'waiting',
            'issue_time' => now(),
        ]);

        // 🔄 Add this check to return JSON if requested by Electron (not Vue)
        if ($request->expectsJson()) {
            return response()->json($ticket);
        }

        // Default for Vue/Inertia for fallback
        return Inertia::render('Tickets/Issued', [
            'ticket' => $ticket,
        ]);
    }
}
