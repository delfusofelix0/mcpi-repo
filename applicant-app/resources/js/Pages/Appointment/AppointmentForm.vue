<script setup>
import {ref, computed, watch} from 'vue';
import {Head, useForm} from '@inertiajs/vue3';
import {useToast} from "primevue/usetoast";

const toast = useToast();
const props = defineProps({
    offices: Array,
    reservedSlots: Array
})

// Change to submission dialog
const confirmDialogVisible = ref(false);
const appointmentDetails = ref({
    name: '',
    office: '',
    date: null,
    time: null,
    purpose: '',
    company_name: '',
    address: ''
});

const form = useForm({
    name: '',
    contact: '',
    date: null,
    time: null,
    office_id: '',
    company_name: '',
    address: '',
    purpose: ''
});

// New reactive variable for the DatePicker
const selectedDateValue = ref(null);
const selectedOffice = ref(null);

// Check for the check pass time slot
const isCurrentDay = (date) => {
    if (!date) return false;
    const today = new Date();
    const compareDate = new Date(date);
    return today.getFullYear() === compareDate.getFullYear() &&
        today.getMonth() === compareDate.getMonth() &&
        today.getDate() === compareDate.getDate();
};

const availableTimeSlots = ref([]);
const fetchTimeSlotsLoading = ref(false);
const fetchTimeSlotsError = ref(false);

const fetchReservedTimeSlots = async (date) => {
    if (!selectedOffice.value || !selectedOffice.value.id) {
        return [];
    }

    try {
        fetchTimeSlotsLoading.value = true;
        fetchTimeSlotsError.value = false;
        const response = await axios.get(route('appointments.reserved-slots'), {
            params: {
                date: date instanceof Date ?
                    `${date.getFullYear()}-${(date.getMonth() + 1).toString().padStart(2, '0')}-${date.getDate().toString().padStart(2, '0')}` :
                    date,
                office_id: selectedOffice.value.id
            }
        });
        fetchTimeSlotsLoading.value = false;
        return response.data.reservedSlots || [];
    } catch (e) {
        toast.add({
            severity: 'error',
            summary: 'Error',
            detail: 'Failed to fetch available time slots',
            life: 5000
        });
        fetchTimeSlotsLoading.value = false;
        fetchTimeSlotsError.value = true;
        return null;
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

// Add this variable to track when we're loading new slots
const loadingNewSlots = ref(false);

const generateTimeSlots = (reservedSlots) => {
    const slots = [];
    const currentDate = new Date();
    const currentHour = currentDate.getHours();
    const isToday = isCurrentDay(selectedDateValue.value);

    for (let hour = 8; hour < 17; hour++) {
        if (hour !== 12) {
            const startHour = hour % 12 || 12;
            const endHour = (hour + 1) % 12 || 12;
            const startAmPm = hour < 12 ? 'AM' : 'PM';
            const endAmPm = (hour + 1) < 12 ? 'AM' : 'PM';

            const slotLabel = `${startHour}:00 ${startAmPm} - ${endHour}:00 ${endAmPm}`;

            // Disable the slot if it's today and the current hour is >= the slot's hour
            const isPastSlot = isToday && hour <= currentHour;

            slots.push({
                label: slotLabel,
                disabled: isPastSlot,
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

        // If we already have slots displayed, show skeleton loading
        if (availableTimeSlots.value.length > 0) {
            loadingNewSlots.value = true;
        }

        try {
            const reservedSlots = await fetchReservedTimeSlots(newDate);
            // Only generate time slots if we successfully got the reserved slots
            if (reservedSlots !== null) {
                availableTimeSlots.value = generateTimeSlots(reservedSlots);
            } else {
                availableTimeSlots.value = [];
            }
        } catch (error) {
            availableTimeSlots.value = [];
            fetchTimeSlotsError.value = true;
        } finally {
            loadingNewSlots.value = false;
        }
    } else {
        availableTimeSlots.value = [];
        form.date = null;
    }
    form.time = null; // Reset selected time when date changes
});

const isFormValid = computed(() => {
    return form.name && form.contact && form.date && form.time && form.office_id && form.purpose && form.address;
});

const submitForm = () => {
    // Handle form submission
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
                time: form.time,
                purpose: form.purpose,
                company_name: form.company_name || 'N/A',
                address: form.address || 'N/A'
            };

            // Show success dialog
            confirmDialogVisible.value = true;

            // Reset form after successful submission
            form.reset();
            selectedDateValue.value = null;
            selectedOffice.value = null;
            availableTimeSlots.value = [];
        },
        onError: (e) => {
            console.log(e);
            console.table(form);
            toast.add({
                severity: 'error',
                summary: 'Error',
                detail: 'Failed to submit appointment request. Please check your information.',
                life: 5000
            });
        }
    });
};
</script>

