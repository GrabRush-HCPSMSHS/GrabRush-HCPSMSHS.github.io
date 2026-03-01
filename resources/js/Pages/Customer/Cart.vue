<script setup>
import { ref, computed, onMounted } from 'vue';
import { useForm, Link, router } from '@inertiajs/vue3';
import { debounce } from 'lodash';
import { toast } from '@/helpers';
import VueFilePond from 'vue-filepond';
import 'filepond/dist/filepond.min.css';
import 'filepond-plugin-image-preview/dist/filepond-plugin-image-preview.min.css';
import FilePondPluginFileValidateType from 'filepond-plugin-file-validate-type';
import FilePondPluginFileValidateSize from 'filepond-plugin-file-validate-size';
import FilePondPluginImageValidateSize from 'filepond-plugin-image-validate-size';
import FilePondPluginImagePreview from 'filepond-plugin-image-preview';
import AppLayout from '@/Layouts/AppLayout.vue';
import PrimaryButton from '@/Components/PrimaryButton.vue';
import SecondaryButton from '@/Components/SecondaryButton.vue';
import DangerButton from '@/Components/DangerButton.vue';
import PrimaryCircularButton from '@/Components/PrimaryCircularButton.vue';
import SecondaryCircularButton from '@/Components/SecondaryCircularButton.vue';
import ProductImage from '@/Components/Product/ProductImage.vue';

const FilePond = VueFilePond(
    FilePondPluginFileValidateType,
    FilePondPluginImagePreview,
    FilePondPluginFileValidateSize,
    FilePondPluginImageValidateSize
);

const props = defineProps({
    cartItems: {
        type: Array,
        required: true,
    }
});

const tempQuantities = ref({});
const initialized = ref(false);
const isOptionsOpen = ref(false);
const selectedItem = ref({});

const cartItemForm = useForm({ id: '', product_id: '', quantity: '' });
const checkOutForm = useForm({ payment_image_id: '', payment_method: 'upload_receipt', notes: '' });

const selectedIds = ref([]);

const deleteSelectedItems = () => {
    if (!selectedIds.value.length) {
        toast('No items selected', 'error')
        return
    }

    useForm({ ids: selectedIds.value }).delete(route('customer.cart-items.bulk-destroy'), {
        onSuccess: () => {
            toast('Selected items removed', 'success')
            selectedIds.value = []
        },
        onError: () => toast('Failed to remove selected items', 'error'),
        preserveScroll: true
    })
}

const computedQuantities = computed(() => {
    if (!initialized.value) {
        props.cartItems.forEach(item => {
            tempQuantities.value[item.id] = item.quantity;
        });
        initialized.value = true;
    }
    return tempQuantities.value;
});

const totalPrice = computed(() => {
    return props.cartItems.reduce((sum, item) => sum + (item.product.price * (tempQuantities.value[item.id] || 0)), 0);
});

const handleFilePondLoad = (response) => {
    const id = parseInt(response);
    if (isNaN(id)) {
        toast('Invalid image. Only upload jpg or png', 'error');
        return;
    }

    localStorage.setItem('critical', id);
    checkOutForm.payment_image_id = id;
};

const handleFilePondError = (response) => {
    const message = typeof response === 'string' ? response : 'Failed to upload image';
    $toast.error('Image upload failed. Please check the file type and size.');
    console.error('FilePond upload error:', message);
};

const handleFilePondRevert = () => {
    const storedImage = localStorage.getItem('critical');
    if (!storedImage) return;

    router.delete(route('customer.payment-images.destroy', storedImage), {
        onSuccess: () => localStorage.removeItem('critical'),
        onError: (e) => console.error(e),
        preserveScroll: true,
    });
};

onMounted(() => {
    handleFilePondRevert();
});

const updateCart = debounce((item) => {
    cartItemForm.id = item.id;
    cartItemForm.product_id = item.product.id;
    cartItemForm.quantity = tempQuantities.value[item.id];

    cartItemForm.put(route('customer.cart-items.update', cartItemForm.id), {
        onSuccess: () => toast('Cart updated successfully', 'success'),
        onError: (e) => toast('Failed to update cart', 'error'),
        preserveScroll: true,
    });
}, 1000);

const increaseQuantity = (item) => {
    if (tempQuantities.value[item.id] >= item.product.quantity) {
        alert('Maximum stock reached.');
        return;
    }
    tempQuantities.value[item.id]++;
    updateCart(item);
};

const decreaseQuantity = (item) => {
    if (tempQuantities.value[item.id] > 1) {
        tempQuantities.value[item.id]--;
        updateCart(item);
    }
};

const validateQuantity = (item) => {
    let newQuantity = tempQuantities.value[item.id];
    newQuantity = Math.max(1, Math.min(newQuantity, item.product.quantity));
    if (newQuantity !== tempQuantities.value[item.id]) {
        toast('Quantity adjusted to available stock.', 'error');
    }
    tempQuantities.value[item.id] = newQuantity;
    updateCart(item);
};

