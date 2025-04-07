<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreAppointmentRequest;
use App\Models\Appointment;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Inertia\Inertia;

class AppointmentController extends Controller
{
    public function index()
    {
        return Inertia::render('Appointment/AppointmentSettings');
    }

    /**
     * Store a newly created appointment in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function store(StoreAppointmentRequest $request)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255',
            'contact' => 'required|string|max:255',
            'date' => 'required|date|after_or_equal:today',
            'time' => 'required|string',
        ]);

        if ($validator->fails()) {
            return redirect()->back()
                ->withErrors($validator)
                ->withInput();
        }

        Appointment::create($validator->validated());

        return redirect()->back()->with('success', 'Appointment booked successfully!');
    }

    /**
     * Get reserved time slots for a specific date.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Inertia\Response
     */
    public function getReservedTimeSlots(StoreAppointmentRequest $request)
    {
        $validator = Validator::make($request->all(), [
            'date' => 'required|date|after_or_equal:today',
        ]);

        if ($validator->fails()) {
            return Inertia::render('Appointment/AppointmentForm', [
                'errors' => $validator->errors(),
            ])->withViewData(['error' => 'Validation failed']);
        }

        $reservedSlots = Appointment::where('date', $request->date)
            ->pluck('time')
            ->toArray();

        return Inertia::render('Appointment/AppointmentForm', [
            'reservedSlots' => $reservedSlots,
        ]);
    }
}
