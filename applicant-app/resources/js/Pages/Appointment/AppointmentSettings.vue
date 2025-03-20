<script setup>
import {Head} from "@inertiajs/vue3";
import AuthenticatedLayout from "@/Layouts/AuthenticatedLayout.vue";
import OfficeManagement from "@/Pages/Appointment/OfficeManagement.vue";

const props = defineProps({
    offices: Array
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
                <OfficeManagement :offices="offices" />

                <div class="mt-6">
                    <!-- Additional settings sections can be added here -->
                </div>
            </div>
        </div>
    </AuthenticatedLayout>
</template>

<style scoped>

</style>
