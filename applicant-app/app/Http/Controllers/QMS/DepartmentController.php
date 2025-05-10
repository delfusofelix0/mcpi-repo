<?php

namespace App\Http\Controllers\QMS;

use App\Http\Controllers\Controller;
use App\Models\Ticket;
use App\Models\UserWindow;
use App\Models\Window;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Inertia\Inertia;

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

        $waitingTickets = $waitingTicketsQuery->orderBy('issue_time')->paginate(5);

        return Inertia::render('Department/Dashboard', [
            'window' => $window,
            'waitingTickets' => $waitingTickets,
            'currentTicket' => $window->currentTicket,
            'department' => $department
        ]);
    }

    /**
     * @throws \Throwable
     */
    public function callNext(Request $request)
    {
        $department = $this->getDepartmentFromRole();
        $user = Auth::user();

        // Get active window assignment
        $activeWindowAssignment = UserWindow::where('user_id', $user->id)
            ->where('is_active', true)
            ->first();

        $windowId = $activeWindowAssignment ? $activeWindowAssignment->window_id : null;

        if (!$windowId) {
            return redirect()->back()->with('error', 'Please select a window first.');
        }

        $window = Window::with(['currentTicket' => function($query) use ($department) {
            $query->where('department', $department);
        }])->find($windowId);

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

        if ($window->currentTicket) {
            return redirect()->back()->with('error', 'Please complete or skip the current ticket first.');
        }

        $nextTicket = null;

        DB::transaction(function () use (&$nextTicket, $window, $department) {
            $departmentQuery = Ticket::where('status', 'waiting');

            if ($department === 'registrar') {
                $windowName = strtolower($window->name);

                // Check window name to determine which registrar department to serve
                if ($windowName === 'registrar 1 - grade school/college') {
                    $departmentQuery->where('department', 'registrar-gradecollege');
                } elseif ($windowName === 'registrar 2 - junior high school') {
                    $departmentQuery->where('department', 'registrar-jhs');
                } elseif ($windowName === 'registrar 3 - senior high school') {
                    $departmentQuery->where('department', 'registrar-shs');
                } else {
                    // Fallback to any registrar ticket if window name doesn't match
                    $departmentQuery->where(function($query) {
                        $query->where('department', 'like', 'registrar-%');
                    });
                }
            } else {
                $departmentQuery->where('department', $department);
            }

            $nextTicket = $departmentQuery
                ->orderBy('issue_time')
                ->lockForUpdate()
                ->first();

            if ($nextTicket) {
                $nextTicket->window_id = $window->id;
                $nextTicket->status = 'serving';
                $nextTicket->call_time = now();
                $nextTicket->save();
            }
        });

        if (!$nextTicket) {
            return redirect()->back()->with('error', 'No waiting tickets available.');
        }

        return redirect()->back()->with('success', 'Ticket #' . $nextTicket->ticket_number . ' called.');
    }

    public function complete(Request $request, Ticket $ticket)
    {
        $department = $this->getDepartmentFromRole();

        // Special handling for registrar department
        if ($department === 'registrar') {
            if (!str_starts_with($ticket->department, 'registrar-')) {
                return redirect()->back()->with('error', 'You do not have permission to complete this ticket.');
            }
        } elseif ($ticket->department !== $department) {
            return redirect()->back()->with('error', 'You do not have permission to complete this ticket.');
        }

        if ($ticket->status !== 'serving') {
            return redirect()->back()->with('error', 'Only serving tickets can be completed.');
        }

        $ticket->status = 'completed';
        $ticket->completion_time = now();
        $ticket->save();

        return redirect()->back()->with('success', 'Ticket #' . $ticket->ticket_number . ' completed.');
    }

    public function skip(Request $request, Ticket $ticket)
    {
        $department = $this->getDepartmentFromRole();

        // Special handling for registrar department
        if ($department === 'registrar') {
            if (!str_starts_with($ticket->department, 'registrar-')) {
                return redirect()->back()->with('error', 'You do not have permission to skip this ticket.');
            }
        } elseif ($ticket->department !== $department) {
            return redirect()->back()->with('error', 'You do not have permission to skip this ticket.');
        }

        if ($ticket->status !== 'serving') {
            return redirect()->back()->with('error', 'Only serving tickets can be skipped.');
        }

        $ticket->status = 'skipped';
        $ticket->window_id = null;
        $ticket->save();

        return redirect()->back()->with('success', 'Ticket #' . $ticket->ticket_number . ' skipped.');
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
