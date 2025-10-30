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
        timer_minutes: step.timer_minutes,
    })),
    images: [],
});

function handleImageUpload(event) {
    const files = Array.from(event.target.files);
    form.images = files;
}

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
                <div class="bg-white overflow-hidden shadow-xl sm:rounded-lg p-6">
                    <form @submit.prevent="submit" class="space-y-6">
                        <RecipeFormFields :form="form" />

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