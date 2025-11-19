<script setup>
import { useI18n } from 'vue-i18n';
import { useMediaConversions } from '@/composables/useMediaConversions';

const { t } = useI18n();
const { getConversionUrl, getSrcset, getSizes, getRecipeImage } = useMediaConversions();

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
        <div v-if="showTitle" class="px-4 md:px-6 pt-6 pb-4 md:pb-6">
            <h1 class="text-2xl md:text-3xl lg:text-4xl font-bold text-gray-900 text-center">{{ recipe.title }}</h1>
        </div>
        <div class="h-64 md:h-96 bg-gray-100 rounded-lg overflow-hidden">
            <img
                :src="getRecipeImage(recipe, 'large')"
                :srcset="recipe.media && recipe.media.length > 0 ? getSrcset(recipe.media[0]) : ''"
                :sizes="getSizes('header')"
                :alt="recipe.title"
                loading="eager"
                class="w-full h-full object-cover"
            />
        </div>
    </div>
</template>
