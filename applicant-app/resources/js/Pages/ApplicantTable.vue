<script setup>
import DataTable from 'primevue/datatable';
import Column from 'primevue/column';
import Button from 'primevue/button';
import Dialog from 'primevue/dialog';
import Select from 'primevue/select';
import DatePicker from "primevue/datepicker";
import {FilterMatchMode, FilterOperator} from '@primevue/core/api';
import {useToast} from "primevue/usetoast";
import {computed, ref} from 'vue';
import {useForm, usePage} from "@inertiajs/vue3";
import {router} from '@inertiajs/vue3'

const props = defineProps([
    'registrations',
    'positions'
]);

const page = usePage();
const isAdmin = page.props.auth.user.isAdmin
const canDelete = computed(() => isAdmin);

const toast = useToast();

const dialogVisible = ref(false);
const selectedApplicant = ref(null);
const selectedStatus = ref(null);
const deleteDialogVisible = ref(false);
const applicantToDelete = ref(null);

const statusOptions = [
    {label: 'Pending', value: 'Pending'},
    {label: 'Hired', value: 'Hired'},
    {label: 'For Demo', value: 'For Demo'},
    {label: 'For Interview', value: 'For Interview'},
    {label: 'Reserved', value: 'Reserved'},
    {label: 'Viewed', value: 'Viewed'},
    {label: 'Rejected', value: 'Rejected'},
];

const statusForm = useForm({
    status: '',
});

const deleteForm = useForm({});

const formatDate = (value) => {
    return new Date(value).toLocaleString('en-US', {
        year: 'numeric',
        month: 'long',
        day: 'numeric',
    });
};

const mapdates = (data) => {
    return [...(data || [])].map((d) => {
        d.created_at = new Date(d.created_at);
        d.updated_at = new Date(d.updated_at);
        return d;
    });
};

const formattedRegistrations = computed(() => {
    return mapdates(props.registrations);
});

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
    statusForm.post(route('applicant.statusStore', {id: selectedApplicant.value.id}), {
        preserveScroll: true,
        onSuccess: () => {
            toast.add({severity: 'success', summary: 'Success!', detail: 'Status changed.', group: 'tl', life: 3000});
            dialogVisible.value = false;
            selectedApplicant.value = null;
        },
        onError: () => {
            toast.add({severity: 'error', summary: 'Error!', detail: 'Status not changed.', group: 'tl', life: 3000});
        },
    });
};

const viewApplicant = (id) => {
    router.visit(route('applicant.show', {id: id}));
};

