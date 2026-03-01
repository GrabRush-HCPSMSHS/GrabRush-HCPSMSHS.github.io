<script setup>
import SecondaryButton from '@/Components/SecondaryButton.vue';
import { router } from '@inertiajs/vue3';

defineProps({
    data: {
        type: Object,
        required: true,
    },
});

const changePage = (url) => {
    if (url) {
        router.get(url);
    }
};
</script>

<template>
    <div class="flex items-center justify-end space-x-4">
        <p class="text-gray-700 dark:text-gray-300">
            Page {{ data.current_page }} - {{ Math.min(data.current_page * data.per_page, data.total) }}
            of {{ data.total }}
        </p>

        <div class="flex items-center justify-end space-x-2">
            <SecondaryButton
                @click="changePage(data.prev_page_url)"
                :disabled="!data.prev_page_url"
            >
                <
            </SecondaryButton>

            <SecondaryButton
                v-for="page in data.last_page"
                :disabled="data.current_page === page"
                :key="page"
                @click="changePage(`${data.path}?page=${page}`)"
                :class="{'bg-blue-500 text-dark dark:text-white': data.current_page === page}"
                class="px-4 py-2 border rounded hover:bg-gray-200 dark:hover:bg-gray-600"
            >
                {{ page }}
            </SecondaryButton>

            <SecondaryButton
                @click="changePage(data.next_page_url)"
                :disabled="!data.next_page_url"
            >
                >
            </SecondaryButton>
        </div>
    </div>
</template>
