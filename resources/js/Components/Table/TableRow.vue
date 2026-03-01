<script setup>
defineProps({
    rowClass: {
        type: String,
        default: '',
    },
});
</script>

<template>
    <tr :class="rowClass" class="hover:bg-gray-50 hover:dark:bg-gray-700">
        <slot></slot>
    </tr>
</template>
