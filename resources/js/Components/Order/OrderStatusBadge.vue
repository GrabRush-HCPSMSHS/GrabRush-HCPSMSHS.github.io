<script setup>
defineProps({
	status: {
		type: String,
		required: true,
	},
});
</script>

<template>
	<div class="">
		<span
		    :class="[
		        status === 'pending' ? 'bg-yellow-100 text-yellow-800 dark:bg-yellow-700 dark:text-yellow-300' :
		        status === 'preparing' ? 'bg-blue-100 text-blue-800 dark:bg-blue-700 dark:text-blue-300' :
		        status === 'ready' ? 'bg-violet-100 text-violet-800 dark:bg-violet-700 dark:text-violet-300' :
		        status === 'complete' ? 'bg-green-100 text-green-800 dark:bg-green-700 dark:text-green-300' : '',
		        'inline-block px-3 py-1 text-xs font-semibold rounded-full flex-shrink-0'
		    ]"
		>
		    {{ status.toUpperCase() }}
		</span>
	</div>
</template>