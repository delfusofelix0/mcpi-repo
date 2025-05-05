<script setup>
import { onMounted, ref } from 'vue';
import { Link } from '@inertiajs/vue3';

const props = defineProps({
    ticket: Object
});

// Auto-redirect after 5 seconds
const countdown = ref(5);
const timer = ref(null);

// Function to stop the timer
const stopTimer = () => {
    if (timer.value) {
        clearInterval(timer.value);
        timer.value = null;
    }
};

onMounted(() => {
    // Automatically print the ticket when the page loads
    printTicket();

    // Start countdown for redirect
    timer.value = setInterval(() => {
        countdown.value--;
        if (countdown.value <= 0) {
            stopTimer();
            window.location = route('tickets.index');
        }
    }, 1000);
});

// This is a placeholder for the actual printing functionality
// You'll need to implement the actual printing logic for your thermal printer
const printTicket = () => {
    // Placeholder for the actual printing logic
    console.log('Printing ticket:', props.ticket);

    // Print the current page using the browser's print function
    // You'll replace this with your thermal printer integration
    window.print();
};
</script>

<template>
    <!-- Regular page view -->
    <div class="screen-only flex justify-center items-center min-h-screen bg-gray-100">
        <Card class="w-full max-w-md">
            <template #title>
                <div class="text-center">
                    <h1 class="text-3xl font-bold">Your Ticket</h1>
                </div>
            </template>

            <template #content>
                <div class="text-center">
                    <div class="bg-gray-200 p-8 rounded-lg mb-6">
                        <div class="text-7xl font-bold mb-2">{{ ticket.ticket_number }}</div>
                        <div class="text-xl">Please wait for your number</div>
                    </div>

                    <p class="mb-2">Your ticket has been printed.</p>
                    <p class="text-sm text-gray-500 mb-6">This screen will reset in {{ countdown }} seconds</p>

                    <Link :href="route('tickets.index')" @click="stopTimer">
                        <Button label="Get Another Ticket" class="px-4" />
                    </Link>
                </div>
            </template>
        </Card>
    </div>

    <!-- Print-only template -->
    <div class="print-only p-4">
        <div class="text-center">
            <h1 class="text-xl font-bold mb-2">School Queue System</h1>
            <div class="border-t border-b border-gray-300 py-6 my-4">
                <div class="text-5xl font-bold mb-2">{{ ticket.ticket_number }}</div>
                <div class="text-lg">Cashier Queue</div>
            </div>
            <p class="text-sm">{{ new Date(ticket.issue_time).toLocaleString() }}</p>
            <p class="text-sm mt-4">Please wait for your number to be called</p>
        </div>
    </div>
</template>

<style scoped>
/* Hide elements based on screen or print mode */
@media screen {
    .print-only {
        display: none;
    }
}

@media print {
    .screen-only {
        display: none;
    }

    .print-only {
        display: block;
    }
}
</style>
