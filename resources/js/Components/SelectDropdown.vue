<script setup>
import { onMounted, ref, computed } from 'vue';

const props = defineProps({
    modelValue: String,
    options: Array,
    selected: String,
});

defineEmits(['update:modelValue']);

const input = ref(null);

onMounted(() => {
    if (input.value?.hasAttribute('autofocus')) {
        input.value.focus();
    }
});

defineExpose({ focus: () => input.value.focus() });

const selectedValue = computed(() => props.modelValue || '');
</script>

<template>
    <select
        ref="input"
        class="border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-300 focus:border-indigo-500 dark:focus:border-indigo-600 focus:ring-indigo-500 dark:focus:ring-indigo-600 rounded-lg shadow-md"
        :value="selectedValue"
        @change="$emit('update:modelValue', $event.target.value)"
    >
        <option disabled value="">{{ selected }}</option>
        <option value="">Show All</option>
        <option v-for="(option, index) in options" :key="index" :value="option.value" :selected="option.value === selectedValue">
            {{ option.label }}
        </option>
    </select>
</template>