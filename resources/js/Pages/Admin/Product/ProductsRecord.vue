<script setup>
import { ref, onMounted, onBeforeUnmount, computed } from 'vue';
import { useForm, router, Link, WhenVisible } from '@inertiajs/vue3';
import { useDebouncedFilter, formatNumber, toast } from '@/helpers.js';
import AppLayout from '@/Layouts/AppLayout.vue';
import TextInput from '@/Components/TextInput.vue';
import SelectDropdown from '@/Components/SelectDropdown.vue';
import InputGroup from '@/Components/InputGroup.vue';
import PrimaryButton from '@/Components/PrimaryButton.vue';
import SecondaryButton from '@/Components/SecondaryButton.vue';
import ProductCard from '@/Components/Product/ProductCard.vue';
import Table from '@/Components/Table/Table.vue';
import TableBody from '@/Components/Table/TableBody.vue';
import TableData from '@/Components/Table/TableData.vue';
import TableHead from '@/Components/Table/TableHead.vue';
import TableRow from '@/Components/Table/TableRow.vue';
import Paginator from '@/Components/Table/Paginator.vue';
import ProductImage from '@/Components/Product/ProductImage.vue';
import ProductStatusBadge from '@/Components/Product/ProductStatusBadge.vue';
import VueFilePond from 'vue-filepond';
import 'filepond/dist/filepond.min.css';
import 'filepond-plugin-image-preview/dist/filepond-plugin-image-preview.min.css';
import FilePondPluginFileValidateType from 'filepond-plugin-file-validate-type/dist/filepond-plugin-file-validate-type.esm.js';
import FilePondPluginFileValidateSize from 'filepond-plugin-file-validate-size/dist/filepond-plugin-file-validate-size.esm.js';
import FilePondPluginImageValidateSize from 'filepond-plugin-image-validate-size/dist/filepond-plugin-image-validate-size.esm.js';
import FilePondPluginImagePreview from 'filepond-plugin-image-preview/dist/filepond-plugin-image-preview.esm.js';

const FilePond = VueFilePond(
    FilePondPluginFileValidateType,
    FilePondPluginFileValidateSize,
    FilePondPluginImageValidateSize,
    FilePondPluginImagePreview
);

const props = defineProps({
    products: { type: Array, required: true },
    products_pagination: { type: Object, required: true },
    categories: { type: Array, required: true },
});

const categoryOptions = computed(() =>
    props.categories.map(category => ({
        label: category.name,
        value: category.id
    }))
);

const redirectShowProduct = 'admin.products.show';
const filterProductName = ref('');
const filterCategoryId = ref('');
const filterProductStatus = ref('');
const isModalOpen = ref(false);
const isLgScreen = ref(false);
const tempImageUrl = ref(null);

const form = useForm({
    category_id: '',
    name: '',
    description: '',
    product_image_id: '',
    quantity: '1',
    price: '0.00',
    is_available: '1',
});

const handleFilePondAddFile = (error, file) => {
    if (error) return;
    if (tempImageUrl.value && tempImageUrl.value.startsWith('blob:')) {
        URL.revokeObjectURL(tempImageUrl.value);
    }
    tempImageUrl.value = URL.createObjectURL(file.file);
};

const handleFilePondRemoveFile = () => {
    if (tempImageUrl.value && tempImageUrl.value.startsWith('blob:')) {
        URL.revokeObjectURL(tempImageUrl.value);
    }
    tempImageUrl.value = null;
};

const submitForm = () => {
    form.post(route('admin.products.store'), {
        onSuccess: () => {
            form.reset();
            localStorage.removeItem('critical');
            toggleModalStatus();
            toast('Product created successfully', 'success');
        },
        onError: () => {
            toast('Failed to create product', 'error');
        },
        preserveScroll: true,
    });
};

const toggleModalStatus = () => {
    isModalOpen.value = !isModalOpen.value;
    if (!isModalOpen.value) {
        handleFilePondRemoveFile();
        if (localStorage.getItem('critical')) {
            handleFilePondRevert();
        }
    }
};

const tableView = ref(localStorage.getItem('adminProductViewDefault') === 'true');

