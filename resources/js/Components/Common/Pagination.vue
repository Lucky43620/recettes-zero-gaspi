<script setup>
import { Link } from '@inertiajs/vue3';
import { useI18n } from 'vue-i18n';

const { t } = useI18n();

defineProps({
    links: {
        type: Array,
        required: true,
    },
});
</script>

<template>
    <nav v-if="links && links.length > 3" class="flex gap-2">
        <component
            v-for="(link, index) in links"
            :key="index"
            :is="link.url ? Link : 'span'"
            :href="link.url"
            :class="{
                'bg-green-600 text-white': link.active,
                'bg-white text-gray-700 hover:bg-gray-50': !link.active && link.url,
                'cursor-not-allowed opacity-50': !link.url,
            }"
            class="px-4 py-2 rounded-md border inline-flex items-center"
        >
            <span v-if="link.label.includes('Previous') || link.label.includes('pagination.previous')">← {{ t('common.previous') }}</span>
            <span v-else-if="link.label.includes('Next') || link.label.includes('pagination.next')">{{ t('common.next') }} →</span>
            <span v-else>{{ link.label.replace('&laquo;', '').replace('&raquo;', '').trim() }}</span>
        </component>
    </nav>
</template>