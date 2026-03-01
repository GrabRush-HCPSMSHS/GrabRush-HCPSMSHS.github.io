<script setup>
import { ref, onMounted, onBeforeUnmount } from 'vue';
import StatCard from '@/Components/Customer/StatCard.vue';

const isLgScreen = ref(false);

const iconTotalSpent = `<svg class="w-8 h-8 text-violet-600 dark:text-violet-400" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 9V7a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2m2 4h10a2 2 0 002-2v-6a2 2 0 00-2-2H9a2 2 0 00-2 2v6a2 2 0 002 2zm7-5a2 2 0 01-2 2H9a2 2 0 01-2-2V7a2 2 0 012-2h7a2 2 0 012 2v2"></path></svg>`;
const iconPendingOrders = `<svg class="w-8 h-8 text-yellow-600 dark:text-yellow-400" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>`;
const iconPreparingOrders = `<svg class="w-8 h-8 text-blue-600 dark:text-blue-400" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-3 7h3m-3 4h3m-6-4h.01M18 14h.01"></path></svg>`;
const iconReadyOrders = `<svg class="w-8 h-8 text-green-600 dark:text-green-400" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>`;

defineProps({
    totalMoneySpent: { type: Number, default: 0 },
    pendingOrders: { type: Number, default: 0 },
    preparingOrders: { type: Number, default: 0 },
    readyOrders: { type: Number, default: 0 },
});

const checkScreenSize = () => {
    isLgScreen.value = window.innerWidth >= 800;
};

onMounted(() => {
    checkScreenSize();
    window.addEventListener('resize', checkScreenSize);
});

onBeforeUnmount(() => {
    window.removeEventListener('resize', checkScreenSize);
});
</script>

<template>
    <div v-if="isLgScreen" class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-6">
        <StatCard
            :icon="iconTotalSpent"
            title="Total Spent"
            subtitle="Lifetime expenditure"
            :value="'₱' + totalMoneySpent"
            valueColor="text-violet-600 dark:text-violet-400"
        />

        <StatCard
            :icon="iconPendingOrders"
            title="Pending Orders"
            subtitle="Awaiting confirmation"
            :value="pendingOrders"
            valueColor="text-yellow-600 dark:text-yellow-400"
        />

        <StatCard
            :icon="iconPreparingOrders"
            title="Preparing Orders"
            subtitle="Being prepared by canteen"
            :value="preparingOrders"
            valueColor="text-blue-600 dark:text-blue-400"
        />

        <StatCard
            :icon="iconReadyOrders"
            title="Ready Orders"
            subtitle="Ready for pickup"
            :value="readyOrders"
            valueColor="text-green-600 dark:text-green-400"
        />
    </div>
    <div v-else class="sm:grid flex-grow grid-cols-1 overflow-x-auto pb-4">
        <div class="flex gap-4">
            <StatCard
                :icon="iconTotalSpent"
                title="Total Spent"
                subtitle="Lifetime expenditure"
                :value="'₱' + totalMoneySpent"
                valueColor="text-violet-600 dark:text-violet-400"
                class="shrink-0 w-48 aspect-video"
            />

            <StatCard
                :icon="iconPendingOrders"
                title="Pending Orders"
                subtitle="Awaiting confirmation"
                :value="pendingOrders"
                valueColor="text-yellow-600 dark:text-yellow-400"
                class="shrink-0 w-48 aspect-video"
            />

            <StatCard
                :icon="iconPreparingOrders"
                title="Preparing Orders"
                subtitle="Being prepared by canteen"
                :value="preparingOrders"
                valueColor="text-blue-600 dark:text-blue-400"
                class="shrink-0 w-48 aspect-video"
            />

            <StatCard
                :icon="iconReadyOrders"
                title="Ready Orders"
                subtitle="Ready for pickup"
                :value="readyOrders"
                valueColor="text-green-600 dark:text-green-400"
                class="shrink-0 w-48 aspect-video"
            />
        </div>
    </div>
</template>