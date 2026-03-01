<script setup>
import { computed } from 'vue';
import ProductImageSVG from '@/Components/Product/ProductImageSVG.vue';

const props = defineProps({
    path: {
        type: String,
        default: null,
    },
    alt: {
        type: String,
        required: true,
    },
});

const hasPath = computed(() => !!props.path);

const isBlob = computed(() => hasPath.value && props.path.startsWith('blob:'));

const src = computed(() => {
    if (!hasPath.value) {
        return '';
    }
    if (isBlob.value) {
        return props.path;
    }
    if (props.path.startsWith('http')) {
        return props.path;
    }
    return `/uploads/${props.path}`;
});

</script>

<template>
    <ProductImageSVG>
        <img
            v-if="hasPath"
            class="object-contain w-full h-full"
            :src="src"
            :alt="alt"
        />
    </ProductImageSVG>
</template>
