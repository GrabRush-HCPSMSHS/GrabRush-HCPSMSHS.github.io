<script setup>
import { ref, onMounted } from 'vue';

const svgBackground = {
    background: `url('data:image/svg+xml,%3Csvg xmlns="http://www.w3.org/2000/svg" width="100%" height="100%" viewBox="0 0 200 200"%3E%3Ccircle cx="100" cy="100" r="80" fill="rgba(138, 43, 226, 0.5)" /%3E%3Ccircle cx="50" cy="50" r="60" fill="rgba(0, 255, 255, 0.5)" /%3E%3Ccircle cx="150" cy="150" r="40" fill="rgba(255, 20, 147, 0.5)" /%3E%3Ccircle cx="100" cy="150" r="20" fill="rgba(255, 182, 193, 0.5)" /%3E%3Ccircle cx="150" cy="50" r="10" fill="rgba(138, 43, 226, 0.5)" /%3E%3C/svg%3E')`,
    darkModeBackground: `url('data:image/svg+xml,%3Csvg xmlns="http://www.w3.org/2000/svg" width="100%" height="100%" viewBox="0 0 200 200"%3E%3Ccircle cx="100" cy="100" r="80" fill="rgba(75, 0, 130, 0.5)" /%3E%3Ccircle cx="50" cy="50" r="60" fill="rgba(0, 139, 139, 0.5)" /%3E%3Ccircle cx="150" cy="150" r="40" fill="rgba(255, 105, 180, 0.5)" /%3E%3Ccircle cx="100" cy="150" r="20" fill="rgba(255, 105, 180, 0.3)" /%3E%3Ccircle cx="150" cy="50" r="10" fill="rgba(75, 0, 130, 0.5)" /%3E%3C/svg%3E')`
};

const isDarkMode = ref(false);

onMounted(() => {
  isDarkMode.value = document.documentElement.classList.contains('dark');
});
</script>

<template>
	<div
	    class="bg-gray-200 dark:bg-gray-700 rounded-full w-full aspect-square flex justify-center items-center overflow-hidden"
	    :style="{
	          background: isDarkMode ? svgBackground.darkModeBackground : svgBackground.background
	        }"
	  >
		<slot></slot>
	</div>
</template>