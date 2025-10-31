<script setup>
import { useForm } from '@inertiajs/vue3';
import AppLayout from '@/Layouts/AppLayout.vue';
import RecipeStepEditor from '@/Components/Recipe/RecipeStepEditor.vue';
import RecipeFormFields from '@/Components/Recipe/RecipeFormFields.vue';
import RecipeIngredientEditor from '@/Components/Recipe/RecipeIngredientEditor.vue';
import FileUpload from '@/Components/Common/FileUpload.vue';

const props = defineProps({
    units: Array,
});

const form = useForm({
    title: '',
    summary: '',
    servings: 4,
    prep_minutes: null,
    cook_minutes: null,
    difficulty: '',
    cuisine: '',
    is_public: true,
    calories: null,
    steps: [{ text: '', timer_minutes: null }],
    ingredients: [],
    images: [],
});

function submit() {
    form.post(route('recipes.store'), {
        preserveScroll: true,
    });
}
</script>

<template>
    <AppLayout title="Créer une recette">
        <template #header>
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                Créer une recette
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
                                        Ingrédients
                                    </h3>
                                    <div class="pl-10">
                                        <RecipeIngredientEditor
                                            v-model="form.ingredients"
                                            :units="units"
                                        />
                                    </div>
                                </div>

                                <div class="border-t pt-6">
                                    <h3 class="text-lg font-semibold text-gray-900 mb-4 flex items-center gap-2">
                                        <span class="flex items-center justify-center w-8 h-8 bg-green-100 text-green-700 rounded-full text-sm font-bold">3</span>
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
                                        <span class="flex items-center justify-center w-8 h-8 bg-green-100 text-green-700 rounded-full text-sm font-bold">4</span>
                                        Photos de la recette
                                    </h3>
                                    <div class="pl-10">
                                        <FileUpload
                                            v-model="form.images"
                                            multiple
                                            label=""
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
                                    :href="route('recipes.index')"
                                    class="px-6 py-2.5 bg-white border-2 border-gray-300 text-gray-700 rounded-lg hover:bg-gray-50 font-medium transition-colors"
                                >
                                    Annuler
                                </a>
                                <button
                                    type="submit"
                                    :disabled="form.processing"
                                    class="px-6 py-2.5 bg-green-600 text-white rounded-lg hover:bg-green-700 disabled:opacity-50 disabled:cursor-not-allowed font-medium shadow-sm transition-all"
                                >
                                    {{ form.processing ? 'Création...' : 'Créer la recette' }}
                                </button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </AppLayout>
</template>