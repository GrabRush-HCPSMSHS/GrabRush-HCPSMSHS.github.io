<script setup>
import { ref } from 'vue';
import { WhenVisible } from '@inertiajs/vue3';
import { useDebouncedFilter } from '@/helpers';
import AppLayout from '@/Layouts/AppLayout.vue';
import OrderCard from '@/Components/Order/OrderCard.vue';
import TextInput from '@/Components/TextInput.vue';

defineProps({
    orders: {
        type: Array,
        required: true,
    },
    orders_pagination: {
        type: Object,
        required: true,
    },
});

const filterSearch = ref('');

useDebouncedFilter([{ name: 'filterSearch', value: filterSearch}], 'customer.orders.index');
</script>

<template>
    <AppLayout title="My Orders">
        <div class="py-12">
            <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-4">
                <h3 class="text-3xl text-gray-800 dark:text-gray-200 font-serif">
                    My Orders
                </h3>
                <TextInput
                    v-model="filterSearch"
                    placeholder="Search product"
                    class="rounded-xl border border-gray-50 w-full sm:w-auto lg:w-64"
                />
                <div class="orders-column">
                    <OrderCard v-for="order in orders" :key="order.id" :order="order" />
                </div>
                <WhenVisible
                    always
                    :params="{
                        data: {
                            page: orders_pagination.current_page + 1
                        },
                        only: ['orders', 'orders_pagination']
                    }"
                >
                    <div v-if="orders_pagination.current_page < orders_pagination.last_page" class="text-lg font-serif text-gray-800 dark:text-gray-200">Loading...</div>
                </WhenVisible>
            </div>
        </div>
    </AppLayout>
</template>

<style scoped>
.orders-column {
    column-width: 450px;
    column-gap: 1rem;
    width: 100%;
}

.orders-card {
    display: inline-block;
    width: 100%;
    margin-bottom: 1em;
}
</style>