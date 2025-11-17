<template>
    <div class="space-y-4 md:space-y-6">
        <div>
            <h3 class="text-xl md:text-2xl font-bold text-gray-900 mb-3 md:mb-4">{{ t('cook.ingredients_needed') }}</h3>
            <div class="bg-gray-50 rounded-lg p-4 md:p-6">
                <p class="text-base md:text-lg text-gray-600 mb-3 md:mb-4">{{ t('cook.for_servings', { servings: recipe.servings }) }}</p>
                <ul class="space-y-2 md:space-y-3">
                    <li v-for="(ingredient, index) in ingredients" :key="index" class="flex items-center text-sm md:text-lg">
                        <div class="w-2 h-2 bg-green-600 rounded-full mr-2 md:mr-3 flex-shrink-0"></div>
                        <span class="font-medium mr-2">{{ ingredient.quantity }} {{ ingredient.unit }}</span>
                        <span>{{ ingredient.name }}</span>
                    </li>
                </ul>
            </div>
        </div>

        <div class="bg-blue-50 border-l-4 border-blue-500 p-4 md:p-6 rounded-r-lg">
            <p class="text-sm md:text-lg text-blue-900">
                ⏱️ {{ t('cook.preparation_time', { time: recipe.prep_time }) }}
            </p>
            <p class="text-sm md:text-lg text-blue-900 mt-2">
                🔥 {{ t('cook.cooking_time', { time: recipe.cook_time }) }}
            </p>
        </div>
    </div>
</template>

<script setup>
import { useI18n } from 'vue-i18n';

const { t } = useI18n();

defineProps({
    recipe: {
        type: Object,
        required: true
    },
    ingredients: {
        type: Array,
        required: true
    }
});
</script>