const toggleView = () => {
    tableView.value = !tableView.value;
    localStorage.setItem('adminProductViewDefault', tableView.value);
};

const handleFilePondLoad = (response) => {
    const id = parseInt(response);
    if (isNaN(id)) {
        toast('Invalid image. Only upload jpg or png', 'error');
        return;
    }

    localStorage.setItem('critical', id);
    form.product_image_id = id;
};

const handleFilePondError = (response) => {
    const message = typeof response === 'string' ? response : 'Failed to upload image';
    $toast.error('Image upload failed. Please check the file type and size.');
    console.error('FilePond upload error:', message);
};

const handleFilePondRevert = () => {
    const storedImage = localStorage.getItem('critical');
    if (!storedImage) return;

    router.delete(route('admin.product-images.destroy', storedImage), {
        onSuccess: () => {
            localStorage.removeItem('critical');
            toast('Temporary image removed', 'success');
            handleFilePondRemoveFile();
        },
        onError: () => {
            toast('Failed to remove temporary image', 'error');
        },
        preserveScroll: true,
    });
};

const checkScreenSize = () => {
    isLgScreen.value = window.innerWidth >= 800;
};

onMounted(() => {
    tableView.value = localStorage.getItem('adminProductViewDefault') === 'true';
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
    { name: 'filterCategoryId', value: filterCategoryId },
    { name: 'filterProductStatus', value: filterProductStatus },
], 'admin.products.index');
</script>

