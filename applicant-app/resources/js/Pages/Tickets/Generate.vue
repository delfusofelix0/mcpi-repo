<script setup>
import { useForm } from '@inertiajs/vue3';

const form = useForm({});

const generateTicket = () => {
    form.post(route('tickets.generate'));
};
</script>

<template>
    <div class="flex justify-center items-center min-h-screen bg-gray-100">
        <Card class="w-full max-w-md">
            <template #title>
                <div class="text-center mb-6">
                    <h1 class="text-3xl font-bold">Queue Ticket System</h1>
                    <p class="text-gray-600 mt-2">Touch the button below to get a ticket</p>
                </div>
            </template>

            <template #content>
                <div class="flex justify-center">
                    <Button
                        @click="generateTicket"
                        label="Get Ticket"
                        severity="primary"
                        size="large"
                        class="text-2xl px-8 py-6"
                    />
                </div>
            </template>
        </Card>
    </div>
</template>
