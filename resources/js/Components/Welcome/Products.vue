<script setup>
import ProductCard from '@/Components/Product/ProductCard.vue';

defineProps({
    products: Array,
});
</script>

<template>
    <section id="products" class="mx-auto max-w-7xl px-4 sm:px-6 lg:px-8 py-24 sm:py-32 opacity-0 fade-in">
        <div class="mx-auto max-w-2xl lg:max-w-none">
            <h2 class="text-3xl font-bold tracking-tight text-gray-900 dark:text-white sm:text-4xl text-center">Explore Our Menu</h2>
            <p class="mt-4 text-xl leading-8 text-gray-600 dark:text-gray-400 text-center">A few of our most popular and delicious offerings.</p>

            <div class="mt-10 grid grid-cols-1 gap-x-6 gap-y-10 sm:grid-cols-2 lg:grid-cols-4 xl:gap-x-8">
                <ProductCard
                    v-for="product in products"
                    :key="product.id"
                    :product="product"
                    :redirect-show-product="''"
                />
            </div>
        </div>
    </section>
</template>
