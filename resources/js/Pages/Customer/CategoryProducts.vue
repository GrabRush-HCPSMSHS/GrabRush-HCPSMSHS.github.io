<script setup>
import { ref, computed, onMounted, onBeforeUnmount } from 'vue';
import { WhenVisible } from '@inertiajs/vue3';
import { useDebouncedFilter } from '@/helpers.js';
import AppLayout from '@/Layouts/AppLayout.vue';
import ProductCard from '@/Components/Product/ProductCard.vue';
import TextInput from '@/Components/TextInput.vue';

const redirectShowProduct = 'customer.products.show';
const filterProductName = ref('');
const isLgScreen = ref(false);
const productCount = computed(() => props.products.length);

const props = defineProps({
    category: { type: Object, required: true },
    products: { type: Array, required: true },
    products_pagination: { type: Object, required: true },
});

const checkScreenSize = () => {
    isLgScreen.value = window.innerWidth >= 800;
};

onMounted(() => {
    const storedImage = localStorage.getItem('critical');
    if (storedImage) {
        handleFilePondRevert();
    }
    checkScreenSize();
    window.addEventListener('resize', checkScreenSize);
});

onBeforeUnmount(() => {
    window.removeEventListener('resize', checkScreenSize);
});

useDebouncedFilter([
    { name: 'filterProductName', value: filterProductName },
], 'customer.products', { category: props.category.name.toLowerCase(), id: props.category.id });
</script>

<template>
    <AppLayout title="Home">
        <div class="py-12">
            <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-4 mb-8">
                <h3 class="text-3xl text-gray-800 dark:text-gray-200 font-serif">
                    {{ category.name }}
                </h3>
                <div class="flex flex-col sm:flex-row lg:justify-start sm:justify-center gap-2">
                    <div class="flex flex-col sm:flex-row gap-2">
                        <TextInput
                            v-model="filterProductName"
                            placeholder="Search product"
                            class="rounded-xl border border-gray-50 w-full sm:w-64"
                        />
                    </div>
                </div>
                <div v-if="isLgScreen" class="grid sm:grid-cols-2 md:grid-cols-3 lg:grid-cols-5 gap-4">
                    <ProductCard v-for="product in products" :key="product.id" :product="product" :redirect-show-product="redirectShowProduct" class="" />
                </div>
                <div v-else class="product-customers">
                    <p v-if="filterProductName" class="text-gray-700 dark:text-gray-400 text-4xl mb-4">
                        Found {{ productCount }} results
                    </p>
                    <ProductCard v-for="product in products" :key="product.id" :product="product" :redirect-show-product="redirectShowProduct" class="shrink-0 w-9/12" />
                </div>

                <WhenVisible
                    always
                    :params="{
                        data: {
                            page: products_pagination.current_page + 1
                        },
                        only: ['products', 'products_pagination']
                    }"
                >
                    <div v-if="products_pagination.current_page < products_pagination.last_page" class="text-lg font-serif text-gray-800 dark:text-gray-200">Loading...</div>
                </WhenVisible>
            </div>
        </div>
    </AppLayout>
</template>

<style scoped>
@media (max-width: 640px) {
    .product-customers {
        column-width: 150px;
        column-gap: 1rem;
        width: 100%;
    }

    .product-card {
        display: inline-block;
        width: 100%;
        margin-bottom: 1em;
    }
}
</style>
