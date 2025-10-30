<template>
    <img src="/logo.png" :alt="alt" class="block w-auto" :class="sizeClass" />
</template>

<script setup>
import { computed } from 'vue';

const props = defineProps({
    alt: {
        type: String,
        default: 'Recettes Zéro Gaspi'
    },
    size: {
        type: String,
        default: 'md'
    }
});

const sizeClass = computed(() => {
    const sizes = {
        'sm': 'h-8',
        'md': 'h-10',
        'lg': 'h-20'
    };
    return sizes[props.size] || sizes.md;
});
</script>
