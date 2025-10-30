<script setup>
import { Link } from '@inertiajs/vue3';

defineProps({
    href: String,
    variant: {
        type: String,
        default: 'primary'
    },
    icon: String
});

const variantClasses = {
    primary: 'text-white bg-green-600 hover:bg-green-700',
    secondary: 'text-gray-700 bg-white hover:bg-gray-50 border border-gray-300',
    tertiary: 'text-orange-700 bg-white hover:bg-orange-50 border border-orange-300'
};
</script>

<template>
    <Link
        :href="href"
        :class="['inline-flex items-center justify-center px-4 py-2 text-sm font-medium rounded-md transition', variantClasses[variant]]"
    >
        <svg v-if="icon === 'plus'" class="mr-2 h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
        </svg>
        <svg v-else-if="icon === 'book'" class="mr-2 h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.746 0 3.332.477 4.5 1.253v13C19.832 18.477 18.246 18 16.5 18c-1.746 0-3.332.477-4.5 1.253" />
        </svg>
        <svg v-else-if="icon === 'search'" class="mr-2 h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
        </svg>
        <slot />
    </Link>
</template>
