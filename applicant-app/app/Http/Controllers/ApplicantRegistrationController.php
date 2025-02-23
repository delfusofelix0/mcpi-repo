<?php

namespace App\Http\Controllers;

use App\Models\Registration;
use App\Models\WorkPosition;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;
use Inertia\Inertia;

class ApplicantRegistrationController
{
    public function workPosition()
    {
        // return an inertia response with the positions
        $positions = WorkPosition::select('id', 'name', 'description')->get();
        return Inertia::render('ApplicantForm', [
            'positions' => $positions
        ]);
    }

    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'position' => 'required|exists:work_positions,id',
            'photo' => 'required|image|mimes:jpeg,png,jpg|max:2048',
            'first_name' => 'required|string|max:255',
            'middle_initial' => 'nullable|string|max:1',
            'last_name' => 'required|string|max:255',
            'suffix' => 'nullable|string|max:3',
            'email' => 'required|email:rfc,dns|max:255',
            'phone' => 'required|string|max:20',
            'religion' => 'nullable|string|max:255',
            'sogie' => 'required|string|max:255',
            'birth_date' => 'required|date',
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
            'application_letter' => 'required|file|mimes:pdf|max:10240',
            'personal_data_sheet' => 'required|file|mimes:pdf|max:10240',
            'performance_rating' => 'required|file|mimes:pdf|max:10240',
            'eligibility_proof' => 'required|file|mimes:pdf|max:10240',
            'transcript' => 'required|file|mimes:pdf|max:10240',
            'employment_proof' => 'required|file|mimes:pdf|max:10240',
            'training_certificates' => 'required|file|mimes:pdf|max:10240',
//            'cf-turnstile-response' => ['required', Rule::turnstile()],
        ], [
            'position.required' => 'Position is required.',
            'photo.required' => 'Photo is required.',
            'photo.image' => 'Photo must be an image file.',
            'photo.mimes' => 'Photo must be a JPEG, PNG, or JPG file.',
            'photo.max' => 'Photo must not exceed 2MB.',
            'first_name.required' => 'First name is required.',
            'first_name.max' => 'First name must not exceed 255 characters.',
            'middle_initial.max' => 'Middle initial must not exceed 1 character.',
            'last_name.required' => 'Last name is required.',
            'last_name.max' => 'Last name must not exceed 255 characters.',
            'suffix.max' => 'Suffix must not exceed 3 characters.',
            'email.required' => 'Email address is required.',
            'email.email' => 'Please enter a valid email address.',
            'email.max' => 'Email must not exceed 255 characters.',
            'phone.required' => 'Phone number is required.',
            'phone.max' => 'Phone number must not exceed 20 characters.',
            'sogie.required' => 'SOGIE information is required.',
            'sogie.max' => 'SOGIE information must not exceed 255 characters.',
            'birth_date.required' => 'Birthdate is required.',
            'birth_date.date' => 'Please enter a valid date for birthdate.',
            'address.required' => 'Address is required.',
            'highest_education.required' => 'Highest education is required.',
            'highest_education.max' => 'Highest education must not exceed 255 characters.',
            'last_employment_date.date' => 'Please enter a valid date for last employment.',
            'application_letter.required' => 'Application letter is required.',
            'application_letter.mimes' => 'Application letter must be a PDF file.',
            'application_letter.max' => 'Application letter must not exceed 10MB.',
            'personal_data_sheet.required' => 'Personal data sheet is required.',
            'personal_data_sheet.mimes' => 'Personal data sheet must be a PDF file.',
            'personal_data_sheet.max' => 'Personal data sheet must not exceed 10MB.',
            'performance_rating.required' => 'Performance rating is required.',
            'performance_rating.mimes' => 'Performance rating must be a PDF file.',
            'performance_rating.max' => 'Performance rating must not exceed 10MB.',
            'eligibility_proof.required' => 'Eligibility proof is required.',
            'eligibility_proof.mimes' => 'Eligibility proof must be a PDF file.',
            'eligibility_proof.max' => 'Eligibility proof must not exceed 10MB.',
            'transcript.required' => 'Transcript is required.',
            'transcript.mimes' => 'Transcript must be a PDF file.',
            'transcript.max' => 'Transcript must not exceed 10MB.',
            'employment_proof.required' => 'Employment proof is required.',
            'employment_proof.mimes' => 'Employment proof must be a PDF file.',
            'employment_proof.max' => 'Employment proof must not exceed 10MB.',
            'training_certificates.required' => 'Training certificates are required.',
            'training_certificates.mimes' => 'Training certificates must be a PDF file.',
            'training_certificates.max' => 'Training certificates must not exceed 10MB.',
//            'cf-turnstile-response.required' => 'Please complete the CAPTCHA verification.',
//            'cf-turnstile-response.turnstile' => 'CAPTCHA verification failed. Please try again.',
        ]);

        if ($validator->fails()) {
            return redirect()->back()
                ->withErrors($validator)
                ->withInput();
        }

        $validatedData = $validator->validated();

        // Remove documents from validatedData
//        $documents = $validatedData['documents'];
//        unset($validatedData['documents']);

        // Handle photo upload
        if ($request->hasFile('photo')) {
            $photoPath = $request->file('photo')->store('applicant_photos', 'public');
            $validatedData['image_path'] = $photoPath;
        }

        // Create registration with position
        $registration = WorkPosition::findOrFail($validatedData['position'])
            ->registrations()->create($validatedData);

        // Handle file uploads
        $documentTypes = [
            'application_letter',
            'personal_data_sheet',
            'performance_rating',
            'eligibility_proof',
            'transcript',
            'employment_proof',
            'training_certificates'
        ];

        foreach ($documentTypes as $type) {
            if ($request->hasFile($type)) {
                $file = $request->file($type);
                $path = $file->store('registration_documents', 'public');
                $registration->documents()->create([
                    'document_type' => $type,
                    'file_path' => $path,
                ]);
            }
        }

        return Inertia::render('ApplicantForm', [
            'submitted' => true
        ]);
    }
}
