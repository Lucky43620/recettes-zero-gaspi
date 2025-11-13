<script setup>
import { useI18n } from 'vue-i18n';
import RecipeCard from './RecipeCard.vue';

const { t } = useI18n();

defineProps({
    recipes: Array,
    emptyMessage: {
        type: String,
        default: ''
    },
    fromMyRecipes: {
        type: Boolean,
        default: false
    }
});
</script>

<template>
    <div v-if="recipes && recipes.length" class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
        <RecipeCard
            v-for="recipe in recipes"
            :key="recipe.id"
            :recipe="recipe"
            :from-my-recipes="fromMyRecipes"
        />
    </div>
    <div v-else class="text-center text-gray-500 py-12">
        {{ emptyMessage || t('recipe.no_recipes_found') }}
    </div>
</template>
