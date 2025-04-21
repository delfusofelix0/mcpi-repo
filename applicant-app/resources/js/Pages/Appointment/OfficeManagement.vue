<script setup>
import Toast from 'primevue/toast';
import { useToast } from 'primevue/usetoast';
import {useForm} from "@inertiajs/vue3";
import {ref} from "vue";
import {FilterMatchMode} from "@primevue/core/api";

const props = defineProps({
    offices: {
        type: Array,
        required: true
    }
});

const toast = useToast();
const deleteForm = useForm({});
const officeForm = useForm({
    name: '',
    is_available: true
});

const confirmDeleteDialog = ref(false);
const officeToDelete = ref(null);
const tableLoading = ref(false);

// Add this new code for filters
const filters = ref({
    global: { value: null, matchMode: FilterMatchMode.CONTAINS },
    name: { value: null, matchMode: FilterMatchMode.STARTS_WITH },
    is_available: { value: null, matchMode: FilterMatchMode.EQUALS }
});

const clearFilter = () => {
    initFilters();
};

const initFilters = () => {
    filters.value = {
        global: { value: null, matchMode: FilterMatchMode.CONTAINS },
        name: { value: null, matchMode: FilterMatchMode.STARTS_WITH },
        is_available: { value: null, matchMode: FilterMatchMode.EQUALS }
    };
};


const submitOffice = () => {
    officeForm.post(route('offices.store'), {
        onSuccess: () => {
            officeForm.reset();
            toast.add({ severity: 'success', summary: 'Success', detail: 'Office added successfully', life: 3000 });
        },
        onError: () => {
            toast.add({ severity: 'error', summary: 'Error', detail: 'Failed to add office', life: 3000 });
        }
    });
};

const onCellEditComplete = (event) => {
    const { data, newValue, field } = event;
    tableLoading.value = true;
    if (field === 'name') {
        useForm({
            name: newValue
        }).put(route('offices.updateName', data.id), {
            preserveState: false,
            preserveScroll: true,
            onSuccess: () => {
                tableLoading.value = false;
                toast.add({ severity: 'success', summary: 'Success', detail: 'Office name updated successfully', life: 3000 });
            },
            onError: () => {
                // Revert the change if the update fails
                tableLoading.value = false;
                data[field] = event.originalEvent.target.defaultValue;
                toast.add({ severity: 'error', summary: 'Error', detail: 'Failed to update office name', life: 3000 });
            }
        });
    }
};

const confirmDelete = (office) => {
    officeToDelete.value = office;
    confirmDeleteDialog.value = true;
};

const deleteOffice = () => {
    deleteForm.delete(route('offices.destroy', officeToDelete.value.id), {
        preserveScroll: true,
        preserveState: false,
        onSuccess: () => {
            confirmDeleteDialog.value = false;
            officeToDelete.value = null;
            toast.add({ severity: 'success', summary: 'Success', detail: 'Office deleted successfully', life: 3000 });
        },
        onError: () => {
            toast.add({ severity: 'error', summary: 'Error', detail: 'Failed to delete office', life: 3000 });
        }
    });
};

const toggleAvailability = (office) => {
    tableLoading.value = true;
    const newAvailability = !office.is_available;
    officeForm.reset();
    officeForm.is_available = newAvailability;

    officeForm.put(route('offices.updateAvailability', office.id), {
        preserveScroll: true,
        preserveState: false,
        onSuccess: () => {
            tableLoading.value = false;
            // Update the local state
            office.is_available = newAvailability;

            const status = newAvailability ? 'available' : 'unavailable';
            toast.add({
                severity: 'info',
                summary: 'Status Changed',
                detail: `Office is now ${status}`,
                life: 3000
            });
        },
        onError: (errors) => {
            tableLoading.value = false;
            // Revert the local state if the API call fails
            office.is_available = !newAvailability;
            toast.add({
                severity: 'error',
                summary: 'Error',
                detail: 'Failed to update office availability',
                life: 3000
            });
        }
    });
};
</script>

