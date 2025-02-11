<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Registration;
use Illuminate\Support\Facades\Validator;

class RegistrationController
{
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'firstname' => 'required|string|max:255',
            'mi' => 'nullable|string|max:1',
            'lastname' => 'required|string|max:255',
            'suffix' => 'nullable|string|max:3',
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
            'person_with_disability' => 'nullable|boolean',
            'disability_details' => 'nullable|string',
            'pregnant' => 'nullable|boolean',
            'indigenous_community' => 'nullable|boolean',
            'indigenous_details' => 'nullable|string',
            'document1' => 'file|mimes:pdf|max:10240',
            'document2' => 'file|mimes:pdf|max:10240',
            'document3' => 'file|mimes:pdf|max:10240',
            'document4' => 'file|mimes:pdf|max:10240',
            'document5' => 'file|mimes:pdf|max:10240',
            'document6' => 'file|mimes:pdf|max:10240',
            'document7' => 'file|mimes:pdf|max:10240',
        ]);

        if ($validator->fails()) {
            return redirect()->back()
                ->withErrors($validator)
                ->withInput();
        }

        $validatedData = $validator->validated();

        // Remove document fields from validatedData
        $documentFields = ['document1', 'document2', 'document3', 'document4', 'document5', 'document6', 'document7'];
        foreach ($documentFields as $field) {
            unset($validatedData[$field]);
        }

        $registration = new Registration($validatedData);

        // Handle file uploads
        $fileFields = [
            'document1' => 'application_letter_path',
            'document2' => 'personal_data_sheet_path',
            'document3' => 'performance_rating_path',
            'document4' => 'eligibility_proof_path',
            'document5' => 'transcript_path',
            'document6' => 'employment_proof_path',
            'document7' => 'training_certificates_path'
        ];

        foreach ($fileFields as $inputField => $dbField) {
            if ($request->hasFile($inputField)) {
                $path = $request->file($inputField)->store('registration_documents', 'public');
                $registration->$dbField = $path;
            }
        }

        $registration->save();

        return redirect()->back()->with('success', 'Registration submitted successfully');
    }
}
