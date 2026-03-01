<script setup>
import { ref } from 'vue';
import { useForm } from '@inertiajs/vue3';
import { formatDate, useDebouncedFilter, toast } from '@/helpers';
import AppLayout from '@/Layouts/AppLayout.vue';
import PrimaryButton from '@/Components/PrimaryButton.vue';
import SecondaryButton from '@/Components/SecondaryButton.vue';
import TextInput from '@/Components/TextInput.vue';
import InputLabel from '@/Components/InputLabel.vue';
import InputError from '@/Components/InputError.vue';
import Table from '@/Components/Table/Table.vue';
import TableBody from '@/Components/Table/TableBody.vue';
import TableData from '@/Components/Table/TableData.vue';
import TableHead from '@/Components/Table/TableHead.vue';
import TableRow from '@/Components/Table/TableRow.vue';
import Paginator from '@/Components/Table/Paginator.vue';

defineProps({
    staffs: { type: Object, required: true },
});

const isStaffModalOpen = ref(false);
const selectedStaff = ref(null);
const filterStaffName = ref('');

const form = useForm({
    id: '',
    name: '',
    email: '',
    password: '',
    password_confirmation: '',
    type: 'staff',
});

const submitForm = () => {
    const options = {
        onSuccess: () => {
            form.reset();
            toggleStaffModal();
            toast('Staff saved successfully', 'success');
        },
        preserveScroll: true,
    };

    form.id
        ? form.put(route('admin.staffs.update', form.id), options)
        : form.post(route('admin.staffs.store'), options);
};

const deleteStaff = (id) => {
    if (!confirm('Are you sure you want to delete this staff?')) return;

    form.delete(route('admin.staffs.destroy', id), {
        onSuccess: () => {
            toast('Staff deleted', 'success');
        },
        onError: () => {
            toast('Failed to delete staff', 'error');
        },
        preserveScroll: true,
    });
};

const editStaff = (staff) => {
    selectedStaff.value = staff;
    form.id = staff.id;
    form.name = staff.name;
    form.email = staff.email;
    toggleStaffModal();
};

const cancelEditStaff = () => {
    form.reset();
    selectedStaff.value = null;
    toggleStaffModal();
};

const toggleStaffModal = () => {
    isStaffModalOpen.value = !isStaffModalOpen.value;
};

useDebouncedFilter([
    { name: 'filterStaffName', value: filterStaffName },
], 'admin.staffs.index');
</script>

<template>
    <AppLayout title="Dashboard">
        <div class="py-12">
            <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-4 mb-8">
                <div class="flex items-center justify-between">
                    <h3 class="text-3xl text-gray-800 dark:text-gray-200 font-serif">
                        Staff
                    </h3>
                    <PrimaryButton @click="toggleStaffModal()">add a Staff</PrimaryButton>
                </div>
                <TextInput v-model="filterStaffName" placeholder="Search email or name" class="rounded-xl border border-gray-50 w-64" />
                <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-xl rounded-3xl mb-4">
                    <Table>
                        <TableHead :headers="['#', 'email', 'name', 'created at', 'actions']" />
                        <TableBody>
                            <TableRow v-for="(staff, index) in staffs.data"
                                :key="staff.id"
                                :rowClass="index !== staffs.data.length - 1 ? 'border-b border-gray-200 dark:border-gray-700' : ''">
                                <TableData>{{ index + 1 }}</TableData>
                                <TableData>{{ staff.email }}</TableData>
                                <TableData>{{ staff.name }}</TableData>
                                <TableData>{{ formatDate(staff.created_at) }}</TableData>
                                <TableData>
                                    <div class="space-x-2">
                                        <button @click="editStaff(staff)" class="text-blue-500 hover:underline">Edit</button>
                                        <button @click="deleteStaff(staff)" class="text-red-500 hover:underline">Delete</button>
                                    </div>
                                </TableData>
                            </TableRow>
                        </TableBody>
                    </Table>
                </div>
                <Paginator :data="staffs" />
            </div>
        </div>

        <div v-if="isStaffModalOpen" class="fixed inset-0 flex items-center justify-center bg-black bg-opacity-50 z-50">
            <div class="bg-white dark:bg-gray-800 p-6 rounded-lg shadow-xl w-96">
                <h3 class="text-2xl text-gray-800 dark:text-gray-200 mb-4">Staff Form</h3>
                <form @submit.prevent="submitForm" class="space-y-4">
                    <div>
                        <InputLabel for="name" value="Name" />
                        <TextInput
                            id="name"
                            v-model="form.name"
                            type="text"
                            class="mt-1 block w-full"
                            required
                            autofocus
                            autocomplete="name"
                        />
                        <InputError class="mt-2" :message="form.errors.name" />
                    </div>

                    <div class="mt-4">
                        <InputLabel for="email" value="Email" />
                        <TextInput
                            id="email"
                            v-model="form.email"
                            type="email"
                            class="mt-1 block w-full"
                            required
                            autocomplete="username"
                        />
                        <InputError class="mt-2" :message="form.errors.email" />
                    </div>

                    <div v-if="selectedStaff == null" class="mt-4">
                        <InputLabel for="password" value="Password" />
                        <TextInput
                            id="password"
                            v-model="form.password"
                            type="password"
                            class="mt-1 block w-full"
                            required
                            autocomplete="new-password"
                        />
                        <InputError class="mt-2" :message="form.errors.password" />
                    </div>

                    <div v-if="selectedStaff == null" class="mt-4">
                        <InputLabel for="password_confirmation" value="Confirm Password" />
                        <TextInput
                            id="password_confirmation"
                            v-model="form.password_confirmation"
                            type="password"
                            class="mt-1 block w-full"
                            required
                            autocomplete="new-password"
                        />
                        <InputError class="mt-2" :message="form.errors.password_confirmation" />
                    </div>

                    <div class="flex justify-end space-x-2">
                        <PrimaryButton class="" :class="{ 'opacity-25': form.processing }" :disabled="form.processing">
                            Save
                        </PrimaryButton>
                        <SecondaryButton @click="cancelEditStaff" :disabled="form.processing">Cancel</SecondaryButton>
                    </div>
                </form>
            </div>
        </div>
    </AppLayout>
</template>
