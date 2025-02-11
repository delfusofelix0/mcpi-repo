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
            'documents.application_letter' => 'required|file|mimes:pdf|max:10240',
            'documents.personal_data_sheet' => 'required|file|mimes:pdf|max:10240',
            'documents.performance_rating' => 'required|file|mimes:pdf|max:10240',
            'documents.eligibility_proof' => 'required|file|mimes:pdf|max:10240',
            'documents.transcript' => 'required|file|mimes:pdf|max:10240',
            'documents.employment_proof' => 'required|file|mimes:pdf|max:10240',
            'documents.training_certificates' => 'required|file|mimes:pdf|max:10240',
        ]);

        if ($validator->fails()) {
            return redirect()->back()
                ->withErrors($validator)
                ->withInput();
        }

        $validatedData = $validator->validated();

        // Remove documents from validatedData
        $documents = $validatedData['documents'];
        unset($validatedData['documents']);

        $registration = Registration::create($validatedData);

        // Handle file uploads
        foreach ($documents as $type => $file) {
            $path = $file->store('registration_documents', 'public');
            $registration->documents()->create([
                'document_type' => $type,
                'file_path' => $path,
            ]);
        }

        return redirect()->back()->with('success', 'Registration submitted successfully');
    }
}