<template>
    <Toast/>
    <div class="bg-[#EDEFEF] min-h-screen py-8">
        <Head title="Appointment Form"/>

        <!-- Submission Dialog -->
        <Dialog v-model:visible="confirmDialogVisible" modal header="Appointment Request Submitted"
                :style="{ width: '90%', maxWidth: '500px' }">
            <div class="p-4">
                <div class="flex flex-col items-center mb-4">
                    <i class="pi pi-check-circle text-green-500 text-5xl mb-3"></i>
                    <h3 class="text-xl font-bold">Thank You!</h3>
                    <p class="text-center mt-2">Your appointment request has been submitted successfully.</p>
                </div>

                <Message severity="info" class="w-full mb-4">
                    <span class="font-bold">Important:</span> Your appointment is pending approval.
                    You will receive a confirmation text message once your appointment is approved by the admin.
                    <br><br>
                    Please do not visit the school until you receive the confirmation text.
                </Message>

                <div class="flex justify-center">
                    <Button label="Close" @click="confirmDialogVisible = false" class="w-full md:w-auto"/>
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
                            <Message size="small" severity="secondary" variant="simple">Use the same name as your valid
                                GOVERNMENT ID.
                            </Message>
                            <p class="text-red-500" v-if="form.errors.name">{{ form.errors.name }}</p>
                        </div>

                        <div class="field">
                            <label for="contact" class="block mb-2">Phone Number</label>
                            <InputMask id="contact" :class="{ 'p-invalid': form.errors.contact }"
                                       @input="form.clearErrors('contact')"
                                       mask="99999999999" placeholder="09XXXXXXXXX"
                                       v-model="form.contact" required class="w-full"/>
                            <Message size="small" severity="secondary" variant="simple">Use a working phone number.
                            </Message>
                            <p class="text-red-500" v-if="form.errors.contact">{{ form.errors.contact }}</p>
                        </div>
                    </div>

                    <!-- Add this after the Name and Phone Number section -->
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mt-4">
                        <div class="field">
                            <label for="company_name" class="block mb-2">Company Name</label>
                            <InputText id="company_name" placeholder="Company name (if applicable)"
                                       :class="{ 'p-invalid': form.errors.company_name }"
                                       @input="form.clearErrors('company_name')"
                                       v-model="form.company_name" class="w-full"/>
                            <p class="text-red-500" v-if="form.errors.company_name">{{ form.errors.company_name }}</p>
                        </div>

                        <div class="field">
                            <label for="address" class="block mb-2">Address</label>
                            <InputText id="address" placeholder="Your address"
                                       :class="{ 'p-invalid': form.errors.address }"
                                       @input="form.clearErrors('address')"
                                       v-model="form.address" class="w-full"/>
                            <p class="text-red-500" v-if="form.errors.address">{{ form.errors.address }}</p>
                        </div>
                    </div>

                    <div class="field mt-4">
                        <label for="purpose" class="block mb-2">Purpose of Visit</label>
                        <Textarea id="purpose" placeholder="Please describe the purpose of your visit"
                                  :class="{ 'p-invalid': form.errors.purpose }"
                                  @input="form.clearErrors('purpose')"
                                  v-model="form.purpose" required rows="3" class="w-full"/>
                        <p class="text-red-500" v-if="form.errors.purpose">{{ form.errors.purpose }}</p>
                    </div>

                    <!-- Office and DatePicker side by side -->
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mt-4">
                        <div class="field">
                            <label for="office" class="block mb-2">Office</label>
                            <Select id="office" v-model="selectedOffice" :options="offices" optionLabel="name"
                                    placeholder="Select an Office" class="w-full"
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
                                    <template v-if="loadingNewSlots">
                                        <div v-for="i in 4" :key="`morning-skeleton-${i}`" class="col-12">
                                            <Skeleton height="2.5rem" width="100%" class="rounded-md"></Skeleton>
                                        </div>
                                    </template>
                                    <template v-else>
                                        <div v-for="slot in availableTimeSlots.slice(0, 4)" :key="slot.label" class="col-12">
                                            <Button
                                                :label="slot.label"
                                                class="w-full text-sm p-2 h-[2.5rem]"
                                                :severity="getSeverity(slot)"
                                                @click="selectTime(slot)"
                                                :disabled="slot.disabled || slot.reserved"
                                                size="small"
                                            />
                                        </div>
                                    </template>
                                </div>
                            </div>

                            <!-- Afternoon slots (remaining hours) -->
                            <div class="flex-1 min-w-[45%]">
                                <h4 class="text-md font-medium mb-2">Afternoon</h4>
                                <div class="grid gap-2">
                                    <template v-if="loadingNewSlots">
                                        <div v-for="i in 4" :key="`afternoon-skeleton-${i}`" class="col-12">
                                            <Skeleton height="2.5rem" width="100%" class="rounded-md"></Skeleton>
                                        </div>
                                    </template>
                                    <template v-else>
                                        <div v-for="slot in availableTimeSlots.slice(4)" :key="slot.label" class="col-12">
                                            <Button
                                                :label="slot.label"
                                                class="w-full text-sm p-2 h-[2.5rem]"
                                                :severity="getSeverity(slot)"
                                                @click="selectTime(slot)"
                                                :disabled="slot.disabled || slot.reserved"
                                                size="small"
                                            />
                                        </div>
                                    </template>
                                </div>
                            </div>
                        </div>

                        <!-- Single column grid for small screens -->
                        <div class="grid gap-2 md:hidden">
                            <template v-if="loadingNewSlots">
                                <div v-for="i in 8" :key="`mobile-skeleton-${i}`" class="col-6 sm:col-4">
                                    <Skeleton height="2.5rem" width="100%" class="rounded-md"></Skeleton>
                                </div>
                            </template>
                            <template v-else>
                                <div v-for="slot in availableTimeSlots" :key="slot.label" class="col-6 sm:col-4">
                                    <Button
                                        :label="slot.label"
                                        class="w-full text-sm p-2 h-[2.5rem]"
                                        :severity="getSeverity(slot)"
                                        @click="selectTime(slot)"
                                        :disabled="slot.disabled || slot.reserved"
                                        size="small"
                                    />
                                </div>
                            </template>
                        </div>
                    </div>
                    <div v-else-if="fetchTimeSlotsLoading" class="text-center py-4">
                        <i class="pi pi-spin pi-spinner text-2xl"></i>
                        <p class="mt-2">Loading available time slots...</p>
                    </div>
                    <div v-else-if="fetchTimeSlotsError"
                         class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded relative mt-4">
                        <strong class="font-bold">Error!</strong>
                        <span class="block sm:inline ml-2">Failed to fetch reserved time slots. Please try again.</span>
                    </div>

                    <div class="flex justify-center mt-6">
                        <Button type="submit" label="Submit Appointment Request"
                                :disabled="!isFormValid || !form.time"/>
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
