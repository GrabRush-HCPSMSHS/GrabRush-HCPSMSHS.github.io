<script setup>
import { defineModel, computed } from 'vue';
import TextInput from '@/Components/TextInput.vue';
import TextAreaInput from '@/Components/TextAreaInput.vue';
import SelectDropdown from '@/Components/SelectDropdown.vue';
import InputLabel from '@/Components/InputLabel.vue';
import InputError from '@/Components/InputError.vue';

const isTextarea = computed(() => props.textarea);
const isDropdown = computed(() => props.dropdown);

const model = defineModel({
	type: null,
	required: true
});

const props = defineProps({
	label: {
		type: String,
		required: true,
	},
	type: {
		type: String,
		default: 'text'
	},
	error: String,
	textarea: {
		type: Boolean,
		default: false,
	},
	dropdown: {
		type: Boolean,
		default: false,
	},
	selected: {
		type: String,
	},
	options: {
		type: Array,
		default: () => [],
	},
})
</script>

<template>
	<div>
		<InputLabel v-if="label" :for="label">{{ label }}</InputLabel>

		<!-- Dropdown Input -->
		<SelectDropdown
			v-if="isDropdown"
			:id="label"
			v-model="model"
			:selected="selected"
			:options="options"
			class="w-full"
		/>

		<!-- Textarea Input -->
		<TextAreaInput
			v-else-if="isTextarea"
			:id="label"
			v-model="model"
			class="w-full border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm"
		></TextAreaInput>

		<!-- Other Input Types -->
		<TextInput
			v-else
			:id="label"
			:type="type"
			v-model="model"
			class="w-full"
		/>

		<InputError v-if="error" :message="error" />
	</div>
</template>
