<script setup>
import { computed, onMounted } from 'vue';
import { useForm, router } from '@inertiajs/vue3';
import { toast } from '@/helpers';
import AppLayout from '@/Layouts/AppLayout.vue';
import InputGroup from '@/Components/InputGroup.vue';
import PrimaryButton from '@/Components/PrimaryButton.vue';
import DangerButton from '@/Components/DangerButton.vue';
import ProductImage from '@/Components/Product/ProductImage.vue';
import VueFilePond from 'vue-filepond';
import 'filepond/dist/filepond.min.css';
import 'filepond-plugin-image-preview/dist/filepond-plugin-image-preview.min.css';
import FilePondPluginFileValidateType from 'filepond-plugin-file-validate-type/dist/filepond-plugin-file-validate-type.esm.js';
import FilePondPluginFileValidateSize from 'filepond-plugin-file-validate-size/dist/filepond-plugin-file-validate-size.esm.js';
import FilePondPluginImageValidateSize from 'filepond-plugin-image-validate-size/dist/filepond-plugin-image-validate-size.esm.js';
import FilePondPluginImagePreview from 'filepond-plugin-image-preview/dist/filepond-plugin-image-preview.esm.js';
import ReviewList from '@/Components/Product/ReviewList.vue';

const props = defineProps({
    product: { type: Object, required: true },
    categories: { type: Array, required: true },
    reviews: { type: Array, required: true },
});

const categoryOptions = computed(() =>
    props.categories.map(category => ({
        label: category.name,
        value: category.id
    }))
);

const FilePond = VueFilePond(
    FilePondPluginFileValidateType,
    FilePondPluginImagePreview,
    FilePondPluginFileValidateSize,
    FilePondPluginImageValidateSize
);

const form = useForm({
    category_id: String(props.product.category.id),
    name: props.product.name,
    description: props.product.description,
    product_image_id: String(props.product.image.id),
    old_product_image_id: String(props.product.image.id),
    quantity: String(props.product.quantity),
    price: String(props.product.price),
    is_available: String(props.product.is_available),
});

const selectedStatus = computed(() => (form.is_available == '1' ? 'Available' : 'Unavailable'));
const selectedCategory = computed(() =>
    props.categories.find(category => category.id === props.product.category.id) || { name: "Unknown" }
);

onMounted(() => {
    if (localStorage.getItem('critical')) {
        handleFilePondRevert();
    }
});

const submitForm = () => {
    form.put(route('admin.products.update', { product: props.product.id }), {
        onSuccess: () => {
            form.old_product_image_id = form.product_image_id;
            localStorage.removeItem('critical');
            toast("Product updated successfully", "success");
        },
        onError: () => {
            toast("Failed to update product", "error");
        },
        preserveScroll: true,
    });
};

const deleteProduct = () => {
    if (!confirm("Are you sure you want to delete this product?")) return;

    form.delete(route('admin.products.destroy', {
        product: props.product.id,
    }), {
        onSuccess: () => {
            toast("Product deleted successfully", "success");
        },
        onError: () => {
            toast("Failed to delete product", "error");
        },
    });
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
            toast("Temporary image removed", "success");
        },
        onError: () => {
            toast("Failed to remove temporary image", "error");
        },
        preserveScroll: true,
    });
};
</script>

<template>
    <AppLayout :title="product.name">
        <div class="py-10">
            <div class="max-w-7xl mx-auto px-6 lg:px-8">
                <div class="space-y-4 mb-8">
                    <form @submit.prevent="submitForm" class="bg-white dark:bg-gray-800 shadow-lg rounded-3xl p-6">
                        <div class="grid lg:grid-cols-3 gap-x-12 items-start">
                            <div class="flex flex-col items-center space-y-4">
                                <div class="bg-white dark:bg-gray-800 rounded-lg overflow-hidden h-96 w-full flex items-center justify-center">
                                    <ProductImage :path="product.image.path" :alt="product.name" class="w-full" />
                                </div>
                                <file-pond
                                    class="w-full"
                                    name="product-image"
                                    ref="pond"
                                    class-name="my-pond"
                                    label-idle="Change product image"
                                    allow-multiple="false"
                                    accepted-file-types="image/jpg, image/png"
                                    max-file-size="2mb"
                                    image-validate-size-max-width="5000"
                                    image-validate-size-max-height="5000"
                                    image-validate-size-label-image-size-too-big="Image is too large"
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
                                    Edit Product
                                </h1>
                                <div class="space-y-5">
                                    <InputGroup label="Name:" v-model="form.name" :error="form.errors.name" />
                                    <InputGroup label="Category:" v-model="form.category_id" :error="form.errors.category_id" :dropdown="true" :selected="selectedCategory.name" :options="categoryOptions" />
                                    <div class="grid sm:grid-cols-3 gap-4">
                                        <InputGroup label="Status:" v-model="form.is_available" :error="form.errors.is_available" :dropdown="true" :selected="selectedStatus" :options="[{ label: 'Available', value: 1}, { label: 'Unavailable', value: 0}]"/>
                                        <InputGroup label="Quantity:" v-model="form.quantity" type="number" :error="form.errors.quantity" />
                                        <InputGroup label="Price:" v-model="form.price" type="number" :error="form.errors.price" />
                                    </div>
                                    <InputGroup label="Description:" v-model="form.description" textarea />
                                </div>

                                <div class="flex justify-end space-x-4 mt-8">
                                    <PrimaryButton type="submit" :disabled="form.processing">Save Changes</PrimaryButton>
                                    <DangerButton @click="deleteProduct" :disabled="form.processing">Delete Product</DangerButton>
                                </div>
                            </div>
                        </div>
                    </form>
                </div>

                <div class="bg-white dark:bg-gray-800 shadow-lg rounded-3xl p-6">
                    <ReviewList :reviews="reviews" />
                </div>
            </div>
        </div>
    </AppLayout>
</template>