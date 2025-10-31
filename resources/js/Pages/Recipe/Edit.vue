<script setup>
import { useForm } from '@inertiajs/vue3';
import AppLayout from '@/Layouts/AppLayout.vue';
import RecipeStepEditor from '@/Components/Recipe/RecipeStepEditor.vue';
import RecipeFormFields from '@/Components/Recipe/RecipeFormFields.vue';
import FileUpload from '@/Components/Common/FileUpload.vue';

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
        timer_minutes: step.timer_minutes,
    })),
    images: [],
});

function submit() {
    form.transform((data) => ({
        ...data,
        _method: 'PUT',
    })).post(route('recipes.update', props.recipe.slug), {
        preserveScroll: true,
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
                <div class="bg-white overflow-hidden shadow-xl sm:rounded-lg">
                    <div class="p-6 md:p-8">
                        <form @submit.prevent="submit" class="space-y-8">
                            <div class="space-y-6">
                                <div>
                                    <h3 class="text-lg font-semibold text-gray-900 mb-4 flex items-center gap-2">
                                        <span class="flex items-center justify-center w-8 h-8 bg-green-100 text-green-700 rounded-full text-sm font-bold">1</span>
                                        Informations générales
                                    </h3>
                                    <div class="pl-10">
                                        <RecipeFormFields :form="form" />
                                    </div>
                                </div>

                                <div class="border-t pt-6">
                                    <h3 class="text-lg font-semibold text-gray-900 mb-4 flex items-center gap-2">
                                        <span class="flex items-center justify-center w-8 h-8 bg-green-100 text-green-700 rounded-full text-sm font-bold">2</span>
                                        Étapes de préparation
                                    </h3>
                                    <div class="pl-10">
                                        <RecipeStepEditor
                                            v-model="form.steps"
                                            :errors="form.errors"
                                        />
                                    </div>
                                </div>

                                <div class="border-t pt-6">
                                    <h3 class="text-lg font-semibold text-gray-900 mb-4 flex items-center gap-2">
                                        <span class="flex items-center justify-center w-8 h-8 bg-green-100 text-green-700 rounded-full text-sm font-bold">3</span>
                                        Photos de la recette
                                    </h3>
                                    <div class="pl-10">
                                        <div v-if="recipe.media && recipe.media.length > 0" class="mb-4">
                                            <p class="text-sm text-gray-600 mb-2">Images actuelles:</p>
                                            <div class="grid grid-cols-2 md:grid-cols-3 gap-4">
                                                <div
                                                    v-for="media in recipe.media"
                                                    :key="media.id"
                                                    class="relative"
                                                >
                                                    <img
                                                        :src="media.original_url"
                                                        alt="Image de la recette"
                                                        class="w-full h-32 object-cover rounded-lg border-2 border-gray-200"
                                                    />
                                                </div>
                                            </div>
                                        </div>

                                        <FileUpload
                                            v-model="form.images"
                                            multiple
                                            label="Ajouter de nouvelles images"
                                            hint="PNG, JPG, GIF jusqu'à 10MB par image"
                                            :error="form.errors.images"
                                        />
                                    </div>
                                </div>

                                <div class="border-t pt-6">
                                    <div class="pl-10">
                                        <div class="bg-gray-50 rounded-lg p-4 border border-gray-200">
                                            <label class="flex items-center cursor-pointer">
                                                <input
                                                    v-model="form.is_public"
                                                    type="checkbox"
                                                    class="rounded border-gray-300 text-green-600 shadow-sm focus:border-green-500 focus:ring-green-500 h-5 w-5"
                                                />
                                                <div class="ml-3">
                                                    <span class="text-sm font-medium text-gray-900">Rendre cette recette publique</span>
                                                    <p class="text-xs text-gray-500 mt-0.5">
                                                        Les recettes publiques sont visibles par tous les utilisateurs
                                                    </p>
                                                </div>
                                            </label>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <div class="border-t pt-6 flex justify-end gap-4">
                                <a
                                    :href="route('recipes.show', recipe.slug)"
                                    class="px-6 py-2.5 bg-white border-2 border-gray-300 text-gray-700 rounded-lg hover:bg-gray-50 font-medium transition-colors"
                                >
                                    Annuler
                                </a>
                                <button
                                    type="submit"
                                    :disabled="form.processing"
                                    class="px-6 py-2.5 bg-green-600 text-white rounded-lg hover:bg-green-700 disabled:opacity-50 disabled:cursor-not-allowed font-medium shadow-sm transition-all"
                                >
                                    {{ form.processing ? 'Mise à jour...' : 'Mettre à jour' }}
                                </button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </AppLayout>
</template>