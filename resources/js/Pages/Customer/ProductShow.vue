<script setup>
import { useForm, usePage } from '@inertiajs/vue3';
import { debounce } from 'lodash';
import { computed, ref, nextTick, watch } from 'vue';
import AppLayout from '@/Layouts/AppLayout.vue';
import SingleProductCard from '@/Components/Product/SingleProductCard.vue';
import ProductImage from '@/Components/Product/ProductImage.vue';
import PrimaryButton from '@/Components/PrimaryButton.vue';
import DangerButton from '@/Components/DangerButton.vue';
import PrimaryCircularButton from '@/Components/PrimaryCircularButton.vue';
import SecondaryCircularButton from '@/Components/SecondaryCircularButton.vue';
import ReviewList from '@/Components/Product/ReviewList.vue';
import ReviewForm from '@/Components/Product/ReviewForm.vue';
import { toast } from '@/helpers';

const props = defineProps({
    product: { type: Object, required: true },
    cartItem: { type: Object, default: null },
    reviews: { type: Array, required: true },
});

const user = usePage().props.auth.user;

const userReview = computed(() => {
    if (!user) return null;
    return props.reviews.find(review => review.user_id === user.id);
});

// Controls the visibility of the review form.
// The form is initially hidden if the user has already left a review.
const showReviewForm = ref(!userReview.value);

const reviewFormRef = ref(null);

const handleEditReview = () => {
    showReviewForm.value = true;
    // Use nextTick to ensure the form is rendered before scrolling.
    nextTick(() => {
        reviewFormRef.value.$el.scrollIntoView({ behavior: 'smooth' });
    });
};

const handleReviewSubmitted = () => {
    showReviewForm.value = false;
};

watch(userReview, (newValue, oldValue) => {
    // When a review is deleted, the new value is null.
    // Show the form so the user can add a new one.
    if (oldValue && !newValue) {
        showReviewForm.value = true;
    }
});



const form = useForm({
    product_id: props.product.id,
    quantity: props.cartItem ? props.cartItem.quantity : 1,
});

const updateCart = debounce(() => {
    if (props.cartItem) {
        form.put(route('customer.cart-items.update', props.cartItem.id), {
            onSuccess: () => { toast('Cart item quantity updated', 'success'); },
            onError: (e) => { toast('Failed to update cart item', 'error'); },
            preserveScroll: true,
        });
    }
}, 500);

const increaseQuantity = () => {
    if (form.quantity >= props.product.quantity) {
        alert('You have reached the maximum available stock for this product.');
        return;
    }
    form.quantity++;
    if (props.cartItem) updateCart();
};

const decreaseQuantity = () => {
    if (form.quantity > 1) {
        form.quantity--;
        if (props.cartItem) updateCart();
    }
};

const validateQuantity = () => {
    if (form.quantity < 1) {
        form.quantity = 1;
    } else if (form.quantity > props.product.quantity) {
        form.quantity = props.product.quantity;
        alert('You have reached the maximum available stock for this product.');
    }
    if (props.cartItem) updateCart();
};

const addToCart = () => {
    form.post(route('customer.cart-items.store'), {
        onSuccess: () => { toast('Added to cart!', 'success'); },
        onError: (e) => { toast('Failed to add to cart', 'error'); },
        preserveScroll: true,
    });
};

const removeFromCart = () => {
    form.delete(route('customer.cart-items.destroy', props.cartItem.id), {
        onSuccess: () => {
            form.reset();
            form.quantity = 1;
            toast('Removed from cart!', 'success');
        },
        onError: (e) => { toast('Failed to remove from cart', 'error'); },
        preserveScroll: true,
    });
};
</script>

