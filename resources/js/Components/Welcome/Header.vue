<script setup>
import { Link } from '@inertiajs/vue3';

defineProps({
    canLogin: Boolean,
    canRegister: Boolean,
});
</script>

<template>
    <header class="absolute inset-x-0 top-0 z-50">
        <nav class="flex items-center justify-between p-6 lg:px-8" aria-label="Global">
            <div class="flex lg:flex-1">
                <a href="#" class="-m-1.5 p-1.5">
                    <span class="text-2xl font-bold bg-gradient-to-r from-violet-500 to-pink-400 bg-clip-text text-transparent">{{ $page.props.appName }}</span>
                </a>
            </div>
            <div v-if="canLogin" class="flex lg:flex-1 lg:justify-end space-x-6">
                <Link v-if="$page.props.auth.user" :href="route('admin.dashboard')" class="text-sm font-semibold leading-6 text-gray-900 dark:text-white">
                    Dashboard
                </Link>
                <template velse>
                    <Link :href="route('login')" class="text-sm font-semibold leading-6 text-gray-900 dark:text-white">
                        Log in <span aria-hidden="true">&rarr;</span>
                    </Link>
                    <Link v-if="canRegister" :href="route('register')" class="text-sm font-semibold leading-6 text-gray-900 dark:text-white">
                        Register
                    </Link>
                </template>
            </div>
        </nav>
    </header>
</template>