const deleteApplicant = () => {
    if (applicantToDelete.value) {
        deleteForm.delete(route('applicant.destroyApplicant', {id: applicantToDelete.value.id}), {
            preserveScroll: true,
            onSuccess: () => {
                toast.add({
                    severity: 'success',
                    summary: 'Success!',
                    detail: 'Applicant deleted successfully',
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
                    detail: 'Failed to delete applicant',
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
        'first_name': {value: null, matchMode: FilterMatchMode.STARTS_WITH},
        'last_name': {value: null, matchMode: FilterMatchMode.STARTS_WITH},
        'email': {value: null, matchMode: FilterMatchMode.CONTAINS},
        'position.name': {value: null, matchMode: FilterMatchMode.CONTAINS},
        'status': {value: null, matchMode: FilterMatchMode.EQUALS},
        'created_at': {operator: FilterOperator.AND, constraints: [{value: null, matchMode: FilterMatchMode.DATE_IS}]}
    };
};

initFilters();

const clearFilter = () => {
    initFilters();
};

const statuses = ref(['Pending', 'Hired', 'For Demo', 'For Interview', 'Reserved', 'Viewed', 'Rejected']);
const getSeverity = (status) => {
    switch (status) {
        case 'Pending':
            return 'info';
        case 'Hired':
            return 'success';
        case 'For Demo':
            return 'warning';
        case 'For Interview':
            return 'help';
        case 'Reserved':
            return 'secondary';
        case 'Viewed':
            return 'primary';
        case 'Rejected':
            return 'danger';
        default:
            return 'info';
    }
};
</script>


<template>
    <Toast position="top-left" group="tl"/>
    <div class="bg-white p-4 rounded-lg shadow-md">
        <DataTable v-model:filters="filters"
                   :value="formattedRegistrations"
                   showGridlines
                   stripedRows
                   paginator
                   :rows="10"
                   :rowsPerPageOptions="[10, 20, 30, 40, 50]"
                   tableStyle="min-width: 25rem"
                   class="p-datatable-sm"
                   responsiveLayout="scroll"
                   filterDisplay="menu"
                   :globalFilterFields="['first_name', 'last_name']">
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
            <Column header="Name" class="text-left">
                <template #body="slotProps">
                    {{ slotProps.data.first_name }} {{ slotProps.data.last_name }}
                </template>
            </Column>
            <Column field="email" header="Email" class="text-left" :show-filter-match-modes="false">
                <template #body="{ data }">
                    {{ data.email }}
                </template>
                <template #filter="{ filterModel }">
                    <InputText v-model="filterModel.value" type="text" placeholder="Search by email"/>
                </template>
            </Column>
            <Column filterField="position.name" header="Position" :showFilterMatchModes="false" class="text-left">
                <template #body="{ data }">
                    {{ data.position.name }}
                </template>
                <template #filter="{ filterModel }">
                    <InputText v-model="filterModel.value" type="text" placeholder="Search by position"/>
                </template>
            </Column>
            <Column field="status" header="Status" :showFilterMatchModes="false" class="text-left">
                <template #body="{ data }">
                    <Tag :value="data.status" :severity="getSeverity(data.status)"/>
                </template>
                <template #filter="{ filterModel }">
                    <Select v-model="filterModel.value" :options="statuses" placeholder="Select One" showClear>
                        <template #option="slotProps">
                            <Tag :value="slotProps.option" :severity="getSeverity(slotProps.option)"/>
                        </template>
                    </Select>
                </template>
            </Column>
            <Column filterField="created_at" dataType="date" header="Applied At" class="text-left">
                <template #body="slotProps">
                    {{ formatDate(slotProps.data.created_at) }}
                </template>
                <template #filter="{ filterModel }">
                    <DatePicker v-model="filterModel.value" dateFormat="mm/dd/yy" placeholder="mm/dd/yyyy"/>
                </template>
            </Column>
            <Column header="Action" class="text-center">
                <template #body="slotProps">
                    <div class="flex justify-center gap-2">
                        <Button icon="pi pi-pencil" class="p-button-text p-button-rounded p-button-info"
                                @click="openStatusDialog(slotProps.data)" aria-label="Edit status"/>
                        <Button icon="pi pi-eye" class="p-button-text p-button-rounded p-button-success"
                                @click="viewApplicant(slotProps.data.id)" aria-label="View applicant"/>
                        <Button v-if="canDelete" icon="pi pi-trash"
                                class="p-button-text p-button-rounded p-button-danger"
                                @click="openDeleteDialog(slotProps.data)" aria-label="Delete applicant"/>
                    </div>
                </template>
            </Column>
        </DataTable>

        <!-- Status Update Dialog -->
        <Dialog v-model:visible="dialogVisible" modal header="Update Applicant Status" :style="{ width: '350px' }">
            <div class="flex flex-col gap-4">
                <Select v-model="statusForm.status" :options="statusOptions" optionLabel="label" optionValue="value"
                        placeholder="Select Status" class="w-full"/>
            </div>
            <template #footer>
                <Button label="Cancel" icon="pi pi-times" @click="dialogVisible = false" class="p-button-text" text/>
                <Button label="Save" icon="pi pi-check" @click="updateStatus" class="p-button-text p-button-danger"
                        :disabled="statusForm.processing" autofocus/>
            </template>
        </Dialog>

        <!-- Delete Confirmation Dialog -->
        <Dialog v-model:visible="deleteDialogVisible" modal header="Confirm Deletion" :style="{ width: '350px' }">
            <p>Are you sure you want to delete this applicant?</p>
            <template #footer>
                <Button label="No" icon="pi pi-times" @click="deleteDialogVisible = false" class="p-button-text"/>
                <Button label="Yes" icon="pi pi-check" @click="deleteApplicant" class="p-button-text p-button-danger"
                        :disabled="deleteForm.processing" autofocus/>
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
