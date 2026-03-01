<script setup>
import { Link } from '@inertiajs/vue3';
import { formatNumber } from '@/helpers';
import ProductStatusBadge from '@/Components/Product/ProductStatusBadge.vue';
import ProductImage from '@/Components/Product/ProductImage.vue';

defineProps({
    product: { type: Object, required: true },
    redirectShowProduct: { type: String, required: true },
});

</script>

<template>
    <component
        :is="redirectShowProduct ? Link : 'div'"
        :href="redirectShowProduct ? route(redirectShowProduct, product.id) : null"
        class="product-card lg:w-full md:w-fit sm:w-full sm:h-24 lg:h-auto md:h-full flex flex-col items-between justify-between rounded-3xl cursor-pointer transition-all duration-500 ease-out bg-white dark:bg-gray-800 p-4 space-y-4
        hover:bg-gradient-to-b hover:from-violet-300 hover:to-white dark:hover:from-violet-950 dark:hover:to-gray-700 shadow-lg lg:hover:scale-110 lg:hover:shadow-xl hover:animate-gradient"
    >
        <div>

            <ProductImage :path="product.image.path" :alt="product.name" />

            <div class="mt-3 name-category space-y-1">
                <h3 class="text-lg text-gray-900 dark:text-gray-100 title-clamp">
                    {{ product.name }}
                </h3>
                <p class="text-sm text-gray-400 font-serif category-clamp">
                    {{ product.category_name }}
                </p>
            </div>

            <p class="mt-3 text-2xl font-bold text-violet-800 dark:text-violet-400">
                ₱{{ product.price }}.00
            </p>

            <p class="mt-2 text-sm text-gray-700 dark:text-gray-300 description-clamp">
                {{ product.description }}
            </p>
        </div>

        <div class="flex flex-col sm:flex-row lg:items-center justify-between gap-4">
            <span class="text-gray-400 font-black text-lg hidden lg:block">
                {{ formatNumber(product.quantity) }} pc/s
            </span>
            <ProductStatusBadge :isAvailable="product.is_available" />
        </div>
    </component>
</template>

<style scoped>
.title-clamp {
    display: -webkit-box;
    -webkit-line-clamp: 2;
    -webkit-box-orient: vertical;
    overflow: hidden;
}

@media (max-width: 640px) {
    .title-clamp {
        -webkit-line-clamp: 1;
    }
}

.category-clamp {
    display: -webkit-box;
    -webkit-line-clamp: 1;
    -webkit-box-orient: vertical;
    overflow: hidden;
}

.description-clamp {
    display: -webkit-box;
    -webkit-line-clamp: 2;
    -webkit-box-orient: vertical;
    overflow: hidden;
}

@media (max-width: 640px) {
    .description-clamp {
        display: none;
    }
}

.name-category {
    min-height: 4.6em;
}

.product-card {
    position: relative;
    transition: transform 0.5s ease-out;
    z-index: 1;
}

.product-card:hover {
    z-index: 10;
    transform: scale(1.1);
}

@media (max-width: 1024px) {
    .product-card:hover {
        transform: none !important;
    }
}
</style>
