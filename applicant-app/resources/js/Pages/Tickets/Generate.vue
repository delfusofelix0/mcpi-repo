<script setup>
import { ref } from 'vue';
import { useForm } from '@inertiajs/vue3';

const props = defineProps({
    token: String,
})
const showDepartments = ref(false);
const form = useForm({
    department: ''
});

const showDepartmentOptions = () => {
    showDepartments.value = true;
};

const generateTicket = (department) => {
    form.department = department;
    form.post(route('tickets.generate') + '?token=' + props.token);
};
</script>

<template>
    <div class="flex justify-center items-center min-h-screen bg-gray-100">
        <Card class="w-full max-w-md">
            <template #title>
                <div class="text-center mb-6">
                    <h1 class="text-3xl font-bold">Queue Ticket System</h1>
                    <p class="text-gray-600 mt-2" v-if="!showDepartments">Touch the button below to get a ticket</p>
                    <p class="text-gray-600 mt-2" v-else>Select your destination</p>
                </div>
            </template>

            <template #content>
                <div v-if="!showDepartments" class="flex justify-center">
                    <Button
                        @click="showDepartmentOptions"
                        label="Get Ticket"
                        severity="primary"
                        size="large"
                        class="text-2xl px-8 py-6"
                    />
                </div>

                <div v-else class="flex flex-col gap-4">
                    <Button
                        @click="generateTicket('cashier')"
                        label="Cashier"
                        severity="info"
                        size="large"
                        class="text-xl px-8 py-4"
                    />

                    <Button
                        @click="generateTicket('accounting')"
                        label="Accounting"
                        severity="info"
                        size="large"
                        class="text-xl px-8 py-4"
                    />

                    <Button
                        @click="generateTicket('registrar-gradecollege')"
                        label="Registrar - Grade School/College"
                        severity="info"
                        size="large"
                        class="text-xl px-8 py-4"
                    />

                    <Button
                        @click="generateTicket('registrar-jhs')"
                        label="Registrar - Junior High School"
                        severity="info"
                        size="large"
                        class="text-xl px-8 py-4"
                    />

                    <Button
                        @click="generateTicket('registrar-shs')"
                        label="Registrar - Senior High School"
                        severity="info"
                        size="large"
                        class="text-xl px-8 py-4"
                    />
                </div>
            </template>
        </Card>
    </div>
</template>
