<script setup>
import { Link } from '@inertiajs/vue3';

const props = defineProps({
    recipe: Object,
});

function getDifficultyLabel(level) {
    const labels = {
        easy: 'Facile',
        medium: 'Moyen',
        hard: 'Difficile',
    };
    return labels[level] || level;
}
</script>

<template>
    <Link
        :href="route('recipes.show', recipe.slug)"
        class="bg-white overflow-hidden shadow-xl sm:rounded-lg hover:shadow-2xl transition-shadow block"
    >
        <div class="h-48 bg-gray-200 flex items-center justify-center">
            <span class="text-gray-400">Image</span>
        </div>
        <div class="p-6">
            <h3 class="text-lg font-semibold text-gray-900 mb-2">
                {{ recipe.title }}
            </h3>
            <p class="text-sm text-gray-600 mb-4 line-clamp-2">
                {{ recipe.summary }}
            </p>
            <div class="flex justify-between items-center text-sm text-gray-500">
                <span>{{ recipe.author.name }}</span>
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