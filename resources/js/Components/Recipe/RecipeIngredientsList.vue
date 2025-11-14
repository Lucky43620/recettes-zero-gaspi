<script setup>
import { useI18n } from 'vue-i18n';

const { t } = useI18n();

defineProps({
    ingredients: Array,
});
</script>

<template>
    <div v-if="ingredients && ingredients.length > 0" class="mb-6">
        <h3 class="text-lg md:text-xl font-semibold text-gray-900 mb-3 md:mb-4">{{ t('recipe.ingredients') }}</h3>
        <div class="bg-white border border-gray-200 rounded-lg divide-y divide-gray-200">
            <div
                v-for="ingredient in ingredients"
                :key="ingredient.id"
                class="px-3 md:px-4 py-2.5 md:py-3 flex items-center justify-between hover:bg-gray-50 text-sm md:text-base"
            >
                <span class="text-gray-800">{{ ingredient.name }}</span>
                <span v-if="ingredient.pivot.quantity" class="text-gray-600 font-medium whitespace-nowrap ml-2">
                    {{ ingredient.pivot.quantity }} {{ ingredient.pivot.unit_code || '' }}
                </span>
            </div>
        </div>
    </div>
</template>
