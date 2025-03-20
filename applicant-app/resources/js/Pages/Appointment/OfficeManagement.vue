<script setup>
import Toast from 'primevue/toast';
import { useToast } from 'primevue/usetoast';
import {useForm} from "@inertiajs/vue3";
import {ref} from "vue";

const props = defineProps({
    offices: {
        type: Array,
        required: true
    }
});

console.log(props.offices);

const toast = useToast();
const officeForm = useForm({
    name: '',
    is_available: true
});

const confirmDeleteDialog = ref(false);
const officeToDelete = ref(null);

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

    if (field === 'name') {
        useForm({
            name: newValue
        }).put(route('offices.updateName', data.id), {
            preserveState: false,
            preserveScroll: true,
            onSuccess: () => {
                toast.add({ severity: 'success', summary: 'Success', detail: 'Office name updated successfully', life: 3000 });
            },
            onError: () => {
                // Revert the change if the update fails
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
    useForm().delete(route('offices.destroy', officeToDelete.value.id), {
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
    const newAvailability = !office.is_available;
    useForm({
        is_available: newAvailability
    }).put(route('offices.updateAvailability', office.id), {
        preserveScroll: true,
        preserveState: false,
        onSuccess: () => {
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
            console.error(errors);
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
                <h4 class="text-lg font-medium mb-3">Office List</h4>

                <div v-if="props.offices.length === 0" class="p-4 border-round bg-gray-100 text-center">
                    <i class="pi pi-info-circle mr-2"></i>
                    <span class="text-gray-600">No offices added yet.</span>
                </div>

                <DataTable
                    v-else
                    :value="props.offices"
                    stripedRows
                    paginator
                    :rows="10"
                    :rowsPerPageOptions="[5, 10, 20]"
                    tableStyle="min-width: 50rem"
                    class="p-datatable-sm"
                    editMode="cell"
                    @cell-edit-complete="onCellEditComplete"
                    dataKey="id"
                >
                    <Column field="name" header="Office Name" sortable>
                        <template #editor="{ data, field }">
                            <InputText v-model="data[field]" autofocus />
                        </template>
                    </Column>
                    <Column header="Status" class="w-10rem">
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
                    <Column header="Actions" class="w-10rem">
                        <template #body="slotProps">
                            <Button
                                icon="pi pi-trash"
                                severity="danger"
                                text
                                rounded
                                aria-label="Delete"
                                @click="confirmDelete(slotProps.data)"
                            />
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
            <Button label="Yes" icon="pi pi-check" severity="danger" @click="deleteOffice" />
        </template>
    </Dialog>
</template>

<style scoped>

</style>
