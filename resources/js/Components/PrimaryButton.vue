<script setup>
defineProps({
    type: {
        type: String,
        default: 'submit',
    },
});
</script>

<template>
    <button
        :type="type"
        class="inline-flex items-center px-4 py-2 bg-violet-600 dark:bg-violet-700 rounded-xl font-semibold text-xs text-violet-50 dark:text-white uppercase tracking-widest shadow-md
               hover:scale-105
               hover:bg-gradient-to-r hover:from-violet-500 hover:to-pink-400
               dark:hover:from-violet-700 dark:hover:to-pink-700
               active:scale-95 active:animate-rainbow
               active:bg-gradient-to-r
               focus:outline-none focus:ring-2 focus:ring-offset-2 dark:focus:ring-offset-violet-800
               focus:bg-gradient-to-r animate-gradient
               transition ease-in-out duration-150">
        <slot />
    </button>
</template>

<style>
@keyframes gradientShift {
    0% { background-position: 0% 50%; }
    50% { background-position: 100% 50%; }
    100% { background-position: 0% 50%; }
}

@keyframes rainbowEffect {
    0% { background-position: 0% 50%; background-color: #ff0000; }
    20% { background-position: 20% 50%; background-color: #ff7f00; }
    40% { background-position: 40% 50%; background-color: #ffff00; }
    60% { background-position: 60% 50%; background-color: #00ff00; }
    80% { background-position: 80% 50%; background-color: #0000ff; }
    100% { background-position: 100% 50%; background-color: #8b00ff; }
}

.animate-gradient {
    background-size: 200% 200%;
    animation: gradientShift 2s ease infinite;
}

.animate-rainbow {
    background-size: 300% 300%;
    animation: rainbowEffect 0.5s linear infinite;
}
</style>

