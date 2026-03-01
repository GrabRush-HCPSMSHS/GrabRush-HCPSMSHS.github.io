<script setup>
import { Head } from '@inertiajs/vue3';
import Header from '@/Components/Welcome/Header.vue';
import Footer from '@/Components/Welcome/Footer.vue';

defineProps({
    canLogin: Boolean,
    canRegister: Boolean,
    title: String,
});
</script>

<template>
    <Head :title="title" />
    <div class="bg-violet-100 dark:bg-gray-900 text-gray-800 dark:text-white min-h-screen flex flex-col">
        <Header :can-login="canLogin" :can-register="canRegister" />

        <main class="flex-grow flex items-center justify-center">
            <div class="w-full sm:max-w-md mt-6 px-6 py-4 bg-white dark:bg-gray-800 shadow-md overflow-hidden sm:rounded-lg">
                <slot />
            </div>
        </main>

        <Footer />
    </div>
</template>
