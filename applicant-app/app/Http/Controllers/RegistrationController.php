<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Registration;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;
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
            'cf-turnstile-response' => ['required', Rule::turnstile()],
        ],[
            'firstname.required' => 'First name is required.',
            'firstname.max' => 'First name must not exceed 255 characters.',
            'mi.max' => 'Middle initial must not exceed 1 character.',
            'lastname.required' => 'Last name is required.',
            'lastname.max' => 'Last name must not exceed 255 characters.',
            'suffix.max' => 'Suffix must not exceed 3 characters.',
            'email.required' => 'Email address is required.',
            'email.email' => 'Please enter a valid email address.',
            'email.max' => 'Email must not exceed 255 characters.',
            'phone.required' => 'Phone number is required.',
            'phone.max' => 'Phone number must not exceed 20 characters.',
            'sogie.required' => 'SOGIE information is required.',
            'sogie.max' => 'SOGIE information must not exceed 255 characters.',
            'birthdate.required' => 'Birthdate is required.',
            'birthdate.date' => 'Please enter a valid date for birthdate.',
            'address.required' => 'Address is required.',
            'highest_education.required' => 'Highest education is required.',
            'highest_education.max' => 'Highest education must not exceed 255 characters.',
            'last_employment_date.date' => 'Please enter a valid date for last employment.',
            'documents.application_letter.required' => 'Application letter is required.',
            'documents.application_letter.mimes' => 'Application letter must be a PDF file.',
            'documents.application_letter.max' => 'Application letter must not exceed 10MB.',
            'documents.personal_data_sheet.required' => 'Personal data sheet is required.',
            'documents.personal_data_sheet.mimes' => 'Personal data sheet must be a PDF file.',
            'documents.personal_data_sheet.max' => 'Personal data sheet must not exceed 10MB.',
            'documents.performance_rating.required' => 'Performance rating is required.',
            'documents.performance_rating.mimes' => 'Performance rating must be a PDF file.',
            'documents.performance_rating.max' => 'Performance rating must not exceed 10MB.',
            'documents.eligibility_proof.required' => 'Eligibility proof is required.',
            'documents.eligibility_proof.mimes' => 'Eligibility proof must be a PDF file.',
            'documents.eligibility_proof.max' => 'Eligibility proof must not exceed 10MB.',
            'documents.transcript.required' => 'Transcript is required.',
            'documents.transcript.mimes' => 'Transcript must be a PDF file.',
            'documents.transcript.max' => 'Transcript must not exceed 10MB.',
            'documents.employment_proof.required' => 'Employment proof is required.',
            'documents.employment_proof.mimes' => 'Employment proof must be a PDF file.',
            'documents.employment_proof.max' => 'Employment proof must not exceed 10MB.',
            'documents.training_certificates.required' => 'Training certificates are required.',
            'documents.training_certificates.mimes' => 'Training certificates must be a PDF file.',
            'documents.training_certificates.max' => 'Training certificates must not exceed 10MB.',
            'cf-turnstile-response.required' => 'Please complete the CAPTCHA verification.',
            'cf-turnstile-response.turnstile' => 'CAPTCHA verification failed. Please try again.',
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
