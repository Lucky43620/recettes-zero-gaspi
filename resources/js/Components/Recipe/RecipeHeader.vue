<script setup>
import { useI18n } from 'vue-i18n';

const { t } = useI18n();

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
        <div v-if="showTitle" class="mb-6">
            <h1 class="text-3xl font-bold text-gray-900 mb-2">{{ recipe.title }}</h1>
        </div>
        <div v-if="recipe.media && recipe.media.length > 0" class="h-96 bg-gray-100">
            <img
                :src="recipe.media[0].original_url"
                :alt="recipe.title"
                class="w-full h-full object-cover"
            />
        </div>
        <div v-else class="h-96 bg-gray-200 flex items-center justify-center">
            <span class="text-gray-400 text-xl">{{ t('recipe.no_image') }}</span>
        </div>
    </div>
</template>
