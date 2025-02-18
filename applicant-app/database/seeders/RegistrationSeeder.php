<?php

namespace Database\Seeders;

use App\Models\Registration;
use App\Models\RegistrationDocument;
use App\Models\WorkPosition;
use Faker\Factory as Faker;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Storage;

class RegistrationSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $faker = Faker::create();

        // Ensure we have at least one WorkPosition
        $position = WorkPosition::first() ?? WorkPosition::factory()->create();

        // Create 10 sample registrations
        for ($i = 0; $i < 10; $i++) {
            $personWithDisability = $faker->boolean(10);
            $indigenousCommunity = $faker->boolean(10);

            $registration = Registration::create([
                'work_position_id' => $position->id,
                'firstname' => $faker->firstName,
                'mi' => strtoupper($faker->randomLetter),
                'lastname' => $faker->lastName,
                'suffix' => $faker->optional(0.2)->randomElement(['Jr', 'Sr', 'II', 'III', 'IV']),
                'email' => $faker->unique()->safeEmail,
                'phone' => $faker->phoneNumber,
                'religion' => $faker->randomElement(['Christianity', 'Islam', 'Hinduism', 'Buddhism', 'Judaism', 'Other']),
                'sogie' => $faker->randomElement(['Male', 'Female', 'Non-binary', 'Prefer not to say']),
                'birthdate' => $faker->date('Y-m-d', '-18 years'),
                'address' => $faker->address,
                'highest_education' => $faker->randomElement(['High School', 'Associate\'s Degree', 'Bachelor\'s Degree', 'Master\'s Degree', 'Doctorate']),
                'latest_company' => $faker->company,
                'present_position' => $faker->jobTitle,
                'status_employment' => $faker->randomElement(['Employed', 'Unemployed', 'Self-employed', 'Student']),
                'last_employment_date' => $faker->optional()->date('Y-m-d', 'now'),
                'eligibility' => $faker->randomElement(['Licensed Professional', 'Civil Service Eligible', 'Board Passer', 'None']),
                'person_with_disability' => $personWithDisability,
                'disability_details' => $personWithDisability ? $faker->sentence : null,
                'pregnant' => $faker->boolean(5),
                'indigenous_community' => $indigenousCommunity,
                'indigenous_details' => $indigenousCommunity ? $faker->sentence : null,
            ]);

            $this->createDocuments($registration);
        }
    }

    /**
     * Create sample documents for a registration.
     */
    private function createDocuments(Registration $registration): void
    {
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
            // Create a dummy PDF file
            $content = "Sample content for $type";
            $fileName = $registration->id . "_" . $type . ".pdf";
            Storage::disk('public')->put("registration_documents/$fileName", $content);

            // Create document record
            RegistrationDocument::create([
                'registration_id' => $registration->id,
                'document_type' => $type,
                'file_path' => "registration_documents/$fileName",
            ]);
        }
    }
}
