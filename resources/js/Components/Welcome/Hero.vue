<script setup>
import { Link } from '@inertiajs/vue3';
</script>

<template>
    <section class="relative px-6 lg:px-8">
        <div class="mx-auto max-w-4xl text-center py-40 sm:py-40 lg:py-40">
            <div class="flex justify-center">
                <img src="/2.svg" alt="Logo" class="block h-50 w-auto dark:hidden">
                <img src="/1.svg" alt="Logo" class="hidden h-50 w-auto dark:block">
            </div>
            <p class="mt-8 text-xl leading-8 text-gray-600 dark:text-gray-300 max-w-2xl mx-auto">
                The best meals start with the best ingredients. Get ready to taste the difference.
            </p>
            <div class="mt-12 flex items-center justify-center gap-x-6">
                <Link :href="route('register')" class="rounded-full bg-indigo-600 px-8 py-3 text-lg font-semibold text-white shadow-lg hover:bg-indigo-500 focus-visible:outline focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-indigo-600 transform hover:scale-105 transition-transform duration-300">
                    Order Now
                </Link>
                <a href="#products" class="text-lg font-semibold leading-6 text-gray-900 dark:text-white group">
                    Explore Menu <span aria-hidden="true" class="inline-block transition-transform group-hover:translate-x-1 motion-reduce:transform-none">→</span>
                </a>
            </div>
        </div>
    </section>
</template>