<template>
    <AppLayout :title="product.name">
        <div class="py-12">
            <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
                <div class="grid lg:grid-cols-2 grid-cols-1 gap-4 bg-white dark:bg-gray-800 overflow-hidden shadow-xl rounded-3xl p-6">
                    <div class="">
                        <div class="flex flex-col">
                            <div class="self-center w-1/2">
                                <ProductImage :path="product.image.path" :alt="product.name" class="w-full" />
                            </div>

                            <SingleProductCard :product="product" />
                        </div>
                    </div>
                    <div class="lg:block hidden">
                        <div class="bg-gray-100 dark:bg-gray-900 p-6 rounded-3xl">
                            <h3 class="text-3xl font-bold text-gray-900 dark:text-gray-100">
                                Order Summary
                            </h3>

                            <ul class="text-gray-700 dark:text-gray-400 text-sm space-y-2 mt-4">
                                <li>📦 <strong>Estimated Delivery:</strong> {{ product.estimated_delivery || '3-5 business days' }}</li>
                                <li>🔒 <strong>Secure Checkout:</strong> All transactions are encrypted and safe.</li>
                                <li>✅ <strong>100% Satisfaction Guarantee:</strong> If you're not happy with your order, we offer hassle-free returns.</li>
                            </ul>

                            <div class="flex justify-between items-center mt-8">
                                <span class="text-lg sm:text-xl font-medium sm:font-semibold text-gray-700 dark:text-gray-300">
                                    Available Stock:
                                </span>
                                <span class="text-lg sm:text-xl font-bold text-gray-900 dark:text-gray-100">
                                    {{ product.quantity }} pc/s
                                </span>
                            </div>

                            <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4 mt-4">
                                <span class="text-2xl font-semibold text-gray-700 dark:text-gray-300">
                                    Quantity:
                                </span>

                                <div class="flex items-center justify-between w-full sm:w-auto space-x-4 sm:space-x-6">
                                    <SecondaryCircularButton class="sm:me-3" @click="decreaseQuantity">-</SecondaryCircularButton>

                                    <input
                                        type="number"
                                        v-model="form.quantity"
                                        @input="validateQuantity"
                                        class="text-gray-800 dark:text-gray-200 text-2xl font-bold w-20 text-center border border-gray-100 dark:border-gray-900 rounded-lg bg-gray-100 dark:bg-gray-900"
                                    />

                                    <PrimaryCircularButton @click="increaseQuantity">+</PrimaryCircularButton>
                                </div>
                            </div>

                            <div class="space-y-6 mt-8 mb-4">
                                <div class="bg-white dark:bg-gray-800 border border-gray-200 dark:border-gray-600 p-4 rounded-xl flex flex-col space-y-3">
                                    <div class="flex justify-between font-semibold text-gray-600 dark:text-gray-400">
                                        <span>Subtotal:</span>
                                        <span>P{{ (product.price * form.quantity).toFixed(2) }}</span>
                                    </div>
                                    <div class="flex justify-between font-semibold text-gray-600 dark:text-gray-400">
                                        <span>Tax (12%):</span>
                                        <span>P{{ ((product.price * form.quantity) * 0.12).toFixed(2) }}</span>
                                    </div>
                                    <div class="flex justify-between text-xl font-extrabold text-gray-900 dark:text-gray-100 lg:text-xl">
                                        <span>Total:</span>
                                        <span>P{{ ((product.price * form.quantity) * 1.12).toFixed(2) }}</span>
                                    </div>
                                </div>
                            </div>
                            <PrimaryButton v-if="cartItem?.product_id !== product.id" @click="addToCart" class="w-full py-5 text-2xl flex justify-center">
                                Add to Cart
                            </PrimaryButton>
                            <DangerButton v-else @click="removeFromCart" class="w-full py-5 text-2xl flex justify-center">
                                Remove from cart
                            </DangerButton>
                        </div>
                    </div>
                </div>
                <div class="lg:hidden block mt-4">
                    <div class="bg-white dark:bg-gray-800 p-6 rounded-3xl">
                        <h3 class="text-3xl font-bold text-gray-900 dark:text-gray-100">
                            Order Summary
                        </h3>

                        <ul class="text-gray-700 dark:text-gray-400 text-sm space-y-2 mt-4">
                            <li>📦 <strong>Estimated Delivery:</strong> {{ product.estimated_delivery || '3-5 business days' }}</li>
                            <li>🔒 <strong>Secure Checkout:</strong> All transactions are encrypted and safe.</li>
                            <li>✅ <strong>100% Satisfaction Guarantee:</strong> If you're not happy with your order, we offer hassle-free returns.</li>
                        </ul>

                        <div class="flex justify-between items-center mt-8">
                            <span class="text-lg sm:text-xl font-medium sm:font-semibold text-gray-700 dark:text-gray-300">
                                Available Stock:
                            </span>
                            <span class="text-lg sm:text-xl font-bold text-gray-900 dark:text-gray-100">
                                {{ product.quantity }} pc/s
                            </span>
                        </div>

                        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4 mt-4">
                            <span class="text-2xl font-semibold text-gray-700 dark:text-gray-300">
                                Quantity:
                            </span>

                            <div class="flex items-center justify-between w-full sm:w-auto space-x-4 sm:space-x-6">
                                <SecondaryCircularButton class="sm:me-3" @click="decreaseQuantity">-</SecondaryCircularButton>

                                <input
                                    type="number"
                                    v-model="form.quantity"
                                    @input="validateQuantity"
                                    class="text-gray-800 dark:text-gray-200 text-2xl font-bold w-20 text-center border border-white dark:border-gray-800 rounded-lg bg-white dark:bg-gray-800"
                                />

                                <PrimaryCircularButton @click="increaseQuantity">+</PrimaryCircularButton>
                            </div>
                        </div>

                        <div class="space-y-6 mt-8 mb-4">
                            <div class="bg-gray-100 dark:bg-gray-900 border border-gray-200 dark:border-gray-600 p-4 rounded-xl flex flex-col space-y-3">
                                <div class="flex justify-between font-semibold text-gray-600 dark:text-gray-400">
                                    <span>Subtotal:</span>
                                    <span>P{{ (product.price * form.quantity).toFixed(2) }}</span>
                                </div>
                                <div class="flex justify-between font-semibold text-gray-600 dark:text-gray-400">
                                    <span>Tax (12%):</span>
                                    <span>P{{ ((product.price * form.quantity) * 0.12).toFixed(2) }}</span>
                                </div>
                                <div class="flex justify-between text-xl font-extrabold text-gray-900 dark:text-gray-100 lg:text-xl">
                                    <span>Total:</span>
                                    <span>P{{ ((product.price * form.quantity) * 1.12).toFixed(2) }}</span>
                                </div>
                            </div>
                        </div>
                        <PrimaryButton v-if="cartItem?.product_id !== product.id" @click="addToCart" class="w-full py-5 text-2xl flex justify-center">
                            Add to Cart
                        </PrimaryButton>
                        <DangerButton v-else @click="removeFromCart" class="w-full py-5 text-2xl flex justify-center">
                            Remove from cart
                        </DangerButton>
                    </div>
                </div>

                <div class="mt-8 bg-white dark:bg-gray-800 overflow-hidden shadow-xl rounded-3xl p-6">
                    <ReviewForm
                        ref="reviewFormRef"
                        v-if="user && user.role === 'customer' && showReviewForm"
                        :product="product"
                        :user-review="userReview"
                        @review-submitted="handleReviewSubmitted"
                    />
                    <ReviewList :reviews="reviews" @edit-review="handleEditReview" />
                </div>
            </div>
        </div>
    </AppLayout>
</template>
