<script setup>
import { Link } from '@inertiajs/vue3';
</script>

<template>
    <section class="bg-violet-200 dark:bg-gray-800/50 opacity-0 fade-in">
        <div class="mx-auto max-w-7xl px-6 py-24 sm:py-32 lg:flex lg:items-center lg:justify-between lg:px-8">
            <h2 class="text-3xl font-bold tracking-tight text-gray-900 dark:text-white sm:text-4xl">
                Ready to dive in?
                <br />
                Start your first order today.
            </h2>
            <div class="mt-10 flex items-center gap-x-6 lg:mt-0 lg:flex-shrink-0">
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
