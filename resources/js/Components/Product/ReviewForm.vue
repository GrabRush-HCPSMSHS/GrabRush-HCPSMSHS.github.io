<script setup>
import { useForm } from '@inertiajs/vue3';
import { ref } from 'vue';
import PrimaryButton from '@/Components/PrimaryButton.vue';
import TextAreaInput from '@/Components/TextAreaInput.vue';
import InputLabel from '@/Components/InputLabel.vue';
import InputError from '@/Components/InputError.vue';

const props = defineProps({
    product: { type: Object, required: true },
    userReview: { type: Object, default: null },
});

const emit = defineEmits(['review-submitted']);

const form = useForm({
    rating: props.userReview?.rating || 0,
    comment: props.userReview?.comment || '',
});

const ratingHover = ref(0);

const submit = () => {
    const options = {
        preserveScroll: true,
        onSuccess: () => emit('review-submitted'),
    };

    if (props.userReview) {
        form.put(route('customer.reviews.update', props.userReview.id), options);
    } else {
        form.post(route('customer.reviews.store', props.product.id), options);
    }
};
</script>

<template>
    <div class="mb-8">
        <h3 class="text-2xl font-bold text-gray-900 dark:text-gray-100">
            {{ userReview ? 'Edit Your Review' : 'Write a Review' }}
        </h3>
        <form @submit.prevent="submit" class="mt-4 space-y-4">
            <div>
                <InputLabel for="rating" value="Rating" />
                <div class="flex items-center mt-1">
                    <span v-for="i in 5" :key="i" class="cursor-pointer" @click="form.rating = i" @mouseover="ratingHover = i" @mouseleave="ratingHover = 0">
                        <svg class="w-6 h-6 fill-current" :class="{
                            'text-pink-400 dark:text-pink-700': ratingHover > 0 && i <= ratingHover,
                            'text-violet-600 dark:text-violet-700': ratingHover === 0 && i <= form.rating,
                            'text-gray-300 dark:text-gray-600': i > (ratingHover || form.rating)
                        }" viewBox="0 0 20 20"><path d="M10 15l-5.878 3.09 1.123-6.545L.489 6.91l6.572-.955L10 0l2.939 5.955 6.572.955-4.756 4.635 1.123 6.545z"/></svg>
                    </span>
                </div>
                <InputError :message="form.errors.rating" class="mt-2" />
            </div>
            <div>
                <InputLabel for="comment" value="Comment" />
                <TextAreaInput id="comment" v-model="form.comment" class="mt-1 block w-full" rows="4" />
                <InputError :message="form.errors.comment" class="mt-2" />
            </div>
            <div>
                <PrimaryButton :disabled="form.processing">
                    {{ userReview ? 'Update Review' : 'Submit Review' }}
                </PrimaryButton>
            </div>
        </form>
    </div>
</template>
