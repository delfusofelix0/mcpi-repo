<?php

namespace App\Http\Controllers;

use App\Models\WorkPosition;
use App\Models\Registration;
use Illuminate\Http\Request;
use Inertia\Inertia;

class ApplicantController extends Controller
{
    public function view($id)
    {
        $applicant = Registration::findOrFail($id);
        return Inertia::render('Dashboard', [
            'applicant' => $applicant
        ]);
    }

    public function statusStore(Request $request, $id)
    {
        $request->validate([
            'status' => 'required|in:Pending,Hired,Option,Viewed,Rejected',
        ]);

        $registration = Registration::findOrFail($id);
        $registration->status = $request->status;
        $registration->save();

        return redirect()->back()->with('success', 'Applicant status updated successfully.');
    }

    public function show($id)
    {
        $applicant = Registration::with(['documents' => function ($query) {
            $query->select('id', 'registration_id', 'document_type', 'file_path');
        }])->findOrFail($id);

        return Inertia::render('Applicant/ApplicantInformation', [
            'applicant' => $applicant
        ]);
    }

    public function destroyApplicant($id)
    {
        try {
            $registration = Registration::findOrFail($id);
            $registration->delete();

            return redirect()->route('dashboard')->with('success', 'Applicant deleted successfully');
        } catch (\Exception $e) {
            return redirect()->route('dashboard')->with('error', 'Failed to delete applicant');
        }
    }
}
