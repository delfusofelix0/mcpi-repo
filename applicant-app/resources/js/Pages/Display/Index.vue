<script setup>
import {onMounted, onUnmounted, ref, computed} from 'vue';
import axios from 'axios';

const props = defineProps({
    currentTickets: {
        type: Array,
        default: () => []
    }
});

const tickets = ref(props.currentTickets)
const currentTime = ref(new Date().toLocaleTimeString());
const currentDate = ref(new Date().toLocaleDateString());

// Get the token from the URL
const urlParams = new URLSearchParams(window.location.search);
const token = urlParams.get('token');

// Update the time every second
setInterval(() => {
    currentTime.value = new Date().toLocaleTimeString();
}, 1000);

// For now, we'll use basic polling to update the tickets
// This will be replaced with WebSockets in the future
const refreshTickets = async () => {
    try {
        console.log('Refreshing tickets...');
        // Use the encrypted token directly from the URL
        const url = `${route('api.display-tickets')}?token=${encodeURIComponent(token)}`;

        const response = await axios.get(url);

        const newTickets = response.data.tickets || [];

        // Update the ref array reactively
        tickets.value.splice(0, tickets.value.length, ...newTickets);
        console.log('Tickets updated:', tickets.value);
    } catch (error) {
        console.error('Error refreshing tickets:', error);
    }
};

let intervalId;

// Refresh every 3 seconds (this is temporary until WebSockets are implemented)
onMounted(() => {
    console.log('Mounted!');
    console.log('Token:', token);
    console.log('Route:', route('api.display-tickets')); // check route
    refreshTickets();                         // call once
    intervalId = setInterval(refreshTickets, 3000); // call every 3s
});

onUnmounted(() => {
    clearInterval(intervalId);
});

// Computed properties to group tickets by department
// Group tickets by department
const cashierTickets = computed(() => {
    return tickets.value.filter(ticket => ticket.window.toLowerCase().includes('cashier'));
});

const accountingTickets = computed(() => {
    return tickets.value.filter(ticket => ticket.window.toLowerCase().includes('accounting'));
});

const registrarTickets = computed(() => {
    return tickets.value.filter(ticket => ticket.window.toLowerCase().includes('registrar'));
});
</script>

<template>
    <div class="bg-blue-900 min-h-screen text-white">
        <!-- Header -->
        <div class="bg-blue-800 p-4 flex justify-between items-center">
            <div class="flex items-center">
                <img src="images/mcpi-logo.png" alt="mcpi-logo" class="w-10 h-10 mr-2"/>
                <div class="text-2xl font-bold">Maryknoll College of Panabo Inc.</div>
            </div>
            <div class="text-right">
                <div class="text-xl">{{ currentTime }}</div>
                <div>{{ currentDate }}</div>
            </div>
        </div>

        <!-- Main display area -->
        <div class="p-6">
            <h1 class="text-4xl font-bold text-center">NOW SERVING</h1>

            <!-- Group tickets by department -->
            <div class="space-y-6">
                <!-- Cashier Windows -->
                <div v-if="accountingTickets.length > 0">
                    <h2 class="text-2xl font-bold mb-2">Cashier</h2>
                    <div class="grid grid-cols-2 gap-4">
                        <div
                            v-for="ticket in accountingTickets"
                            :key="ticket.id"
                            class="bg-blue-700 rounded-lg p-4 text-center shadow-lg"
                        >
                            <div class="text-2xl font-bold mb-2">{{ ticket.window }}</div>
                            <div class="text-6xl font-bold p-4" v-if="ticket.ticket">
                                {{ ticket.ticket }}
                            </div>
                            <div class="text-4xl italic p-6 text-gray-300" v-else>
                                ---
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Accounting and Registrar Windows Side by Side -->
                <div class="grid grid-cols-2 gap-4">
                    <!-- Accounting Windows -->
                    <div v-if="cashierTickets.length > 0">
                        <h2 class="text-2xl font-bold mb-2">Accounting</h2>
                        <div class="grid grid-cols-1 gap-4">
                            <div
                                v-for="ticket in cashierTickets"
                                :key="ticket.id"
                                class="bg-blue-700 rounded-lg p-4 text-center shadow-lg"
                            >
                                <div class="text-2xl font-bold mb-2">{{ ticket.window }}</div>
                                <div class="text-6xl font-bold p-4" v-if="ticket.ticket">
                                    {{ ticket.ticket }}
                                </div>
                                <div class="text-4xl italic p-6 text-gray-300" v-else>
                                    ---
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Registrar Windows -->
                    <div v-if="registrarTickets.length > 0">
                        <h2 class="text-2xl font-bold mb-2">Registrar</h2>
                        <div class="grid grid-cols-1 gap-4">
                            <div
                                v-for="ticket in registrarTickets"
                                :key="ticket.id"
                                class="bg-blue-700 rounded-lg p-4 text-center shadow-lg"
                            >
                                <div class="text-2xl font-bold mb-2">{{ ticket.window }}</div>
                                <div class="text-6xl font-bold p-4" v-if="ticket.ticket">
                                    {{ ticket.ticket }}
                                </div>
                                <div class="text-4xl italic p-6 text-gray-300" v-else>
                                    ---
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Footer -->
        <div class="bg-blue-800 p-4 fixed bottom-0 w-full">
            <div class="text-2xl text-center">Thank you for your patience</div>
        </div>
    </div>
</template>
<style scoped>
.form-background {
    background-image: url('/images/bg.webp'); /* Light blue background */
    background-size: cover;
    background-position: center;
    background-attachment: fixed; /* This makes the background fixed while scrolling */
}

</style>
