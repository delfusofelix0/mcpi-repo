<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import DeleteUserForm from './Partials/DeleteUserForm.vue';
import UpdatePasswordForm from './Partials/UpdatePasswordForm.vue';
import UpdateProfileInformationForm from './Partials/UpdateProfileInformationForm.vue';
import {router, usePage} from '@inertiajs/vue3';
import { Head } from '@inertiajs/vue3';
import Message from 'primevue/message';
import Button from 'primevue/button';
import ConfirmDialog from 'primevue/confirmdialog';
import { useConfirm } from "primevue/useconfirm";
import { ref } from 'vue';

defineProps({
    mustVerifyEmail: {
        type: Boolean,
    },
    status: {
        type: String,
    },
});

const page = usePage();
const isAdmin = page.props.auth.user.isAdmin;

const showSuccessMessage = ref(false);
const showErrorMessage = ref(false);
const errorMessage = ref('');
const confirm = useConfirm();

const deleteAllTickets = () => {
    confirm.require({
        message: 'Are you sure you want to delete all tickets? This action cannot be undone.',
        header: 'Delete All Tickets',
        icon: 'pi pi-exclamation-triangle',
        acceptClass: 'p-button-danger',
        accept: () => {
            router.delete(route('tickets.deleteAll'), {
                preserveScroll: true,
                onSuccess: () => {
                    console.log('All tickets deleted successfully.');
                    showSuccessMessage.value = true;
                    setTimeout(() => {
                        showSuccessMessage.value = false;
                    }, 3000);
                },
                onError: (errors) => {
                    console.error('Error deleting tickets:', errors);
                    errorMessage.value = 'Failed to delete tickets. Please try again.';
                    showErrorMessage.value = true;
                    setTimeout(() => {
                        showErrorMessage.value = false;
                    }, 3000);
                }
            });
        }
    });
};
</script>

<template>
    <Head title="Profile" />

    <AuthenticatedLayout>
        <template #header>
            <h2
                class="text-xl font-semibold leading-tight text-gray-800"
            >
                Profile
            </h2>
        </template>

        <ConfirmDialog />

        <div class="py-12">
            <div class="mx-auto max-w-7xl space-y-6 sm:px-6 lg:px-8">
                <div
                    class="bg-white p-4 shadow sm:rounded-lg sm:p-8"
                >
                    <UpdateProfileInformationForm
                        :must-verify-email="mustVerifyEmail"
                        :status="status"
                        class="max-w-xl"
                    />
                </div>

                <div
                    class="bg-white p-4 shadow sm:rounded-lg sm:p-8"
                >
                    <UpdatePasswordForm class="max-w-xl" />
                </div>

                <div
                    class="bg-white p-4 shadow sm:rounded-lg sm:p-8"
                >
                    <DeleteUserForm class="max-w-xl" />
                </div>

                <div v-if="isAdmin" class="bg-white p-4 shadow sm:rounded-lg sm:p-8" >
                    <section class="space-y-6">
                        <header>
                            <h2 class="text-lg font-medium text-gray-900">
                                Delete All Tickets
                            </h2>

                            <p class="mt-1 text-sm text-gray-600">
                                Once all tickets are deleted, all of their data will be permanently deleted.
                                Before deleting, please ensure you no longer need any ticket information.
                            </p>
                        </header>

                        <div class="flex items-center gap-4">
                            <Button @click="deleteAllTickets" severity="danger" class="p-button-danger">
                                Delete All Tickets
                            </Button>
                            <div v-if="showSuccessMessage" class="flex">
                                <Message severity="success">All tickets deleted successfully.</Message>
                            </div>
                            <div v-if="showErrorMessage" class="flex">
                                <Message severity="error">Failed to delete tickets.</Message>
                            </div>
                        </div>
                    </section>
                </div>
            </div>
        </div>
    </AuthenticatedLayout>
</template>
