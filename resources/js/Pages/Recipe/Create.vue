<script setup>
import { useForm } from '@inertiajs/vue3';
import AppLayout from '@/Layouts/AppLayout.vue';
import RecipeStepEditor from '@/Components/Recipe/RecipeStepEditor.vue';
import RecipeFormFields from '@/Components/Recipe/RecipeFormFields.vue';

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
    steps: [{ text: '', timer_seconds: null }],
    images: [],
});

function handleImageUpload(event) {
    const files = Array.from(event.target.files);
    form.images = files;
}

function submit() {
    form.post(route('recipes.store'), {
        onSuccess: () => {
            console.log('Recipe created successfully');
        },
        onError: (errors) => {
            console.error('Creation failed:', errors);
        },
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
                <div class="bg-white overflow-hidden shadow-xl sm:rounded-lg p-6">
                    <form @submit.prevent="submit" class="space-y-6">
                        <RecipeFormFields :form="form" />

                        <RecipeStepEditor
                            v-model="form.steps"
                            :errors="form.errors"
                        />

                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">
                                Images
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
                                :href="route('recipes.index')"
                                class="px-4 py-2 bg-gray-200 text-gray-700 rounded-md hover:bg-gray-300"
                            >
                                Annuler
                            </a>
                            <button
                                type="submit"
                                :disabled="form.processing"
                                class="px-4 py-2 bg-blue-600 text-white rounded-md hover:bg-blue-700 disabled:opacity-50"
                            >
                                Créer la recette
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </AppLayout>
</template>