<script setup>
import DataTable from 'primevue/datatable';
import Column from 'primevue/column';
import ColumnGroup from 'primevue/columngroup';   // optional
import Row from 'primevue/row';                   // optional
import Button from 'primevue/button';
import Dialog from 'primevue/dialog';
import Select from 'primevue/select';
import { useToast } from "primevue/usetoast";
import { ref } from 'vue';
import {useForm} from "@inertiajs/vue3";
import { router } from '@inertiajs/vue3'

const props = defineProps([
    'registrations'
]);

const toast = useToast();

const dialogVisible = ref(false);
const selectedApplicant = ref(null);
const selectedStatus = ref(null);
const deleteDialogVisible = ref(false);
const applicantToDelete = ref(null);

const statusOptions = [
    { label: 'Pending', value: 'Pending' },
    { label: 'Hired', value: 'Hired' },
    { label: 'Option', value: 'Option' },
    { label: 'Viewed', value: 'Viewed' },
    { label: 'Rejected', value: 'Rejected' },
];

const statusForm = useForm({
    status: '',
});

const deleteForm = useForm({});

const formatDate = (value) => {
    if (value) {
        return new Date(value).toLocaleString('en-US', {
            year: 'numeric',
            month: 'long',
            day: 'numeric',
        });
    }
    return '';
};

const openStatusDialog = (applicant) => {
    selectedApplicant.value = applicant;
    selectedStatus.value = applicant.status;
    dialogVisible.value = true;
};

const openDeleteDialog = (applicant) => {
    applicantToDelete.value = applicant;
    deleteDialogVisible.value = true;
};

const updateStatus = () => {
    statusForm.post(route('applicant.statusStore', { id: selectedApplicant.value.id }), {
        preserveScroll: true,
        onSuccess: () => {
            toast.add({ severity: 'success', summary: 'Success!', detail: 'Status changed.', group: 'tl', life: 3000 });
            console.log('Status updated successfully');
            dialogVisible.value = false;
            selectedApplicant.value = null;
        },
        onError: (error) => {
            toast.add({ severity: 'error', summary: 'Error!', detail: 'Status not changed.', group: 'tl', life: 3000 });
            console.error('Error updating status', error);
        },
    });
};

const viewApplicant = (id) => {
    router.visit(route('applicant.show', { id: id }));
};

const deleteApplicant = () => {
    if (applicantToDelete.value) {
        deleteForm.delete(route('applicant.destroy', { id: applicantToDelete.value.id }), {
            preserveScroll: true,
            onSuccess: () => {
                toast.add({ severity: 'success', summary: 'Success!', detail: 'Applicant deleted successfully', group: 'tl', life: 3000 });
                deleteDialogVisible.value = false;
                applicantToDelete.value = null;
            },
            onError: () => {
                toast.add({ severity: 'error', summary: 'Error!', detail: 'Failed to delete applicant', group: 'tl', life: 3000 });
            },
        });
    }
};
</script>


<template>
    <Toast position="top-left" group="tl" />
    <div class="bg-white p-4 rounded-lg shadow-md">
        <DataTable :value="props.registrations"
                   showGridlines
                   stripedRows
                   paginator
                   :rows="5"
                   :rowsPerPageOptions="[5, 10, 20, 50]"
                   tableStyle="min-width: 25rem"
                   class="p-datatable-sm"
                   responsiveLayout="scroll">
            <Column header="Name" class="text-left">
                <template #body="slotProps">
                    {{ slotProps.data.first_name }} {{ slotProps.data.last_name }}
                </template>
            </Column>
            <Column field="email" header="Email" class="text-left"></Column>
            <Column field="position.name" header="Position" class="text-left"></Column>
            <Column field="status" header="Status" class="text-left"></Column>
            <Column field="created_at" header="Applied At" class="text-left">
                <template #body="slotProps">
                    {{ formatDate(slotProps.data.created_at) }}
                </template>
            </Column>
            <Column header="Action" class="text-center">
                <template #body="slotProps">
                    <div class="flex justify-center gap-2">
                        <Button icon="pi pi-pencil" class="p-button-text p-button-rounded p-button-info" @click="openStatusDialog(slotProps.data)" />
                        <Button icon="pi pi-eye" class="p-button-text p-button-rounded p-button-success" @click="viewApplicant(slotProps.data.id)" />
                        <Button icon="pi pi-trash" class="p-button-text p-button-rounded p-button-danger" @click="openDeleteDialog(slotProps.data)" />
                    </div>
                </template>
            </Column>
            <template #empty>
                <div class="p-4 text-center">
                    <p>No applicants found.</p>
                </div>
            </template>
        </DataTable>

        <!-- Status Update Dialog -->
        <Dialog v-model:visible="dialogVisible" modal header="Update Applicant Status" :style="{ width: '350px' }">
            <div class="flex flex-col gap-4">
                <Select v-model="statusForm.status" :options="statusOptions" optionLabel="label" optionValue="value" placeholder="Select Status" class="w-full" />
            </div>
            <template #footer>
                <Button label="Cancel" icon="pi pi-times" @click="dialogVisible = false" class="p-button-text" text />
                <Button label="Save" icon="pi pi-check" @click="updateStatus" class="p-button-text p-button-danger" :disabled="statusForm.processing" autofocus />
            </template>
        </Dialog>

        <!-- Delete Confirmation Dialog -->
        <Dialog v-model:visible="deleteDialogVisible" modal header="Confirm Deletion" :style="{ width: '350px' }">
            <p>Are you sure you want to delete this applicant?</p>
            <template #footer>
                <Button label="No" icon="pi pi-times" @click="deleteDialogVisible = false" class="p-button-text"/>
                <Button label="Yes" icon="pi pi-check" @click="deleteApplicant" class="p-button-text p-button-danger" :disabled="deleteForm.processing" autofocus />
            </template>
        </Dialog>
    </div>
</template>

<style scoped>
:deep(.p-datatable) {
    @apply text-sm;
}

:deep(.p-datatable .p-datatable-header) {
    @apply bg-gray-100 border border-gray-200 border-x-0 p-4;
}

:deep(.p-datatable .p-datatable-thead > tr > th) {
    @apply bg-gray-100 text-gray-700 font-semibold p-3;
}

:deep(.p-datatable .p-datatable-tbody > tr) {
    @apply bg-white;
}

:deep(.p-datatable .p-datatable-tbody > tr:nth-child(even)) {
    @apply bg-gray-50;
}

:deep(.p-datatable .p-datatable-tbody > tr > td) {
    @apply p-3;
}

:deep(.p-datatable .p-paginator) {
    @apply p-4;
}

:deep(.p-button.p-button-icon-only) {
    @apply w-8 h-8;
}
</style>
