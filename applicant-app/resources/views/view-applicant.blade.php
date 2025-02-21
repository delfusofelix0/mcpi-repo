<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Applicant Information') }}
        </h2>
    </x-slot>

    <section class="p-4">
        <div class="flex flex-wrap justify-center gap-4">
            <!-- Personal Information Card -->
            <div class="card w-96 bg-base-100 shadow-xl">
                <div class="card-body">
                    <h2 class="card-title">Personal Information</h2>
                    <div class="grid grid-cols-2 gap-4">
                        <div class="col-span-1">Firstname:</div>
                        <div class="col-span-1 font-semibold">{{ $applicant->firstname }}</div>
                        <div class="col-span-1">M.I:</div>
                        <div class="col-span-1 font-semibold">{{ $applicant->mi }}</div>
                        <div class="col-span-1">Lastname:</div>
                        <div class="col-span-1 font-semibold">{{ $applicant->lastname }}</div>
                        <div class="col-span-1">Suffix:</div>
                        <div class="col-span-1 font-semibold">{{ $applicant->suffix }}</div>
                        <div class="col-span-1">Email:</div>
                        <div class="col-span-1 font-semibold break-words">{{ $applicant->email }}</div>
                        <div class="col-span-1">Phone Number:</div>
                        <div class="col-span-1 font-semibold">{{ $applicant->phone }}</div>
                        <div class="col-span-1">Religion:</div>
                        <div class="col-span-1 font-semibold">{{ $applicant->religion }}</div>
                        <div class="col-span-1">Birth Date:</div>
                        <div class="col-span-1 font-semibold">{{ $applicant->birthdate->format('m/d/Y') }}</div>
                        <div class="col-span-1">Gender:</div>
                        <div class="col-span-1 font-semibold">{{ $applicant->sogie }}</div>
                        <div class="col-span-1">Address:</div>
                        <div class="col-span-1 font-semibold">{{ $applicant->address }}</div>
                    </div>
                </div>
            </div>

            <!-- Education/Others Card -->
            <div class="card w-96 bg-base-100 shadow-xl">
                <div class="card-body">
                    <h2 class="card-title">Education/Others</h2>
                    <div class="grid grid-cols-2 gap-4">
                        <div class="col-span-1">Highest Education:</div>
                        <div class="col-span-1 font-semibold">{{ $applicant->highest_education }}</div>
                        <div class="col-span-1">Latest Company:</div>
                        <div class="col-span-1 font-semibold">{{ $applicant->latest_company }}</div>
                        <div class="col-span-1">Present Position:</div>
                        <div class="col-span-1 font-semibold">{{ $applicant->present_position }}</div>
                        <div class="col-span-1">Employment Status:</div>
                        <div class="col-span-1 font-semibold">{{ $applicant->status_employment }}</div>
                        <div class="col-span-1">Last Employment Date:</div>
                        <div class="col-span-1 font-semibold">{{ $applicant->last_employment_date ?? 'N/A' }}</div>
                        <div class="col-span-1">Civil Service Eligible:</div>
                        <div class="col-span-1 font-semibold">{{ $applicant->civil_service_eligible ? 'Yes' : 'No' }}</div>
                        <div class="col-span-1">Disability:</div>
                        <div class="col-span-1 font-semibold">{{ $applicant->disability ?? 'None' }}</div>
                        <div class="col-span-1">Pregnant:</div>
                        <div class="col-span-1 font-semibold">{{ $applicant->is_pregnant ? 'Yes' : 'No' }}</div>
                        <!-- Applicant Image -->
                        @if($applicant->image_path)
                            <div class="mb-4">
                                <h3 class="font-semibold mb-2">Applicant Image:</h3>
                                <img src="{{ asset('storage/' . $applicant->image_path) }}"
                                     alt="Applicant Image"
                                     class="w-full h-auto rounded-lg shadow-md">
                            </div>
                        @else
                            <p class="text-gray-600 mb-4">No applicant image uploaded.</p>
                        @endif
                    </div>
                </div>
            </div>

            <!-- Documents Card -->
            <div class="card w-96 bg-base-100 shadow-xl">
                <div class="card-body">
                    <h2 class="card-title">Documents</h2>
                    <!-- Documents -->
                    @forelse($applicant->documents as $document)
                        <div class="flex justify-between items-center mb-2">
                            <span class="document-type font-semibold" data-type="{{ $document->document_type }}">
                                {{ $document->document_type }}
                            </span>
                            <a href="{{ asset('storage/' . $document->file_path) }}"
                               target="_blank"
                               class="btn btn-primary btn-sm">
                                Open
                            </a>
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
