<script setup>
import {computed, ref} from 'vue';
import {useForm} from '@inertiajs/vue3';
import {FilterMatchMode, FilterOperator} from "@primevue/core/api";
import {useToast} from "primevue/usetoast";

const props = defineProps({
    appointments: Array,
});

const toast = useToast();
const tableLoading = ref(false);
const confirmCancelDialog = ref(false);
const selectedAppointment = ref(null);
const filters = ref();

// Format date for display
const formatDate = (date) => {
    return new Date(date).toLocaleString('en-US', {
        year: 'numeric',
        month: 'long',
        day: 'numeric',
    });
};

const mapDates = (data) => {
    return [...(data || [])].map((d) => {
        d.date = new Date(d.date);
        return d;
    });
};

const formattedAppointments = computed(() => {
    return mapDates(props.appointments);
});

// Filter setup for DataTable
const initFilters = () => {
    filters.value = {
        global: {value: null, matchMode: FilterMatchMode.CONTAINS},
        name: {value: null, matchMode: FilterMatchMode.STARTS_WITH},
        'office.name': {value: null, matchMode: FilterMatchMode.CONTAINS},
        date: {operator: FilterOperator.AND, constraints: [{value: null, matchMode: FilterMatchMode.DATE_IS}]},
        time: {value: null, matchMode: FilterMatchMode.CONTAINS},
        company_name: {value: null, matchMode: FilterMatchMode.CONTAINS}
    };
};

initFilters();

const clearFilter = () => {
    initFilters();
};

// Form for sending SMS
const smsForm = useForm({
    id: null
});

const confirmAppointment = (appointment) => {
    selectedAppointment.value = appointment;
    confirmCancelDialog.value = true;
};

const sendAppointmentSms = () => {
    smsForm.id = selectedAppointment.value.id;

    smsForm.post(route('appointments.send-sms', selectedAppointment.value.id), {
        onSuccess: () => {
            confirmCancelDialog.value = false;
            selectedAppointment.value = null;
            toast.add({
                severity: 'success',
                summary: 'Success',
                detail: 'SMS sent successfully',
                life: 3000
            });
        },
        onError: (errors) => {
            toast.add({
                severity: 'error',
                summary: 'Error',
                detail: errors.message || 'Failed to send SMS',
                life: 3000
            });
        }
    });
};


</script>

<template>
    <Toast/>

    <Card>
        <template #title>
            <div class="flex items-center justify-between">
                <h3 class="text-xl font-semibold text-primary">Appointment List</h3>
            </div>
        </template>

        <template #content>
            <DataTable v-model:filters="filters"
                       :value="formattedAppointments"
                       showGridlines
                       stripedRows
                       paginator
                       :rows="10"
                       :rowsPerPageOptions="[10, 20, 30, 40, 50]"
                       tableStyle="min-width: 25rem"
                       class="p-datatable-sm"
                       responsiveLayout="scroll"
                       filterDisplay="menu"
                       :globalFilterFields="['name', 'contact', 'purpose', 'company_name', 'office.name']"
                       :loading="tableLoading"
                       dataKey="id"
                       removableSort>
                <template #loading>
                    <div class="flex flex-column items-center justify-center p-4">
                        <span class="mt-2 text-lg font-medium text-white">Loading appointments...</span>
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
                        <p>No appointments found.</p>
                    </div>
                </template>
                <Column field="name" header="Name" :showFilterMatchModes="false">
                    <template #filter="{ filterModel }">
                        <InputText v-model="filterModel.value" type="text" placeholder="Search by name" />
                    </template>
                </Column>
                <Column field="contact" header="Contact"></Column>
                <Column field="office.name" header="Office" :showFilterMatchModes="false">
                    <template #filter="{ filterModel }">
                        <InputText v-model="filterModel.value" type="text" placeholder="Search by office" />
                    </template>
                </Column>
                <Column filterField="date" header="Date" dataType="date">
                    <template #body="slotProps">
                        {{ formatDate(slotProps.data.date) }}
                    </template>
                    <template #filter="{ filterModel }">
                        <DatePicker v-model="filterModel.value" dateFormat="mm/dd/yy" placeholder="mm/dd/yyyy"/>
                    </template>
                </Column>
                <Column field="time" header="Time" :sortable="true"></Column>
                <Column field="purpose" header="Purpose" style="max-width: 150px; overflow: hidden;">
                    <template #body="slotProps">
                        <div class="truncate" v-tooltip.top="slotProps.data.purpose">
                            {{ slotProps.data.purpose }}
                        </div>
                    </template>
                </Column>
                <Column field="company_name" header="Company"></Column>
                <Column header="Action" class="text-center" style="width: 8%">
                    <template #body="slotProps">
                        <div class="flex justify-center gap-2">
                            <Button icon="pi pi-send" class="p-button-text p-button-rounded p-button-success"
                                    @click="confirmAppointment(slotProps.data)"
                                    aria-label="Confirm Appointment"/>
                        </div>
                    </template>
                </Column>
            </DataTable>
        </template>
    </Card>

    <!-- Confirmation Dialog for SMS -->
    <Dialog
        v-model:visible="confirmCancelDialog"
        modal
        header="Send Appointment Confirmation"
        :style="{ width: '450px' }"
    >
        <div class="flex items-center gap-2 mb-4">
            <i class="pi pi-send text-blue-500" style="font-size: 2rem"></i>
            <span>Are you sure you want to send an SMS confirmation for this appointment?</span>
        </div>
        <template #footer>
            <Button label="No" icon="pi pi-times" outlined @click="confirmCancelDialog = false"/>
            <Button label="Yes" icon="pi pi-check" severity="success" @click="sendAppointmentSms"
                    :loading="smsForm.processing"/>
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
