<script setup>
import { ArrowLeftIcon } from '@heroicons/vue/24/outline';
import { Link } from '@inertiajs/vue3';

const props = defineProps({
    href: {
        type: String,
        default: null
    },
    label: {
        type: String,
        default: 'Retour'
    }
});

function goBack() {
    window.history.back();
}
</script>

<template>
    <Link
        v-if="href"
        :href="href"
        class="inline-flex items-center gap-2 text-green-600 hover:text-green-700 font-medium transition-colors group"
    >
        <ArrowLeftIcon class="h-5 w-5 group-hover:-translate-x-1 transition-transform" />
        {{ label }}
    </Link>
    <button
        v-else
        @click="goBack"
        class="inline-flex items-center gap-2 text-green-600 hover:text-green-700 font-medium transition-colors group"
    >
        <ArrowLeftIcon class="h-5 w-5 group-hover:-translate-x-1 transition-transform" />
        {{ label }}
    </button>
</template>
