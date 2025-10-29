<script setup>
import { useForm } from '@inertiajs/vue3';
import AppLayout from '@/Layouts/AppLayout.vue';
import RecipeStepEditor from '@/Components/Recipe/RecipeStepEditor.vue';
import RecipeFormFields from '@/Components/Recipe/RecipeFormFields.vue';

const props = defineProps({
    recipe: Object,
    units: Array,
});

const form = useForm({
    title: props.recipe.title,
    summary: props.recipe.summary,
    servings: props.recipe.servings,
    prep_minutes: props.recipe.prep_minutes,
    cook_minutes: props.recipe.cook_minutes,
    difficulty: props.recipe.difficulty || '',
    cuisine: props.recipe.cuisine || '',
    is_public: props.recipe.is_public,
    calories: props.recipe.calories,
    steps: props.recipe.steps.map(step => ({
        text: step.text,
        timer_seconds: step.timer_seconds,
    })),
    images: [],
});

function handleImageUpload(event) {
    const files = Array.from(event.target.files);
    form.images = files;
}

function submit() {
    form.post(route('recipes.update', props.recipe.slug), {
        _method: 'put',
    });
}
</script>

<template>
    <AppLayout title="Modifier la recette">
        <template #header>
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                Modifier la recette
            </h2>
        </template>

        <div class="py-12">
            <div class="max-w-4xl mx-auto sm:px-6 lg:px-8">
                <div class="bg-white overflow-hidden shadow-xl sm:rounded-lg p-6">
                    <form @submit.prevent="submit" class="space-y-6">
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">
                                Titre *
                            </label>
                            <input
                                v-model="form.title"
                                type="text"
                                required
                                class="w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500"
                            />
                            <div v-if="form.errors.title" class="text-red-600 text-sm mt-1">
                                {{ form.errors.title }}
                            </div>
                        </div>

                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">
                                Résumé
                            </label>
                            <textarea
                                v-model="form.summary"
                                rows="3"
                                class="w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500"
                            />
                            <div v-if="form.errors.summary" class="text-red-600 text-sm mt-1">
                                {{ form.errors.summary }}
                            </div>
                        </div>

                        <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-2">
                                    Portions *
                                </label>
                                <input
                                    v-model.number="form.servings"
                                    type="number"
                                    min="1"
                                    required
                                    class="w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500"
                                />
                            </div>

                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-2">
                                    Préparation (min)
                                </label>
                                <input
                                    v-model.number="form.prep_minutes"
                                    type="number"
                                    min="0"
                                    class="w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500"
                                />
                            </div>

                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-2">
                                    Cuisson (min)
                                </label>
                                <input
                                    v-model.number="form.cook_minutes"
                                    type="number"
                                    min="0"
                                    class="w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500"
                                />
                            </div>
                        </div>

                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-2">
                                    Difficulté
                                </label>
                                <select
                                    v-model="form.difficulty"
                                    class="w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500"
                                >
                                    <option value="">Sélectionner</option>
                                    <option value="easy">Facile</option>
                                    <option value="medium">Moyen</option>
                                    <option value="hard">Difficile</option>
                                </select>
                            </div>

                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-2">
                                    Cuisine
                                </label>
                                <input
                                    v-model="form.cuisine"
                                    type="text"
                                    class="w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500"
                                />
                            </div>
                        </div>

                        <RecipeStepEditor
                            v-model="form.steps"
                            :errors="form.errors"
                        />

                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">
                                Ajouter des images
                            </label>
                            <input
                                type="file"
                                multiple
                                accept="image/*"
                                @change="handleImageUpload"
                                class="w-full"
                            />
                            <div v-if="form.errors.images" class="text-red-600 text-sm mt-1">
                                {{ form.errors.images }}
                            </div>
                        </div>

                        <div class="flex items-center">
                            <input
                                v-model="form.is_public"
                                type="checkbox"
                                id="is_public"
                                class="rounded border-gray-300 text-blue-600 shadow-sm focus:border-blue-500 focus:ring-blue-500"
                            />
                            <label for="is_public" class="ml-2 text-sm text-gray-700">
                                Rendre cette recette publique
                            </label>
                        </div>

                        <div class="flex justify-end gap-4">
                            <a
                                :href="route('recipes.show', recipe.slug)"
                                class="px-4 py-2 bg-gray-200 text-gray-700 rounded-md hover:bg-gray-300"
                            >
                                Annuler
                            </a>
                            <button
                                type="submit"
                                :disabled="form.processing"
                                class="px-4 py-2 bg-blue-600 text-white rounded-md hover:bg-blue-700 disabled:opacity-50"
                            >
                                Mettre à jour
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </AppLayout>
</template>