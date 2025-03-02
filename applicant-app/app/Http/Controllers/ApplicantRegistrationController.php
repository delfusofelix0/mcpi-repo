<?php

namespace App\Http\Controllers;

use App\Models\Registration;
use App\Models\WorkPosition;
use Coderflex\LaravelTurnstile\Rules\TurnstileCheck;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;
use Inertia\Inertia;

class ApplicantRegistrationController
{
    public function workPosition()
    {
        // return an inertia response with the positions
        $query = WorkPosition::select('id', 'name', 'description')->latest();

        $positions = $query->get();

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
            'course_major' => [
                'nullable',
                Rule::requiredIf(function () use ($request) {
                    return in_array($request->input('highest_education'), [
                        'Vocational', 'College Level', 'Masters Degree', 'Doctors Degree'
                    ]);
                }),
                'string',
                'max:255'
            ],
            'has_previous_company' => 'required|boolean',
            'latest_company' => 'nullable|required_if:has_previous_company,true|string|max:255',
            'present_position' => 'nullable|required_if:has_previous_company,true|string|max:255',
            'years_of_service' => 'nullable|required_if:has_previous_company,true|string|max:255',
            'last_employment_date' => 'nullable|required_if:has_previous_company,true|date',
            'eligibility' => 'nullable|string|max:255',
            'person_with_disability' => 'nullable|boolean',
            'disability_details' => 'nullable|string',
            'pregnant' => 'nullable|boolean',
            'indigenous_community' => 'nullable|boolean',
            'indigenous_details' => 'nullable|string',
            'application_letter' => 'required|file|mimes:pdf|max:5120',
            'personal_data_sheet' => 'required|file|mimes:pdf|max:5120',
            'eligibility_proof' => 'required|file|mimes:pdf|max:5120',
            'transcript' => 'required|file|mimes:pdf|max:5120',
            'training_certificates' => 'required|file|mimes:pdf|max:5120',
            'skip_performance_rating' => 'required|boolean',
            'skip_employment_proof' => 'required|boolean',
            'performance_rating' => 'nullable|required_if:skip_performance_rating,false|file|mimes:pdf|max:5120',
            'employment_proof' => 'nullable|required_if:skip_employment_proof,false|file|mimes:pdf|max:5120',
            'cf-turnstile-response' => ['required', new TurnstileCheck()],
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
            'sogie.required' => 'Gender is required.',
            'sogie.max' => 'SOGIE information must not exceed 255 characters.',
            'birth_date.required' => 'Birthdate is required.',
            'birth_date.date' => 'Please enter a valid date for birthdate.',
            'address.required' => 'Address is required.',
            'highest_education.required' => 'Highest education is required.',
            'highest_education.max' => 'Highest education must not exceed 255 characters.',
            'course_major.required_if' => 'Course major is required.',
            'course_major.max' => 'Course major must not exceed 255 characters.',
            'course_major.string' => 'Course major must be a string.',
            'latest_company.required_if' => 'Please provide your latest company name.',
            'latest_company.max' => 'Company name must not exceed 255 characters.',
            'present_position.required_if' => 'Please provide your present position.',
            'present_position.max' => 'Present position must not exceed 255 characters.',
            'years_of_service.required_if' => 'Please provide your years of service.',
            'years_of_service.numeric' => 'Years of service must be a number.',
            'years_of_service.min' => 'Years of service must be at least 0.',
            'years_of_service.max' => 'Years of service must not exceed 100.',
            'last_employment_date.required_if' => 'Please provide your last employment date.',
            'last_employment_date.date' => 'Please enter a valid date for last employment.',
            'application_letter.required' => 'Application letter is required.',
            'application_letter.mimes' => 'Application letter must be a PDF file.',
            'application_letter.max' => 'Application letter must not exceed 5MB.',
            'personal_data_sheet.required' => 'Personal data sheet is required.',
            'personal_data_sheet.mimes' => 'Personal data sheet must be a PDF file.',
            'personal_data_sheet.max' => 'Personal data sheet must not exceed 5MB.',
            'eligibility_proof.required' => 'Eligibility proof is required.',
            'eligibility_proof.mimes' => 'Eligibility proof must be a PDF file.',
            'eligibility_proof.max' => 'Eligibility proof must not exceed 5MB.',
            'transcript.required' => 'Transcript is required.',
            'transcript.mimes' => 'Transcript must be a PDF file.',
            'transcript.max' => 'Transcript must not exceed 5MB.',
            'training_certificates.required' => 'Training certificates are required.',
            'training_certificates.mimes' => 'Training certificates must be a PDF file.',
            'training_certificates.max' => 'Training certificates must not exceed 5MB.',
            'skip_performance_rating.required' => 'Please indicate whether you want to skip the performance rating.',
            'skip_employment_proof.required' => 'Please indicate whether you want to skip the employment proof.',
            'performance_rating.required_if' => 'Performance rating is required when not skipped.',
            'employment_proof.required_if' => 'Employment proof is required when not skipped.',
            'performance_rating.mimes' => 'Performance rating must be a PDF file.',
            'performance_rating.max' => 'Performance rating must not exceed 5MB.',
            'employment_proof.mimes' => 'Employment proof must be a PDF file.',
            'employment_proof.max' => 'Employment proof must not exceed 5MB.',
            'cf-turnstile-response.required' => 'Please complete the CAPTCHA verification.',
            'cf-turnstile-response.turnstile' => 'CAPTCHA verification failed. Please try again.',
        ]);
        if ($validator->fails()) {
            return redirect()->back()
                ->withErrors($validator)
                ->withInput();
        }

        $validatedData = $validator->validated();

        // Remove has_previous_company from validatedData if it exists
        unset($validatedData['has_previous_company']);

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
            ->registrations()->create(array_merge($validatedData, [
                'performance_rating_skipped' => $validatedData['skip_performance_rating'],
                'employment_proof_skipped' => $validatedData['skip_employment_proof'],
            ]));


        // Handle file uploads
        $documentTypes = [
            'application_letter',
            'personal_data_sheet',
            'eligibility_proof',
            'transcript',
            'training_certificates'
        ];

        if (!$validatedData['skip_performance_rating']) {
            $documentTypes[] = 'performance_rating';
        }

        if (!$validatedData['skip_employment_proof']) {
            $documentTypes[] = 'employment_proof';
        }

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
