<script setup>
import DataTable from 'primevue/datatable';
import Column from 'primevue/column';
import ColumnGroup from 'primevue/columngroup';   // optional
import Row from 'primevue/row';                   // optional
import Button from 'primevue/button';
import Dialog from 'primevue/dialog';
import Select from 'primevue/select';
import {FilterMatchMode, FilterOperator} from '@primevue/core/api';
import {useToast} from "primevue/usetoast";
import {ref} from 'vue';
import {useForm} from "@inertiajs/vue3";
import {router} from '@inertiajs/vue3'

const props = defineProps([
    'positions'
]);

const toast = useToast();

const dialogVisible = ref(false);
const deleteDialogVisible = ref(false);
const applicantToDelete = ref(null);
const addDialogVisible = ref(false);

const newPositionForm = useForm({
    name: '',
    description: '',
});

const positionForm = useForm({
    id: '',
    name: '',
    description: '',
});

const deleteForm = useForm({});

const openStatusDialog = (position) => {
    positionForm.id = position.id;
    positionForm.name = position.name;
    positionForm.description = position.description;
    dialogVisible.value = true;
};

const openDeleteDialog = (applicant) => {
    applicantToDelete.value = applicant;
    deleteDialogVisible.value = true;
};

const openAddDialog = () => {
    newPositionForm.reset();
    addDialogVisible.value = true;
};

const addPosition = () => {
    newPositionForm.post(route('positions.store'), {
        preserveScroll: true,
        onSuccess: () => {
            toast.add({severity: 'success', summary: 'Success!', detail: 'Position added.', group: 'tl', life: 3000});
            addDialogVisible.value = false;
        },
        onError: (error) => {
            toast.add({
                severity: 'error',
                summary: 'Error!',
                detail: 'Failed to add position.',
                group: 'tl',
                life: 3000
            });
            console.error('Error adding position', error);
        },
    });
};

const updatePosition = () => {
    positionForm.put(route('positions.update', {id: positionForm.id}), {
        preserveScroll: true,
        onSuccess: () => {
            toast.add({severity: 'success', summary: 'Success!', detail: 'Position updated.', group: 'tl', life: 3000});
            dialogVisible.value = false;
        },
        onError: (error) => {
            toast.add({
                severity: 'error',
                summary: 'Error!',
                detail: 'Failed to update position.',
                group: 'tl',
                life: 3000
            });
            console.error('Error updating position', error);
        },
    });
};

const deletePosition = () => {
    if (applicantToDelete.value) {
        deleteForm.delete(route('positions.destroy', {id: applicantToDelete.value.id}), {
            preserveScroll: true,
            onSuccess: () => {
                toast.add({
                    severity: 'success',
                    summary: 'Success!',
                    detail: 'Position deleted successfully',
                    group: 'tl',
                    life: 3000
                });
                deleteDialogVisible.value = false;
                applicantToDelete.value = null;
            },
            onError: () => {
                toast.add({
                    severity: 'error',
                    summary: 'Error!',
                    detail: 'Failed to delete position',
                    group: 'tl',
                    life: 3000
                });
            },
        });
    }
};

const filters = ref();

const initFilters = () => {
    filters.value = {
        global: {value: null, matchMode: FilterMatchMode.CONTAINS},
    };
};

initFilters();

const clearFilter = () => {
    initFilters();
};
</script>


