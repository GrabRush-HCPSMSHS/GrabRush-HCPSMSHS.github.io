<script setup>
import TableRow from './TableRow.vue';
import TableHeader from './TableHeader.vue';

defineProps({
	headers: {
		type: Array,
		required: true,
	}
});
</script>

<template>
	<thead class="bg-white dark:bg-gray-800 border-b dark:border-gray-700">
		<TableRow>
			<TableHeader v-for="(header, index) in headers" :key="index">
				{{ header.toUpperCase() }}
			</TableHeader>
		</TableRow>
	</thead>
</template>
