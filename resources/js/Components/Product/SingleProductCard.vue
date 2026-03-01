<script setup>
import { formatNumber } from '@/helpers';
import ProductStatusBadge from '@/Components/Product/ProductStatusBadge.vue';
defineProps({
    product: {
        type: Object,
        required: true,
    },
    onStatusToggle: {
        type: Function,
    }
});
</script>

<template>
    <div class="flex flex-col space-y-6 bg-white dark:bg-gray-800">
        <div class="flex flex-col space-y-2">
            <h3 class="text-3xl lg:text-5xl font-bold text-gray-900 dark:text-gray-100">
                {{ product.name }}
            </h3>
            <p class="text-lg font-medium font-serif text-gray-400 dark:text-gray-500">
                {{ product.category.name }}
            </p>
        </div>

        <div class="flex items-center justify-between">
            <p class="text-4xl font-bold text-violet-800 dark:text-violet-400">
                ₱{{ product.price }}.00
            </p>
            <p class="text-lg font-semibold text-gray-500 dark:text-gray-400">
                {{ formatNumber(product.quantity) }} pc/s
            </p>
        </div>

        <p class="text-base leading-relaxed text-gray-600 dark:text-gray-300">
            {{ product.description }}
        </p>

        <div class="flex items-center justify-between">
            <ProductStatusBadge :isAvailable="product.is_available" @click="onStatusToggle" class="cursor-pointer" />
        </div>
    </div>
</template>
