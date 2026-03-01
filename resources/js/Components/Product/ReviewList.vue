<script setup>
import { usePage, router } from '@inertiajs/vue3';
import SecondaryButton from '@/Components/SecondaryButton.vue';
import DangerButton from '@/Components/DangerButton.vue';

const props = defineProps({
    reviews: {
        type: Array,
        required: true,
    },
});

const emit = defineEmits(['edit-review']);

const user = usePage().props.auth.user;

const deleteReview = (reviewId) => {
    if (confirm('Are you sure you want to delete this review?')) {
        router.delete(route('customer.reviews.destroy', reviewId), {
            preserveScroll: true,
        });
    }
};

</script>

<template>
    <div>
        <h3 class="text-2xl font-bold text-gray-900 dark:text-gray-100">
            Customer Reviews
        </h3>
        <div v-if="reviews.length > 0" class="mt-4 space-y-6">
            <div v-for="review in reviews" :key="review.id" class="p-4 bg-gray-100 dark:bg-gray-900 rounded-lg">
                <div class="flex items-center mb-2">
                    <p class="font-semibold text-gray-800 dark:text-gray-200">{{ review.user.name }}</p>
                    <div class="ml-4 flex items-center">
                        <span v-for="i in 5" :key="i">
                            <svg v-if="i <= review.rating" class="w-5 h-5 fill-current text-violet-600 dark:text-violet-700" viewBox="0 0 20 20"><path d="M10 15l-5.878 3.09 1.123-6.545L.489 6.91l6.572-.955L10 0l2.939 5.955 6.572.955-4.756 4.635 1.123 6.545z"/></svg>
                            <svg v-else class="w-5 h-5 fill-current text-gray-300 dark:text-gray-600" viewBox="0 0 20 20"><path d="M10 15l-5.878 3.09 1.123-6.545L.489 6.91l6.572-.955L10 0l2.939 5.955 6.572.955-4.756 4.635 1.123 6.545z"/></svg>
                        </span>
                    </div>
                </div>
                <p class="text-gray-600 dark:text-gray-400">{{ review.comment }}</p>
                <div v-if="user && user.id === review.user_id" class="mt-3 flex space-x-2">
                    <SecondaryButton @click="emit('edit-review')">Edit</SecondaryButton>
                    <DangerButton @click="deleteReview(review.id)">Delete</DangerButton>
                </div>
            </div>
        </div>
        <div v-else class="mt-4">
            <p class="text-gray-600 dark:text-gray-400">No reviews yet. Be the first to review this product!</p>
        </div>
    </div>
</template>
