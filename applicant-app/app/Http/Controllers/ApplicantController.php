<?php

namespace App\Http\Controllers;

use App\Models\Registration;
use App\Services\M360SmsService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Inertia\Inertia;
use function Laravel\Prompts\error;

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
        $validator = Validator::make($request->all(), [
            'status' => 'required|string',
            'remarks' => 'nullable|string',
            'remarks_date' => 'nullable|date',
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        $applicant = Registration::findOrFail($id);

        // Only allow remarks_date for specific statuses
        $allowedStatusesForDate = ['Hired', 'For Interview', 'For Demo', 'Recommended'];
        $remarksDate = in_array($request->status, $allowedStatusesForDate) ? $request->remarks_date : null;

        $applicant->update([
            'status' => $request->status,
            'remarks' => $request->remarks,
            'remarks_date' => $remarksDate,
        ]);

        return redirect()->back()->with('success', 'Status updated successfully');
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

    public function sendSms(Request $request, $id, M360SmsService $smsService)
    {
        $validator = Validator::make($request->all(), [
            'phone' => 'required|string',
            'message' => 'required|string|max:2000',
        ]);

        if ($validator->fails()) {
            return back()->withErrors($validator)->withInput();
        }

        Registration::findOrFail($id);

        // Send SMS using the M360SmsService
        $success = $smsService->send($request->phone, $request->message);

        if ($success) {
            // FUTURE TODO:
            // Log the SMS in your database if needed
            // You could create an SmsLog model to track all sent messages

            return back()->with('success', 'SMS sent successfully');
        }
        return back()->withErrors(['message' => 'Failed to send SMS. Please try again.']);
    }
}
