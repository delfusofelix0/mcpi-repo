<script setup>
import {Head} from "@inertiajs/vue3";
import AuthenticatedLayout from "@/Layouts/AuthenticatedLayout.vue";
import OfficeManagement from "@/Pages/Appointment/OfficeManagement.vue";
import AppointmentList from "@/Pages/Appointment/AppointmentList.vue";

const props = defineProps({
    offices: Array,
    appointments: Array
});
</script>

<template>
    <Head title="Appointment Settings" />

    <AuthenticatedLayout>
        <template #header>
            <h2 class="text-xl font-semibold leading-tight text-gray-800">
                Appointment Settings
            </h2>
        </template>

        <div class="py-12">
            <div class="mx-auto max-w-7xl sm:px-6 lg:px-8">
                <div class="mt-6">
                    <AppointmentList :appointments="appointments"/>
                </div>
                <div class="mt-6">
                    <OfficeManagement :offices="offices" />
                </div>
            </div>
        </div>
    </AuthenticatedLayout>
</template>

<style scoped>

</style>
