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
    <AppLayout title="Recettes">
        <template #header>
            <div class="flex justify-between items-center">
                <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                    Recettes
                </h2>
                <Link
                    :href="route('recipes.create')"
                    class="bg-blue-600 text-white px-4 py-2 rounded-md hover:bg-blue-700"
                >
                    Créer une recette
                </Link>
            </div>
        </template>

        <div class="py-12">
            <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
                <RecipeFilters :filters="filters" class="mb-6" />

                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                    <RecipeCard
                        v-for="recipe in recipes.data"
                        :key="recipe.id"
                        :recipe="recipe"
                    />
                </div>

                <div v-if="recipes.links" class="mt-6 flex justify-center">
                    <nav class="flex gap-2">
                        <component
                            v-for="(link, index) in recipes.links"
                            :key="index"
                            :is="link.url ? Link : 'span'"
                            :href="link.url"
                            :class="{
                                'bg-blue-600 text-white': link.active,
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