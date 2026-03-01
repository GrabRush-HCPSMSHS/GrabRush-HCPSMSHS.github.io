<script setup>
import { ref, watch } from 'vue';
import { useForm, WhenVisible } from '@inertiajs/vue3';
import { useDebouncedFilter, toast } from '@/helpers';
import AppLayout from '@/Layouts/AppLayout.vue';
import OrderCard from '@/Components/Order/OrderCard.vue';
import TextInput from '@/Components/TextInput.vue';

const props = defineProps({
    status: {
        type: String,
        required: true,
    },
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
const orders = ref([...props.orders]);
const existingIds = ref(new Set(orders.value.map(order => order.id)));

watch(() => props.orders, (newOrders) => {
    orders.value = [];
    existingIds.value.clear();

    newOrders.forEach(order => {
        if (!existingIds.value.has(order.id)) {
            orders.value.push(order);
            existingIds.value.add(order.id);
        }
    });
});

const updateStatus = (order) => {
    let nextStatus;

    switch (order.status) {
        case 'pending':
            nextStatus = 'preparing';
            break;
        case 'preparing':
            nextStatus = 'ready';
            break;
        case 'ready':
            nextStatus = 'complete';
            break;
        default:
            return;
    }

    if (confirm(`Are you sure you want to change the order status to ${nextStatus}?`)) {
        const form = useForm({
            status: nextStatus,
        });

        form.put(route('staff.orders.update', {
            order: order.id,
        }), {
            onSuccess: () => {
                orders.value = orders.value.filter(o => o.id !== order.id);
                existingIds.value.delete(order.id);
                toast(`Order status changed to ${nextStatus}`, 'success');
            },
            onError: () => toast('Failed to update order status', 'error'),
            preserveScroll: true,
        });
    }
};

useDebouncedFilter([{ name: 'filterSearch', value: filterSearch}], 'staff.orders.status', { status: props.status });
</script>

<template>
    <AppLayout title="Staff">
        <div class="py-12">
            <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-4">
                <h3 class="text-3xl text-gray-800 dark:text-gray-200 font-serif">
                    {{ status.charAt(0).toUpperCase() + status.slice(1) }}
                </h3>
                <TextInput
                    v-model="filterSearch"
                    placeholder="Search receipt or name"
                    class="rounded-xl border border-gray-50 w-full sm:w-auto lg:w-64"
                />
                <OrderCard v-for="order in orders" :key="order.id" :order="order" :updateStatus="updateStatus" />
                <WhenVisible
                    v-if="orders_pagination.current_page < orders_pagination.last_page"
                    always
                    :params="{
                        data: {
                            page: orders_pagination.current_page + 1
                        },
                        only: ['orders', 'orders_pagination'],
                        preserveState: true,
                        preserveScroll: true
                    }"
                >
                    <div v-if="orders_pagination.current_page < orders_pagination.last_page" class="text-lg font-serif text-gray-800 dark:text-gray-200">Loading...</div>
                </WhenVisible>
            </div>
        </div>
    </AppLayout>
</template>
