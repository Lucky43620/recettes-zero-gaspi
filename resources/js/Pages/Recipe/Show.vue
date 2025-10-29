<script setup>
import AppLayout from '@/Layouts/AppLayout.vue';
import { Link } from '@inertiajs/vue3';
import { router } from '@inertiajs/vue3';

const props = defineProps({
    recipe: Object,
    canEdit: Boolean,
    canDelete: Boolean,
});

function getDifficultyLabel(level) {
    const labels = {
        easy: 'Facile',
        medium: 'Moyen',
        hard: 'Difficile',
    };
    return labels[level] || level;
}

function deleteRecipe() {
    if (confirm('Êtes-vous sûr de vouloir supprimer cette recette ?')) {
        router.delete(route('recipes.destroy', props.recipe.slug));
    }
}
</script>

<template>
    <AppLayout :title="recipe.title">
        <template #header>
            <div class="flex justify-between items-center">
                <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                    {{ recipe.title }}
                </h2>
                <div v-if="canEdit || canDelete" class="flex gap-2">
                    <Link
                        v-if="canEdit"
                        :href="route('recipes.edit', recipe.slug)"
                        class="px-4 py-2 bg-blue-600 text-white rounded-md hover:bg-blue-700"
                    >
                        Modifier
                    </Link>
                    <button
                        v-if="canDelete"
                        @click="deleteRecipe"
                        class="px-4 py-2 bg-red-600 text-white rounded-md hover:bg-red-700"
                    >
                        Supprimer
                    </button>
                </div>
            </div>
        </template>

        <div class="py-12">
            <div class="max-w-4xl mx-auto sm:px-6 lg:px-8">
                <div class="bg-white overflow-hidden shadow-xl sm:rounded-lg">
                    <div v-if="recipe.media && recipe.media.length > 0" class="h-96 bg-gray-100">
                        <img
                            :src="recipe.media[0].original_url"
                            :alt="recipe.title"
                            class="w-full h-full object-cover"
                        />
                    </div>
                    <div v-else class="h-96 bg-gray-200 flex items-center justify-center">
                        <span class="text-gray-400 text-xl">Pas d'image</span>
                    </div>

                    <div class="p-6">
                        <div class="mb-6">
                            <p class="text-gray-700 text-lg">{{ recipe.summary }}</p>
                        </div>

                        <div class="grid grid-cols-2 md:grid-cols-4 gap-4 mb-6 p-4 bg-gray-50 rounded-lg">
                            <div>
                                <p class="text-sm text-gray-600">Portions</p>
                                <p class="text-lg font-semibold">{{ recipe.servings }}</p>
                            </div>
                            <div v-if="recipe.prep_minutes">
                                <p class="text-sm text-gray-600">Préparation</p>
                                <p class="text-lg font-semibold">{{ recipe.prep_minutes }} min</p>
                            </div>
                            <div v-if="recipe.cook_minutes">
                                <p class="text-sm text-gray-600">Cuisson</p>
                                <p class="text-lg font-semibold">{{ recipe.cook_minutes }} min</p>
                            </div>
                            <div v-if="recipe.difficulty">
                                <p class="text-sm text-gray-600">Difficulté</p>
                                <p class="text-lg font-semibold">{{ getDifficultyLabel(recipe.difficulty) }}</p>
                            </div>
                        </div>

                        <div class="mb-6">
                            <h3 class="text-xl font-semibold text-gray-900 mb-4">Étapes</h3>
                            <ol class="space-y-4">
                                <li
                                    v-for="(step, index) in recipe.steps"
                                    :key="step.id"
                                    class="flex gap-4 p-4 bg-gray-50 rounded-lg"
                                >
                                    <span class="flex-shrink-0 w-8 h-8 bg-blue-600 text-white rounded-full flex items-center justify-center font-semibold">
                                        {{ index + 1 }}
                                    </span>
                                    <div class="flex-1">
                                        <p class="text-gray-800">{{ step.text }}</p>
                                        <p v-if="step.timer_seconds" class="text-sm text-blue-600 mt-2">
                                            ⏱️ {{ Math.floor(step.timer_seconds / 60) }}:{{ String(step.timer_seconds % 60).padStart(2, '0') }}
                                        </p>
                                    </div>
                                </li>
                            </ol>
                        </div>

                        <div class="pt-6 border-t border-gray-200">
                            <div class="flex items-center justify-between text-sm text-gray-600">
                                <span>Par {{ recipe.author.name }}</span>
                                <span v-if="recipe.cuisine">{{ recipe.cuisine }}</span>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="mt-6">
                    <Link
                        :href="route('recipes.index')"
                        class="text-blue-600 hover:text-blue-800"
                    >
                        ← Retour aux recettes
                    </Link>
                </div>
            </div>
        </div>
    </AppLayout>
</template>