<template>
    <Toast position="top-left" group="tl"/>
    <div class="bg-white p-4 rounded-lg shadow-md">
        <div class="flex items-center justify-between mb-3">
            <h3 class="text-xl font-semibold text-primary">Position List</h3>
            <Button label="Add New Position" icon="pi pi-plus" @click="openAddDialog"/>
        </div>
        <DataTable v-model:filters="filters"
                   :value="props.positions"
                   showGridlines
                   stripedRows
                   paginator
                   :rows="10"
                   :rowsPerPageOptions="[10, 20, 30, 40, 50]"
                   tableStyle="min-width: 25rem"
                   class="p-datatable-sm"
                   responsiveLayout="scroll"
                   :globalFilterFields="['name']">
            <template #header>
                <div class="flex justify-between">
                    <Button type="button" icon="pi pi-filter-slash" label="Clear" outlined @click="clearFilter()"/>
                    <IconField>
                        <InputIcon>
                            <i class="pi pi-search"/>
                        </InputIcon>
                        <InputText v-model="filters['global'].value" placeholder="Keyword Search"/>
                    </IconField>
                </div>
            </template>
            <template #empty>
                <div class="p-4 text-center">
                    <p>No applicants found.</p>
                </div>
            </template>
            <Column field="name" header="Name" class="text-left"></Column>
            <Column field="description" header="Description" class="text-left"></Column>
            <Column header="Action" class="text-center">
                <template #body="slotProps">
                    <div class="flex justify-center gap-2">
                        <Button icon="pi pi-pencil" class="p-button-text p-button-rounded p-button-info"
                                @click="openStatusDialog(slotProps.data)" aria-label="Edit status"
                                v-tooltip.top="'Edit Position'"/>
                        <!--                        <Button icon="pi pi-eye" class="p-button-text p-button-rounded p-button-success" @click="viewApplicant(slotProps.data.id)" aria-label="View applicant" />-->
                        <Button icon="pi pi-trash" class="p-button-text p-button-rounded p-button-danger"
                                @click="openDeleteDialog(slotProps.data)" aria-label="Delete applicant"
                                v-tooltip.top="'Delete Applicant'"/>
                    </div>
                </template>
            </Column>
        </DataTable>

        <!-- Position Update Dialog -->
        <Dialog v-model:visible="dialogVisible" modal header="Update Position" :style="{ width: '350px' }">
            <div class="flex flex-col gap-4">
                <div class="field">
                    <label for="name" class="font-bold">Name</label>
                    <InputText id="name" v-model="positionForm.name" required autofocus class="w-full"/>
                    <small class="p-error" v-if="positionForm.errors.name">{{ positionForm.errors.name }}</small>
                </div>
                <div class="field">
                    <label for="description" class="font-bold">Description</label>
                    <Textarea id="description" v-model="positionForm.description" required rows="3" class="w-full"/>
                    <small class="p-error" v-if="positionForm.errors.description">{{
                            positionForm.errors.description
                        }}</small>
                </div>
            </div>
            <template #footer>
                <Button label="Cancel" icon="pi pi-times" @click="dialogVisible = false" severity="danger"
                        class="p-button-text"/>
                <Button label="Save" icon="pi pi-check" @click="updatePosition" severity="success" class="p-button-text"
                        :disabled="positionForm.processing" autofocus/>
            </template>
        </Dialog>

        <!-- Delete Confirmation Dialog -->
        <Dialog v-model:visible="deleteDialogVisible" modal header="Confirm Deletion" :style="{ width: '350px' }">
            <p>Are you sure you want to delete this applicant?</p>
            <template #footer>
                <Button label="No" icon="pi pi-times" @click="deleteDialogVisible = false" class="p-button-text"/>
                <Button label="Yes" icon="pi pi-check" @click="deletePosition" severity="danger" class="p-button-text"
                        :disabled="deleteForm.processing" autofocus/>
            </template>
        </Dialog>


        <!-- Add New Position Dialog -->
        <Dialog v-model:visible="addDialogVisible" modal header="Add New Position" :style="{ width: '500px' }">
            <div class="flex flex-col gap-4">
                <div class="field">
                    <label for="newName" class="font-bold">Name</label>
                    <InputText id="newName" v-model="newPositionForm.name" required autofocus class="w-full"/>
                    <small class="p-error" v-if="newPositionForm.errors.name">{{ newPositionForm.errors.name }}</small>
                </div>
                <div class="field">
                    <label for="newDescription" class="font-bold">Description</label>
                    <Textarea id="newDescription" v-model="newPositionForm.description" required rows="3"
                              class="w-full"/>
                    <small class="p-error"
                           v-if="newPositionForm.errors.description">{{ newPositionForm.errors.description }}</small>
                </div>
            </div>
            <template #footer>
                <Button label="Cancel" icon="pi pi-times" @click="addDialogVisible = false" severity="danger"
                        class="p-button-text"/>
                <Button label="Add" icon="pi pi-check" @click="addPosition" severity="success" class="p-button-text"
                        :disabled="newPositionForm.processing" autofocus/>
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
