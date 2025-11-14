<script setup>
import { useI18n } from 'vue-i18n';
import { useDifficultyLabels } from '@/composables/useDifficultyLabels';

const { t } = useI18n();
const { getDifficultyLabel } = useDifficultyLabels();

defineProps({
    recipe: Object,
});
</script>

<template>
    <div class="grid grid-cols-2 md:grid-cols-4 gap-4 mb-6 p-4 bg-gray-50 rounded-lg">
        <div>
            <p class="text-sm text-gray-600">{{ t('recipe.servings') }}</p>
            <p class="text-lg font-semibold">{{ recipe.servings }}</p>
        </div>
        <div v-if="recipe.prep_minutes">
            <p class="text-sm text-gray-600">{{ t('recipe.preparation') }}</p>
            <p class="text-lg font-semibold">{{ recipe.prep_minutes }} min</p>
        </div>
        <div v-if="recipe.cook_minutes">
            <p class="text-sm text-gray-600">{{ t('recipe.cooking') }}</p>
            <p class="text-lg font-semibold">{{ recipe.cook_minutes }} min</p>
        </div>
        <div v-if="recipe.difficulty">
            <p class="text-sm text-gray-600">{{ t('recipe.difficulty') }}</p>
            <p class="text-lg font-semibold">{{ getDifficultyLabel(recipe.difficulty) }}</p>
        </div>
    </div>
</template>
