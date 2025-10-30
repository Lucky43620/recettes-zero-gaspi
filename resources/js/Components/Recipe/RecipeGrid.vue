<script setup>
import RecipeCard from './RecipeCard.vue';

defineProps({
    recipes: Array,
    emptyMessage: {
        type: String,
        default: 'Aucune recette trouvée'
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
        {{ emptyMessage }}
    </div>
</template>
