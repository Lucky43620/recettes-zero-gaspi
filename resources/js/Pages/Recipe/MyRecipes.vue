<script setup>
import AppLayout from '@/Layouts/AppLayout.vue';
import { Link } from '@inertiajs/vue3';
import RecipeCard from '@/Components/Recipe/RecipeCard.vue';
import RecipeFilters from '@/Components/Recipe/RecipeFilters.vue';
import Pagination from '@/Components/Common/Pagination.vue';
import EmptyState from '@/Components/Common/EmptyState.vue';

const props = defineProps({
    recipes: Object,
    filters: Object,
});
</script>

<template>
    <AppLayout title="Mes Recettes">
        <template #header>
            <div class="flex justify-between items-center">
                <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                    Mes Recettes
                </h2>
                <Link
                    :href="route('recipes.create')"
                    class="bg-green-600 text-white px-4 py-2 rounded-md hover:bg-green-700"
                >
                    Créer une recette
                </Link>
            </div>
        </template>

        <div class="py-12">
            <div class="max-w-[1920px] mx-auto sm:px-6 lg:px-8 min-h-[60vh]">
                <div class="mb-6 bg-white rounded-lg shadow p-4">
                    <div class="flex gap-4 items-center">
                        <div class="flex gap-2">
                            <Link
                                :href="route('recipes.my')"
                                :class="[
                                    'px-4 py-2 rounded-md text-sm font-medium transition',
                                    !filters.visibility ? 'bg-green-600 text-white' : 'bg-gray-100 text-gray-700 hover:bg-gray-200'
                                ]"
                            >
                                Toutes
                            </Link>
                            <Link
                                :href="route('recipes.my', { visibility: 'public' })"
                                :class="[
                                    'px-4 py-2 rounded-md text-sm font-medium transition',
                                    filters.visibility === 'public' ? 'bg-orange-600 text-white' : 'bg-gray-100 text-gray-700 hover:bg-gray-200'
                                ]"
                            >
                                Publiques
                            </Link>
                            <Link
                                :href="route('recipes.my', { visibility: 'private' })"
                                :class="[
                                    'px-4 py-2 rounded-md text-sm font-medium transition',
                                    filters.visibility === 'private' ? 'bg-orange-600 text-white' : 'bg-gray-100 text-gray-700 hover:bg-gray-200'
                                ]"
                            >
                                Privées
                            </Link>
                        </div>
                    </div>
                </div>

                <RecipeFilters :filters="filters" class="mb-6" />

                <div v-if="recipes.data.length" class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                    <div v-for="recipe in recipes.data" :key="recipe.id" class="relative">
                        <RecipeCard :recipe="recipe" :from-my-recipes="true" />
                        <div class="absolute top-2 right-2">
                            <span
                                :class="[
                                    'px-2 py-1 text-xs font-medium rounded-full',
                                    recipe.is_public
                                        ? 'bg-green-100 text-green-800'
                                        : 'bg-orange-100 text-orange-800'
                                ]"
                            >
                                {{ recipe.is_public ? 'Public' : 'Privé' }}
                            </span>
                        </div>
                    </div>
                </div>
                <EmptyState
                    v-else
                    icon="book"
                    title="Aucune recette trouvée"
                    message="Commencez par créer votre première recette"
                    action-label="Créer une recette"
                    :action-href="route('recipes.create')"
                />

                <div v-if="recipes.links" class="mt-6 flex justify-center">
                    <Pagination :links="recipes.links" />
                </div>
            </div>
        </div>
    </AppLayout>
</template>
