<script setup>
import { Head, useForm } from '@inertiajs/vue3';
import Header from '@/Components/Welcome/Header.vue';
import Footer from '@/Components/Welcome/Footer.vue';
import InputError from '@/Components/InputError.vue';
import InputLabel from '@/Components/InputLabel.vue';
import PrimaryButton from '@/Components/PrimaryButton.vue';
import TextInput from '@/Components/TextInput.vue';

defineProps({
    canLogin: Boolean,
    canRegister: Boolean,
    status: String,
});

const form = useForm({
    email: '',
});

const submit = () => {
    form.post(route('password.email'));
};
</script>

<template>
    <Head title="Forgot Password" />
    <div class="bg-violet-100 dark:bg-gray-900 text-gray-800 dark:text-white min-h-screen flex flex-col">
        <Header :can-login="canLogin" :can-register="canRegister" />

        <main class="flex-grow flex items-center justify-center isolate px-4 sm:px-6 lg:px-8">
            <div class="w-full max-w-md p-8 space-y-6 bg-white dark:bg-gray-800 shadow-2xl rounded-2xl">
                <div class="text-center">
                    <h2 class="text-3xl font-bold text-gray-900 dark:text-white">
                        Forgot Password?
                    </h2>
                    <p class="mt-2 text-sm text-gray-600 dark:text-gray-400">
                        No problem. Enter your email below and we'll send you a reset link.
                    </p>
                </div>

                <div v-if="status" class="font-medium text-sm text-green-600 dark:text-green-400">
                    {{ status }}
                </div>

                <form @submit.prevent="submit" class="space-y-6">
                    <div>
                        <InputLabel for="email" value="Email Address" />
                        <TextInput
                            id="email"
                            v-model="form.email"
                            type="email"
                            class="mt-1 block w-full"
                            required
                            autofocus
                            autocomplete="username"
                            placeholder="you@example.com"
                        />
                        <InputError class="mt-2" :message="form.errors.email" />
                    </div>

                    <div>
                        <PrimaryButton class="w-full justify-center" :class="{ 'opacity-25': form.processing }" :disabled="form.processing">
                            Email Password Reset Link
                        </PrimaryButton>
                    </div>
                </form>
            </div>
        </main>

        <Footer />
    </div>
</template>
