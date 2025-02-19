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
//        $registrations = Registration::latest()->get();
        $workPositions = WorkPosition::latest()->paginate(5);
        $registrations = Registration::paginate(5);
        return view('dashboard', compact('registrations', 'workPositions'));
    }

    public function view($id)
    {
        $applicant = Registration::findOrFail($id);
        return view('view-applicant', compact('applicant'));
    }
}
