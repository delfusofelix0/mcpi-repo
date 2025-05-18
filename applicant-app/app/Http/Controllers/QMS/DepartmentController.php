<?php

namespace App\Http\Controllers\QMS;

use App\Http\Controllers\Controller;
use App\Models\Ticket;
use App\Models\UserWindow;
use App\Models\Window;
use Exception;
use Illuminate\Database\QueryException;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Inertia\Inertia;
use Throwable;

class DepartmentController extends Controller
{
    protected $department;

    public function __construct(string $department = null)
    {
        $this->department = $department;
    }

    protected function getDepartmentFromRole()
    {
        $user = Auth::user();

        // Map roles to departments
        $roleToDepartment = [
            'Cashier' => 'cashier',
            'Accounting' => 'accounting',
            'Registrar' => 'registrar',
        ];

        // Get the first matching role
        foreach ($roleToDepartment as $role => $dept) {
            if ($user->hasRole($role)) {
                return $dept;
            }
        }

        // Default to the department set in constructor
        return $this->department;
    }

    public function dashboard()
    {
        try {
            $department = $this->getDepartmentFromRole();
            $user = Auth::user();

            if (!$department) {
                Log::warning('User without department role accessed department dashboard', [
                    'user_id' => $user->id,
                    'user_email' => $user->email
                ]);
                return redirect()->back()->with('error', 'You do not have permission to access this department.');
            }

            // Get active window assignment
            $activeWindowAssignment = UserWindow::where('user_id', $user->id)
                ->where('is_active', true)
                ->first();

            $windowId = $activeWindowAssignment ? $activeWindowAssignment->window_id : null;

            // If no window is selected, show window selection page
            if (!$windowId) {
                // Get available windows for this department that aren't currently in use
                $usedWindowIds = UserWindow::where('is_active', true)
                    ->where('user_id', '!=', $user->id)
                    ->pluck('window_id');

                $windows = Window::where('is_active', true)
                    ->where('department', $department)
                    ->whereNotIn('id', $usedWindowIds)
                    ->get();

                if ($windows->isEmpty()) {
                    Log::info('No available windows for department', [
                        'department' => $department,
                        'user_id' => $user->id
                    ]);
                    return redirect()->back()->with('error', 'No windows are available for your department at this time.');
                }

                return Inertia::render('Department/SelectWindow', [
                    'windows' => $windows,
                    'department' => $department
                ]);
            }

            $window = Window::with(['currentTicket'])->find($windowId);

            if (!$window) {
                // Clear invalid window assignment
                UserWindow::where('user_id', $user->id)
                    ->where('is_active', true)
                    ->update([
                        'is_active' => false,
                        'released_at' => now()
                    ]);

                Log::error('User assigned to non-existent window', [
                    'user_id' => $user->id,
                    'window_id' => $windowId
                ]);

                return redirect()->back()->with('error', 'The selected window does not exist or has been deactivated.');
            }

            $waitingTicketsQuery = Ticket::where('status', 'waiting');

            if ($department === 'registrar') {
                $windowName = strtolower($window->name);

                if ($windowName === 'registrar 1 - grade school/college') {
                    $waitingTicketsQuery->where('department', 'registrar-gradecollege');
                } elseif ($windowName === 'registrar 2 - junior high school') {
                    $waitingTicketsQuery->where('department', 'registrar-jhs');
                } elseif ($windowName === 'registrar 3 - senior high school') {
                    $waitingTicketsQuery->where('department', 'registrar-shs');
                } else {
                    $waitingTicketsQuery->where('department', 'like', 'registrar-%');
                }
            } else {
                $waitingTicketsQuery->where('department', $department);
            }

            $waitingTickets = $waitingTicketsQuery->orderByDesc('is_priority')->orderBy('issue_time')->paginate(99);

            return Inertia::render('Department/Dashboard', [
                'window' => $window,
                'waitingTickets' => $waitingTickets,
                'currentTicket' => $window->currentTicket,
                'department' => $department
            ]);
        } catch (QueryException $e) {
            Log::error('Database error in department dashboard', [
                'error' => $e->getMessage(),
                'user_id' => Auth::id()
            ]);
            return redirect()->back()->with('error', 'A database error occurred while loading the dashboard.');
        } catch (Exception $e) {
            Log::error('Unexpected error in department dashboard', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
                'user_id' => Auth::id()
            ]);
            return redirect()->back()->with('error', 'An unexpected error occurred while loading the dashboard.');
        }
    }

    /**
     * Call the next priority ticket
     *
     * @param Request $request
     * @return RedirectResponse
     */
    public function callNextPriority(Request $request)
    {
        try {
            return $this->processNextTicket(true);
        } catch (QueryException $e) {
            Log::error('Database error calling next priority ticket', [
                'error' => $e->getMessage(),
                'user_id' => Auth::id()
            ]);
            return redirect()->back()->with('error', 'A database error occurred while calling the next priority ticket.');
        } catch (Throwable $e) {
            Log::error('Unexpected error calling next priority ticket', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
                'user_id' => Auth::id()
            ]);
            return redirect()->back()->with('error', 'An unexpected error occurred while calling the next priority ticket.');
        }
    }

    /**
     * Call the next regular ticket
     *
     * @param Request $request
     * @return RedirectResponse
     */
    public function callNext(Request $request)
    {
        try {
            return $this->processNextTicket(false);
        } catch (QueryException $e) {
            Log::error('Database error calling next ticket', [
                'error' => $e->getMessage(),
                'user_id' => Auth::id()
            ]);
            return redirect()->back()->with('error', 'A database error occurred while calling the next ticket.');
        } catch (Throwable $e) {
            Log::error('Unexpected error calling next ticket', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
                'user_id' => Auth::id()
            ]);
            return redirect()->back()->with('error', 'An unexpected error occurred while calling the next ticket.');
        }
    }

    /**
     * Process the next ticket based on priority flag
     *
     * @param bool $priorityOnly Whether to only process priority tickets
     * @return RedirectResponse
     */
    private function processNextTicket(bool $priorityOnly): RedirectResponse
    {
        try {
            $user = Auth::user();
            $department = $this->getDepartmentFromRole();

            if (!$department) {
                Log::warning('User without department role tried to process ticket', [
                    'user_id' => $user->id
                ]);
                return redirect()->back()->with('error', 'You do not have permission to call tickets.');
            }

            return DB::transaction(function () use ($user, $department, $priorityOnly) {
                // Find active window
                $activeWindow = UserWindow::where('user_id', $user->id)
                    ->where('is_active', true)
                    ->lockForUpdate()
                    ->first();

                if (!$activeWindow) {
                    Log::info('User tried to call ticket without selecting window', [
                        'user_id' => $user->id
                    ]);
                    return redirect()->back()->with('error', 'Please select a window first.');
                }

                $window = Window::with('currentTicket')->lockForUpdate()->find($activeWindow->window_id);

                if (!$window) {
                    // Clear broken assignment
                    UserWindow::where('user_id', $user->id)
                        ->where('is_active', true)
                        ->update(['is_active' => false, 'released_at' => now()]);

                    Log::error('User assigned to non-existent window when calling ticket', [
                        'user_id' => $user->id,
                        'window_id' => $activeWindow->window_id
                    ]);
                    return redirect()->back()->with('error', 'The selected window does not exist.');
                }

                // Prevent selecting new ticket if already serving
                if ($window->currentTicket) {
                    Log::info('User tried to call new ticket while already serving one', [
                        'user_id' => $user->id,
                        'window_id' => $window->id,
                        'current_ticket_id' => $window->currentTicket->id
                    ]);
                    return redirect()->back()->with('error', 'Please complete or skip the current ticket first.');
                }

                $query = Ticket::where('status', 'waiting');

                if ($priorityOnly) {
                    $query->where('is_priority', true);
                }

                // Department filtering
                if ($department === 'registrar') {
                    $windowName = strtolower($window->name);

                    $query->where(function ($q) use ($windowName) {
                        if ($windowName === 'registrar 1 - grade school/college') {
                            $q->where('department', 'registrar-gradecollege');
                        } elseif ($windowName === 'registrar 2 - junior high school') {
                            $q->where('department', 'registrar-jhs');
                        } elseif ($windowName === 'registrar 3 - senior high school') {
                            $q->where('department', 'registrar-shs');
                        } else {
                            $q->where('department', 'like', 'registrar-%');
                        }
                    });
                } else {
                    $query->where('department', $department);
                }

                // Always order by time
                $nextTicket = $query->orderBy('issue_time')->lockForUpdate()->first();

                if (!$nextTicket) {
                    $msg = $priorityOnly ? 'No priority tickets available.' : 'No tickets available.';
                    Log::info('No tickets available for call', [
                        'user_id' => $user->id,
                        'window_id' => $window->id,
                        'department' => $department,
                        'priority_only' => $priorityOnly
                    ]);
                    return redirect()->back()->with('error', $msg);
                }

                $nextTicket->update([
                    'window_id' => $window->id,
                    'status' => 'serving',
                    'call_time' => now(),
                ]);

                Log::info('Ticket called successfully', [
                    'user_id' => $user->id,
                    'window_id' => $window->id,
                    'ticket_id' => $nextTicket->id,
                    'ticket_number' => $nextTicket->ticket_number,
                    'is_priority' => $nextTicket->is_priority
                ]);

                $prefix = $nextTicket->is_priority ? 'Priority Ticket #' : 'Ticket #';
                return redirect()->back()->with('success', $prefix . $nextTicket->ticket_number . ' called.');
            });
        } catch (QueryException $e) {
            Log::error('Database error processing next ticket', [
                'error' => $e->getMessage(),
                'user_id' => Auth::id(),
                'priority_only' => $priorityOnly
            ]);
            return redirect()->back()->with('error', 'A database error occurred while processing the next ticket.');
        } catch (Throwable $e) {
            Log::error('Unexpected error processing next ticket', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
                'user_id' => Auth::id(),
                'priority_only' => $priorityOnly
            ]);
            return redirect()->back()->with('error', 'An unexpected error occurred while processing the next ticket.');
        }
    }


    /**
     * Mark a ticket as completed
     *
     * @param Request $request
     * @param Ticket $ticket
     * @return RedirectResponse
     */
    public function complete(Request $request, Ticket $ticket)
    {
        try {
            return DB::transaction(function () use ($request, $ticket) {
                $department = $this->getDepartmentFromRole();
                $user = Auth::user();

                if (!$department) {
                    Log::warning('User without department role tried to complete ticket', [
                        'user_id' => $user->id,
                        'ticket_id' => $ticket->id
                    ]);
                    return redirect()->back()->with('error', 'You do not have permission to complete tickets.');
                }

                // Special handling for registrar department
                if ($department === 'registrar') {
                    if (!str_starts_with($ticket->department, 'registrar-')) {
                        Log::warning('Registrar user tried to complete non-registrar ticket', [
                            'user_id' => $user->id,
                            'ticket_id' => $ticket->id,
                            'ticket_department' => $ticket->department
                        ]);
                        return redirect()->back()->with('error', 'You do not have permission to complete this ticket.');
                    }
                } elseif ($ticket->department !== $department) {
                    Log::warning('User tried to complete ticket from different department', [
                        'user_id' => $user->id,
                        'user_department' => $department,
                        'ticket_id' => $ticket->id,
                        'ticket_department' => $ticket->department
                    ]);
                    return redirect()->back()->with('error', 'You do not have permission to complete this ticket.');
                }

                $ticket = Ticket::lockForUpdate()->find($ticket->id);

                if (!$ticket) {
                    Log::error('Ticket not found when trying to complete', [
                        'user_id' => $user->id,
                        'ticket_id' => $ticket->id
                    ]);
                    return redirect()->back()->with('error', 'The ticket could not be found.');
                }

                if ($ticket->status !== 'serving') {
                    Log::warning('User tried to complete non-serving ticket', [
                        'user_id' => $user->id,
                        'ticket_id' => $ticket->id,
                        'ticket_status' => $ticket->status
                    ]);
                    return redirect()->back()->with('error', 'Only serving tickets can be completed.');
                }

                $ticket->status = 'completed';
                $ticket->completion_time = now();
                $ticket->save();

                Log::info('Ticket completed successfully', [
                    'user_id' => $user->id,
                    'ticket_id' => $ticket->id,
                    'ticket_number' => $ticket->ticket_number
                ]);

                return redirect()->back()->with('success', 'Ticket #' . $ticket->ticket_number . ' completed.');
            });
        } catch (QueryException $e) {
            Log::error('Database error completing ticket', [
                'error' => $e->getMessage(),
                'user_id' => Auth::id(),
                'ticket_id' => $ticket->id
            ]);
            return redirect()->back()->with('error', 'A database error occurred while completing the ticket.');
        } catch (Throwable $e) {
            Log::error('Unexpected error completing ticket', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
                'user_id' => Auth::id(),
                'ticket_id' => $ticket->id
            ]);
            return redirect()->back()->with('error', 'An unexpected error occurred while completing the ticket.');
        }
    }

    /**
     * Skip a ticket and return it to the queue
     *
     * @param Request $request
     * @param Ticket $ticket
     * @return RedirectResponse
     */
    public function skip(Request $request, Ticket $ticket)
    {
        try {
            return DB::transaction(function () use ($request, $ticket) {
                $department = $this->getDepartmentFromRole();
                $user = Auth::user();

                if (!$department) {
                    Log::warning('User without department role tried to skip ticket', [
                        'user_id' => $user->id,
                        'ticket_id' => $ticket->id
                    ]);
                    return redirect()->back()->with('error', 'You do not have permission to skip tickets.');
                }

                // Special handling for registrar department
                if ($department === 'registrar') {
                    if (!str_starts_with($ticket->department, 'registrar-')) {
                        Log::warning('Registrar user tried to skip non-registrar ticket', [
                            'user_id' => $user->id,
                            'ticket_id' => $ticket->id,
                            'ticket_department' => $ticket->department
                        ]);
                        return redirect()->back()->with('error', 'You do not have permission to skip this ticket.');
                    }
                } elseif ($ticket->department !== $department) {
                    Log::warning('User tried to skip ticket from different department', [
                        'user_id' => $user->id,
                        'user_department' => $department,
                        'ticket_id' => $ticket->id,
                        'ticket_department' => $ticket->department
                    ]);
                    return redirect()->back()->with('error', 'You do not have permission to skip this ticket.');
                }

                $ticket = Ticket::lockForUpdate()->find($ticket->id);

                if (!$ticket) {
                    Log::error('Ticket not found when trying to skip', [
                        'user_id' => $user->id,
                        'ticket_id' => $ticket->id
                    ]);
                    return redirect()->back()->with('error', 'The ticket could not be found.');
                }

                if ($ticket->status !== 'serving') {
                    Log::warning('User tried to skip non-serving ticket', [
                        'user_id' => $user->id,
                        'ticket_id' => $ticket->id,
                        'ticket_status' => $ticket->status
                    ]);
                    return redirect()->back()->with('error', 'Only serving tickets can be skipped.');
                }

                $ticket->status = 'skipped';
                $ticket->window_id = null;
                $ticket->save();

                Log::info('Ticket skipped successfully', [
                    'user_id' => $user->id,
                    'ticket_id' => $ticket->id,
                    'ticket_number' => $ticket->ticket_number
                ]);

                return redirect()->back()->with('success', 'Ticket #' . $ticket->ticket_number . ' skipped.');
            });
        } catch (QueryException $e) {
            Log::error('Database error skipping ticket', [
                'error' => $e->getMessage(),
                'user_id' => Auth::id(),
                'ticket_id' => $ticket->id
            ]);
            return redirect()->back()->with('error', 'A database error occurred while skipping the ticket.');
        } catch (Throwable $e) {
            Log::error('Unexpected error skipping ticket', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
                'user_id' => Auth::id(),
                'ticket_id' => $ticket->id
            ]);
            return redirect()->back()->with('error', 'An unexpected error occurred while skipping the ticket.');
        }
    }

    public function selectWindow(Request $request)
    {
        try {
            $request->validate([
                'window_id' => 'required|exists:windows,id'
            ]);

            $windowId = $request->window_id;
            $userId = Auth::id();
            $department = $this->getDepartmentFromRole();

            if (!$department) {
                Log::warning('User without department role tried to select window', [
                    'user_id' => $userId,
                    'window_id' => $windowId
                ]);
                return redirect()->back()->with('error', 'You do not have permission to select windows.');
            }

            // Check if window belongs to user's department
            $window = Window::find($windowId);
            if (!$window || $window->department !== $department) {
                Log::warning('User tried to select window from different department', [
                    'user_id' => $userId,
                    'window_id' => $windowId,
                    'user_department' => $department,
                    'window_department' => $window ? $window->department : 'not found'
                ]);
                return redirect()->back()->with('error', 'You can only select windows from your department.');
            }

            // Check if window is active
            if (!$window->is_active) {
                Log::warning('User tried to select inactive window', [
                    'user_id' => $userId,
                    'window_id' => $windowId
                ]);
                return redirect()->back()->with('error', 'This window is currently inactive and cannot be selected.');
            }

            // Check if window is already in use by another user
            $windowInUse = UserWindow::where('window_id', $windowId)
                ->where('user_id', '!=', $userId)
                ->where('is_active', true)
                ->exists();

            if ($windowInUse) {
                Log::info('User tried to select window already in use', [
                    'user_id' => $userId,
                    'window_id' => $windowId
                ]);
                return redirect()->back()->with('error', 'This window is already being used by another user.');
            }

            // Deactivate any previously active windows for this user
            UserWindow::where('user_id', $userId)
                ->where('is_active', true)
                ->update([
                    'is_active' => false,
                    'released_at' => now()
                ]);

            // Create new active window assignment
            UserWindow::create([
                'user_id' => $userId,
                'window_id' => $windowId,
                'is_active' => true,
                'assigned_at' => now()
            ]);

            Log::info('User selected window successfully', [
                'user_id' => $userId,
                'window_id' => $windowId,
                'department' => $department
            ]);

            return redirect()->back()->with('success', 'Window selected successfully.');
        } catch (QueryException $e) {
            Log::error('Database error selecting window', [
                'error' => $e->getMessage(),
                'user_id' => Auth::id()
            ]);
            return redirect()->back()->with('error', 'A database error occurred while selecting the window.');
        } catch (\Exception $e) {
            Log::error('Unexpected error selecting window', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
                'user_id' => Auth::id()
            ]);
            return redirect()->back()->with('error', 'An unexpected error occurred while selecting the window.');
        }
    }

}
