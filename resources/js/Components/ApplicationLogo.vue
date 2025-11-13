<script setup>
import { computed } from 'vue';
import { useI18n } from 'vue-i18n';

const { t } = useI18n();

const props = defineProps({
    alt: {
        type: String,
        default: ''
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

<template>
    <img src="/logo.png" :alt="alt || t('app.name')" class="block w-auto" :class="sizeClass" />
</template>
