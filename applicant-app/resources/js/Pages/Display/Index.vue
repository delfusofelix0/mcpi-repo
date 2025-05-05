<script setup>
import {onMounted, onUnmounted, ref} from 'vue';
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
        // Add the token to the API request
        const url = `${route('api.display-tickets')}?token=${token}`;
        console.log('Fetching from:', url);

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
</script>

<template>
    <div class="bg-blue-900 min-h-screen text-white">
        <!-- Header -->
        <div class="bg-blue-800 p-4 flex justify-between items-center">
            <div class="text-2xl font-bold">School Cashier Queue</div>
            <div class="text-right">
                <div class="text-xl">{{ currentTime }}</div>
                <div>{{ currentDate }}</div>
            </div>
        </div>

        <!-- Main display area -->
        <div class="p-8">
            <h1 class="text-4xl font-bold text-center mb-12">NOW SERVING</h1>

            <div class="grid grid-cols-2 gap-10">
                <div
                    v-for="(ticket, index) in tickets"
                    :key="index"
                    class="bg-blue-700 rounded-lg p-8 text-center shadow-lg"
                >
                    <div class="text-3xl font-bold mb-4">{{ ticket.window }}</div>
                    <div class="text-8xl font-bold p-8" v-if="ticket.ticket">
                        {{ ticket.ticket }}
                    </div>
                    <div class="text-5xl italic p-12 text-gray-300" v-else>
                        ---
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
