<script setup>
import AppLayout from '@/Layouts/AppLayout.vue';
import { Link } from '@inertiajs/vue3';
import RecipeCard from '@/Components/Recipe/RecipeCard.vue';
import RecipeFilters from '@/Components/Recipe/RecipeFilters.vue';

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
            <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 min-h-[60vh]">
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
                <div v-else class="text-center text-gray-500 py-12">
                    <div class="mb-4">
                        <svg class="mx-auto h-12 w-12 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.746 0 3.332.477 4.5 1.253v13C19.832 18.477 18.246 18 16.5 18c-1.746 0-3.332.477-4.5 1.253" />
                        </svg>
                    </div>
                    <h3 class="text-lg font-medium text-gray-900 mb-2">Aucune recette trouvée</h3>
                    <p class="text-gray-500 mb-4">Commencez par créer votre première recette</p>
                    <Link
                        :href="route('recipes.create')"
                        class="inline-flex items-center px-4 py-2 bg-green-600 text-white rounded-md hover:bg-green-700"
                    >
                        Créer une recette
                    </Link>
                </div>

                <div v-if="recipes.links" class="mt-6 flex justify-center">
                    <nav class="flex gap-2">
                        <component
                            v-for="(link, index) in recipes.links"
                            :key="index"
                            :is="link.url ? Link : 'span'"
                            :href="link.url"
                            :class="{
                                'bg-green-600 text-white': link.active,
                                'bg-white text-gray-700 hover:bg-gray-50': !link.active && link.url,
                                'cursor-not-allowed opacity-50': !link.url,
                            }"
                            class="px-4 py-2 rounded-md border inline-flex items-center"
                        >
                            <span v-if="link.label.includes('Previous') || link.label.includes('pagination.previous')">← Précédent</span>
                            <span v-else-if="link.label.includes('Next') || link.label.includes('pagination.next')">Suivant →</span>
                            <span v-else>{{ link.label.replace('&laquo;', '').replace('&raquo;', '').trim() }}</span>
                        </component>
                    </nav>
                </div>
            </div>
        </div>
    </AppLayout>
</template>
