<script setup>
import { ref, onMounted, onBeforeUnmount } from 'vue';
import { useDebouncedFilter } from '@/helpers';
import AppLayout from '@/Layouts/AppLayout.vue';
import CategoryCard from '@/Components/Category/CategoryCard.vue';
import TextInput from '@/Components/TextInput.vue';

const filterCategoryName = ref('');
const isLgScreen = ref(false);

const props = defineProps({
    categories: { type: Array, required: true },
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

useDebouncedFilter([{ name: 'filterCategoryName', value: filterCategoryName }], 'customer.categories.index');
</script>

<template>
    <AppLayout title="Categories">
        <div class="py-12">
            <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-4 mb-8">
                <h3 class="text-3xl text-gray-800 dark:text-gray-200 font-serif">
                    Categories
                </h3>
                <div class="flex flex-col sm:flex-row lg:justify-start sm:justify-center gap-2">
                    <div class="flex flex-col sm:flex-row gap-2">
                        <TextInput
                            v-model="filterCategoryName"
                            placeholder="Search product"
                            class="rounded-xl border border-gray-50 w-full sm:w-64"
                        />
                    </div>
                </div>
                <div class="grid grid-cols-2 md:grid-cols-3 lg:grid-cols-5 gap-4">
                    <CategoryCard v-for="category in categories" :key="category.id" :category="category" />
                </div>
            </div>
        </div>
    </AppLayout>
</template>