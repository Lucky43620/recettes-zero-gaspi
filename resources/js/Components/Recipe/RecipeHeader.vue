<script setup>
import { useI18n } from 'vue-i18n';
import { useMediaConversions } from '@/composables/useMediaConversions';

const { t } = useI18n();
const { getConversionUrl, getSrcset, getSizes } = useMediaConversions();

defineProps({
    recipe: Object,
    showTitle: {
        type: Boolean,
        default: false,
    },
});
</script>

<template>
    <div>
        <div v-if="showTitle" class="mb-4 md:mb-6">
            <h1 class="text-2xl md:text-3xl font-bold text-gray-900 mb-2">{{ recipe.title }}</h1>
        </div>
        <div v-if="recipe.media && recipe.media.length > 0" class="h-64 md:h-96 bg-gray-100 rounded-lg overflow-hidden">
            <img
                :src="getConversionUrl(recipe.media[0], 'large')"
                :srcset="getSrcset(recipe.media[0])"
                :sizes="getSizes('header')"
                :alt="recipe.title"
                loading="eager"
                class="w-full h-full object-cover"
            />
        </div>
        <div v-else class="h-64 md:h-96 bg-gray-200 flex items-center justify-center rounded-lg">
            <span class="text-gray-400 text-base md:text-xl">{{ t('recipe.no_image') }}</span>
        </div>
    </div>
</template>
