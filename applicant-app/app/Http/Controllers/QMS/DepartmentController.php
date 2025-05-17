<?php

namespace App\Http\Controllers\QMS;

use App\Http\Controllers\Controller;
use App\Models\Ticket;
use App\Models\UserWindow;
use App\Models\Window;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
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
        $department = $this->getDepartmentFromRole();
        $user = Auth::user();

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

            return redirect()->back()->with('error', 'The selected window does not exist.');
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
    }

    /**
     * @throws Throwable
     */
    public function callNextPriority(Request $request)
    {
        return $this->processNextTicket(true);
    }

    /**
     * @throws Throwable
     */
    public function callNext(Request $request)
    {
        return $this->processNextTicket(false);
    }

    /**
     * Process the next ticket based on priority flag
     *
     * @param bool $priorityOnly Whether to only process priority tickets
     * @return RedirectResponse
     * @throws Throwable
     */
    private function processNextTicket(bool $priorityOnly): RedirectResponse
    {
        $user = Auth::user();
        $department = $this->getDepartmentFromRole();

        return DB::transaction(function () use ($user, $department, $priorityOnly) {
            // Find active window
            $activeWindow = UserWindow::where('user_id', $user->id)
                ->where('is_active', true)
                ->lockForUpdate()
                ->first();

            if (!$activeWindow) {
                return redirect()->back()->with('error', 'Please select a window first.');
            }

            $window = Window::with('currentTicket')->lockForUpdate()->find($activeWindow->window_id);

            if (!$window) {
                // Clear broken assignment
                UserWindow::where('user_id', $user->id)
                    ->where('is_active', true)
                    ->update(['is_active' => false, 'released_at' => now()]);

                return redirect()->back()->with('error', 'The selected window does not exist.');
            }

            // Prevent selecting new ticket if already serving
            if ($window->currentTicket) {
                return redirect()->back()->with('error', 'Please complete or skip the current ticket first.');
            }

            $query = Ticket::where('status', 'waiting')
                ->where('is_priority', $priorityOnly);

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
                $msg = $priorityOnly ? 'No priority tickets available.' : 'No non-priority tickets available.';
                return redirect()->back()->with('error', $msg);
            }

            $nextTicket->update([
                'window_id' => $window->id,
                'status' => 'serving',
                'call_time' => now(),
            ]);

            $prefix = $priorityOnly ? 'Priority Ticket #' : 'Ticket #';
            return redirect()->back()->with('success', $prefix . $nextTicket->ticket_number . ' called.');
        });
    }


    public function complete(Request $request, Ticket $ticket)
    {
        return DB::transaction(function () use ($request, $ticket) {
            $department = $this->getDepartmentFromRole();

            // Special handling for registrar department
            if ($department === 'registrar') {
                if (!str_starts_with($ticket->department, 'registrar-')) {
                    return redirect()->back()->with('error', 'You do not have permission to complete this ticket.');
                }
            } elseif ($ticket->department !== $department) {
                return redirect()->back()->with('error', 'You do not have permission to complete this ticket.');
            }

            $ticket = Ticket::lockForUpdate()->find($ticket->id);

            if (!$ticket || $ticket->status !== 'serving') {
                return redirect()->back()->with('error', 'Only serving tickets can be completed.');
            }

            $ticket->status = 'completed';
            $ticket->completion_time = now();
            $ticket->save();

            return redirect()->back()->with('success', 'Ticket #' . $ticket->ticket_number . ' completed.');
        });
    }

    public function skip(Request $request, Ticket $ticket)
    {
        return DB::transaction(function () use ($request, $ticket) {
            $department = $this->getDepartmentFromRole();

            // Special handling for registrar department
            if ($department === 'registrar') {
                if (!str_starts_with($ticket->department, 'registrar-')) {
                    return redirect()->back()->with('error', 'You do not have permission to skip this ticket.');
                }
            } elseif ($ticket->department !== $department) {
                return redirect()->back()->with('error', 'You do not have permission to skip this ticket.');
            }

            $ticket = Ticket::lockForUpdate()->find($ticket->id);

            if (!$ticket || $ticket->status !== 'serving') {
                return redirect()->back()->with('error', 'Only serving tickets can be skipped.');
            }

            $ticket->status = 'skipped';
            $ticket->window_id = null;
            $ticket->save();

            return redirect()->back()->with('success', 'Ticket #' . $ticket->ticket_number . ' skipped.');
        });
    }

    public function selectWindow(Request $request)
    {
        $request->validate([
            'window_id' => 'required|exists:windows,id'
        ]);

        $windowId = $request->window_id;
        $userId = Auth::id();

        // Check if window is already in use by another user
        $windowInUse = UserWindow::where('window_id', $windowId)
            ->where('user_id', '!=', $userId)
            ->where('is_active', true)
            ->exists();

        if ($windowInUse) {
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

        return redirect()->back()->with('success', 'Window selected successfully.');
    }

}
