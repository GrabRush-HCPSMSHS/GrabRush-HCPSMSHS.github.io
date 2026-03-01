<script setup>
import { formatNumber } from '@/helpers';
import AppLayout from '@/Layouts/AppLayout.vue';
import OrderCard from '@/Components/Order/OrderCard.vue';

defineProps({
    cards: {
        type: Array,
        required: true,
    },
    orders: {
        type: Object,
        required: true,
    }
});
</script>

<template>
    <AppLayout title="Dashboard">
        <div class="py-12">
            <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-4">
                <h3 class="text-3xl text-gray-800 dark:text-gray-200 font-serif">
                    Dashboard
                </h3>
                <div class="grid grid-cols-3 gap-4">
                    <div class="flex flex-col space-y-4 col-span-1">
                        <div v-for="(card, index) in cards" :key="index" class="bg-white dark:bg-gray-800 overflow-hidden shadow-md rounded-3xl p-4">
                            <h3 class="text-2xl font-bold text-gray-800 dark:text-gray-200">
                                {{ card.label.charAt(0).toUpperCase() + card.label.slice(1) }}
                            </h3>
                            <p class="text-6xl font-extrabold text-violet-500 dark:text-violet-400 text-end">
                                {{ formatNumber(card.value) }}
                            </p>
                        </div>
                    </div>
                    <div class="space-y-4 h-screen overflow-y-auto col-span-2">
                        <OrderCard v-for="order in orders.data" :order="order" />
                    </div>
                </div>
            </div>
        </div>
    </AppLayout>
</template>
