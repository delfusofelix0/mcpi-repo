<?php

namespace App\Http\Controllers;

use App\Models\Office;
use Illuminate\Http\Request;
use Inertia\Inertia;

class OfficeController extends Controller
{
    public function index()
    {
        $offices = Office::all();

        return Inertia::render('Appointment/AppointmentSettings', [
            'offices' => $offices
        ]);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'is_available' => 'boolean',
        ]);

        Office::create($validated);

        return redirect()->back();
    }

    public function updateOfficeName(Request $request, Office $office)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
        ]);

        $office->update($validated);

        return redirect()->back();
    }

    public function updateAvailability(Request $request, Office $office)
    {
        $validated = $request->validate([
            'is_available' => 'required|boolean',
        ]);

        $office->update($validated);

        return redirect()->back();
    }

    public function destroy(Office $office)
    {
        $office->delete();

        return redirect()->back();
    }
}
