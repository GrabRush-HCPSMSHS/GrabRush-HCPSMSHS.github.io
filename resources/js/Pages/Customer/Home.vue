<script setup>
import { ref, onMounted, onBeforeUnmount, computed } from 'vue';
import { Link, WhenVisible } from '@inertiajs/vue3';
import AppLayout from '@/Layouts/AppLayout.vue';
import ProductCard from '@/Components/Product/ProductCard.vue';
import CustomerStatsCard from '@/Components/Customer/CustomerStatsCard.vue';
import { formatNumber } from '@/helpers';

const isLgScreen = ref(false);
const redirectShowProduct = 'customer.products.show';

const props = defineProps({
    categories: { type: Array, required: true },
    categories_pagination: { type: Object, required: true },
    completedOrdersWithItems: { type: Array, default: () => [] },
    pendingOrders: { type: Number, default: 0 },
    preparingOrders: { type: Number, default: 0 },
    readyOrders: { type: Number, default: 0 },
    totalMoneySpent: { type: Number, default: 0 },
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
    <AppLayout title="Home">
        <div class="py-12">
            <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 mb-8">
                <section class="space-y-4 mb-16">
                    <div>
                        <h2 class="text-4xl font-extrabold tracking-tight text-gray-900 dark:text-white sm:text-5xl">
                            Your Canteen Activity at a Glance
                        </h2>
                        <p class="mt-4 text-xl text-gray-600 dark:text-gray-300">
                            Stay updated with your spending and order statuses.
                        </p>
                    </div>
                    <CustomerStatsCard
                        :totalMoneySpent="formatNumber(totalMoneySpent)"
                        :pendingOrders="pendingOrders"
                        :preparingOrders="preparingOrders"
                        :readyOrders="readyOrders"
                    />
                </section>

                <section class="space-y-4">
                    <h2 class="text-3xl font-extrabold tracking-tight text-gray-900 dark:text-white mb-8">
                        Explore Our Delicious Menu
                    </h2>
                    <div v-for="category in categories" :key="category.id" class="space-y-4">
                        <div class="flex items-center justify-between">
                            <h3 class="text-2xl text-gray-800 dark:text-gray-200 font-serif">
                                {{ category.name }}
                            </h3>
                            <Link :href="route('customer.products', { category: category.name.toLowerCase(), id: category.id })" class="text-lg text-gray-600 dark:text-gray-400">
                                Show All
                            </Link>
                        </div>
                        <div v-if="isLgScreen" class="grid sm:grid-cols-2 md:grid-cols-3 lg:grid-cols-5 gap-4">
                            <ProductCard v-for="product in category.products" :key="product.id" :product="product" :redirect-show-product="redirectShowProduct" class="" />
                        </div>
                        <div v-else class="sm:grid flex-grow"
                            :class="{
                                'min-h-[50px]': category.products.length === 0,
                                'grid-cols-1 overflow-x-auto pb-4 px-1': category.products.length > 0
                            }"
                        >
                            <div class="flex gap-4">
                                <ProductCard v-for="product in category.products" :key="product.id" :product="product" :redirect-show-product="redirectShowProduct" class="shrink-0 w-40" />
                            </div>
                        </div>
                        <br />
                    </div>

                    <WhenVisible
                        always
                        :params="{
                            data: {
                                page: categories_pagination.current_page + 1
                            },
                            only: ['categories', 'categories_pagination']
                        }"
                    >
                        <div v-if="categories_pagination.current_page < categories_pagination.last_page" class="text-lg font-serif text-gray-800 dark:text-gray-200">Loading...</div>
                    </WhenVisible>
                </section>

            </div>
        </div>
    </AppLayout>
</template>