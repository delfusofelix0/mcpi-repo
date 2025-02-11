<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class RegistrationController
{
    public function store(Request $request)
    {
        $validatedData = $request->validate([
            'firstname' => 'required|string|max:255',
            'mi' => 'nullable|string|max:10',
            'lastname' => 'required|string|max:255',
            'suffix' => 'nullable|string|max:10',
            'email' => 'required|email|max:255',
            'phone' => 'required|string|max:20',
            'religion' => 'nullable|string|max:255',
            'sogie' => 'required|string|max:255',
            'birthdate' => 'required|date',
            'address' => 'required|string',
            'highest_education' => 'required|string|max:255',
            'latest_company' => 'nullable|string|max:255',
            'present_position' => 'nullable|string|max:255',
            'status_employment' => 'nullable|string|max:255',
            'last_employment_date' => 'nullable|date',
            'eligibility' => 'nullable|string|max:255',
            'person_with_disability' => 'boolean',
            'disability_details' => 'nullable|string',
            'pregnant' => 'boolean',
            'indigenous_community' => 'boolean',
            'indigenous_details' => 'nullable|string',
            'document2' => 'required',
            'document1' => 'required',
            'document3' => 'required',
            'document4' => 'required',
            'document5' => 'required',
            'document6' => 'required',
            'document7' => 'required',
        ]);

        $registration = new Registration($validatedData);

        // Handle file uploads
        $fileFields = ['document1', 'document2', 'document3', 'document4', 'document5', 'document6', 'document7'];
        $filePathFields = [
            'application_letter_path',
            'personal_data_sheet_path',
            'performance_rating_path',
            'eligibility_proof_path',
            'transcript_path',
            'employment_proof_path',
            'training_certificates_path'
        ];

        foreach ($fileFields as $index => $field) {
            if ($request->hasFile($field)) {
                $path = $request->file($field)->store('registration_documents', 'public');
                $registration->{$filePathFields[$index]} = $path;
            }
        }

        $registration->save();

        return response()->json(['message' => 'Registration submitted successfully', 'data' => $registration], 201);
    }
}
