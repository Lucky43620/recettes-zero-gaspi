<script setup>
import { Link } from '@inertiajs/vue3';
import PublicLayout from '@/Layouts/PublicLayout.vue';
import RecipeGrid from '@/Components/Recipe/RecipeGrid.vue';

defineProps({
    canLogin: Boolean,
    canRegister: Boolean,
    featuredRecipes: Array,
});
</script>

<template>
    <PublicLayout title="Accueil">
        <div class="relative bg-gradient-to-r from-green-600 to-orange-500 py-20">
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 text-center">
                <h1 class="text-4xl md:text-5xl font-bold text-white mb-6">
                    Cuisinez sans gaspiller
                </h1>
                <p class="text-xl text-white/90 mb-8">
                    Découvrez des recettes délicieuses pour utiliser vos ingrédients et réduire le gaspillage alimentaire
                </p>
                <Link
                    :href="route('recipes.index')"
                    class="inline-block px-8 py-3 bg-white text-green-700 font-semibold rounded-lg hover:bg-green-50 transition shadow-lg"
                >
                    Explorer les recettes
                </Link>
            </div>
        </div>

        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-12 min-h-[60vh]">
            <h2 class="text-3xl font-bold text-gray-900 mb-8">Recettes populaires</h2>
            <RecipeGrid
                :recipes="featuredRecipes"
                empty-message="Aucune recette disponible pour le moment"
            />
        </div>
    </PublicLayout>
</template>
