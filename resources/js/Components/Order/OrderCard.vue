<script setup>
import { computed, ref } from 'vue';
import { formatDate } from '@/helpers';
import OrderStatusBadge from './OrderStatusBadge.vue';
import OrderItem from './OrderItem.vue';

const props = defineProps({
    order: {
        type: Object,
        required: true,
    },
    updateStatus: {
        type: Function,
    },
})

const modifiedReceipt = computed(() => {
  return props.order.receipt.split('-').slice(0, 2).join('-');
});

const showImage = ref(false);
const toggleImage = () => {
  showImage.value = !showImage.value;
};
</script>

<template>
    <div class="orders-card bg-white dark:bg-gray-800 rounded-2xl shadow-md p-6">
        <div class="flex flex-row justify-between">
            <div class="flex flex-col">
                <h3 class="text-xl font-semibold text-gray-800 dark:text-gray-200">
                    {{ modifiedReceipt }}
                </h3>
                <span class="text-sm text-gray-800 dark:text-gray-400">{{ formatDate(order.created_at) }}</span>
                <span class="text-xs text-gray-500 dark:text-gray-400">{{ order.user.name }} | {{ order.user.email }}</span>
            </div>

            <OrderStatusBadge :status="order.status" @click="updateStatus(order)" class="cursor-pointer" />
        </div>

        <div class="space-y-4 mt-4">
            <OrderItem v-for="(item, index) in order.order_items" :key="item.id" :item="item" />
        </div>

        <div class="flex justify-between items-center mt-4 pt-4 border-t border-gray-200 dark:border-gray-700">
            <span class="text-sm font-semibold text-gray-800 dark:text-gray-200">Total</span>
            <span class="text-xl font-extrabold text-gray-900 dark:text-gray-100 lg:text-xl">
                ₱{{ order.order_items.reduce((total, item) => total + (item.product.price * item.quantity), 0).toFixed(2) }}
            </span>
        </div>

        <template v-if="order.payment_image">
            <div @click="toggleImage" class="cursor-pointer mt-4 text-blue-500">
                <span>Click to {{ showImage ? 'hide' : 'show' }} payment image</span>
            </div>

            <div v-if="showImage" class="mt-4">
                <img :src="`/uploads/${order.payment_image.path}`" alt="Payment Image" class="w-full h-auto"/>
            </div>
        </template>
        <div v-else class="mt-4">
            <p class="text-sm text-gray-500 dark:text-gray-400">Payment: Over the Counter</p>
        </div>

        <slot></slot>
    </div>
</template>