<template>
    <AppLayout title="Products">
        <div class="py-12">
            <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-4 mb-8">
                <div class="flex items-center justify-between">
                    <h3 class="text-3xl text-gray-800 dark:text-gray-200 font-serif">
                        Products
                    </h3>
                    <div class="space-x-2">
                        <SecondaryButton @click="toggleView()">
                            {{ tableView ? 'Card' : 'Table' }}
                        </SecondaryButton>
                        <PrimaryButton @click="toggleModalStatus()">Add a product</PrimaryButton>
                    </div>
                </div>
                <div class="flex flex-col sm:flex-row sm:justify-between gap-2">
                    <div class="flex flex-col sm:flex-row gap-2 w-full">
                        <TextInput
                            v-model="filterProductName"
                            placeholder="Search product"
                            class="rounded-xl border border-gray-50 w-full sm:w-auto lg:w-64"
                        />
                        <SelectDropdown
                            v-model="filterCategoryId"
                            selected="Filter by category"
                            :options="categoryOptions"
                            class="rounded-xl border border-gray-50 w-full sm:w-auto lg:w-64 text-gray-500 dark:text-gray-500"
                        />
                        <SelectDropdown
                            v-model="filterProductStatus"
                            selected="Filter by status"
                            :options="[{label: 'Available', value: '1'}, {label: 'Unavailable', value: '0'}]"
                            class="rounded-xl border border-gray-50 w-full sm:w-auto lg:w-64 text-gray-500 dark:text-gray-500"
                        />
                    </div>
                </div>
                <div v-if="!tableView" class="">
                    <div v-if="isLgScreen" class="grid sm:grid-cols-2 md:grid-cols-3 lg:grid-cols-5 gap-4">
                        <ProductCard v-for="product in products" :key="product.id" :product="product" :redirect-show-product="redirectShowProduct" class="" />
                    </div>
                    <div v-else class="sm:grid flex-grow"
                        :class="{
                            'min-h-[50px]': products.length === 0,
                            'grid-cols-1 overflow-x-auto pb-8': products.length > 0
                        }"
                    >
                        <div class="flex gap-4">
                            <ProductCard v-for="product in products" :key="product.id" :product="product" :redirect-show-product="redirectShowProduct" class="shrink-0 w-1/2" />
                        </div>
                    </div>
                </div>
                <div v-else class="bg-white dark:bg-gray-800 overflow-hidden shadow-xl rounded-3xl mb-4">
                    <Table>
                        <TableHead :headers="['#', 'product', 'price', 'quantity', 'category', 'actions']" />
                        <TableBody>
                            <TableRow v-for="(product, index) in products"
                                :key="product.id"
                                :rowClass="index !== products.length - 1 ? 'border-b border-gray-200 dark:border-gray-700' : ''"
                            >
                                <TableData>{{ index + 1 }}</TableData>
                                <TableData class="flex items-center gap-2">
                                    <div class="flex aspect-square items-center justify-center overflow-hidden rounded-full bg-gray-200 dark:bg-gray-700 w-8">
                                    <ProductImage :path="product.image.path" :alt="product.name" />
                                </div>
                                    <Link :href="route('admin.products.show', product.id)" class="hover:underline">{{ product.name }}</Link>
                                </TableData>
                                <TableData>₱{{ product.price }}</TableData>
                                <TableData>{{ formatNumber(product.quantity) }}</TableData>
                                <TableData>{{ product.category.name }}</TableData>
                                <TableData>
                                    <ProductStatusBadge :isAvailable="product.is_available" />
                                </TableData>
                            </TableRow>
                        </TableBody>
                    </Table>
                </div>
                <WhenVisible
                    v-if="!tableView"
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
                <Paginator v-else :data="products_pagination" />
            </div>
        </div>

        <div v-if="isModalOpen" class="fixed inset-0 z-50 bg-black bg-opacity-50 flex items-center justify-center p-4 overflow-y-auto">
            <div class="w-full lg:max-w-5xl mx-auto relative flex flex-col">
                <form @submit.prevent="submitForm" class="bg-white dark:bg-gray-800 shadow-lg rounded-3xl p-6">
                    <div class="grid lg:grid-cols-3 gap-x-12 items-start">
                        <div class="flex flex-col items-center space-y-4">
                            <div class="bg-white dark:bg-gray-800 rounded-lg overflow-hidden h-96 w-full flex items-center justify-center">
                                <ProductImage :path="tempImageUrl" alt="Product image placeholder" class="w-full" />
                            </div>
                            <file-pond
                                class="w-full"
                                name="product-image"
                                ref="pond"
                                class-name="my-pond"
                                label-idle="Upload a new product image"
                                allow-multiple="false"
                                accepted-file-types="image/jpg, image/png"
                                max-file-size="2mb"
                                @addfile="handleFilePondAddFile"
                                @removefile="handleFilePondRemoveFile"
                                :server="{
                                    url: '',
                                    process: {
                                        url: route('admin.product-images.store'),
                                        method: 'POST',
                                        onload: handleFilePondLoad,
                                        onError: handleFilePondError,
                                    },
                                    revert: handleFilePondRevert,
                                    headers: {
                                        'X-CSRF-TOKEN': $page.props.csrf_token
                                    },
                                }"
                            />
                        </div>

                        <div class="lg:col-span-2 space-y-6">
                            <h1 class="text-4xl font-bold text-gray-800 dark:text-gray-200">
                                Add New Product
                            </h1>
                            <div class="space-y-5">
                                <InputGroup label="Name:" v-model="form.name" :error="form.errors.name" />
                                <InputGroup label="Category:" v-model="form.category_id" :error="form.errors.category_id" :dropdown="true" selected="Select a category" :options="categoryOptions" />
                                <div class="grid sm:grid-cols-3 gap-4">
                                    <InputGroup label="Status:" v-model="form.is_available" :error="form.errors.is_available" :dropdown="true" selected="Available" :options="[{ label: 'Available', value: 1}, { label: 'Unavailable', value: 0}]"/>
                                    <InputGroup label="Quantity:" v-model="form.quantity" type="number" :error="form.errors.quantity" />
                                    <InputGroup label="Price:" v-model="form.price" type="number" :error="form.errors.price" />
                                </div>
                                <InputGroup label="Description:" v-model="form.description" textarea />
                            </div>

                            <div class="flex justify-end space-x-4 mt-8">
                                <PrimaryButton type="submit" :disabled="form.processing">Create Product</PrimaryButton>
                                <SecondaryButton type="button" @click="toggleModalStatus()">Cancel</SecondaryButton>
                            </div>
                        </div>
                    </div>
                </form>
                <button @click="toggleModalStatus()" class="absolute top-4 right-4 text-gray-500 hover:text-gray-700 dark:hover:text-gray-300 text-2xl font-bold">&times;</button>
            </div>
        </div>
    </AppLayout>
</template>
