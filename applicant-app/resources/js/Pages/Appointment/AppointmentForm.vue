<script setup>
import {ref, computed, watch} from 'vue';
import {Head, useForm} from '@inertiajs/vue3';

import {useToast} from "primevue/usetoast";


const toast = useToast();
const props = defineProps({
    offices: Array,
    reservedSlots: Array
})

// Add these for success dialog
const successDialogVisible = ref(false);
const appointmentDetails = ref({
    name: '',
    office: '',
    date: '',
    time: ''
});

const form = useForm({
    name: '',
    contact: '',
    date: null,
    time: null,
    office_id: '',
});

// New reactive variable for the DatePicker
const selectedDateValue = ref(null);
const selectedOffice = ref(null);
const availableTimeSlots = ref([]);

const fetchReservedTimeSlots = async (date) => {
    if (!selectedOffice.value || !selectedOffice.value.id) {
        return [];
    }

    try {
        const response = await axios.get(route('appointments.reserved-slots'), {
            params: {
                date: date instanceof Date ?
                    `${date.getFullYear()}-${(date.getMonth() + 1).toString().padStart(2, '0')}-${date.getDate().toString().padStart(2, '0')}` :
                    date,
                office_id: selectedOffice.value.id
            }
        });

        return response.data.reservedSlots || [];
    } catch (error) {
        console.error('Error fetching reserved slots:', error);
        toast.add({
            severity: 'error',
            summary: 'Error',
            detail: 'Failed to fetch available time slots',
            life: 3000
        });
        return [];
    }
};

const isWeekday = (date) => {
    const day = date.getDay();
    return day !== 0 && day !== 6;
};

const getSeverity = (slot) => {
    if (slot.disabled) return 'secondary';
    if (slot.reserved) return 'danger';
    if (form.time === slot.label) return 'success';
    return 'primary';
};


const selectTime = (slot) => {
    if (!slot.disabled && !slot.reserved) {
        form.time = slot.label;
    }
};

const generateTimeSlots = (reservedSlots) => {
    const slots = [];
    for (let hour = 8; hour < 17; hour++) {
        if (hour !== 12) {
            const startHour = hour % 12 || 12;
            const endHour = (hour + 1) % 12 || 12;
            const startAmPm = hour < 12 ? 'AM' : 'PM';
            const endAmPm = (hour + 1) < 12 ? 'AM' : 'PM';

            const slotLabel = `${startHour}:00 ${startAmPm} - ${endHour}:00 ${endAmPm}`;
            slots.push({
                label: slotLabel,
                disabled: false,
                reserved: reservedSlots.includes(slotLabel)
            });
        }
    }
    return slots;
};

// Add this watch to reset and fetch new data when office changes
watch(selectedOffice, (newOffice) => {
    // Reset date and time selections
    selectedDateValue.value = null;
    form.date = null;
    form.time = null;
    availableTimeSlots.value = [];

    // Update the office_id in the form
    if (newOffice && newOffice.id) {
        form.office_id = newOffice.id;
    } else {
        form.office_id = '';
    }
});

// Watch for changes to selectedDateValue
watch(selectedDateValue, async (newDate) => {
    if (newDate && isWeekday(newDate)) {
        // Format date for form
        if (newDate instanceof Date) {
            const year = newDate.getFullYear();
            const month = (newDate.getMonth() + 1).toString().padStart(2, '0');
            const day = newDate.getDate().toString().padStart(2, '0');
            form.date = `${year}-${month}-${day}`;
        } else {
            form.date = newDate;
        }

        const reservedSlots = await fetchReservedTimeSlots(newDate);
        availableTimeSlots.value = generateTimeSlots(reservedSlots);
    } else {
        availableTimeSlots.value = [];
        form.date = null;
    }
    form.time = null; // Reset selected time when date changes
});

const isFormValid = computed(() => {
    return form.name && form.contact && form.date && form.time && form.office_id;
});

const submitForm = () => {
    // Handle form submission
    console.table(form);
    form.post(route('appointments.create'), {
        onSuccess: () => {
            // Store appointment details for the success dialog
            appointmentDetails.value = {
                name: form.name,
                office: selectedOffice.value?.name || '',
                date: selectedDateValue.value ? new Date(selectedDateValue.value).toLocaleDateString('en-US', {
                    year: 'numeric',
                    month: 'long',
                    day: 'numeric'
                }) : '',
                time: form.time
            };

            // Show success dialog instead of toast
            successDialogVisible.value = true;

            // Reset form after successful submission
            form.reset();
            selectedDateValue.value = null;
            selectedOffice.value = null;
            availableTimeSlots.value = [];
        },
        onError: (e) => {
            // Show error toast
            console.log(e)
            toast.add({
                severity: 'error',
                summary: 'Error',
                detail: 'Failed to book appointment. Please check your information.',
                life: 5000
            });
        }
    });
};

const closeSuccessDialog = () => {
    successDialogVisible.value = false;
};
</script>