const checkOut = () => {
    checkOutForm.post(route('customer.orders.store'), {
        onSuccess: () => {
            toast('Order submitted', 'success');
            localStorage.removeItem('critical');
        },
        onError: (e) => {
            let errorMessage = 'Some of the products are unavailable';
            if (e && e.errors) {
                errorMessage = Object.values(e.errors).flat().join('\n');
            }
            toast(errorMessage, 'error');
        },
        preserveScroll: true,
    });
};

const removeFromCart = () => {
    if (!selectedItem.value) {
        toast("No item selected", 'error');
        return;
    }
    useForm().delete(route('customer.cart-items.destroy', selectedItem.value.id), {
        onSuccess: () => {
            toast("Removed from cart", 'success');
            isOptionsOpen.value = false;
            selectedItem.value = {};
        },
        onError: (e) => toast("Action failed.", 'error'),
        preserveScroll: true,
    });
};

const setSelectedItem = (item) => {
    selectedItem.value = item;
    isOptionsOpen.value = true;
};
</script>

<template>
    <AppLayout title="Shopping Cart">
        <div class="py-12">
            <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-4">
                <div class="grid lg:grid-cols-3">
                    <div class="flex items-center justify-between col-span-2">
                        <h3 class="text-3xl text-gray-800 dark:text-gray-200 font-serif">
                            Shopping Cart
                        </h3>
                        <button
                            :disabled="selectedIds.length === 0"
                            @click="deleteSelectedItems"
                            class="me-2 text-red-600 hover:underline cursor-pointer disabled:text-gray-400 disabled:cursor-not-allowed"
                        >
                            Remove
                        </button>
                    </div>
                </div>

                <div class="grid grid-cols-1 gap-4 lg:grid-cols-[2fr_1fr]">
                    <div class="h-[32rem] overflow-auto space-y-4 rounded-3xl pb-6 lg:h-lvh">
                        <div
                            v-for="item in cartItems"
                            :key="item.id"
                            class="grid grid-cols-[auto_1fr_auto] items-center gap-4 rounded-3xl bg-white p-4 shadow-md dark:bg-gray-800 cursor-pointer"
                        >
                            <div class="flex items-center">
                                <input
                                    type="checkbox"
                                    class="me-3 rounded-md border-gray-300 text-violet-600 focus:ring-violet-500"
                                    v-model="selectedIds"
                                    :value="item.id"
                                />
                                <div class="flex aspect-square w-20 items-center justify-center overflow-hidden rounded-full lg:w-32">
                                    <ProductImage :path="item.product.image.path" :alt="item.product.name" />
                                </div>
                            </div>

                            <div @click="setSelectedItem(item)" class="flex flex-col space-y-2">
                                <span class="text-sm font-bold text-gray-900 dark:text-gray-100 lg:text-lg">
                                    {{ item.product.name }}
                                </span>
                                <div class="flex flex-col">
                                    <p class="text-lg font-bold text-violet-900 dark:text-violet-300 lg:text-3xl">
                                        ₱{{ item.product.price }}.00
                                        <span class="text-xs text-gray-600 dark:text-gray-400">(each)</span>
                                    </p>
                                    <p class="text-xs text-gray-600 dark:text-gray-400 lg:text-sm">
                                        Total: ₱{{ item.product.price * tempQuantities[item.id] }}.00
                                    </p>
                                </div>
                            </div>

                            <div class="flex flex-col items-center sm:flex-row">
                                <SecondaryCircularButton @click="decreaseQuantity(item)" class="sm:me-4 md:me-2 lg:me-2">
                                    -
                                </SecondaryCircularButton>
                                <div class="mt-1"></div>
                                <input
                                    type="number"
                                    v-model="computedQuantities[item.id]"
                                    @input="validateQuantity(item)"
                                    class="mx-0 w-10 border border-white bg-white text-center text-xs font-bold text-gray-900 dark:border-gray-800 dark:bg-gray-800 dark:text-gray-100 lg:w-24 lg:text-lg"
                                />
                                <PrimaryCircularButton @click="increaseQuantity(item)">
                                    +
                                </PrimaryCircularButton>
                            </div>
                        </div>
                    </div>

                    <div class="space-y-4">
                        <div class="h-fit flex-col justify-between rounded-3xl bg-white p-6 shadow-md dark:bg-gray-800">
                            <h3 class="text-3xl font-black text-gray-900 dark:text-gray-100">Order Summary</h3>
                            <div class="mt-3 flex flex-col space-y-3">
                                <div class="flex justify-between font-semibold text-gray-600 dark:text-gray-300">
                                    <span>Subtotal:</span>
                                    <span>₱{{ totalPrice }}.00</span>
                                </div>
                                <div class="flex justify-between font-semibold text-gray-600 dark:text-gray-300">
                                    <span>Tax (12%):</span>
                                    <span>₱{{ (totalPrice * 0.12).toFixed(2) }}</span>
                                </div>
                                <div class="flex justify-between text-2xl font-extrabold text-gray-900 dark:text-gray-100 lg:text-2xl">
                                    <span>Total:</span>
                                    <span>₱{{ (totalPrice * 1.12).toFixed(2) }}</span>
                                </div>
                            </div>
                        </div>

                        <div class="bg-white dark:bg-gray-800 p-6 rounded-3xl shadow-md">
                            <h3 class="text-3xl font-black text-gray-900 dark:text-gray-100">Payment Details</h3>

                            <div class="mt-4 space-y-4">
                                <div>
                                    <label class="text-lg font-semibold text-gray-900 dark:text-gray-200">Payment Method</label>
                                    <div class="mt-2 flex space-x-4">
                                        <div class="flex items-center">
                                            <input type="radio" id="upload_receipt" value="upload_receipt" v-model="checkOutForm.payment_method" class="text-violet-600 focus:ring-violet-500">
                                            <label for="upload_receipt" class="ml-2 text-sm font-medium text-gray-800 dark:text-gray-300">Upload Payment Receipt</label>
                                        </div>
                                        <div class="flex items-center">
                                            <input type="radio" id="over_the_counter" value="over_the_counter" v-model="checkOutForm.payment_method" class="text-violet-600 focus:ring-violet-500">
                                            <label for="over_the_counter" class="ml-2 text-sm font-medium text-gray-800 dark:text-gray-300">Over The Counter</label>
                                        </div>
                                    </div>
                                </div>

                                <div v-if="checkOutForm.payment_method === 'upload_receipt'">
                                    <p class="mt-2 text-lg font-semibold text-gray-900 dark:text-gray-200">Send payment to GCash:</p>
                                    <p class="text-2xl font-bold text-blue-700 dark:text-blue-400">+63 912 345 6789</p>
                                    <div class="mt-4 flex justify-center">
                                        <img src="<blockquote class="imgur-embed-pub" lang="en" data-id="tOYVQQ7"><a href="https://imgur.com/tOYVQQ7">View post on imgur.com</a></blockquote><script async src="//s.imgur.com/min/embed.js" charset="utf-8"></script>" alt="GCash QR Code" class="w-40 h-40" />
                                    </div>
                                    <p class="mt-2 text-sm font-medium text-gray-800 dark:text-gray-300">
                                        Once you've completed the payment, please save your receipt and upload it below
                                    </p>
                                    <file-pond
                                        class="dark:bg-gray-700 mt-5"
                                        name="payment-image"
                                        ref="pond"
                                        class-name="my-pond"
                                        label-idle="Upload image here"
                                        allow-multiple="false"
                                        accepted-file-types="image/jpeg, image/png"
                                        max-file-size="2mb"
                                        image-validate-size-max-width="5000"
                                        image-validate-size-max-height="5000"
                                        image-validate-size-label-image-size-too-big="Don't even think about it"
                                        :server="{
                                            url: '',
                                            process: {
                                                url: route('customer.payment-images.store'),
                                                method: 'POST',
                                                onload: handleFilePondLoad,
                                                onError: handleFilePondError
                                            },
                                            revert: handleFilePondRevert,
                                            headers: {
                                                'X-CSRF-TOKEN': $page.props.csrf_token
                                            },
                                        }"
                                    />
                                </div>

                                <div>
                                    <label for="notes" class="text-lg font-semibold text-gray-900 dark:text-gray-200">Notes (Optional)</label>
                                    <textarea id="notes" v-model="checkOutForm.notes" rows="3" class="mt-2 block w-full rounded-md border-gray-300 shadow-sm focus:border-violet-500 focus:ring-violet-500 dark:bg-gray-700 dark:border-gray-600 dark:text-gray-200"></textarea>
                                </div>
                            </div>
                            <PrimaryButton v-if="cartItems" :disabled="cartItems.length === 0" @click="checkOut" class="w-full py-4 text-4xl flex justify-center mt-4">
                                Check out
                            </PrimaryButton>
                        </div>
                    </div>

                </div>
            </div>
        </div>

        <div v-if="isOptionsOpen" class="fixed inset-0 flex items-center justify-center bg-black bg-opacity-50 z-50">
            <div class="bg-white dark:bg-gray-800 p-6 rounded-lg shadow-xl w-96">
                <button @click="isOptionsOpen = false"
                    class="text-end text-gray-800 dark:text-gray-200 hover:text-gray-800 dark:hover:text-gray-300 transition">
                    ✕
                </button>

                <h3 class="text-2xl font-bold text-gray-900 dark:text-gray-200 mb-5 text-center">
                    Select Action
                </h3>

                <div class="flex flex-col space-y-4">
                    <Link :href="route('customer.products.show', selectedItem.product.id)" class="w-full">
                        <SecondaryButton class="w-full py-3 text-lg justify-center">
                            View Product
                        </SecondaryButton>
                    </Link>

                    <DangerButton
                        @click="removeFromCart(selectedItem)"
                        class="w-full py-3 text-lg">
                        Remove from Cart
                    </DangerButton>
                </div>
            </div>
        </div>
    </AppLayout>
</template>

<style scoped>
.title-clamp {
    display: -webkit-box;
    -webkit-line-clamp: 2;
    -webkit-box-orient: vertical;
    overflow: hidden;
}
</style>