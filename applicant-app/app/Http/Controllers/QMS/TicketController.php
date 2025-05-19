<?php

namespace App\Http\Controllers\QMS;

use App\Http\Controllers\Controller;
use App\Models\Ticket;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Validation\Rule;
use Inertia\Inertia;

class TicketController extends Controller
{
    private const DEPARTMENTS = [
        'registrar-gradecollege',
        'registrar-jhs',
        'registrar-shs',
        'accounting',
        'cashier',
    ];

    // Simple ticket generation page
    public function index(Request $request)
    {
        return Inertia::render('Tickets/Generate', [
            'token' => $request->query('token')
        ]);
    }

    // Generate a new ticket
    public function generate(Request $request)
    {
        try {
            $validated = $request->validate([
                'department' => ['required', Rule::in(self::DEPARTMENTS)],
                'is_priority' => 'boolean',
            ]);

            $department = $validated['department'];
            $isPriority = $validated['is_priority'] ?? false;

            // Determine prefix based on department
            $prefix = match ($department) {
                'registrar-gradecollege' => 'GC',
                'registrar-jhs' => 'JHS',
                'registrar-shs' => 'SHS',
                default => strtoupper(substr($department, 0, 1)),
            };

            // Get the current count for today for this department
            $count = Ticket::query()->where('department', $department)
                    ->whereDate('created_at', today())
                    ->count() + 1;

            $ticketNumber = $prefix . str_pad($count, 3, '0', STR_PAD_LEFT); // RG001, RJ001, RS001, C001, A001, etc.

            // Create the ticket
            $ticket = Ticket::create([
                'ticket_number' => $ticketNumber,
                'department' => $department,
                'is_priority' => $isPriority,
                'status' => 'waiting',
                'issue_time' => now(),
            ]);

            Log::info('Ticket generated successfully', [
                'ticket_number' => $ticketNumber,
                'department' => $department,
                'is_priority' => $isPriority,
                'user_ip' => $request->ip(),
                'user_agent' => $request->userAgent()
            ]);

            // 🔄 Add this check to return JSON if requested by Electron (not Vue)
            if ($request->expectsJson()) {
                return response()->json($ticket);
            }

            // Default for Vue/Inertia for fallback
            return Inertia::render('Tickets/Issued', [
                'token' => $request->query('token'),
                'ticket' => $ticket,
            ]);
        } catch (\Illuminate\Validation\ValidationException $e) {
            Log::warning('Ticket generation validation failed', [
                'errors' => $e->errors(),
                'user_ip' => $request->ip(),
                'user_agent' => $request->userAgent()
            ]);
            throw $e; // Re-throw to let Laravel handle the validation response
        } catch (\Exception $e) {
            Log::error('Error generating ticket', [
                'message' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
                'user_ip' => $request->ip(),
                'user_agent' => $request->userAgent(),
                'request_data' => $request->all()
            ]);

            if ($request->expectsJson()) {
                return response()->json([
                    'error' => 'Failed to generate ticket',
                    'message' => $e->getMessage()
                ], 500);
            }

            return redirect()->back()
                ->with('error', 'Failed to generate ticket: ' . $e->getMessage())
                ->withInput();
        }
    }

    public function deleteAll()
    {
        Ticket::truncate();

        return redirect()->back()->with('success', 'All tickets have been deleted successfully.');
    }
}
