<script setup>
defineProps({
    isAvailable: {
        type: Number,
        required: true,
    },
});
</script>

<template>
    <span
        :class="[
            isAvailable
                ? 'bg-green-100 text-green-800 dark:bg-green-700 dark:text-green-300'
                : 'bg-red-100 text-red-800 dark:bg-red-700 dark:text-red-300',
            'inline-block px-3 py-1 text-xs font-semibold rounded-full text-center'
        ]"
    >
        {{ isAvailable ? 'AVAILABLE' : 'UNAVAILABLE' }}
    </span>
</template>