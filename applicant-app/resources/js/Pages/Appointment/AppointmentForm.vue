<script setup>
import {ref, computed} from 'vue';
import {useForm} from '@inertiajs/vue3';
import InputText from 'primevue/inputtext';
import DatePicker from 'primevue/datepicker';
import Button from 'primevue/button';
import Card from 'primevue/card';

const form = useForm({
  name: '',
  contact: '',
  date: null,
  time: null,
});

const availableTimeSlots = ref([]);

const fetchReservedTimeSlots = (date) => {
    // This would be an API call in a real application
    // For now, let's just return some random reserved slots
    return ['09:00 - 10:00', '14:00 - 15:00'];
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
            const slotLabel = `${hour.toString().padStart(2, '0')}:00 - ${(hour + 1).toString().padStart(2, '0')}:00`;
            slots.push({
                label: slotLabel,
                disabled: false,
                reserved: reservedSlots.includes(slotLabel)
            });
        } else {
            slots.push({
                label: 'Lunch Break',
                disabled: true,
                reserved: false
            });
        }
    }
    return slots;
};

const onDateSelect = (event) => {
    const selectedDate = event;
    if (selectedDate && isWeekday(selectedDate)) {
        const reservedSlots = fetchReservedTimeSlots(selectedDate);
        availableTimeSlots.value = generateTimeSlots(reservedSlots);
        form.date = selectedDate.toISOString().split('T')[0];
    } else {
        availableTimeSlots.value = [];
        form.date = null;
    }
    form.time = null; // Reset selected time when date changes
};

const isFormValid = computed(() => {
    return form.name && form.contact && form.date && form.time;
});

const submitForm = () => {
  // Handle form submission
  console.log('Form submitted', form);
  form.post
};
</script>

<template>
  <Card class="appointment-form">
    <template #title>
      Book an Appointment
    </template>
    <template #content>
      <form @submit.prevent="submitForm">
        <div class="field">
          <label for="name" class="block mb-2">Name</label>
          <InputText id="name" v-model="form.name" required class="w-full"/>
        </div>

        <div class="field mt-4">
          <label for="contact" class="block mb-2">Email or Phone Number</label>
          <InputText id="contact" v-model="form.contact" required class="w-full"/>
        </div>

        <div class="field mt-4">
          <label class="block mb-2">Select a Date</label>
          <DatePicker
              v-model="form.date"
              :disabledDays="[0,6]"
              @date-select="onDateSelect"
              :minDate="new Date()"
              inline
              showIcon
              class="w-full"
          />
        </div>
          <div v-if="availableTimeSlots.length > 0" class="field mt-4">
              <h3 class="text-xl font-semibold mb-2">Available Time Slots</h3>
              <div class="grid">
                  <div v-for="slot in availableTimeSlots" :key="slot.label" class="col-6 sm:col-4 md:col-3 lg:col-2">
                      <Button
                          :label="slot.label"
                          class="w-full mb-2 cursor-pointer"
                          :severity="getSeverity(slot)"
                          @click="selectTime(slot)"
                          :disabled="slot.disabled || slot.reserved"
                      />
                  </div>
              </div>
          </div>

        <Button type="submit" label="Book Appointment" class="mt-4" :disabled="!isFormValid || !form.time"/>
      </form>
    </template>
  </Card>
</template>

<style scoped>
.appointment-form {
  max-width: 600px;
  margin: 0 auto;
}
</style>
