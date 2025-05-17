<?php

namespace App\Http\Controllers\QMS;

use App\Http\Controllers\Controller;
use App\Models\Window;
use Illuminate\Http\Request;
use Inertia\Inertia;

class DisplayController extends Controller
{
    // Display screen for the TV
    public function index()
    {
        return Inertia::render('Display/Index');
    }

    public function getCurrentTickets()
    {
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
        return response()->json([
            'tickets' => $currentTickets
        ]);
    }
}
