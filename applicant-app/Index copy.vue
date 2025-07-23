<script setup>
import {onMounted, onUnmounted, ref, computed} from 'vue';
import axios from 'axios';

const props = defineProps({
    currentTickets: {
        type: Array,
        default: () => []
    }
});
console.log('Current tickets:', props.currentTickets);

const isFirstLoad = ref(true);
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

        // Store current tickets for comparison
        const previousTickets = [...tickets.value];

        // Update the ref array reactively
        tickets.value.splice(0, tickets.value.length, ...newTickets);

        // Check if there are any new tickets or changes, but skip on first load
        if (!isFirstLoad.value && hasChanges(previousTickets, newTickets)) {
            console.log('Playing notification sound due to changes');
            playNotificationSound();
        } else if (isFirstLoad.value) {
            console.log('First load - skipping notification');
            isFirstLoad.value = false;
        } else {
            console.log('No changes detected');
        }
    } catch (error) {
        console.error('Error refreshing tickets:', error);
    }
};

// Optimized function to check for changes
const hasChanges = (oldTickets, newTickets) => {
    // Quick check: Different length means tickets were added or removed
    if (oldTickets.length !== newTickets.length) {
        console.log('Different length detected');

        // Check if we're only removing tickets or adding empty tickets
        const oldWithNumbers = oldTickets.filter(t => t.ticket).length;
        const newWithNumbers = newTickets.filter(t => t.ticket).length;

        // Only trigger sound if we have more tickets with numbers
        if (newWithNumbers > oldWithNumbers) {
            console.log('New tickets with numbers added');
            return true;
        } else {
            console.log('Only removing tickets or adding empty tickets');
            return false;
        }
    }

    // Create a deep copy of both arrays for comparison
    const oldTicketsJSON = JSON.stringify(oldTickets.map(t => ({
        id: t.id,
        ticket: t.ticket,
        window: t.window,
        is_priority: t.is_priority,
        status: t.status
    })).sort((a, b) => a.id - b.id));

    const newTicketsJSON = JSON.stringify(newTickets.map(t => ({
        id: t.id,
        ticket: t.ticket,
        window: t.window,
        is_priority: t.is_priority,
        status: t.status
    })).sort((a, b) => a.id - b.id));

    // Compare the stringified versions
    const hasChanged = oldTicketsJSON !== newTicketsJSON;

    if (hasChanged) {
        // Check if the only changes are tickets becoming null
        let onlyNullChanges = true;

        for (const newTicket of newTickets) {
            const oldTicket = oldTickets.find(t => t.id === newTicket.id);

            // If we found a ticket that changed from one number to another number
            if (oldTicket && oldTicket.ticket && newTicket.ticket && oldTicket.ticket !== newTicket.ticket) {
                console.log(`Ticket changed from ${oldTicket.ticket} to ${newTicket.ticket}`);
                onlyNullChanges = false;
                break;
            }

            // If we found a ticket that was null but now has a number
            if (oldTicket && !oldTicket.ticket && newTicket.ticket) {
                console.log(`Ticket changed from null to ${newTicket.ticket}`);
                onlyNullChanges = false;
                break;
            }
        }

        if (onlyNullChanges) {
            console.log('Only null changes detected, not triggering sound');
            return false;
        }

        console.log('Meaningful changes detected in tickets');
        return true;
    }

    return false;
};

let intervalId;

// Function to play notification sound
const playNotificationSound = () => {
    const audio = new Audio();
    audio.src = 'alert.mp3'; // Replace with your sound file path
    audio.play().catch(error => {
        console.error('Failed to play notification sound:', error);
    });
};
// Refresh every 2.5 seconds (this is temporary until WebSockets are implemented)
onMounted(() => {
    console.log('Mounted!');
    console.log('Token:', token);
    console.log('Route:', route('api.display-tickets')); // check route
    refreshTickets();                         // call once
    intervalId = setInterval(refreshTickets, 2500); // call every 2.5s
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
                <!-- Accounting Windows -->
                <div v-if="accountingTickets.length > 0">
                    <h2 class="text-2xl font-bold mb-2">Accounting</h2>
                    <div class="grid grid-cols-2 gap-4">
                        <div
                            v-for="ticket in accountingTickets"
                            :key="ticket.id"
                            class="bg-blue-700 rounded-lg p-4 text-center shadow-lg relative window-card"
                        >
                            <div class="text-2xl font-bold mb-2">{{ ticket.window }}</div>
                            <div class="text-6xl font-bold p-4 ticket-number" v-if="ticket.ticket">
                                {{ ticket.ticket }}
                            </div>
                            <div class="text-4xl italic p-6 text-gray-300 ticket-number" v-else>
                                ---
                            </div>
                            <Message v-if="ticket.is_priority" size="large" severity="error"
                                     class="absolute top-2 right-2 uppercase px-2 py-1 rounded-full blinking"
                            >
                                <span class="font-bold tracking-wider">Priority</span>
                            </Message>
                        </div>
                    </div>
                </div>


                <!-- Accounting and Registrar Windows Side by Side -->
                <div class="grid grid-cols-2 gap-4">
                    <!-- Cashier Windows -->
                    <div v-if="cashierTickets.length > 0">
                        <h2 class="text-2xl font-bold mb-2">Cashier</h2>
                        <div class="grid grid-cols-1 gap-4">
                            <div
                                v-for="ticket in cashierTickets"
                                :key="ticket.id"
                                class="bg-blue-700 rounded-lg p-4 text-center shadow-lg relative window-card"
                            >
                                <div class="text-2xl font-bold mb-2">{{ ticket.window }}</div>
                                <div class="text-6xl font-bold p-4 ticket-number" v-if="ticket.ticket">
                                    {{ ticket.ticket }}
                                </div>
                                <div class="text-4xl italic p-6 text-gray-300 ticket-number" v-else>
                                    ---
                                </div>
                                <Message v-if="ticket.is_priority" size="large" severity="error"
                                         class="absolute top-2 right-2 uppercase px-2 py-1 rounded-full blinking"
                                >
                                    <span class="font-bold tracking-wider">Priority</span>
                                </Message>
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
                                class="bg-blue-700 rounded-lg p-4 text-center shadow-lg relative window-card"
                            >
                                <div class="text-2xl font-bold mb-2">{{ ticket.window }}</div>
                                <div class="text-6xl font-bold p-4 ticket-number" v-if="ticket.ticket">
                                    {{ ticket.ticket }}
                                </div>
                                <div class="text-4xl italic p-6 text-gray-300 ticket-number" v-else>
                                    ---
                                </div>
                                <Message v-if="ticket.is_priority" size="large" severity="error"
                                         class="absolute top-2 right-2 uppercase px-2 py-1 rounded-full blinking"
                                >
                                    <span class="font-bold tracking-wider">Priority</span>
                                </Message>
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

.window-card {
    height: 200px; /* Fixed height for all window cards */
    display: flex;
    flex-direction: column;
    justify-content: space-between;
}

.ticket-number {
    flex-grow: 1;
    display: flex;
    align-items: center;
    justify-content: center;
    position: relative;
}

.blinking {
    animation: blinker 2s ease 2s infinite normal forwards;
}

@keyframes blinker {
    0% {
        opacity: 1;
    }

    50% {
        opacity: 0.2;
    }

    100% {
        opacity: 1;
    }
}
</style>
