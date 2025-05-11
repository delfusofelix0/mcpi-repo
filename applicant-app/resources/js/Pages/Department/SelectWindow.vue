<script setup>
import { Head, useForm } from '@inertiajs/vue3';

const props = defineProps({
    windows: Array,
    department: String
});

const form = useForm({
    window_id: null
});

const selectWindow = () => {
    form.post(route('select-window'));
};
</script>

<template>
    <Head :title="`Select Window`" />

    <div class="p-4 bg-gray-100 min-h-screen">
        <div class="max-w-md mx-auto bg-white p-6 rounded-lg shadow-md">
            <h1 class="text-2xl font-bold mb-6 text-center">Select Window</h1>

            <form @submit.prevent="selectWindow">
                <div class="mb-6">
                    <label class="block text-gray-700 mb-2">Please select a window:</label>
                    <select
                        v-model="form.window_id"
                        class="w-full p-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500"
                        required
                    >
                        <option value="" disabled>Select a window</option>
                        <option v-for="window in windows" :key="window.id" :value="window.id">
                            {{ window.name }}
                        </option>
                    </select>
                </div>

                <div class="flex justify-center">
                    <Button
                        type="submit"
                        class="px-4 py-2 bg-blue-600 text-white rounded-md hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-blue-500"
                        :disabled="form.processing"
                    >
                        Continue
                    </Button>
                </div>
            </form>
        </div>
    </div>
</template>
