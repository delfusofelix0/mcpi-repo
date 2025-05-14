<script setup>
import {onMounted, ref, computed} from 'vue';
import {Link, router} from '@inertiajs/vue3';

const props = defineProps({
    ticket: Object,
    token: String
});

const qrImageLoaded = ref(false);
const qrImageSrc = '/images/qrcode-print.jpg';

// Preload the QR code image
const preloadImage = () => {
    const img = new Image();
    img.onload = () => {
        qrImageLoaded.value = true;
        console.log('QR code image preloaded');
    };
    img.onerror = (err) => {
        console.error('Failed to load QR code image:', err);
    };
    img.src = qrImageSrc;
};

const departmentName = computed(() => {
    const dept = props.ticket.department;

    // Handle special registrar department formats
    if (dept === 'registrar-gradecollege') {
        return 'Registrar 1 - Grade School/College';
    } else if (dept === 'registrar-jhs') {
        return 'Registrar 2 - Junior High School';
    } else if (dept === 'registrar-shs') {
        return 'Registrar 3 - Senior High School';
    }

    // Default formatting for other departments
    return dept.charAt(0).toUpperCase() + dept.slice(1);
});

// Auto-redirect after 5 seconds
const countdown = ref(30);
const timer = ref(null);

// Function to stop the timer
const stopTimer = () => {
    if (timer.value) {
        clearInterval(timer.value);
        timer.value = null;
        router.visit(route('tickets.index') + "?token=" + props.token);
    }
};

onMounted(() => {
    // Preload the image first
    preloadImage();

    // Wait a short time to ensure image is in browser cache before printing
    setTimeout(() => {
        printTicket();
    }, 300);

    // Start countdown for redirect
    timer.value = setInterval(() => {
        countdown.value--;
        if (countdown.value <= 0) {
            stopTimer();
            router.visit(route('tickets.index') + "?token=" + props.token);
        }
    }, 1000);
});

const printTicket = () => {
    // Placeholder for the actual printing logic
    console.log('Printing ticket:', props.ticket);

    // Print the current page using the browser's print function
    window.print();
};
</script>

<template>
    <!-- Regular page view -->
    <div class="screen-only flex justify-center items-center min-h-screen bg-gray-100">
        <Card class="w-full max-w-md">
            <template #title>
                <div class="text-center mb-6">
                    <h1 class="text-3xl font-bold">Your Ticket</h1>
                    <p class="text-gray-600 mt-2">Please wait for your number to be called</p>
                </div>
            </template>

            <template #content>
                <div class="text-center">
                    <div class="text-6xl font-bold mb-4">{{ ticket.ticket_number }}</div>
                    <div class="text-xl mb-2">Department: {{ departmentName }}</div>
                    <div class="text-lg text-gray-600">
                        Issued at: {{ new Date(ticket.issue_time).toLocaleTimeString() }}
                    </div>

                    <p class="mb-2">Your ticket has been printed.</p>
                    <p class="text-sm text-gray-500 mb-6">This screen will reset in {{ countdown }} seconds</p>

                    <div class="space-x-2">
                        <Button @click="printTicket" label="Print Again" class="px-4"/>
                        <Button @click="stopTimer" label="Get Another Ticket" class="px-4"/>
                    </div>
                </div>
            </template>
        </Card>
    </div>

    <!-- Print-only template -->
    <div class="print-only p-4">
        <div class="text-center">
            <h1 class="text-xl font-bold mb-2">MCPI Queue</h1>
            <div class="py-2">
                <div class="text-5xl font-bold">{{ ticket.ticket_number }}</div>
                <div class="text-lg font-semibold">{{ departmentName }} Queue</div>
            </div>
            <p class="text-sm">{{ new Date(ticket.issue_time).toLocaleString() }}</p>
            <div class="flex justify-center">
                <Image :src="qrImageSrc" alt="QR Code" width="110" class="print-image"/>
            </div>
            <p class="text-sm mt-2 italic">We'd love to hear your thoughts!</p>
            <p class="text-sm">Please wait for your number to be called</p>
            <p class="text-sm">**This ticket will be valid today.**</p>
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

    /* Force image to be visible during print */
    .print-image {
        display: block !important;
        -webkit-print-color-adjust: exact;
        print-color-adjust: exact;
    }

    /* Override any PrimeVue styles that might interfere with printing */
    :deep(.p-image) {
        display: block !important;
    }

    :deep(.p-image img) {
        display: block !important;
        visibility: visible !important;
    }
}
</style>
