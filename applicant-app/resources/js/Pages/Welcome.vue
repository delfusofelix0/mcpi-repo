<script setup>
import {Head} from '@inertiajs/vue3';
import ApplicationLogo from "@/Components/ApplicationLogo.vue";
import {ref} from "vue";
import {router} from '@inertiajs/vue3';
import Button from 'primevue/button';

const loading = ref(false);

const goToApplicantForm = () => {
    loading.value = true;
    router.visit(route('applicant-form.workPosition'), {
        preserveState: false,
        preserveScroll: false,
        onFinish: () => {
            loading.value = false;
        },
    });
};


</script>

<template>
    <Head title="Welcome"/>
    <div
        class="welcome-background relative flex min-h-screen flex-col items-center justify-center selection:bg-[#FF2D20] selection:text-white"
    >
        <div class="relative w-full max-w-2xl px-6 lg:max-w-7xl">
            <header
                class="grid grid-cols-1 items-center gap-2 py-10"
            >
                <div class="flex justify-center">
                    <ApplicationLogo class="max-h-52"/>
                </div>
            </header>

            <div class="py-12">
                <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
                    <div class="bg-cyan-100 overflow-hidden shadow-sm sm:rounded-lg">
                        <div class="p-6 text-gray-900">
                            <h1 class="text-3xl font-bold mb-4 text-center">Welcome to Maryknoll College of Panabo Inc. Applicant Registration
                                System</h1>
                            <p class="mb-4 text-center">
                                Welcome to the Maryknoll College of Panabo Inc. Applicant Registration System. This system allows applicants to register, view available positions, and manage their profiles. It also provides administrators with tools to manage applicants and positions efficiently.
                            </p>
                            <div class="mt-6 flex justify-center">
                                <Button
                                    label="Go to Applicant Form"
                                    @click="goToApplicantForm"
                                    :loading="loading"
                                    class="text-white font-bold py-2 px-4 rounded"
                                />
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
    background-image: url('/images/bg.webp'); /* Light blue background */
    background-size: cover;
    background-position: center;
    background-attachment: fixed; /* This makes the background fixed while scrolling */
    min-height: 100vh;
}
</style>
