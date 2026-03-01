<script setup>
const features = [
    {
        name: 'Premium Ingredients',
        description: 'We source the freshest, highest-quality ingredients to ensure every dish is delicious and wholesome.',
        icon: `<svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-8 h-8"><path stroke-linecap="round" stroke-linejoin="round" d="M20.25 6.375c0 2.278-3.694 4.125-8.25 4.125S3.75 8.653 3.75 6.375m16.5 0c0-2.278-3.694-4.125-8.25-4.125S3.75 4.097 3.75 6.375m16.5 0v11.25c0 2.278-3.694 4.125-8.25 4.125s-8.25-1.847-8.25-4.125V6.375m16.5 0v3.75m-16.5-3.75v3.75m16.5 0v3.75C20.25 16.153 16.556 18 12 18s-8.25-1.847-8.25-4.125v-3.75m16.5 0c0 2.278-3.694 4.125-8.25 4.125s-8.25-1.847-8.25-4.125" /></svg>`,
    },
    {
        name: 'Speedy Delivery',
        description: 'Craving something now? Our streamlined process ensures your meal is prepared and delivered hot and fresh, right on time.',
        icon: `<svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-8 h-8"><path stroke-linecap="round" stroke-linejoin="round" d="M3.75 13.5l10.5-11.25L12 10.5h8.25L9.75 21.75 12 13.5H3.75z" /></svg>`,
    },
    {
        name: 'Easy Online Ordering',
        description: 'Our intuitive platform makes it simple to browse, customize, and place your order in just a few clicks.',
        icon: `<svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-8 h-8"><path stroke-linecap="round" stroke-linejoin="round" d="M15.75 10.5V6a3.75 3.75 0 10-7.5 0v4.5m11.356-1.993l1.263 12c.07.658-.463 1.243-1.119 1.243H4.25a1.125 1.125 0 01-1.12-1.243l1.264-12A1.125 1.125 0 015.513 7.5h12.974c.576 0 1.059.435 1.119 1.007zM8.625 10.5a.375.375 0 11-.75 0 .375.375 0 01.75 0zm7.5 0a.375.375 0 11-.75 0 .375.375 0 01.75 0z" /></svg>`,
    },
];
</script>

<template>
    <section class="bg-white dark:bg-gray-800 py-24 sm:py-20 opacity-0 fade-in">
        <div class="mx-auto max-w-7xl px-6 lg:px-8">
            <div class="mx-auto max-w-2xl lg:text-center">
                <h2 class="text-base font-semibold leading-7 text-indigo-600 dark:text-indigo-400">Everything You Need</h2>
                <p class="mt-2 text-3xl font-bold tracking-tight text-gray-900 dark:text-white sm:text-4xl">The Modern Canteen Experience</p>
                <p class="mt-6 text-xl leading-8 text-gray-600 dark:text-gray-400">
                    We've reimagined the canteen to bring you a seamless, delicious, and convenient dining experience every day.
                </p>
            </div>
            <div class="mx-auto mt-16 max-w-2xl sm:mt-20 lg:mt-24 lg:max-w-none">
                <dl class="grid max-w-xl grid-cols-1 gap-x-8 gap-y-10 lg:max-w-none lg:grid-cols-3 lg:gap-y-16">
                    <div v-for="feature in features" :key="feature.name" class="relative pl-16 transition-transform duration-300 hover:-translate-y-2">
                        <dt class="text-lg font-semibold leading-7 text-gray-900 dark:text-white">
                            <div class="absolute left-0 top-0 flex h-12 w-12 items-center justify-center rounded-lg bg-indigo-600 text-white" v-html="feature.icon"></div>
                            {{ feature.name }}
                        </dt>
                        <dd class="mt-2 text-lg leading-7 text-gray-600 dark:text-gray-400">{{ feature.description }}</dd>
                    </div>
                </dl>
            </div>
        </div>
    </section>
</template>
