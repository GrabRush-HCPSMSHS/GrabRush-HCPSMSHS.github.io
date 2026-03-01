<script setup>
import AppLayout from '@/Layouts/AppLayout.vue';
import LineChart from '@/Components/Charts/LineChart.vue';
import DonutChart from '@/Components/Charts/DonutChart.vue';

defineProps({
    salesPerMonth: {
        type: Array,
        required: true,
    },
    topCategories: {
        type: Array,
        required: true,
    },
});
</script>

<template>
    <AppLayout title="Dashboard">
        <div class="py-12">
            <div class="max-w-7xl mx-auto px-4 grid grid-cols-1 gap-4 lg:grid-cols-3">
                <div class="lg:col-span-2">
                    <LineChart
                        :chart-data="salesPerMonth"
                        value-key="total_sales"
                        label-key="month"
                        class="col-span-1"
                    />
                </div>

                <DonutChart
                    :chart-data="topCategories"
                    label="Top Categories"
                    value-key="total_sales"
                    label-key="category_name"
                    class="col-span-1"
                />
            </div>
        </div>
    </AppLayout>
</template>
