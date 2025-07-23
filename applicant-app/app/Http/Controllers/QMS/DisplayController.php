<?php

namespace App\Http\Controllers\QMS;

use App\Http\Controllers\Controller;
use App\Models\Window;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Inertia\Inertia;
use App\Models\Ticket;

class DisplayController extends Controller
{
    // Display screen for the TV
    public function index()
    {
        try {
            return Inertia::render('Display/Index');
        } catch (\Exception $e) {
            Log::error('Error rendering display index', [
                'message' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);

            // Return a simple error page if Inertia rendering fails
            return Inertia::render('Error/Index', [
                'message' => 'Unable to load display. Please contact support.',
                'status' => 500
            ]);
        }
    }

    public function getCurrentTickets()
    {
        try {
            // raven, LAST NIMO GIBUHAT IS GI EAGER LOADING
            $windows = Window::with('currentTicket')->where('is_active', true)->get();

            $currentTickets = [];
            foreach ($windows as $window) {
                $currentTicket = $window->currentTicket;
                $currentTickets[] = [
                    'window' => $window->name,
                    'ticket' => $currentTicket ? $currentTicket->ticket_number : null,
                    'is_priority' => $currentTicket ? $currentTicket->is_priority : false,
                ];
            }

            // Add cashier waiting list
            $waitingList = Ticket::query()
                ->where('status', 'waiting')
                ->where('department', 'cashier')
                ->orderByDesc('is_priority')
                ->orderBy('issue_time')
                ->get(['ticket_number', 'is_priority', 'issue_time']);

            Log::info('Current tickets fetched successfully', [
                'window_count' => count($windows),
                'active_tickets' => count(array_filter($currentTickets, fn($t) => $t['ticket'] !== null)),
                'waiting_list_count' => $waitingList->count(),
            ]);

            return response()->json([
                'tickets' => $currentTickets,
                'waitingList' => $waitingList,
            ]);
        } catch (\Exception $e) {
            Log::error('Error fetching current tickets', [
                'message' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
            ]);

            return response()->json([
                'error' => 'Failed to retrieve current tickets',
                'message' => $e->getMessage()
            ], 500);
        }
    }
}
