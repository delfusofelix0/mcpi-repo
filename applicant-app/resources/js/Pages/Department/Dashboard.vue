<script setup>
import {computed, onMounted, ref} from 'vue';
import {Head, router, useForm} from '@inertiajs/vue3';
import AuthenticatedLayout from "@/Layouts/AuthenticatedLayout.vue";

const props = defineProps({
    window: Object,
    waitingTickets: Object,
    currentTicket: Object
});

const callNextForm = useForm({});
const callNextPriorityForm = useForm({});
const completeForm = useForm({});
const skipForm = useForm({});
const loading = ref(false);

const hasPriorityTickets = computed(() => props.waitingTickets.data.some(ticket => ticket.is_priority));
const hasNormalTickets = computed(() => props.waitingTickets.data.some(ticket => !ticket.is_priority));

const waitingCount = computed(() => props.waitingTickets.total);

onMounted(() => {
    loading.value = false;

    setInterval(() => {
        console.log('Refreshing waiting list...');
        refreshWaitingList();
    }, 3000);
});

// Add this function to refresh the waiting list
const refreshWaitingList = () => {
    loading.value = true;
    router.reload({
        only: ['waitingTickets'],
        onFinish: () => {
            loading.value = false;
        }
    });
};

// Call the next priority ticket in queue
const callNextPriority = () => {
    callNextPriorityForm.post(route('department.call-priority', {window_id: props.window.id}), {
        onSuccess: () => {
            refreshWaitingList();
        },
        onError: (errors) => {
            console.error('Error calling next priority ticket:', errors);
        }
    });
};

// Call the next ticket in queue
const callNext = () => {
    callNextForm.post(route('department.call-next', {window_id: props.window.id}), {
        onSuccess: () => {
            refreshWaitingList();
        },
        onError: (errors) => {
            console.error('Error calling next ticket:', errors);
        }
    });
};

// Mark current ticket as complete
const completeTicket = () => {
    if (props.currentTicket) {
        completeForm.post(route('department.complete', {ticket: props.currentTicket.id}), {
            onSuccess: (e) => {
                console.log('Ticket completed:', e);
                refreshWaitingList();
            },
            onError: (errors) => {
                console.error('Error completing ticket:', errors);
            }
        });
    }
};

// Skip current ticket
const skipTicket = () => {
    if (props.currentTicket) {
        skipForm.post(route('department.skip', {ticket: props.currentTicket.id}), {
            onSuccess: () => {
                refreshWaitingList();
            },
            onError: (errors) => {
                console.error('Error skipping ticket:', errors);
            }
        });
    }
};

</script>

<template>
    <Head :title="`${window.name} Dashboard`"/>

    <AuthenticatedLayout>
        <div class="p-4 bg-gray-100">
            <div class="max-w-7xl mx-auto">
                <h1 class="text-3xl font-bold mb-6">{{ window.name }} Window</h1>

                <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                    <!-- Current Ticket Card -->
                    <Card class="col-span-2">
                        <template #title>
                            <div class="flex justify-between items-center">
                                <h2 class="text-xl font-bold">Current Ticket</h2>
                                <template v-if="currentTicket && currentTicket.is_priority">
                                    <Tag
                                        value="SERVING - PRIORITY"
                                        severity="warn"
                                        class="p-2 font-semibold"
                                        size="large"
                                    />
                                </template>
                                <template v-else-if="currentTicket && !currentTicket.is_priority">
                                    <Tag
                                        value="SERVING"
                                        severity="success"
                                        class="p-2 font-semibold"
                                        size="large"
                                    />
                                </template>
                                <template v-else>
                                    <Tag
                                        value="SERVING?"
                                        severity="info"
                                        class="p-2 font-semibold"
                                        size="large"
                                    />
                                </template>
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
                                        :disabled="completeForm.processing"
                                    />
                                    <Button
                                        label="Skip"
                                        icon="pi pi-times"
                                        severity="danger"
                                        @click="skipTicket"
                                        :disabled="skipForm.processing"
                                    />
                                </div>
                            </div>

                            <div v-else class="text-center space-x-4 py-8">
                                <p class="text-gray-500 text-xl mb-6">No active ticket</p>
                                <Button
                                    label="Call Next Ticket"
                                    icon="pi pi-bell"
                                    severity="primary"
                                    size="large"
                                    @click="callNext"
                                    :disabled="waitingCount === 0 || !hasNormalTickets || callNextForm.processing"

                                />
                                <Button
                                    label="Call Priority Ticket"
                                    icon="pi pi-bell"
                                    severity="help"
                                    size="large"
                                    @click="callNextPriority"
                                    :disabled="waitingCount === 0 || !hasPriorityTickets || callNextPriorityForm.processing"
                                />
                            </div>
                        </template>
                    </Card>

                    <!-- Waiting Tickets Card -->
                    <Card>
                        <template #title>
                            <div class="flex items-center justify-between w-full">
                                <div class="flex items-center space-x-2">
                                    <h2 class="text-xl font-bold">Waiting List</h2>
                                    <Button
                                        icon="pi pi-refresh"
                                        @click="refreshWaitingList"
                                        text
                                        rounded
                                        aria-label="Refresh"
                                    />
                                </div>
                                <Badge :value="waitingCount" severity="info" class="p-2"/>
                            </div>
                        </template>

                        <template #content>
                            <DataTable
                                :value="waitingTickets.data"
                                paginator :rows="5"
                                :totalRecords="waitingTickets.total"
                                :loading="loading"
                            >
                                <template #empty> No tickets found.</template>
                                <template #loading> Loading tickets data. Please wait.</template>
                                <Column field="ticket_number" header="Ticket #"/>
                                <Column field="is_priority" header="Priority Lane">
                                    <template #body="{ data }">
                                        <Tag v-if="data.is_priority" class="font-bold" severity="danger">Yes</Tag>
                                        <Tag v-else severity="success">No</Tag>
                                    </template>
                                </Column>
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
    </AuthenticatedLayout>
</template>

<style scoped>

</style>
