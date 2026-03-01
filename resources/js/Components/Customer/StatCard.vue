<script setup>
defineProps({
    icon: { type: String, required: true },
    title: { type: String, required: true },
    subtitle: { type: String, required: true },
    value: { type: [String, Number], required: true },
    valueColor: { type: String, default: 'text-gray-900 dark:text-white' },
});
</script>

<template>
    <div class="bg-white dark:bg-gray-800 shadow-md rounded-2xl p-6 space-y-3 flex flex-col justify-between">
        <div class="flex items-center space-x-3">
            <div v-html="icon" class="w-8 h-8"></div>
            <h3 class="text-lg sm:text-xl font-semibold text-gray-900 dark:text-white">{{ title }}</h3>
        </div>
        <p class="text-xs sm:text-sm text-gray-600 dark:text-gray-400">{{ subtitle }}</p>
        <p class="text-4xl sm:text-5xl" :class="valueColor">{{ value }}</p>
    </div>
</template>