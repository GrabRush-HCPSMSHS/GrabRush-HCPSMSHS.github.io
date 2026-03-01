<script setup>
import { Link } from '@inertiajs/vue3';

defineProps({
	category: {
		type: Object,
		required: true,
	},
});
</script>

<template>
	<Link
		:href="route('customer.products', { category: category.id, id: category.id })"
		class="category-card group flex flex-col items-center justify-center relative bg-white dark:bg-gray-800 relative p-8 rounded-3xl aspect-square text-center space-y-4 shadow-xl transition-all duration-500 ease-out transform hover:scale-110 hover:shadow-2xl hover:bg-gradient-to-b hover:from-violet-200 hover:to-pink-100 dark:hover:from-violet-950 dark:hover:to-pink-950 overflow-hidden"
	>
		<div class="decorative-circles">
			<div class="circle large"></div>
			<div class="circle small"></div>
		</div>

		<h3 class="text-gray-800 dark:text-gray-200 text-3xl font-bold tracking-widest uppercase transition-all duration-300">
			{{ category.name }}
		</h3>

		<p class="text-lg text-gray-800 dark:text-gray-200 font-medium transition-colors duration-300 group-hover:text-gray-900 dark:group-hover:text-gray-100">
			({{ category.products_count }})
		</p>
	</Link>
</template>

<style scoped>
.category-card {
	position: relative;
	transition: transform 0.4s ease-out;
	z-index: 1;
	backdrop-filter: blur(10px);
	overflow: hidden;
}

.category-card:hover {
	z-index: 10;
	transform: scale(1.1);
	box-shadow: 0px 15px 40px rgba(0, 0, 0, 0.3);
}

.decorative-circles {
	position: absolute;
	bottom: -50px;
	right: -50px;
	display: flex;
	align-items: center;
	justify-content: center;
	pointer-events: none;
}

.circle {
	position: absolute;
	border-radius: 50%;
	transition: transform 0.4s ease-in-out, filter 0.4s ease-in-out, opacity 0.4s ease-in-out;
	opacity: 0.3;
}

.large {
	width: 250px;
	height: 250px;
	background-color: rgba(139, 92, 246, 0.8);
}

.small {
	width: 180px;
	height: 180px;
	background-color: rgba(236, 72, 153, 0.8);
	transform: translate(40%, 40%);
}

.category-card:hover .large {
	transform: scale(1.2);
	filter: brightness(1.3);
	opacity: 0.2;
}

.category-card:hover .small {
	transform: scale(1.4) translate(40%, 40%);
	filter: brightness(1.4);
	opacity: 1;
}

@keyframes pulse {
	0%, 100% {
		transform: scale(1);
		opacity: 0.6;
	}
	50% {
		transform: scale(1.15);
		opacity: 1;
	}
}

@keyframes pulseSmall {
	0%, 100% {
		transform: scale(1);
		opacity: 0.6;
	}
	50% {
		transform: scale(1.1);
		opacity: 1;
	}
}

.category-card::before {
	content: '';
	position: absolute;
	top: 0;
	left: 0;
	width: 100%;
	height: 100%;
	background: url('data:image/svg+xml,%3Csvg xmlns="http://www.w3.org/2000/svg" width="100%" height="100%" viewBox="0 0 200 200"%3E%3Ccircle cx="50" cy="50" r="40" fill="rgba(139, 92, 246, 0.6)" /%3E%3Ccircle cx="150" cy="150" r="60" fill="rgba(236, 72, 153, 0.6)" /%3E%3Ccircle cx="100" cy="80" r="30" fill="rgba(255, 223, 186, 0.6)" /%3E%3Ccircle cx="30" cy="170" r="20" fill="rgba(255, 182, 193, 0.6)" /%3E%3C/svg%3E');

	background-size: cover;
	background-position: center;
	opacity: 0.3;
	z-index: -1;
}
</style>