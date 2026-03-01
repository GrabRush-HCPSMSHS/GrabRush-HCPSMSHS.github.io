<script setup>
import { computed } from 'vue';
import { Head, Link, useForm } from '@inertiajs/vue3';
import Header from '@/Components/Welcome/Header.vue';
import Footer from '@/Components/Welcome/Footer.vue';
import PrimaryButton from '@/Components/PrimaryButton.vue';

const props = defineProps({
    canLogin: Boolean,
    canRegister: Boolean,
    status: String,
});

const form = useForm({});

const submit = () => {
    form.post(route('verification.send'));
};

const verificationLinkSent = computed(() => props.status === 'verification-link-sent');
</script>

<template>
    <Head title="Email Verification" />
    <div class="bg-violet-100 dark:bg-gray-900 text-gray-800 dark:text-white min-h-screen flex flex-col">
        <Header :can-login="canLogin" :can-register="canRegister" />

        <main class="flex-grow flex items-center justify-center isolate px-4 sm:px-6 lg:px-8">
            <div class="w-full max-w-md p-8 space-y-6 bg-white dark:bg-gray-800 shadow-2xl rounded-2xl text-center">
                <h2 class="text-3xl font-bold text-gray-900 dark:text-white">
                    Verify Your Email Address
                </h2>

                <p class="text-sm text-gray-600 dark:text-gray-400">
                    Before continuing, could you verify your email address by clicking on the link we just emailed to you? If you didn't receive the email, we will gladly send you another.
                </p>

                <div v-if="verificationLinkSent" class="font-medium text-sm text-green-600 dark:text-green-400">
                    A new verification link has been sent to the email address you provided in your profile settings.
                </div>

                <form @submit.prevent="submit">
                    <div class="mt-6 flex flex-col sm:flex-row items-center justify-center gap-4">
                        <PrimaryButton :class="{ 'opacity-25': form.processing }" :disabled="form.processing">
                            Resend Verification Email
                        </PrimaryButton>

                        <div class="flex gap-4">
                            <Link
                                :href="route('profile.show')"
                                class="text-sm font-medium text-violet-600 hover:text-violet-500"
                            >
                                Edit Profile
                            </Link>

                            <Link
                                :href="route('logout')"
                                method="post"
                                as="button"
                                class="text-sm font-medium text-violet-600 hover:text-violet-500"
                            >
                                Log Out
                            </Link>
                        </div>
                    </div>
                </form>
            </div>
        </main>

        <Footer />
    </div>
</template>
