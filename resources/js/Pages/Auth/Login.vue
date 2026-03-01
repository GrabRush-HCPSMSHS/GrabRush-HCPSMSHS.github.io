<script setup>
import { Head, Link, useForm } from '@inertiajs/vue3';
import Header from '@/Components/Welcome/Header.vue';
import Footer from '@/Components/Welcome/Footer.vue';
import Checkbox from '@/Components/Checkbox.vue';
import InputError from '@/Components/InputError.vue';
import InputLabel from '@/Components/InputLabel.vue';
import PrimaryButton from '@/Components/PrimaryButton.vue';
import TextInput from '@/Components/TextInput.vue';

const props = defineProps({
    canLogin: Boolean,
    canRegister: Boolean,
    canResetPassword: Boolean,
    status: String,
});

const form = useForm({
    email: '',
    password: '',
    remember: false,
});

const submit = () => {
    form.transform(data => ({
        ...data,
        remember: form.remember ? 'on' : '',
    })).post(route('login'), {
        onFinish: () => form.reset('password'),
    });
};
</script>

<template>
    <Head title="Log in" />
    <div class="bg-violet-100 dark:bg-gray-900 text-gray-800 dark:text-white min-h-screen flex flex-col">
        <Header :can-login="canLogin" :can-register="canRegister" />

        <main class="flex-grow flex items-center justify-center isolate px-4 sm:px-6 lg:px-8">
            <div class="w-full max-w-6xl mx-auto grid grid-cols-1 md:grid-cols-2 gap-10 items-center">
                <div class="p-8 md:p-12 hidden md:block">
                    <h1 class="text-4xl lg:text-5xl font-extrabold text-gray-900 dark:text-white leading-tight">
                        Delicious meals are just a click away.
                    </h1>
                    <p class="mt-4 text-lg text-gray-600 dark:text-gray-300">
                        Welcome back! Log in to view today's menu, place your order, and skip the queue. Quick, easy, and tasty.
                    </p>
                </div>

                <div class="w-full max-w-md p-8 space-y-6 bg-white dark:bg-gray-800 shadow-2xl rounded-2xl">
                    <div class="text-center">
                        <h2 class="text-3xl font-bold text-gray-900 dark:text-white">
                            Member Login
                        </h2>
                        <p class="mt-2 text-sm text-gray-600 dark:text-gray-400">
                            Don't have an account?
                            <Link :href="route('register')" class="font-medium text-violet-600 hover:text-violet-500">
                                Sign up
                            </Link>
                        </p>
                    </div>

                    <div v-if="status" class="mb-4 font-medium text-sm text-green-600 dark:text-green-400">
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
                            <div class="flex items-center justify-between">
                                <InputLabel for="password" value="Password" />
                                <Link v-if="canResetPassword" :href="route('password.request')" class="text-sm font-medium text-violet-600 hover:text-violet-500">
                                    Forgot password?
                                </Link>
                            </div>
                            <TextInput
                                id="password"
                                v-model="form.password"
                                type="password"
                                class="mt-1 block w-full"
                                required
                                autocomplete="current-password"
                                placeholder="••••••••"
                            />
                            <InputError class="mt-2" :message="form.errors.password" />
                        </div>

                        <div class="flex items-center justify-between">
                            <label class="flex items-center">
                                <Checkbox v-model:checked="form.remember" name="remember" />
                                <span class="ms-2 text-sm text-gray-600 dark:text-gray-400">Remember me</span>
                            </label>
                        </div>

                        <div>
                            <PrimaryButton class="w-full justify-center" :class="{ 'opacity-25': form.processing }" :disabled="form.processing">
                                Log in
                            </PrimaryButton>
                        </div>
                    </form>
                </div>
            </div>
        </main>

        <Footer />
    </div>
</template>
