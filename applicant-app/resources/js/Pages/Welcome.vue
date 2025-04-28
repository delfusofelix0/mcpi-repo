<script setup>
import { Head } from '@inertiajs/vue3';
import ApplicationLogo from "@/Components/ApplicationLogo.vue";
import { ref } from "vue";
import { router } from '@inertiajs/vue3';
import Button from 'primevue/button';

const loadingApplicant = ref(false);
const loadingAppointment = ref(false);

const goToApplicantForm = () => {
    loadingApplicant.value = true;
    router.visit(route('applicant-form.workPosition'), {
        preserveState: false,
        preserveScroll: false,
        onFinish: () => {
            loadingApplicant.value = false;
        },
    });
};

const goToAppointmentSystem = () => {
    loadingAppointment.value = true;
    router.visit(route('appointments.index'), {
        preserveState: false,
        preserveScroll: false,
        onFinish: () => {
            loadingAppointment.value = false;
        },
    });
};
</script>

<template>
    <Head title="Welcome"/>
    <div
        class="welcome-background relative flex min-h-screen flex-col items-center justify-center selection:bg-[#FF2D20] selection:text-white"
    >
        <div class="relative w-full px-6 lg:max-w-7xl">
            <header class="grid grid-cols-1 items-center gap-2 py-10">
                <div class="flex justify-center">
                    <ApplicationLogo class="max-h-52"/>
                </div>
            </header>

            <div class="py-12">
                <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
                    <div class="bg-white bg-opacity-90 overflow-hidden shadow-lg sm:rounded-lg border border-blue-700">
                        <div class="p-6 text-gray-900">
                            <h1 class="text-3xl font-bold mb-8 text-center text-blue-800">Maryknoll College of Panabo Inc.</h1>

                            <div class="flex flex-col md:flex-row justify-center gap-8 mb-6">
                                <!-- Applicant Card -->
                                <div class="flex-1 bg-white rounded-lg shadow-md p-6 hover:shadow-lg transition-shadow border-l-4 border-blue-600 flex flex-col">
                                    <div class="flex flex-col items-center flex-grow">
                                        <div class="w-16 h-16 bg-blue-600 rounded-full flex items-center justify-center mb-4">
                                            <i class="pi pi-user text-white text-4xl"></i>
                                        </div>
                                        <h2 class="text-xl font-semibold text-gray-800 mb-2">Applicant</h2>
                                        <p class="text-gray-600 text-center mb-4">Register as an applicant and explore available positions</p>
                                        <div class="flex-grow"></div> <!-- This pushes the button to the bottom -->
                                        <Button
                                            label="Go to Applicant System"
                                            @click="goToApplicantForm"
                                            :loading="loadingApplicant"
                                            class="p-button-primary mt-auto"
                                        />
                                    </div>
                                </div>

                                <!-- Appointment Card -->
                                <div class="flex-1 bg-white rounded-lg shadow-md p-6 hover:shadow-lg transition-shadow border-l-4 border-green-600 flex flex-col">
                                    <div class="flex flex-col items-center flex-grow">
                                        <div class="w-16 h-16 bg-green-600 rounded-full flex items-center justify-center mb-4">
                                            <i class="pi pi-calendar text-white text-4xl"></i>
                                        </div>
                                        <h2 class="text-xl font-semibold text-gray-800 mb-2">Appointment</h2>
                                        <p class="text-gray-600 text-center mb-4">Schedule or manage your appointments</p>
                                        <div class="flex-grow"></div> <!-- This pushes the button to the bottom -->
                                        <Button
                                            label="Go to Appointment System"
                                            @click="goToAppointmentSystem"
                                            :loading="loadingAppointment"
                                            class="p-button-success mt-auto"
                                        />
                                    </div>
                                </div>
                            </div>

                            <div class="text-center text-sm text-gray-700 mt-4 font-medium">
                                If you encounter any issues, please contact us at
                                <a href="https://www.facebook.com/InfoTechSSS" target="_blank" class="text-blue-600 hover:underline">
                                    MCPI Information Technology System Support Services
                                </a>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</template>

<style scoped>
.welcome-background {
    background-image: url('/images/bg.webp');
    background-size: cover;
    background-position: center;
    background-attachment: fixed;
    min-height: 100vh;
    position: relative;
}

.welcome-background::before {
    content: "";
    position: absolute;
    top: 0;
    left: 0;
    width: 100%;
    height: 100%;
    background-color: rgba(255, 255, 255, 0.3);
    backdrop-filter: blur(2px);
}
</style>
