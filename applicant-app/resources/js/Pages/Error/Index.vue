<script setup>
import { Head } from '@inertiajs/vue3';
import GuestLayout from '@/Layouts/GuestLayout.vue';
import Button from 'primevue/button';
import Card from 'primevue/card';
import { useToast } from 'primevue/usetoast';
import { onMounted } from 'vue';

const props = defineProps({
    message: {
        type: String,
        default: 'An error occurred'
    },
    status: {
        type: Number,
        default: 500
    }
});

const toast = useToast();

onMounted(() => {
    toast.add({
        severity: 'error',
        summary: 'Error',
        detail: props.message,
        life: 5000
    });
});

const getStatusMessage = (status) => {
    switch (status) {
        case 404:
            return 'Page Not Found';
        case 403:
            return 'Forbidden';
        case 401:
            return 'Unauthorized';
        case 500:
        default:
            return 'Server Error';
    }
};

const goHome = () => {
    window.location.href = '/';
};

const tryAgain = () => {
    window.location.reload();
};
</script>

<template>
    <Head :title="`Error ${status}`" />

        <Toast position="top-center" />

        <div class="flex justify-center items-center min-h-screen p-4">
            <Card class="w-full max-w-lg shadow-lg">
                <template #header>
                    <div class="bg-red-50 p-4 flex items-center justify-center">
                        <i class="pi pi-exclamation-circle text-6xl text-red-500"></i>
                    </div>
                </template>

                <template #title>
                    <div class="text-center">
                        <h1 class="text-3xl font-bold text-gray-800">{{ getStatusMessage(status) }}</h1>
                        <div class="text-xl font-semibold text-red-500 mt-2">Error {{ status }}</div>
                    </div>
                </template>

                <template #content>
                    <div class="text-center mb-6 text-gray-600">
                        <p class="text-lg">{{ message }}</p>
                        <p class="mt-2 text-sm">Please try again or contact support if the problem persists.</p>
                    </div>
                </template>

                <template #footer>
                    <div class="flex justify-center gap-3 pt-2">
                        <Button
                            label="Return Home"
                            icon="pi pi-home"
                            @click="goHome"
                            class="p-button-primary"
                        />
                        <Button
                            label="Try Again"
                            icon="pi pi-refresh"
                            @click="tryAgain"
                            class="p-button-outlined"
                        />
                    </div>
                </template>
            </Card>
        </div>
</template>

<style scoped>
:deep(.p-card) {
    border-radius: 8px;
    overflow: hidden;
}

:deep(.p-card-header) {
    padding: 0;
}

:deep(.p-card-body) {
    padding: 1.5rem;
}

:deep(.p-card-content) {
    padding: 0;
}

:deep(.p-card-footer) {
    padding: 0;
    padding-top: 1rem;
    border-top: 1px solid #f0f0f0;
}

:deep(.p-button) {
    padding: 0.75rem 1.5rem;
}
</style>
