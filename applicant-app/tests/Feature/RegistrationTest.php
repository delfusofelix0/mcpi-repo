<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class RegistrationTest extends TestCase
{
    use RefreshDatabase, WithFaker;

    public function setUp(): void
    {
        parent::setUp();
        Storage::fake('public');
    }

    public function test_registration_form_can_be_submitted_successfully()
    {
        $formData = [
            'firstname' => $this->faker->firstName,
            'mi' => $this->faker->randomLetter,
            'lastname' => $this->faker->lastName,
            'suffix' => $this->faker->randomElement(['Jr', 'Sr', 'III', '']),
            'email' => $this->faker->unique()->safeEmail,
            'phone' => $this->faker->phoneNumber,
            'religion' => $this->faker->word,
            'sogie' => $this->faker->randomElement(['male', 'female', 'other']),
            'birthdate' => $this->faker->date,
            'address' => $this->faker->address,
            'highest_education' => 'College Graduate',
            'latest_company' => $this->faker->company,
            'present_position' => $this->faker->jobTitle,
            'status_employment' => 'Employed',
            'still_employed' => $this->faker->date,
            'eligibility' => 'Licensed',
            'person_with_disability' => true,
            'disability_details' => $this->faker->sentence,
            'pregnant' => false,
            'indigenous_community' => true,
            'indigenous_details' => $this->faker->word,
            'documents' => [
                'application_letter' => UploadedFile::fake()->create('application.pdf', 100),
                'personal_data_sheet' => UploadedFile::fake()->create('pds.pdf', 100),
                'performance_rating' => UploadedFile::fake()->create('rating.pdf', 100),
                'eligibility_proof' => UploadedFile::fake()->create('eligibility.pdf', 100),
                'transcript' => UploadedFile::fake()->create('transcript.pdf', 100),
                'employment_proof' => UploadedFile::fake()->create('employment.pdf', 100),
                'training_certificates' => UploadedFile::fake()->create('certificates.pdf', 100),
            ],
        ];

        $response = $this->post('/submit-form', $formData);

        $response->assertStatus(302); // Assert that the response is a redirect
        $response->assertSessionHas('success'); // Assert that a success message is flashed to the session

        $this->assertDatabaseHas('registrations', [
            'firstname' => $formData['firstname'],
            'lastname' => $formData['lastname'],
            'email' => $formData['email'],
        ]);

        // Get the latest registration
        $registration = \App\Models\Registration::latest()->first();

        // Check if documents were saved in the registration_documents table
        foreach ($formData['documents'] as $documentType => $file) {
            $this->assertDatabaseHas('registration_documents', [
                'registration_id' => $registration->id,
                'document_type' => $documentType,
            ]);
        }

        // Optionally, you can check if the files actually exist in storage
        foreach ($formData['documents'] as $documentType => $file) {
            $document = \App\Models\RegistrationDocument::where('registration_id', $registration->id)
                ->where('document_type', $documentType)
                ->first();

            Storage::disk('public')->assertExists($document->file_path);
        }
    }

    public function test_registration_form_validation_errors()
    {
        $response = $this->post('/submit-form', []);

        $response->assertSessionHasErrors([
            'firstname', 'lastname', 'email', 'phone', 'birthdate', 'address',
            'highest_education', 'documents.personal_data_sheet',
            'documents.eligibility_proof', 'documents.transcript',
            'documents.employment_proof', 'documents.training_certificates'
        ]);
    }

    public function test_registration_form_handles_file_upload_errors()
    {
        $response = $this->post('/submit-form', [
            'firstname' => $this->faker->firstName,
            'lastname' => $this->faker->lastName,
            'email' => $this->faker->unique()->safeEmail,
            // Add other required fields here
            'phone' => $this->faker->phoneNumber,
            'religion' => $this->faker->word,
            'sogie' => $this->faker->randomElement(['male', 'female', 'other']),
            'birthdate' => $this->faker->date,
            'address' => $this->faker->address,
            'highest_education' => 'College Graduate',
            'latest_company' => $this->faker->company,
            'present_position' => $this->faker->jobTitle,
            'status_employment' => 'Employed',
            'still_employed' => $this->faker->date,
            'eligibility' => 'Licensed',
            'documents' => [
                'application_letter' => UploadedFile::fake()->create('application.txt', 100),
            ],
        ]);

        $response->assertSessionHasErrors(['documents.application_letter']);
    }

    public function test_checkbox_fields_are_correctly_processed()
    {
        $response = $this->post('/submit-form', [
            'firstname' => $this->faker->firstName,
            'lastname' => $this->faker->lastName,
            'email' => $this->faker->unique()->safeEmail,
            // Add other required fields here
            'phone' => $this->faker->phoneNumber,
            'religion' => $this->faker->word,
            'sogie' => $this->faker->randomElement(['male', 'female', 'other']),
            'birthdate' => $this->faker->date,
            'address' => $this->faker->address,
            'highest_education' => 'College Graduate',
            'latest_company' => $this->faker->company,
            'present_position' => $this->faker->jobTitle,
            'status_employment' => 'Employed',
            'still_employed' => $this->faker->date,
            'eligibility' => 'Licensed',
            'person_with_disability' => true,
            'pregnant' => true,
            'indigenous_community' => true,
            'documents' => [
                'application_letter' => UploadedFile::fake()->create('application.pdf', 100),
                'personal_data_sheet' => UploadedFile::fake()->create('pds.pdf', 100),
                'performance_rating' => UploadedFile::fake()->create('rating.pdf', 100),
                'eligibility_proof' => UploadedFile::fake()->create('eligibility.pdf', 100),
                'transcript' => UploadedFile::fake()->create('transcript.pdf', 100),
                'employment_proof' => UploadedFile::fake()->create('employment.pdf', 100),
                'training_certificates' => UploadedFile::fake()->create('certificates.pdf', 100),
            ],
        ]);

        $response->assertSessionHas('success');

        $this->assertDatabaseHas('registrations', [
            'person_with_disability' => true,
            'pregnant' => true,
            'indigenous_community' => true,
        ]);
    }
}
