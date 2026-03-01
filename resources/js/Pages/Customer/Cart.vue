<script setup>
import { ref, computed, onMounted, watch } from 'vue'; // Added watch
import { useForm, Link, router } from '@inertiajs/vue3';
import { debounce } from 'lodash';
import { toast } from '@/helpers';
// ... (Your FilePond imports stay the same)

// ... (FilePond registration stays the same)

const props = defineProps({
    cartItems: { type: Array, required: true }
});

const tempQuantities = ref({});
const selectedIds = ref([]);
const isOptionsOpen = ref(false);
const selectedItem = ref({});

const cartItemForm = useForm({ id: '', product_id: '', quantity: '' });
const checkOutForm = useForm({ payment_image_id: '', payment_method: 'upload_receipt', notes: '' });

// FIX 1: Proper initialization of quantities
// We use a watcher or onMounted instead of a computed side-effect
onMounted(() => {
    props.cartItems.forEach(item => {
        tempQuantities.value[item.id] = item.quantity;
    });
    handleFilePondRevert(); // Clean up stray uploads
});

// FIX 2: Better formatting for currency
const formatCurrency = (value) => {
    return new Intl.NumberFormat('en-PH', {
        style: 'currency',
        currency: 'PHP',
    }).format(value);
};

const totalPrice = computed(() => {
    return props.cartItems.reduce((sum, item) => {
        const qty = tempQuantities.value[item.id] || 0;
        return sum + (item.product.price * qty);
    }, 0);
});

// FIX 3: Validation logic for the Checkout button
const canCheckout = computed(() => {
    if (props.cartItems.length === 0) return false;
    if (checkOutForm.payment_method === 'upload_receipt' && !checkOutForm.payment_image_id) {
        return false;
    }
    return true;
});

// ... (handleFilePondLoad and handleFilePondError stay the same)

const handleFilePondRevert = () => {
    const storedImage = localStorage.getItem('critical');
    if (!storedImage) return;

    router.delete(route('customer.payment-images.destroy', storedImage), {
        onSuccess: () => {
            localStorage.removeItem('critical');
            checkOutForm.payment_image_id = '';
        },
        preserveScroll: true,
    });
};

const updateCart = debounce((item) => {
    cartItemForm.id = item.id;
    cartItemForm.product_id = item.product.id;
    cartItemForm.quantity = tempQuantities.value[item.id];

    cartItemForm.put(route('customer.cart-items.update', cartItemForm.id), {
        onSuccess: () => toast('Cart updated', 'success'),
        preserveScroll: true,
    });
}, 1000);

const validateQuantity = (item) => {
    let val = parseInt(tempQuantities.value[item.id]);
    if (isNaN(val) || val < 1) val = 1;
    if (val > item.product.quantity) val = item.product.quantity;
    
    tempQuantities.value[item.id] = val;
    updateCart(item);
};

const checkOut = () => {
    if (!canCheckout.value) {
        toast('Please upload your payment receipt first', 'error');
        return;
    }
    checkOutForm.post(route('customer.orders.store'), {
        onSuccess: () => {
            toast('Order submitted', 'success');
            localStorage.removeItem('critical');
        },
        preserveScroll: true,
    });
};

// ... (rest of your functions)
</script>

<template>
    <AppLayout title="Shopping Cart">
        <div class="grid grid-cols-1 gap-4 lg:grid-cols-[2fr_1fr]">
            <div class="h-[32rem] overflow-auto space-y-4 rounded-3xl pb-6 lg:h-lvh">
                <div v-for="item in cartItems" :key="item.id" class="...">
                    <div @click="setSelectedItem(item)" class="flex flex-col space-y-2">
                        <span class="text-sm font-bold text-gray-900 dark:text-gray-100 lg:text-lg">
                            {{ item.product.name }}
                        </span>
                        <p class="text-lg font-bold text-violet-900 dark:text-violet-300 lg:text-3xl">
                            {{ formatCurrency(item.product.price) }}
                        </p>
                        <p class="text-xs text-gray-600 dark:text-gray-400 lg:text-sm">
                            Total: {{ formatCurrency(item.product.price * tempQuantities[item.id]) }}
                        </p>
                    </div>

                    <div class="flex flex-col items-center sm:flex-row">
                        <SecondaryCircularButton @click.stop="decreaseQuantity(item)">-</SecondaryCircularButton>
                        <input
                            type="number"
                            v-model.number="tempQuantities[item.id]" 
                            @change="validateQuantity(item)"
                            class="..."
                        />
                        <PrimaryCircularButton @click.stop="increaseQuantity(item)">+</PrimaryCircularButton>
                    </div>
                </div>
            </div>

            <div class="space-y-4">
                <div class="h-fit rounded-3xl bg-white p-6 shadow-md dark:bg-gray-800">
                    <h3 class="text-3xl font-black text-gray-900 dark:text-gray-100">Order Summary</h3>
                    <div class="mt-3 space-y-3">
                        <div class="flex justify-between font-semibold text-gray-600 dark:text-gray-300">
                            <span>Subtotal:</span>
                            <span>{{ formatCurrency(totalPrice) }}</span>
                        </div>
                        <div class="flex justify-between font-semibold text-gray-600 dark:text-gray-300">
                            <span>Tax (12%):</span>
                            <span>{{ formatCurrency(totalPrice * 0.12) }}</span>
                        </div>
                        <div class="flex justify-between text-2xl font-extrabold text-gray-900 dark:text-gray-100">
                            <span>Total:</span>
                            <span>{{ formatCurrency(totalPrice * 1.12) }}</span>
                        </div>
                    </div>
                </div>

                <div class="bg-white dark:bg-gray-800 p-6 rounded-3xl shadow-md">
                    <PrimaryButton 
                        :disabled="!canCheckout" 
                        @click="checkOut" 
                        class="w-full py-4 text-2xl flex justify-center mt-4 transition-all"
                        :class="{'opacity-50 grayscale': !canCheckout}"
                    >
                        Check out
                    </PrimaryButton>
                </div>
            </div>
        </div>
    </AppLayout>
</template>
