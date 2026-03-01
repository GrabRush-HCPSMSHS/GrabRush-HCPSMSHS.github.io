<script setup>
defineProps({
    reviews: Array,
});
</script>

<template>
    <section class="py-24 sm:py-32 opacity-0 fade-in">
        <div class="mx-auto max-w-7xl px-6 lg:px-8">
            <div class="mx-auto max-w-2xl text-center">
                <h2 class="text-3xl font-bold tracking-tight text-gray-900 dark:text-white sm:text-4xl">Straight from the Foodies</h2>
                <p class="mt-2 text-xl leading-8 text-gray-600 dark:text-gray-400">Hear what our happy customers have to say about their favorite meals.</p>
            </div>
        </div>
        <div class="w-full inline-flex flex-nowrap overflow-hidden [mask-image:_linear-gradient(to_right,transparent_0,_black_128px,_black_calc(100%-200px),transparent_100%)] mt-10">
            <ul class="flex items-stretch justify-center md:justify-start [&_li]:mx-4 [&_img]:max-w-none animate-infinite-scroll">
                <li v-for="review in reviews" :key="review.id" class="flex-shrink-0 w-[28rem]">
                    <figure class="p-6 rounded-lg bg-white dark:bg-gray-800/50 border border-gray-200 dark:border-gray-700 shadow-md h-full flex flex-col">
                        <div class="flex-grow">
                            <div class="flex items-center justify-between mb-2">
                                <div class="flex items-center">
                                    <svg v-for="i in 5" :key="i" class="w-5 h-5" :class="i <= review.rating ? 'text-yellow-400' : 'text-gray-300 dark:text-gray-600'" fill="currentColor" viewBox="0 0 20 20">
                                        <path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.286 3.957a1 1 0 00.95.69h4.162c.969 0 1.371 1.24.588 1.81l-3.368 2.448a1 1 0 00-.364 1.118l1.287 3.957c.3.921-.755 1.688-1.539 1.118l-3.368-2.448a1 1 0 00-1.176 0l-3.368 2.448c-.784.57-1.838-.197-1.539-1.118l1.287-3.957a1 1 0 00-.364-1.118L2.051 9.39c-.783-.57-.38-1.81.588-1.81h4.162a1 1 0 00.95-.69L9.049 2.927z" />
                                    </svg>
                                </div>
                                <blockquote class="text-sm text-gray-500 dark:text-gray-400">{{ new Date(review.created_at).toLocaleDateString() }}</blockquote>
                            </div>
                            <p class="mt-4 text-gray-700 dark:text-gray-300 italic">"{{ review.comment }}"</p>
                        </div>
                        <figcaption class="mt-4 text-right">
                            <div class="text-sm font-semibold text-gray-900 dark:text-white">- {{ review.user.name }}</div>
                        </figcaption>
                    </figure>
                </li>
            </ul>
            <ul class="flex items-stretch justify-center md:justify-start [&_li]:mx-4 [&_img]:max-w-none animate-infinite-scroll" aria-hidden="true">
                <li v-for="review in reviews" :key="review.id" class="flex-shrink-0 w-[28rem]">
                    <figure class="p-6 rounded-lg bg-white dark:bg-gray-800/50 border border-gray-200 dark:border-gray-700 shadow-md h-full flex flex-col">
                        <div class="flex-grow">
                            <div class="flex items-center justify-between mb-2">
                                <div class="flex items-center">
                                    <svg v-for="i in 5" :key="i" class="w-5 h-5" :class="i <= review.rating ? 'text-yellow-400' : 'text-gray-300 dark:text-gray-600'" fill="currentColor" viewBox="0 0 20 20">
                                        <path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.286 3.957a1 1 0 00.95.69h4.162c.969 0 1.371 1.24.588 1.81l-3.368 2.448a1 1 0 00-.364 1.118l1.287 3.957c.3.921-.755 1.688-1.539 1.118l-3.368-2.448a1 1 0 00-1.176 0l-3.368 2.448c-.784.57-1.838-.197-1.539-1.118l1.287-3.957a1 1 0 00-.364-1.118L2.051 9.39c-.783-.57-.38-1.81.588-1.81h4.162a1 1 0 00.95-.69L9.049 2.927z" />
                                    </svg>
                                </div>
                                <blockquote class="text-sm text-gray-500 dark:text-gray-400">{{ new Date(review.created_at).toLocaleDateString() }}</blockquote>
                            </div>
                            <p class="mt-4 text-gray-700 dark:text-gray-300 italic">"{{ review.comment }}"</p>
                        </div>
                        <figcaption class="mt-4 text-right">
                            <div class="text-sm font-semibold text-gray-900 dark:text-white">- {{ review.user.name }}</div>
                        </figcaption>
                    </figure>
                </li>
            </ul>
        </div>
    </section>
</template>
