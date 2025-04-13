<script setup>
import {defineProps, ref} from 'vue';
import {Head, Link} from "@inertiajs/vue3";
import Tag from 'primevue/tag';

const props = defineProps({
    applicant: Object
});

const formatDocumentType = (docType) => {
    return docType.split('_').map(word => word.charAt(0).toUpperCase() + word.slice(1)).join(' ');
};

const hasDocument = (docType) => {
    return props.applicant.documents.some(doc => doc.document_type === docType);
};

const getDocumentPath = (docType) => {
    const document = props.applicant.documents.find(doc => doc.document_type === docType);
    return document ? `/storage/${document.file_path}` : '';
};

const getImageUrl = (imagePath) => {
    return imagePath ? `/storage/${imagePath}` : '/path/to/default/image.jpg';
};

const getStatusClass = (status) => {
    switch (status) {
        case 'Pending':
            return 'bg-blue-100 text-blue-800';
        case 'Hired':
            return 'bg-green-100 text-green-800';
        case 'For Demo':
            return 'bg-yellow-100 text-yellow-800';
        case 'For Interview':
            return 'bg-purple-100 text-purple-800';
        case 'Reserved':
            return 'bg-gray-100 text-gray-800';
        case 'Viewed':
            return 'bg-indigo-100 text-indigo-800';
        case 'Rejected':
            return 'bg-red-100 text-red-800';
        case 'Recommended':
            return 'bg-teal-100 text-teal-800';
        default:
            return 'bg-red-100 text-red-800';
    }
};

const getInitials = (firstName, lastName) => {
    return (firstName?.charAt(0) || '') + (lastName?.charAt(0) || '');
};
</script>

