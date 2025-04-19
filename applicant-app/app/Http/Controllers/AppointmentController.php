<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreAppointmentRequest;
use App\Models\Appointment;
use App\Models\Office;
use App\Services\M360SmsService;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Inertia\Inertia;

class AppointmentController extends Controller
{
    public function index()
    {
        $offices = Office::where('is_available', true)->get();

        return Inertia::render('Appointment/AppointmentForm', [
            'offices' => $offices,
        ]);
    }

    /**
     * Get reserved time slots for a specific date.
     *
     * @param \Illuminate\Http\Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function getReservedTimeSlots(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'date' => 'required|date',
            'office_id' => 'required|exists:offices,id'
        ]);

        if ($validator->fails()) {
            //log the error message
            \Log::error('Validation failed: ' . $validator->errors()->first());
            return response()->json(['error' => $validator->errors()], 422);
        }

        try {
            $reservedSlots = Appointment::where('date', $request->date)
                ->where('office_id', $request->office_id)
                ->pluck('time')
                ->toArray();

            return response()->json(['reservedSlots' => $reservedSlots]);
        } catch (Exception $e) {
            \Log::error('Failed to fetch reserved time slots: ' . $e->getMessage());
            return response()->json(['error' => 'Failed to fetch reserved time slots. Please try again.'], 500);
        }
    }

    /**
     * Store a newly created appointment in storage.
     *
     * @param \Illuminate\Http\Request $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function store(StoreAppointmentRequest $request)
    {
        try {
            Appointment::createAppointment($request->validated());

            // Send SMS notification
//            $this->sendAppointmentSms($appointment);

            return redirect()->back()->with('success', 'Appointment booked successfully!');
        } catch (Exception $e) {
            \Log::error('Failed to book appointment: ' . $e->getMessage());
            return redirect()->back()
                ->withErrors(['error' => 'Failed to book appointment'])
                ->withInput();
        }
    }

    /**
     * Send SMS confirmation for the appointment.
     *
     * @param \App\Models\Appointment $appointment
     * @param \App\Services\M360SmsService $smsService
     * @return \Illuminate\Http\RedirectResponse
     */
    public function sendSms(Appointment $appointment)
    {
        try {
            // Send SMS notification
            $this->sendAppointmentSms($appointment);

            return redirect()->back()->with('success', 'SMS sent successfully!');
        } catch (Exception $e) {
            \Log::error('Failed to send SMS: ' . $e->getMessage());
            return redirect()->back()->withErrors(['message' => 'Failed to send SMS']);
        }
    }

    /**
     * Send SMS notification for the appointment.
     *
     * @param \App\Models\Appointment $appointment
     * @return void
     */
    private function sendAppointmentSms(Appointment $appointment)
    {
        try {
            // Load the office relationship
            $appointment->load('office');

            // Format the date
            $formattedDate = date('F j, Y', strtotime($appointment->date));

            // Prepare the message
            $message = "Your appointment has been confirmed at {$appointment->office->name} on $formattedDate at $appointment->time. Please present a VALID ID and this confirmation message to the School Guard upon arrival. Thank you!";

            // Send SMS using the M360SmsService
            app(M360SmsService::class)->send(
                $appointment->contact,
                $message
            );

            \Log::info("SMS sent for appointment #{$appointment->id}");
        } catch (Exception $e) {
            \Log::error("Failed to send appointment SMS: " . $e->getMessage());
            // Don't throw the exception - we don't want to fail the appointment creation
            // if SMS sending fails
        }
    }
}
