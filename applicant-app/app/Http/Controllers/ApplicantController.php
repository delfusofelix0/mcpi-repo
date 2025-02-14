<?php

namespace App\Http\Controllers;

use App\Models\Position;
use App\Models\Applicant;
use App\Models\Registration;
use Illuminate\Http\Request;

class ApplicantController extends Controller
{
    public function index()
    {
        $registrations = Registration::latest()->get();
        return view('dashboard', compact('registrations'));
    }

    public function view($id)
    {
        $applicant = Registration::findOrFail($id);
        return view('view-applicant', compact('applicant'));
    }
}
