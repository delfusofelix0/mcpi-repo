<script setup>
import { computed } from 'vue';
import {Head, router, useForm} from '@inertiajs/vue3';

const props = defineProps({
    window: Object,
    waitingTickets: Object,
    currentTicket: Object
});

const callNextForm = useForm({});
const completeForm = useForm({});
const skipForm = useForm({});

const waitingCount = computed(() => props.waitingTickets.length);

const onPageChange = (event) => {
    router.get(route('cashier.dashboard'), { page: (event.page / event.rows) + 1 }, {
        preserveState: true,
        preserveScroll: true,
    });
};

// Call the next ticket in queue
const callNext = () => {
    callNextForm.post(route('cashier.call-next', { window_id: props.window.id }));
};

// Mark current ticket as complete
const completeTicket = () => {
    if (props.currentTicket) {
        completeForm.post(route('cashier.complete', { ticket: props.currentTicket.id }));
    }
};

// Skip current ticket
const skipTicket = () => {
    if (props.currentTicket) {
        skipForm.post(route('cashier.skip', { ticket: props.currentTicket.id }));
    }
};
</script>

<template>
    <Head :title="`${window.name} Dashboard`" />

    <div class="p-4 bg-gray-100 min-h-screen">
        <div class="max-w-7xl mx-auto">
            <h1 class="text-3xl font-bold mb-6">{{ window.name }} Dashboard</h1>

            <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                <!-- Current Ticket Card -->
                <Card class="col-span-2">
                    <template #title>
                        <div class="flex justify-between items-center">
                            <h2 class="text-xl font-bold">Current Ticket</h2>
                            <Badge
                                v-if="currentTicket"
                                :value="'SERVING'"
                                severity="success"
                                class="p-2"
                            />
                        </div>
                    </template>

                    <template #content>
                        <div v-if="currentTicket" class="text-center py-4">
                            <div class="text-7xl font-bold mb-6">{{ currentTicket.ticket_number }}</div>

                            <div class="flex justify-center space-x-4 mt-4">
                                <Button
                                    label="Complete"
                                    icon="pi pi-check"
                                    severity="success"
                                    @click="completeTicket"
                                />
                                <Button
                                    label="Skip"
                                    icon="pi pi-times"
                                    severity="danger"
                                    @click="skipTicket"
                                />
                            </div>
                        </div>

                        <div v-else class="text-center py-8">
                            <p class="text-gray-500 text-xl mb-6">No active ticket</p>

                            <Button
                                label="Call Next Ticket"
                                icon="pi pi-bell"
                                severity="primary"
                                size="large"
                                @click="callNext"
                                :disabled="waitingCount === 0"
                            />
                        </div>
                    </template>
                </Card>

                <!-- Waiting Tickets Card -->
                <Card>
                    <template #title>
                        <div class="flex justify-between items-center">
                            <h2 class="text-xl font-bold">Waiting List</h2>
                            <Badge :value="waitingCount" severity="info" class="p-2" />
                        </div>
                    </template>

                    <template #content>
                        <DataTable
                            :value="waitingTickets.data"
                            :rows="waitingTickets.per_page"
                            :totalRecords="waitingTickets.total"
                            :first="(waitingTickets.current_page - 1) * waitingTickets.per_page"
                            @page="onPageChange"
                        >
                            <Column field="ticket_number" header="Ticket #" />
                            <Column field="issue_time" header="Time Issued">
                                <template #body="{ data }">
                                    {{ new Date(data.issue_time).toLocaleTimeString() }}
                                </template>
                            </Column>
                        </DataTable>
                    </template>
                </Card>
            </div>
        </div>
    </div>
</template>

<style scoped>

</style>
