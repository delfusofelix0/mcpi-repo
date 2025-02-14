<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Applicant Information') }}
        </h2>
    </x-slot>

    <section>
        <div class="flex flex-wrap justify-evenly items-start gap-4 text-black bg-white">

            <!-- Left Card (Personal Information) -->
            <div class="card w-72 bg-gray-300" style="border-radius: 0.5rem; box-shadow: 0 0 4px 6px rgba(0, 0, 0, 0.1);">
                <div class="text-center mb-4 p-2 font-semibold">
                    <h3>Personal Information</h3>
                </div>
                <div class="col-span-1 p-2">Firstname:
                    <span id="info">{{ $applicant->firstname }}</span>
                </div>
                <div class="col-span-1 p-2">M.I:
                    <span id="info">{{ $applicant->mi }}</span>
                </div>
                <div class="col-span-1 p-2">Lastname:
                    <span id="info">{{ $applicant->lastname }}</span>
                </div>
                <div class="col-span-1 p-2">Suffix:
                    <span id="info">{{ $applicant->suffix }}</span>
                </div>

                <div class="col-span-1 p-2">Email:
                    <span id="info">{{ $applicant->email }}</span>
                </div>
                <div class="col-span-1 p-2">Phone Number:
                    <span id="info">{{ $applicant->phone }}</span>
                </div>

                <div class="col-span-1 p-2">Religion:
                    <span id="info">{{ $applicant->religion }}</span>
                </div>
                <div class="col-span-1 p-2">Birth Date:
                    <span id="info">{{ $applicant->birthdate->format('m/d/Y') }}</span>
                </div>
                <div class="col-span-1 p-2">Gender:
                    <span id="info">{{ $applicant->sogie }}</span>
                </div>

                <div class="col-span-1 p-2">Address:
                    <span id="info">{{ $applicant->address }}</span>
                </div>
            </div>

            <!-- Right Card (Employment and Education Details) -->
            <div class="card w-72 bg-gray-300" style="border-radius: 0.5rem; box-shadow: 0 0 4px 6px rgba(0, 0, 0, 0.1);">
                <div class="text-center mb-4 p-2 font-semibold">
                    <h3>Education/Others</h3>
                </div>
                <div class="col-span-1 p-2">Highest Educational Attainment:
                    <span id="info">{{ $applicant->highest_education }}</span>
                </div>

                <div class="col-span-1 p-2">Latest Company/Agency:
                    <span id="info">{{ $applicant->latest_company }}</span>
                </div>
                <div class="col-span-1 p-2">Present/Latest Position:
                    <span id="info">{{ $applicant->present_position }}</span>
                </div>
                <div class="col-span-1 p-2">Status of Employment:
                    <span id="info">{{ $applicant->status_employment }}</span>
                </div>
                <div class="col-span-1 p-2">Last Date of Employment:
                    <span id="info">{{ $applicant->last_employment_date ?? 'N/A' }}</span>
                </div>

                <div class="col-span-1 p-2">Civil Service Eligible:
                    <span id="info">{{ $applicant->civil_service_eligible ? 'Yes' : 'No' }}</span>
                </div>

                <div class="col-span-1 p-2">Person with Disability:
                    <span id="info">{{ $applicant->disability ?? 'None' }}</span>
                </div>

                <div class="col-span-1 p-2">Pregnant:
                    <span id="info">{{ $applicant->is_pregnant ? 'Yes' : 'No' }}</span>
                </div>
            </div>

            <!-- Bottom-left Card (Document Information) -->
            <!-- Bottom-left Card (Document Information) -->
            <div class="card w-72 bg-gray-300" style="border-radius: 0.5rem; box-shadow: 0 0 4px 6px rgba(0, 0, 0, 0.1);">
                <div class="col-span-1 p-2">
                    <div class="text-center">
                        <h3 class="font-bold text-lg mb-4">Documents</h3>
                    </div>

                    @forelse($applicant->documents as $document)
                        <div class="mb-4">
                            <div class="col-span-1 p-2 flex justify-between items-center">
                                <h4 class="font-semibold document-type" data-type="{{ $document->document_type }}">{{ $document->document_type }}</h4>
                                <a href="{{ asset('storage/' . $document->file_path) }}"
                                   target="_blank"
                                   class="bg-blue-500 hover:bg-blue-700 text-white font-bold py-1 px-2 rounded text-xs">
                                    Open
                                </a>
                            </div>
                        </div>
                    @empty
                        <p class="text-gray-600">No documents uploaded.</p>
                    @endforelse
                </div>
            </div>
        </div>
    </section>
</x-app-layout>
<script>
    document.addEventListener('DOMContentLoaded', function() {
        const documentTypes = {
            'application_letter': 'Application Letter',
            'personal_data_sheet': 'Personal Data Sheet',
            'performance_rating': 'Performance Rating',
            'eligibility_proof': 'Transcript of Records',
            'transcript': 'Transcript of Records',
            'employment_proof': 'Employment History',
            'training_certificates': 'Training Certificate',
        };

        document.querySelectorAll('.document-type').forEach(function(element) {
            const type = element.getAttribute('data-type');
            element.textContent = documentTypes[type] || type;
        });
    });
</script>