<template>
    <div class="bg-[#EDEFEF] min-h-screen py-8">
        <Head title="Appointment Form"/>

        <!-- Success Dialog -->
        <Dialog v-model:visible="successDialogVisible" modal header="Appointment Confirmed!" :style="{ width: '90%', maxWidth: '500px' }">
            <div class="p-4">
                <div class="flex flex-col items-center mb-4">
                    <i class="pi pi-check-circle text-green-500 text-5xl mb-3"></i>
                    <h3 class="text-xl font-bold">Thank You, {{ appointmentDetails.name }}!</h3>
                    <p class="text-center mt-2">Your appointment has been successfully booked.</p>
                </div>

                <div class="bg-gray-50 p-4 rounded-lg mb-4">
                    <div class="grid grid-cols-2 gap-2 mb-2">
                        <div class="font-semibold">Office:</div>
                        <div>{{ appointmentDetails.office }}</div>
                    </div>
                    <div class="grid grid-cols-2 gap-2 mb-2">
                        <div class="font-semibold">Date:</div>
                        <div>{{ appointmentDetails.date }}</div>
                    </div>
                    <div class="grid grid-cols-2 gap-2">
                        <div class="font-semibold">Time:</div>
                        <div>{{ appointmentDetails.time }}</div>
                    </div>
                </div>

                <Message severity="info" class="w-full mb-4">
                    <span class="font-bold">Important:</span> A confirmation text has been sent to your phone.
                    Please present a <span class="font-bold text-red-500">valid ID</span> and show the text to the School Guard upon arrival.
                    <br>If you don't receive the text, please take a screenshot of this confirmation as backup.
                </Message>

                <div class="flex justify-center">
                    <Button label="Close" @click="closeSuccessDialog" class="w-full md:w-auto" />
                </div>
            </div>
        </Dialog>

        <Card class="appointment-form mt-8 mb-8 shadow-lg">
            <template #title>
                Book an Appointment
            </template>
            <template #content>
                <form @submit.prevent="submitForm">
                    <!-- Name and Phone Number side by side -->
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div class="field">
                            <label for="name" class="block mb-2">Name</label>
                            <InputText id="name" placeholder="Full name" :class="{ 'p-invalid': form.errors.name }"
                                       @input="form.clearErrors('name')"
                                       v-model="form.name" required class="w-full"/>
                            <Message size="small" severity="secondary" variant="simple">Use the same name as your valid GOVERNMENT ID.</Message>
                            <p class="text-red-500" v-if="form.errors.name">{{ form.errors.name }}</p>
                        </div>

                        <div class="field">
                            <label for="contact" class="block mb-2">Phone Number</label>
                            <InputMask id="contact" :class="{ 'p-invalid': form.errors.contact }"
                                       @input="form.clearErrors('contact')"
                                       mask="99999999999" placeholder="09XXXXXXXXX"
                                       v-model="form.contact" required class="w-full"/>
                            <Message size="small" severity="secondary" variant="simple">Use a working phone number.</Message>
                            <p class="text-red-500" v-if="form.errors.contact">{{ form.errors.contact }}</p>
                        </div>
                    </div>

                    <!-- Office and DatePicker side by side -->
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mt-4">
                        <div class="field">
                            <label for="office" class="block mb-2">Office</label>
                            <Select id="office" v-model="selectedOffice" :options="offices" optionLabel="name"
                                    placeholder="Select a City" class="w-full"
                                    @change="form.office_id = selectedOffice?.id || ''"/>
                            <p class="text-red-500" v-if="form.errors.office_id">{{ form.errors.office_id }}</p>
                        </div>

                        <div class="field">
                            <label class="block mb-2">Select a Date</label>
                            <DatePicker
                                placeholder="Select a date"
                                :disabled="selectedOffice === null"
                                v-model="selectedDateValue"
                                :disabledDays="[0,6]"
                                :minDate="new Date()"
                                showIcon
                                class="w-full"
                                dateFormat="yy-mm-dd"
                            />
                        </div>
                    </div>

                    <!-- Time slots centered -->
                    <div v-if="availableTimeSlots.length > 0" class="field mt-6 max-w-2xl mx-auto">
                        <h3 class="text-xl font-semibold mb-2 text-center">Available Time Slots</h3>

                        <!-- Two-column layout for medium screens and up -->
                        <div class="hidden md:flex flex-wrap gap-4">
                            <!-- Morning slots (first 4 hours) -->
                            <div class="flex-1 min-w-[45%]">
                                <h4 class="text-md font-medium mb-2">Morning</h4>
                                <div class="grid gap-2">
                                    <div v-for="slot in availableTimeSlots.slice(0, 4)" :key="slot.label"
                                         class="col-12">
                                        <Button
                                            :label="slot.label"
                                            class="w-full text-sm p-2 h-auto"
                                            :severity="getSeverity(slot)"
                                            @click="selectTime(slot)"
                                            :disabled="slot.disabled || slot.reserved"
                                            size="small"
                                        />
                                    </div>
                                </div>
                            </div>

                            <!-- Afternoon slots (remaining hours) -->
                            <div class="flex-1 min-w-[45%]">
                                <h4 class="text-md font-medium mb-2">Afternoon</h4>
                                <div class="grid gap-2">
                                    <div v-for="slot in availableTimeSlots.slice(4)" :key="slot.label" class="col-12">
                                        <Button
                                            :label="slot.label"
                                            class="w-full text-sm p-2 h-auto"
                                            :severity="getSeverity(slot)"
                                            @click="selectTime(slot)"
                                            :disabled="slot.disabled || slot.reserved"
                                            size="small"
                                        />
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Single column grid for small screens -->
                        <div class="grid gap-2 md:hidden">
                            <div v-for="slot in availableTimeSlots" :key="slot.label" class="col-6 sm:col-4">
                                <Button
                                    :label="slot.label"
                                    class="w-full text-sm p-2 h-auto"
                                    :severity="getSeverity(slot)"
                                    @click="selectTime(slot)"
                                    :disabled="slot.disabled || slot.reserved"
                                    size="small"
                                />
                            </div>
                        </div>
                    </div>

                    <div class="flex justify-center mt-6">
                        <Button type="submit" label="Book Appointment" :disabled="!isFormValid || !form.time"/>
                    </div>
                </form>
            </template>
        </Card>
    </div>
</template>

<style scoped>
.appointment-form {
    max-width: 800px;
    margin: 2rem auto;
    padding: 1rem;
    border-radius: 8px;
}
</style>
