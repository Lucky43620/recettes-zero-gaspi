<script setup>
import AppLayout from '@/Layouts/AppLayout.vue';
import RecipeCard from '@/Components/Recipe/RecipeCard.vue';
import { useForm } from '@inertiajs/vue3';

const props = defineProps({
    collection: Object,
    canEdit: Boolean,
});

const deleteForm = useForm({});

function deleteCollection() {
    if (confirm('Supprimer cette collection ?')) {
        deleteForm.delete(route('collections.destroy', props.collection.id));
    }
}
</script>

<template>
    <AppLayout :title="collection.name">
        <template #header>
            <div class="flex justify-between items-center">
                <div>
                    <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                        {{ collection.name }}
                    </h2>
                    <p v-if="collection.description" class="text-sm text-gray-600 mt-1">
                        {{ collection.description }}
                    </p>
                </div>
                <button
                    v-if="canEdit"
                    @click="deleteCollection"
                    class="px-4 py-2 bg-red-600 text-white rounded-md hover:bg-red-700"
                >
                    Supprimer
                </button>
            </div>
        </template>

        <div class="py-12">
            <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
                <div class="mb-6 flex items-center justify-between">
                    <div class="text-sm text-gray-600">
                        {{ collection.recipes.length }} recettes •
                        <span v-if="collection.is_public" class="text-green-600">Publique</span>
                        <span v-else>Privée</span>
                    </div>
                    <div class="text-sm text-gray-600">
                        Par {{ collection.user.name }}
                    </div>
                </div>

                <div v-if="collection.recipes.length" class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                    <RecipeCard
                        v-for="recipe in collection.recipes"
                        :key="recipe.id"
                        :recipe="recipe"
                    />
                </div>

                <div v-else class="bg-white rounded-lg shadow p-8 text-center">
                    <p class="text-gray-600">
                        Cette collection est vide
                    </p>
                </div>
            </div>
        </div>
    </AppLayout>
</template>
