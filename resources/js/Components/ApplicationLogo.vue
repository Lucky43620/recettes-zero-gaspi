<script setup>
import { computed } from 'vue';
import { Link } from '@inertiajs/vue3';
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
    },
    linkToHome: {
        type: Boolean,
        default: false
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
    <Link v-if="linkToHome" :href="route('home')">
        <img src="/logo.png" :alt="alt || t('app.name')" class="block w-auto" :class="sizeClass" />
    </Link>
    <img v-else src="/logo.png" :alt="alt || t('app.name')" class="block w-auto" :class="sizeClass" />
</template>
