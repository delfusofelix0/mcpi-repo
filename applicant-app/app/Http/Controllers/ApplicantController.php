<?php

namespace App\Http\Controllers;

use App\Models\Position;
use App\Models\Registration;
use App\Models\WorkPosition;
use Illuminate\Http\Request;

class ApplicantController extends Controller
{
    public function index(Request $request)
    {
        $query = Registration::query();

        // Sorting
        $sortColumn = $request->get('sort', 'created_at');
        $sortDirection = $request->get('direction', 'desc');

        if ($sortColumn === 'position') {
            $query->join('work_positions', 'registrations.position_id', '=', 'work_positions.id')
                ->orderBy('work_positions.name', $sortDirection);
        } elseif ($sortColumn === 'status') {
            $query->orderBy('status', $sortDirection);
        } else {
            $query->orderBy('created_at', 'desc');
        }

        // Fetch registrations with position relation
        $registrations = $query->paginate(10)->appends($request->query());

        $workPositions = WorkPosition::latest()->get();

        return view('dashboard', compact('registrations', 'workPositions', 'sortColumn', 'sortDirection'));
    }

    public function view($id)
    {
        $applicant = Registration::findOrFail($id);
        return view('view-applicant', compact('applicant'));
    }
}
