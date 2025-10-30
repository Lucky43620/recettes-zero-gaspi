<script setup>
import { Link } from '@inertiajs/vue3';
import { useDifficultyLabels } from '@/composables/useDifficultyLabels';
import { computed } from 'vue';

const props = defineProps({
    recipe: Object,
    fromMyRecipes: {
        type: Boolean,
        default: false
    }
});

const { getDifficultyLabel } = useDifficultyLabels();

const recipeUrl = computed(() => {
    const params = props.fromMyRecipes ? { from: 'my' } : {};
    return route('recipes.show', [props.recipe.slug, params]);
});
</script>

<template>
    <Link
        :href="recipeUrl"
        class="bg-white overflow-hidden shadow-xl sm:rounded-lg hover:shadow-2xl transition-shadow block"
    >
        <div v-if="recipe.media && recipe.media.length > 0" class="h-48 bg-gray-100">
            <img
                :src="recipe.media[0].original_url"
                :alt="recipe.title"
                class="w-full h-full object-cover"
            />
        </div>
        <div v-else class="h-48 bg-gray-200 flex items-center justify-center">
            <span class="text-gray-400">Pas d'image</span>
        </div>
        <div class="p-6">
            <h3 class="text-lg font-semibold text-gray-900 mb-2">
                {{ recipe.title }}
            </h3>
            <p class="text-sm text-gray-600 mb-4 line-clamp-2">
                {{ recipe.summary }}
            </p>
            <div class="flex justify-between items-center text-sm text-gray-500">
                <span v-if="recipe.author">{{ recipe.author.name }}</span>
                <span v-else>Anonyme</span>
                <span v-if="recipe.difficulty">
                    {{ getDifficultyLabel(recipe.difficulty) }}
                </span>
            </div>
            <div class="flex justify-between items-center text-sm text-gray-500 mt-2">
                <span v-if="recipe.prep_minutes || recipe.cook_minutes">
                    {{ (recipe.prep_minutes || 0) + (recipe.cook_minutes || 0) }} min
                </span>
                <span v-if="recipe.rating_count > 0 && recipe.rating_avg">
                    ⭐ {{ parseFloat(recipe.rating_avg).toFixed(1) }} ({{ recipe.rating_count }})
                </span>
            </div>
        </div>
    </Link>
</template>