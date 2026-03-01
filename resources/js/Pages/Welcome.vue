<script setup>
import { Head } from '@inertiajs/vue3';
import { onMounted } from 'vue';
import Header from '@/Components/Welcome/Header.vue';
import Hero from '@/Components/Welcome/Hero.vue';
import Features from '@/Components/Welcome/Features.vue';
import Products from '@/Components/Welcome/Products.vue';
import Steps from '@/Components/Welcome/Steps.vue';
import Reviews from '@/Components/Welcome/Reviews.vue';
import CTA from '@/Components/Welcome/CTA.vue';
import Footer from '@/Components/Welcome/Footer.vue';

defineProps({
    canLogin: Boolean,
    canRegister: Boolean,
    products: Array,
    reviews: Array,
});

onMounted(() => {
    if (localStorage.getItem('isDark') === 'true') {
        document.documentElement.classList.add('dark');
    } else {
        document.documentElement.classList.remove('dark');
    }

    const observer = new IntersectionObserver((entries) => {
        entries.forEach(entry => {
            if (entry.isIntersecting) {
                entry.target.classList.add('animate-fade-in-up');
                entry.target.classList.remove('opacity-0');
            }
        });
    }, {
        threshold: 0.1,
    });

    document.querySelectorAll('.fade-in').forEach(el => {
        observer.observe(el);
    });
});
</script>

<template>
    <Head title="Welcome" />
    <div class="bg-gray-100 dark:bg-gray-900 text-gray-800 dark:text-white">
        <Header :can-login="canLogin" :can-register="canRegister" />

        <main class="isolate">
            <Hero />
            <Features />
            <Products :products="products" />
            <Steps />
            <Reviews :reviews="reviews" />
            <CTA />
        </main>

        <Footer />
    </div>
</template>