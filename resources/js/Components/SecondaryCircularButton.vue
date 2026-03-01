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
            dark:border-gray-400

            bg-white
            dark:bg-gray-900

            hover:bg-gray-200
            dark:hover:bg-gray-700

            active:bg-gray-900
            dark:active:bg-gray-500

            active:scale-95

            text-gray-800
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
