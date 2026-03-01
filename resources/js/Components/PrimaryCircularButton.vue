<script setup>
const props = defineProps({
    onClick: Function,
});

const handleClick = (event) => {
    if (props.onClick) props.onClick(event);
};
</script>

<template>
    <button
        class="
            border
            border-gray-200
            dark:border-gray-900

            bg-pink-500
            dark:bg-pink-800

            hover:bg-pink-600
            dark:hover:bg-pink-800

            active:bg-pink-900
            dark:active:bg-pink-500

            active:scale-95

            text-white
            dark:text-gray-200

            text-xl
            md:text-2xl

            font-bold

            transition

            w-8 h-8 sm:w-12 sm:h-12 md:w-10 md:h-10
            flex items-center justify-center
            rounded-full
            shadow-md
            "
        @click="handleClick"
    >
        <slot />
    </button>
</template>
