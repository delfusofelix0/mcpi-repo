<?php

namespace App\Http\Controllers;

use App\Models\Appointment;
use App\Models\Office;
use Inertia\Inertia;

class AppointmentSettingController extends Controller
{
    public function index()
    {
        $offices = Office::all();
        $appointments = Appointment::with(['office' => function($query) {
            $query->withTrashed();
        }])->orderBy('date', 'desc')->get();

        return Inertia::render('Appointment/AppointmentSettings', [
            'offices' => $offices,
            'appointments' => $appointments
        ]);
    }
}
