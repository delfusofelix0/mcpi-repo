<script setup>
import {defineProps} from 'vue';
import AuthenticatedLayout from "@/Layouts/AuthenticatedLayout.vue";
import {Head, Link} from "@inertiajs/vue3";

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

console.table(props.applicant);
</script>

<template>
    <Head title="Applicant Information"/>

    <div class="bg-gradient-to-r from-blue-50 to-indigo-50 shadow-lg rounded-lg p-8 max-w-4xl mx-auto mt-8">
        <div class="mb-4 text-sm">
            <Link :href="route('dashboard')" class="text-indigo-600 hover:text-indigo-800">Dashboard</Link>
            <span class="mx-2 text-gray-500">/</span>
            <span class="text-gray-700">Applicant Information</span>
        </div>

        <div class="flex flex-wrap sm:flex-nowrap justify-between items-center mb-8 border-b-2 border-indigo-200 pb-2">
            <h1 class="text-3xl font-bold text-indigo-800 sm:mb-0">Applicant Information</h1>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-2 gap-8">
            <div class="space-y-6">
                <div class="bg-white p-6 rounded-lg shadow-md">
                    <h2 class="text-xl font-semibold text-indigo-700 mb-4">Personal Details</h2>
                    <div class="flex items-center mb-4">
                        <img :src="getImageUrl(applicant.image_path)" alt="Applicant Photo"
                             class="w-32 h-32 object-cover rounded-full mr-4">
                        <div>
                            <p class="font-medium text-lg text-indigo-600">{{ applicant.first_name }}
                                {{ applicant.middle_initial }} {{ applicant.last_name }} {{ applicant.suffix }}</p>
                            <p class="text-gray-600">{{ applicant.email }}</p>
                        </div>
                    </div>
                    <div class="space-y-2">
                        <p><span class="font-medium text-gray-700">Phone:</span> <span
                            class="text-indigo-600">{{ applicant.phone }}</span></p>
                        <p><span class="font-medium text-gray-700">Religion:</span> <span
                            class="text-indigo-600">{{ applicant.religion }}</span></p>
                        <p><span class="font-medium text-gray-700">SOGIE:</span> <span
                            class="text-indigo-600">{{ applicant.sogie }}</span></p>
                        <p><span class="font-medium text-gray-700">Birth Date:</span> <span
                            class="text-indigo-600">{{ new Date(applicant.birth_date).toLocaleDateString() }}</span>
                        </p>
                        <p><span class="font-medium text-gray-700">Address:</span> <span
                            class="text-indigo-600">{{ applicant.address }}</span></p>
                    </div>
                </div>

                <div class="bg-white p-6 rounded-lg shadow-md">
                    <h2 class="text-xl font-semibold text-indigo-700 mb-4">Education and Work</h2>
                    <div class="space-y-2">
                        <p><span class="font-medium text-gray-700">Highest Education:</span> <span
                            class="text-indigo-600">{{ applicant.highest_education }}</span></p>
                        <p><span class="font-medium text-gray-700">Latest Company:</span> <span
                            class="text-indigo-600">{{
                                applicant.latest_company
                            }}</span></p>
                        <p><span class="font-medium text-gray-700">Present Position:</span> <span
                            class="text-indigo-600">{{ applicant.present_position }}</span></p>
                        <p><span class="font-medium text-gray-700">Employment Status:</span> <span
                            class="text-indigo-600">{{ applicant.status_employment }}</span></p>
                        <p><span class="font-medium text-gray-700">Last Employment Date:</span> <span
                            class="text-indigo-600">{{
                                applicant.last_employment_date ? new Date(applicant.last_employment_date).toLocaleDateString() : 'N/A'
                            }}</span></p>
                    </div>
                </div>
            </div>

            <div class="space-y-6">
                <div class="bg-white p-6 rounded-lg shadow-md">
                    <h2 class="text-xl font-semibold text-indigo-700 mb-4">Additional Information</h2>
                    <div class="space-y-2">
                        <p><span class="font-medium text-gray-700">Eligibility:</span> <span
                            class="text-indigo-600">{{ applicant.eligibility }}</span></p>
                        <p><span class="font-medium text-gray-700">Person with Disability:</span> <span
                            class="text-indigo-600">{{ applicant.person_with_disability ? 'Yes' : 'No' }}</span></p>
                        <p v-if="applicant.person_with_disability"><span class="font-medium text-gray-700">Disability Details:</span>
                            <span class="text-indigo-600">{{ applicant.disability_details }}</span></p>
                        <p><span class="font-medium text-gray-700">Pregnant:</span> <span
                            class="text-indigo-600">{{ applicant.pregnant ? 'Yes' : 'No' }}</span></p>
                        <p><span class="font-medium text-gray-700">Indigenous Community:</span> <span
                            class="text-indigo-600">{{ applicant.indigenous_community ? 'Yes' : 'No' }}</span></p>
                        <p v-if="applicant.indigenous_community"><span class="font-medium text-gray-700">Indigenous Details:</span>
                            <span class="text-indigo-600">{{ applicant.indigenous_details }}</span></p>
                    </div>
                </div>

                <div class="bg-white p-6 rounded-lg shadow-md">
                    <h2 class="text-xl font-semibold text-indigo-700 mb-4">Application Status</h2>
                    <p><span class="font-medium text-gray-700">Status:</span> <span
                        class="text-indigo-600">{{ applicant.status }}</span></p>
                </div>

                <div class="bg-white p-6 rounded-lg shadow-md">
                    <h2 class="text-xl font-semibold text-indigo-700 mb-4">Documents</h2>
                    <div class="space-y-2">
                        <p v-for="docType in ['application_letter', 'personal_data_sheet', 'performance_rating', 'eligibility_proof', 'transcript', 'employment_proof', 'training_certificates']"
                           :key="docType">
                            <span class="font-medium text-gray-700">{{ formatDocumentType(docType) }}: </span>
                            <span class="text-indigo-600">
                                {{ hasDocument(docType) ? 'Submitted' : 'Not submitted' }}
                            </span>
                            <a v-if="hasDocument(docType)" :href="getDocumentPath(docType)" target="_blank"
                               class="ml-2 text-blue-500 hover:underline">View
                            </a>
                        </p>
                    </div>
                </div>
            </div>
        </div>
    </div>
</template>

<style scoped>
/* Any additional component-specific styles can go here */
</style>