<template>
    <Head title="Applicant Information"/>

    <div class="bg-gradient-to-r from-blue-50 to-indigo-50 shadow-lg rounded-lg p-8 max-w-4xl mx-auto mt-8">
        <div class="mb-4 text-sm">
            <Link :href="route('dashboard')" class="text-indigo-600 hover:text-indigo-800">Dashboard</Link>
            <span class="mx-2 text-gray-500">/</span>
            <span class="text-gray-700">Applicant Information</span>
        </div>

        <!-- Profile Header -->
        <div class="bg-white rounded-lg shadow-md p-6 mb-8 flex flex-col md:flex-row items-center md:items-start">
            <!-- Profile Image -->
            <div v-if="applicant.image_path" class="w-24 h-24 rounded-full flex items-center justify-center mb-4 md:mb-0 md:mr-6 shadow overflow-hidden">
                <img :src="getImageUrl(applicant.image_path)" alt="Applicant Photo" class="w-full h-full object-cover" />
            </div>
            <div v-else class="w-24 h-24 bg-gray-200 rounded-full flex items-center justify-center mb-4 md:mb-0 md:mr-6 shadow">
                <span class="text-3xl text-gray-500 font-semibold">{{ getInitials(applicant.first_name, applicant.last_name) }}</span>
            </div>

            <!-- Profile Info -->
            <div class="flex-1 text-center md:text-left">
                <h1 class="text-2xl font-bold text-gray-800">
                    {{ applicant.first_name || 'N/A' }}
                    {{ applicant.middle_initial ? applicant.middle_initial + '.' : '' }}
                    {{ applicant.last_name || 'N/A' }}
                    {{ applicant.suffix ? applicant.suffix : '' }}
                </h1>
                <div :class="['inline-block px-3 py-1 rounded-full text-sm mb-3', getStatusClass(applicant.status)]">
                    {{ applicant.status || 'N/A' }}
                </div>
                <div class="flex flex-wrap gap-4 justify-center md:justify-start">
                    <div class="flex items-center text-gray-600">
                        <span class="mr-1">📧</span>
                        <span>{{ applicant.email || 'N/A' }}</span>
                    </div>
                    <div class="flex items-center text-gray-600">
                        <span class="mr-1">📱</span>
                        <span>{{ '+'+applicant.phone || 'N/A' }}</span>
                    </div>
                </div>
            </div>
        </div>

        <!-- Main Content Sections -->
        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
            <!-- Personal Details Section -->
            <div class="bg-white rounded-lg shadow-md p-6">
                <h2 class="text-lg font-semibold text-gray-800 pb-3 mb-4 border-b border-gray-200">
                    Personal Details
                </h2>
                <div class="grid grid-cols-2 gap-y-3">
                    <div class="text-gray-600 font-medium">Gender</div>
                    <div class="text-gray-800">{{ applicant.sogie || 'N/A' }}</div>

                    <div class="text-gray-600 font-medium">Birth Date</div>
                    <div class="text-gray-800">{{ applicant.birth_date ? new Date(applicant.birth_date).toLocaleDateString() : 'N/A' }}</div>

                    <div class="text-gray-600 font-medium">Religion</div>
                    <div class="text-gray-800">{{ applicant.religion || 'N/A' }}</div>

                    <div class="text-gray-600 font-medium">Present Address</div>
                    <div class="text-gray-800">{{ applicant.address || 'N/A' }}</div>
                </div>
            </div>

            <!-- Additional Information Section -->
            <div class="bg-white rounded-lg shadow-md p-6">
                <h2 class="text-lg font-semibold text-gray-800 pb-3 mb-4 border-b border-gray-200">
                    Additional Information
                </h2>
                <div class="grid grid-cols-2 gap-y-3">
                    <div class="text-gray-600 font-medium">Eligibility</div>
                    <div class="text-gray-800">{{ applicant.eligibility || 'N/A' }}</div>

                    <div class="text-gray-600 font-medium">Person with Disability</div>
                    <div class="text-gray-800">{{ applicant.person_with_disability !== null ? (applicant.person_with_disability ? 'Yes' : 'No') : 'N/A' }}</div>

                    <template v-if="applicant.person_with_disability">
                        <div class="text-gray-600 font-medium">Disability Details</div>
                        <div class="text-gray-800">{{ applicant.disability_details || 'N/A' }}</div>
                    </template>

                    <div class="text-gray-600 font-medium">Pregnant</div>
                    <div class="text-gray-800">{{ applicant.pregnant !== null ? (applicant.pregnant ? 'Yes' : 'No') : 'N/A' }}</div>

                    <div class="text-gray-600 font-medium">Indigenous Community</div>
                    <div class="text-gray-800">{{ applicant.indigenous_community !== null ? (applicant.indigenous_community ? 'Yes' : 'No') : 'N/A' }}</div>

                    <template v-if="applicant.indigenous_community">
                        <div class="text-gray-600 font-medium">Indigenous Details</div>
                        <div class="text-gray-800">{{ applicant.indigenous_details || 'N/A' }}</div>
                    </template>
                </div>
            </div>

            <!-- Education and Work Section -->
            <div class="bg-white rounded-lg shadow-md p-6">
                <h2 class="text-lg font-semibold text-gray-800 pb-3 mb-4 border-b border-gray-200">
                    Education and Work
                </h2>
                <div class="grid grid-cols-2 gap-y-3">
                    <div class="text-gray-600 font-medium">Highest Education</div>
                    <div class="text-gray-800">{{ applicant.highest_education || 'N/A' }}</div>

                    <div class="text-gray-600 font-medium">Course/Major</div>
                    <div class="text-gray-800">{{ applicant.course_major || 'N/A' }}</div>

                    <div class="text-gray-600 font-medium">Latest Company</div>
                    <div class="text-gray-800">{{ applicant.latest_company || 'N/A' }}</div>

                    <div class="text-gray-600 font-medium">Present Position</div>
                    <div class="text-gray-800">{{ applicant.present_position || 'N/A' }}</div>

                    <div class="text-gray-600 font-medium">Years of Service</div>
                    <div class="text-gray-800">{{ applicant.years_of_service || 'N/A' }}</div>

                    <div class="text-gray-600 font-medium">Last Employment Date</div>
                    <div class="text-gray-800">{{ applicant.last_employment_date ? new Date(applicant.last_employment_date).toLocaleDateString() : 'N/A' }}</div>
                </div>
            </div>

            <!-- Documents Section -->
            <div class="bg-white rounded-lg shadow-md p-6">
                <h2 class="text-lg font-semibold text-gray-800 pb-3 mb-4 border-b border-gray-200">
                    Documents
                </h2>
                <div class="space-y-2">
                    <div v-for="docType in ['application_letter', 'personal_data_sheet', 'eligibility_proof', 'transcript', 'training_certificates', 'performance_rating', 'employment_proof']"
                         :key="docType"
                         class="flex justify-between py-2 border-b border-gray-100">
                        <div class="text-gray-800">{{ formatDocumentType(docType) }}</div>
                        <div class="flex items-center">
                            <template v-if="docType === 'performance_rating' || docType === 'employment_proof' || docType === 'eligibility_proof'">
                                <template v-if="applicant[`${docType}_skipped`]">
                                    <div class="w-2 h-2 rounded-full bg-gray-400 mr-2"></div>
                                    <span class="text-gray-600 text-sm">Skipped</span>
                                </template>
                                <template v-else-if="hasDocument(docType)">
                                    <div class="w-2 h-2 rounded-full bg-green-500 mr-2"></div>
                                    <span class="text-gray-600 text-sm">Submitted</span>
                                    <a :href="getDocumentPath(docType)" target="_blank" class="text-blue-500 ml-2 text-sm">(View)</a>
                                </template>
                                <template v-else>
                                    <div class="w-2 h-2 rounded-full bg-red-500 mr-2"></div>
                                    <span class="text-gray-600 text-sm">Not submitted</span>
                                </template>
                            </template>
                            <template v-else>
                                <template v-if="hasDocument(docType)">
                                    <div class="w-2 h-2 rounded-full bg-green-500 mr-2"></div>
                                    <span class="text-gray-600 text-sm">Submitted</span>
                                    <a :href="getDocumentPath(docType)" target="_blank" class="text-blue-500 ml-2 text-sm">(View)</a>
                                </template>
                                <template v-else>
                                    <div class="w-2 h-2 rounded-full bg-red-500 mr-2"></div>
                                    <span class="text-gray-600 text-sm">Not submitted</span>
                                </template>
                            </template>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</template>

<style scoped>
:deep(.p-image-preview-mask) {
    border-radius: 9999px; /* Makes the hover mask rounded */
}

:deep(.p-image-preview) {
    border-radius: 9999px; /* Makes the preview container rounded */
}
</style>
