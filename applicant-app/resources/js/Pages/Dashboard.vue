<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import { Head } from '@inertiajs/vue3';
import ApplicantTable from "@/Pages/ApplicantTable.vue";
import PositionTable from "@/Pages/PositionTable.vue";

const props = defineProps([
    'registrations', 'positions'
]);

</script>

<template>
    <Head title="Dashboard" />

    <AuthenticatedLayout>
        <template #header>
            <h2
                class="text-xl font-semibold leading-tight text-gray-800"
            >
                Dashboard
            </h2>
        </template>

        <div class="py-12">
            <div class="mx-auto max-w-7xl sm:px-6 lg:px-8">
                <div class="mt-6">
                    <ApplicantTable :registrations="registrations" :positions="positions"/>
                </div>
                <div class="mt-6">
                    <PositionTable :positions="positions"/>
                </div>
            </div>
        </div>
    </AuthenticatedLayout>
</template>
