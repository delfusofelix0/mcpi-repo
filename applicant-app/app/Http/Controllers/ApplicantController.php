<?php

namespace App\Http\Controllers;

use App\Models\Position;
use App\Models\Applicant;
use App\Models\Registration;
use Illuminate\Http\Request;
use Inertia\Inertia;

class ApplicantController extends Controller
{
    public function index()
    {
        $query = Registration::latest();

        $registrations = $query->get();

        return Inertia::render('Dashboard', [
            'registrations' => $registrations
        ]);
    }

    public function view($id)
    {
        $applicant = Registration::findOrFail($id);
        return Inertia::render('Dashboard', [
            'applicant' => $applicant
        ]);
    }
}