<template>
    <Toast />

    <Card>
        <template #title>
            <div class="flex align-items-center justify-content-between">
                <h3 class="text-xl font-semibold text-primary">Office Management</h3>
            </div>
        </template>

        <template #content>
            <!-- Add Office Form -->
            <Card class="mb-4">
                <template #title>
                    Add New Office
                </template>
                <template #content>
                    <form @submit.prevent="submitOffice" class="p-fluid">
                        <div class="field mb-4">
                            <label for="name" class="font-medium mb-2 block">Office Name</label>
                            <InputText
                                id="name"
                                v-model="officeForm.name"
                                :class="{ 'p-invalid': officeForm.errors.name }"
                                placeholder="Enter office name"
                                required
                            />
                            <small v-if="officeForm.errors.name" class="p-error">{{ officeForm.errors.name }}</small>
                        </div>

                        <div class="field-checkbox mb-4">
                            <Checkbox
                                id="is_available"
                                v-model="officeForm.is_available"
                                :binary="true"
                            />
                            <label for="is_available" class="ml-2">Office is available</label>
                        </div>

                        <Button
                            type="submit"
                            label="Add Office"
                            icon="pi pi-plus"
                            :loading="officeForm.processing"
                        />
                    </form>
                </template>
            </Card>

            <!-- Offices List -->
            <div class="mt-6">
                <h3 class="text-xl font-semibold text-primary">Office List</h3>

                <DataTable v-model:filters="filters"
                           :value="props.offices"
                           showGridlines
                           stripedRows
                           paginator
                           :rows="10"
                           :rowsPerPageOptions="[10, 20, 30, 40, 50]"
                           tableStyle="min-width: 25rem"
                           class="p-datatable-sm"
                           responsiveLayout="scroll"
                           filterDisplay="menu"
                           :globalFilterFields="['name']"
                           :loading="tableLoading"
                           editMode="cell"
                           @cell-edit-complete="onCellEditComplete"
                           dataKey="id">
                    <template #loading>
                        <div class="flex flex-column align-items-center justify-content-center p-4">
                            <span class="mt-2 text-lg font-medium text-white">Loading office data...</span>
                        </div>
                    </template>
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
                            <p>No offices found.</p>
                        </div>
                    </template>
                    <Column field="name" header="Office Name" :showFilterMatchModes="false" style="width: 25%">
                        <template #editor="{ data, field }">
                            <InputText v-model="data[field]" autofocus />
                        </template>
                    </Column>
                    <Column header="Status" class="w-10rem" removableSort>
                        <template #body="slotProps">
                            <div class="flex align-items-center">
                                <Checkbox
                                    :modelValue="slotProps.data.is_available"
                                    :binary="true"
                                    @change="toggleAvailability(slotProps.data)"
                                />
                                <span class="ml-2" :class="slotProps.data.is_available ? 'text-green-500' : 'text-red-500'">
                            {{ slotProps.data.is_available ? 'Available' : 'Not Available' }}
                        </span>
                            </div>
                        </template>
                    </Column>
                    <Column header="Action" class="text-center" style="width: 5%">
                        <template #body="slotProps">
                            <div class="flex justify-center gap-2">
                                <Button icon="pi pi-trash" class="p-button-text p-button-rounded p-button-danger" @click="confirmDelete(slotProps.data)" aria-label="Delete office" />
                            </div>
                        </template>
                    </Column>
                </DataTable>
            </div>
        </template>
    </Card>

    <!-- Confirmation Dialog for Delete -->
    <Dialog
        v-model:visible="confirmDeleteDialog"
        modal
        header="Confirm Deletion"
        :style="{ width: '450px' }"
    >
        <div class="flex align-items-center gap-2 mb-4">
            <i class="pi pi-exclamation-triangle text-yellow-500" style="font-size: 2rem"></i>
            <span>Are you sure you want to delete this office?</span>
        </div>
        <template #footer>
            <Button label="No" icon="pi pi-times" outlined @click="confirmDeleteDialog = false" />
            <Button label="Yes" icon="pi pi-check" severity="danger" @click="deleteOffice" :loading="deleteForm.processing"/>
        </template>
    </Dialog>
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
