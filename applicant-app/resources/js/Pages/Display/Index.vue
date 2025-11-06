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
const waitingList = ref([]);
const isLoading = ref(false);

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
        isLoading.value = true;
        console.log('Refreshing tickets...');
        // Use the encrypted token directly from the URL
        const url = `${route('api.display-tickets')}?token=${encodeURIComponent(token)}`;

        const response = await axios.get(url);
        const newTickets = response.data.tickets || [];
        // Add this for waiting list
        waitingList.value = response.data.waitingList || [];

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
    } finally {
        isLoading.value = false;
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

// Helper to get the ticket for each cashier window
function getCashierTicket(n) {
  const label = `Cashier ${n}`;
  const ticket = tickets.value.find(t => t.window && t.window.toLowerCase() === label.toLowerCase() && t.ticket);
  return ticket ? ticket.ticket : '---';
}
// Helper to get queue tickets (not currently serving)
function getQueueTickets() {
  // Use the backend-provided waitingList for the cashier, limited to 7 tickets
  return waitingList.value.slice(0, 7);
}

function getCashierTicketObj(n) {
  const label = `Cashier ${n}`;
  return tickets.value.find(t => t.window && t.window.toLowerCase() === label.toLowerCase() && t.ticket) || {};
}
</script>

<template>
  <div class="display-root">
    <div class="display-content-flex">
      <div class="main-content">
        <!-- Left: Ticket Info -->
        <div class="ticket-section improved-bg">
          <!-- Cashier 1 -->
          <div class="ticket-card card-cashier1">
            <div class="accent-bar accent-purple"></div>
            <div class="ticket-flex">
              <div class="ticket-label-col">
                <div v-if="getCashierTicketObj(1).is_priority" class="priority-label"><span class="star">★</span>Priority Number</div>
                <div class="ticket-label">Cashier 1</div>
              </div>
              <div class="ticket-number-col">
                <div :class="['ticket-number', { 'priority-number': getCashierTicketObj(1).is_priority }]">{{ getCashierTicket(1) }}</div>
              </div>
            </div>
          </div>
          <!-- Cashier 2 -->
          <div class="ticket-card card-cashier2">
            <div class="accent-bar accent-blue"></div>
            <div class="ticket-flex">
              <div class="ticket-label-col">
                <div v-if="getCashierTicketObj(2).is_priority" class="priority-label"><span class="star">★</span>Priority Number</div>
                <div class="ticket-label">Cashier 2</div>
              </div>
              <div class="ticket-number-col">
                <div :class="['ticket-number', { 'priority-number': getCashierTicketObj(2).is_priority }]">{{ getCashierTicket(2) }}</div>
              </div>
            </div>
          </div>
          <!-- Cashier 3 (Priority) -->
          <div class="ticket-card card-cashier3">
            <div class="accent-bar accent-red"></div>
            <div class="ticket-flex">
              <div class="ticket-label-col">
                <div v-if="getCashierTicketObj(3).is_priority" class="priority-label"><span class="star">★</span>Priority Number</div>
                <div class="ticket-label">Cashier 3</div>
              </div>
              <div class="ticket-number-col">
                <div :class="['ticket-number', { 'priority-number': getCashierTicketObj(3).is_priority }]">{{ getCashierTicket(3) }}</div>
              </div>
            </div>
          </div>
        </div>
        <!-- Right: Video Player -->
        <div class="video-section improved-video">
          <div class="video-bg animated-gradient">
            <iframe
              width="100%"
              height="100%"
              src="https://www.youtube.com/embed/lFy4kaHp1lY?playlist=lFy4kaHp1lY,UZvSPbvP2uA,HFZH8N6orwc,huP3f3A3ooE,b6RQbgwKSvE,T9Igvml0SpI,eppi31c1MbI,A2EGZ3zRbn8,tIgMe7OFp1E&autoplay=1&loop=1&rel=0&controls=1&mute=1"
              frameborder="0"
              allow="autoplay; encrypted-media"
              allowfullscreen
              class="youtube-iframe"
            ></iframe>
          </div>
        </div>
      </div>
      <div class="queue-bar improved-queue">
        <span :class="['queue-icon', { 'loading-effect': isLoading }]">⏳</span>
        <span class="queue-label">Waiting List:</span>
        <span v-for="ticket in getQueueTickets()" :key="ticket.ticket_number" class="queue-ticket">{{ ticket.ticket_number }}</span>
      </div>
    </div>
    <footer class="display-footer">
      <div class="marquee">
        <span>
          Please wait for your turn at the cashier. Thank you for your patience. We're happy to serve you. For updates and announcements, visit us at https://mcpi.edu.ph or follow us on Facebook at https://www.facebook.com/MCPIcommunity.
        </span>
      </div>
    </footer>
  </div>
</template>

<style scoped>
.display-footer {
  width: 100vw;
  background: #f8fafc;
  color: #222;
  font-family: 'Times New Roman', Times, serif;
  font-size: 2.2rem;
  font-weight: 600;
  text-align: center;
  padding: 2rem 0 2.5rem 0;
  letter-spacing: 0.02em;
  box-shadow: 0 -2px 12px 0 rgba(30,58,138,0.04);
  margin-top: 0;
  overflow: hidden;
  position: relative;
}
.marquee {
  width: 100%;
  overflow: hidden;
  white-space: nowrap;
  position: relative;
}
.marquee span {
  display: inline-block;
  padding-left: 100vw;
  animation: marquee-scroll 18s linear infinite;
}
@keyframes marquee-scroll {
  0% { transform: translateX(0); }
  100% { transform: translateX(-100%); }
}
</style>
