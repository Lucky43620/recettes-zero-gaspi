<script setup>
import { ArrowLeftIcon } from '@heroicons/vue/24/outline';
import { Link } from '@inertiajs/vue3';
import { useI18n } from 'vue-i18n';

const { t } = useI18n();

const props = defineProps({
    href: {
        type: String,
        default: null
    },
    label: {
        type: String,
        default: null
    }
});

function goBack() {
    window.history.back();
}

const displayLabel = props.label || t('common.back');
</script>

<template>
    <Link
        v-if="href"
        :href="href"
        class="inline-flex items-center gap-2 text-green-600 hover:text-green-700 font-medium transition-colors group"
    >
        <ArrowLeftIcon class="h-5 w-5 group-hover:-translate-x-1 transition-transform" />
        {{ displayLabel }}
    </Link>
    <button
        v-else
        @click="goBack"
        class="inline-flex items-center gap-2 text-green-600 hover:text-green-700 font-medium transition-colors group"
    >
        <ArrowLeftIcon class="h-5 w-5 group-hover:-translate-x-1 transition-transform" />
        {{ displayLabel }}
    </button>
</template>
