<script setup>
import { Link } from '@inertiajs/vue3';
import { formatNumber } from '@/helpers';
import ProductImage from '@/Components/Product/ProductImage.vue';
defineProps({
	item: {
		type: Object,
		required: true,
	},
});
</script>

<template>
	<div class="flex items-center justify-between py-2">
	    <div class="flex aspect-square items-center justify-center overflow-hidden rounded-full w-16">
	        <ProductImage :path="item.product.image.path" :alt="item.product.name" />
	    </div>

	    <div class="flex-1 ml-4">
	        <Link :href="route('customer.products.show', item.product.id)" class="text-lg font-medium text-gray-800 dark:text-gray-200 hover:underline">{{ item.product.name }}</Link>
	        <div class="text-sm text-gray-400 dark:text-gray-500 font-serif">{{ item.product.category.name }}</div>
	    </div>

	    <div class="flex items-center justify-end">
	        <span class="text-xs text-gray-600 dark:text-gray-400">x{{ formatNumber(item.quantity) }}</span>
	        <div class="ml-2 text-sm font-bold text-violet-800 dark:text-violet-400">
	            ₱{{ (item.product.price * item.quantity).toFixed(2) }}
	        </div>
	    </div>
	</div>
</template>