<script setup>
import { ref } from 'vue';
import { useForm } from '@inertiajs/vue3';
import { useDebouncedFilter, toast } from '@/helpers';
import AppLayout from '@/Layouts/AppLayout.vue';
import PrimaryButton from '@/Components/PrimaryButton.vue';
import SecondaryButton from '@/Components/SecondaryButton.vue';
import TextInput from '@/Components/TextInput.vue';
import InputGroup from '@/Components/InputGroup.vue';
import Table from '@/Components/Table/Table.vue';
import TableBody from '@/Components/Table/TableBody.vue';
import TableData from '@/Components/Table/TableData.vue';
import TableHead from '@/Components/Table/TableHead.vue';
import TableRow from '@/Components/Table/TableRow.vue';
import Paginator from '@/Components/Table/Paginator.vue';

const selectedCategory = ref(null);
const isCategoryModalOpen = ref(false);
const filterCategoryName = ref('');

defineProps({
    categories: { type: Object, required: true },
});

const form = useForm({
    id: null,
    name: '',
});

const submitForm = () => {
    const options = {
        onSuccess: () => {
            form.reset();
            selectedCategory.value = null;
            toggleCategoryModal();
            toast("Category saved successfully", "success");
        },
        onError: () => {
            toast("Failed to save category", "error");
        },
        preserveScroll: true
    };

    form.id
        ? form.put(route('admin.categories.update', form.id), options)
        : form.post(route('admin.categories.store'), options);
};

const deleteCategory = (id) => {
    if (confirm('Are you sure you want to delete this category?')) {
        form.delete(route('admin.categories.destroy', id), {
            onSuccess: () => {
                toast("Category deleted successfully", "success");
            },
            onError: () => {
                toast("Failed to delete category", "error");
            },
            preserveScroll: true
        });
    }
};

const toggleCategoryModal = () => {
    isCategoryModalOpen.value = !isCategoryModalOpen.value;
};

const editCategory = (category) => {
    selectedCategory.value = category;
    form.id = category.id;
    form.name = category.name;
    toggleCategoryModal();
};

const cancelEditCategory = () => {
    form.reset();
    selectedCategory.value = null;
    toggleCategoryModal();
};

useDebouncedFilter([
    { name: 'filterCategoryName', value: filterCategoryName },
], 'admin.categories.index');
</script>

<template>
    <AppLayout title="Categories">
        <div class="py-12">
            <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-4 mb-8">
                <div class="flex items-center justify-between">
                    <h3 class="text-3xl text-gray-800 dark:text-gray-200 font-serif">
                        Categories
                    </h3>
                    <PrimaryButton @click="toggleCategoryModal()">add a category</PrimaryButton>
                </div>
                <div class="flex justify-between gap-2">
                    <TextInput v-model="filterCategoryName" placeholder="Search category" class="rounded-xl border border-gray-50 w-64" />
                </div>
                <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-xl rounded-3xl mb-4">
                    <Table>
                        <TableHead :headers="['#', 'category', 'actions']" />
                        <TableBody>
                            <TableRow v-for="(category, index) in categories.data"
                                :key="category.id"
                                :rowClass="index !== categories.data.length - 1 ? 'border-b border-gray-200 dark:border-gray-700' : ''">
                                <TableData>{{ index + 1 }}</TableData>
                                <TableData>{{ category.name }}</TableData>
                                <TableData>
                                    <div class="space-x-2">
                                        <button @click="editCategory(category)" class="text-blue-500 hover:underline">Edit</button>
                                        <button @click="deleteCategory(category)" class="text-red-500 hover:underline">Delete</button>
                                    </div>
                                </TableData>
                            </TableRow>
                        </TableBody>
                    </Table>
                </div>
                <Paginator :data="categories" />
            </div>
        </div>

        <div v-if="isCategoryModalOpen" class="fixed inset-0 flex items-center justify-center z-50">
            <div class="bg-white dark:bg-gray-800 p-6 rounded-lg shadow-xl w-96">
                <h3 class="text-2xl text-gray-800 dark:text-gray-200 mb-4">Category Form</h3>
                <form @submit.prevent="submitForm" class="space-y-4">
                    <InputGroup label="Name:" v-model="form.name" :error="form.errors.name" />
                    <div class="flex justify-end space-x-2">
                        <PrimaryButton :disabled="form.processing">Submit</PrimaryButton>
                        <SecondaryButton @click="cancelEditCategory" :disabled="form.processing">Cancel</SecondaryButton>
                    </div>
                </form>
            </div>
        </div>
    </AppLayout>
</template>
