<script setup>
import { Link } from '@inertiajs/vue3';
import { useDifficultyLabels } from '@/composables/useDifficultyLabels';
import { computed } from 'vue';
import { ClockIcon, UserIcon, StarIcon } from '@heroicons/vue/24/outline';
import { StarIcon as StarIconSolid } from '@heroicons/vue/24/solid';

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

const difficultyColor = computed(() => {
    const colors = {
        easy: 'bg-green-100 text-green-700 border-green-200',
        medium: 'bg-orange-100 text-orange-700 border-orange-200',
        hard: 'bg-red-100 text-red-700 border-red-200',
    };
    return colors[props.recipe.difficulty] || 'bg-gray-100 text-gray-700 border-gray-200';
});
</script>

<template>
    <Link
        :href="recipeUrl"
        class="group bg-white overflow-hidden rounded-xl shadow-md hover:shadow-xl transition-all duration-300 block border border-gray-100"
    >
        <div class="relative overflow-hidden">
            <div v-if="recipe.media && recipe.media.length > 0" class="h-56 bg-gray-100">
                <img
                    :src="recipe.media[0].original_url"
                    :alt="recipe.title"
                    class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-300"
                />
            </div>
            <div v-else class="h-56 bg-gradient-to-br from-green-100 to-orange-100 flex items-center justify-center">
                <svg class="w-20 h-20 text-gray-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z" />
                </svg>
            </div>

            <div v-if="recipe.difficulty" :class="['absolute top-3 right-3 px-3 py-1 rounded-full text-xs font-semibold border backdrop-blur-sm bg-white/90', difficultyColor]">
                {{ getDifficultyLabel(recipe.difficulty) }}
            </div>
        </div>

        <div class="p-5">
            <h3 class="text-lg font-bold text-gray-900 mb-2 group-hover:text-green-600 transition-colors line-clamp-1">
                {{ recipe.title }}
            </h3>

            <p class="text-sm text-gray-600 mb-4 line-clamp-2 leading-relaxed">
                {{ recipe.summary || 'Aucune description disponible' }}
            </p>

            <div class="flex items-center gap-3 text-sm text-gray-500 mb-3">
                <div class="flex items-center gap-1.5">
                    <UserIcon class="h-4 w-4" />
                    <span class="truncate" v-if="recipe.author">{{ recipe.author.name }}</span>
                    <span v-else>Anonyme</span>
                </div>

                <div v-if="recipe.prep_minutes || recipe.cook_minutes" class="flex items-center gap-1.5">
                    <ClockIcon class="h-4 w-4" />
                    <span>{{ (recipe.prep_minutes || 0) + (recipe.cook_minutes || 0) }} min</span>
                </div>
            </div>

            <div v-if="recipe.rating_count > 0 && recipe.rating_avg" class="flex items-center gap-2 pt-3 border-t border-gray-100">
                <div class="flex items-center">
                    <StarIconSolid v-for="n in 5" :key="n" :class="[
                        'h-4 w-4',
                        n <= Math.round(parseFloat(recipe.rating_avg)) ? 'text-yellow-400' : 'text-gray-300'
                    ]" />
                </div>
                <span class="text-sm font-semibold text-gray-700">
                    {{ parseFloat(recipe.rating_avg).toFixed(1) }}
                </span>
                <span class="text-xs text-gray-500">
                    ({{ recipe.rating_count }} {{ recipe.rating_count > 1 ? 'avis' : 'avis' }})
                </span>
            </div>
        </div>
    </Link>
</template>