<script setup>
import { Head, Link, useForm } from '@inertiajs/vue3';
import Header from '@/Components/Welcome/Header.vue';
import Footer from '@/Components/Welcome/Footer.vue';
import Checkbox from '@/Components/Checkbox.vue';
import InputError from '@/Components/InputError.vue';
import InputLabel from '@/Components/InputLabel.vue';
import PrimaryButton from '@/Components/PrimaryButton.vue';
import TextInput from '@/Components/TextInput.vue';

defineProps({
    canLogin: Boolean,
    canRegister: Boolean,
});

const form = useForm({
    name: '',
    email: '',
    password: '',
    password_confirmation: '',
    terms: false,
});

const submit = () => {
    form.post(route('register'), {
        onFinish: () => form.reset('password', 'password_confirmation'),
    });
};
</script>

<template>
    <Head title="Register" />
    <div class="bg-violet-100 dark:bg-gray-900 text-gray-800 dark:text-white min-h-screen flex flex-col">
        <Header :can-login="canLogin" :can-register="canRegister" />

        <main class="flex-grow flex items-center justify-center isolate px-4 sm:px-6 lg:px-8">
            <div class="w-full max-w-6xl mx-auto grid grid-cols-1 md:grid-cols-2 gap-10 items-center">
                <div class="p-8 md:p-12 hidden md:block">
                    <h1 class="text-4xl lg:text-5xl font-extrabold text-gray-900 dark:text-white leading-tight">
                        Your next favorite meal is waiting.
                    </h1>
                    <p class="mt-4 text-lg text-gray-600 dark:text-gray-300">
                        Create an account to get started. Enjoy seamless ordering and view your order history.
                    </p>
                </div>

                <div class="w-full max-w-md p-8 space-y-6 bg-white dark:bg-gray-800 shadow-2xl rounded-2xl">
                    <div class="text-center">
                        <h2 class="text-3xl font-bold text-gray-900 dark:text-white">
                            Create an Account
                        </h2>
                        <p class="mt-2 text-sm text-gray-600 dark:text-gray-400">
                            Already have an account?
                            <Link :href="route('login')" class="font-medium text-violet-600 hover:text-violet-500">
                                Log in
                            </Link>
                        </p>
                    </div>

                    <form @submit.prevent="submit" class="space-y-4">
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
                                placeholder="John Doe"
                            />
                            <InputError class="mt-2" :message="form.errors.name" />
                        </div>

                        <div>
                            <InputLabel for="email" value="Email Address" />
                            <TextInput
                                id="email"
                                v-model="form.email"
                                type="email"
                                class="mt-1 block w-full"
                                required
                                autocomplete="username"
                                placeholder="you@example.com"
                            />
                            <InputError class="mt-2" :message="form.errors.email" />
                        </div>

                        <div>
                            <InputLabel for="password" value="Password" />
                            <TextInput
                                id="password"
                                v-model="form.password"
                                type="password"
                                class="mt-1 block w-full"
                                required
                                autocomplete="new-password"
                                placeholder="••••••••"
                            />
                            <InputError class="mt-2" :message="form.errors.password" />
                        </div>

                        <div>
                            <InputLabel for="password_confirmation" value="Confirm Password" />
                            <TextInput
                                id="password_confirmation"
                                v-model="form.password_confirmation"
                                type="password"
                                class="mt-1 block w-full"
                                required
                                autocomplete="new-password"
                                placeholder="••••••••"
                            />
                            <InputError class="mt-2" :message="form.errors.password_confirmation" />
                        </div>

                        <div v-if="$page.props.jetstream.hasTermsAndPrivacyPolicyFeature" class="pt-2">
                            <InputLabel for="terms">
                                <div class="flex items-start">
                                    <Checkbox id="terms" v-model:checked="form.terms" name="terms" required />
                                    <div class="ms-2 text-sm">
                                        I agree to the <a target="_blank" :href="route('terms.show')" class="underline text-gray-600 dark:text-gray-400 hover:text-gray-900 dark:hover:text-gray-100">Terms of Service</a> and <a target="_blank" :href="route('policy.show')" class="underline text-gray-600 dark:text-gray-400 hover:text-gray-900 dark:hover:text-gray-100">Privacy Policy</a>
                                    </div>
                                </div>
                                <InputError class="mt-2" :message="form.errors.terms" />
                            </InputLabel>
                        </div>

                        <div class="pt-2">
                            <PrimaryButton class="w-full justify-center" :class="{ 'opacity-25': form.processing }" :disabled="form.processing">
                                Register
                            </PrimaryButton>
                        </div>
                    </form>
                </div>
            </div>
        </main>

        <Footer />
    </div>
</template>
