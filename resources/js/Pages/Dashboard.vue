<script setup>
import { Link } from '@inertiajs/vue3';
import AppLayout from '@/Layouts/AppLayout.vue';
import RecipeGrid from '@/Components/Recipe/RecipeGrid.vue';
import StatsCard from '@/Components/Dashboard/StatsCard.vue';
import ActionButton from '@/Components/Dashboard/ActionButton.vue';
import PageHeader from '@/Components/Common/PageHeader.vue';

defineProps({
    stats: Object,
});
</script>

<template>
    <AppLayout title="Dashboard">
        <template #header>
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                Mon espace
            </h2>
        </template>

        <div class="py-12">
            <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
                <PageHeader
                    :title="`Bonjour ${$page.props.auth.user.name} ! 👋`"
                    subtitle="Voici un aperçu de votre activité culinaire"
                />

                <!-- Statistiques -->
                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 mb-8">
                    <StatsCard
                        icon="book"
                        label="Mes recettes"
                        :value="stats.totalRecipes"
                    />
                    <StatsCard
                        icon="globe"
                        label="Publiques"
                        :value="stats.publicRecipes"
                        icon-color="text-orange-600"
                    />
                    <StatsCard
                        icon="star"
                        label="Note moyenne"
                        :value="stats.averageRating ? Math.round(stats.averageRating * 10) / 10 : 'N/A'"
                    />
                    <StatsCard
                        icon="chat"
                        label="Commentaires"
                        :value="stats.totalComments"
                        icon-color="text-orange-600"
                    />
                </div>

                <!-- Actions rapides -->
                <div class="bg-white shadow rounded-lg mb-8">
                    <div class="px-4 py-5 sm:p-6">
                        <h3 class="text-lg leading-6 font-medium text-gray-900 mb-4">Actions rapides</h3>
                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                            <ActionButton :href="route('recipes.create')" icon="plus">
                                Créer une recette
                            </ActionButton>
                            <ActionButton :href="route('recipes.my')" variant="secondary" icon="book">
                                Voir toutes mes recettes
                            </ActionButton>
                        </div>
                    </div>
                </div>

                <!-- Mes dernières recettes -->
                <div v-if="stats.recentRecipes.length" class="bg-white shadow rounded-lg">
                    <div class="px-4 py-5 sm:p-6">
                        <div class="flex items-center justify-between mb-4">
                            <h3 class="text-lg leading-6 font-medium text-gray-900">Mes dernières recettes</h3>
                            <Link
                                :href="route('recipes.my')"
                                class="text-sm text-green-600 hover:text-green-800"
                            >
                                Voir toutes →
                            </Link>
                        </div>
                        <RecipeGrid :recipes="stats.recentRecipes" :from-my-recipes="true" />
                    </div>
                </div>

                <!-- Message d'encouragement si pas de recettes -->
                <div v-else class="bg-white shadow rounded-lg">
                    <div class="px-4 py-5 sm:p-6 text-center">
                        <svg class="mx-auto h-12 w-12 text-gray-400 mb-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.746 0 3.332.477 4.5 1.253v13C19.832 18.477 18.246 18 16.5 18c-1.746 0-3.332.477-4.5 1.253" />
                        </svg>
                        <h3 class="text-lg font-medium text-gray-900 mb-2">Commencez votre aventure culinaire !</h3>
                        <p class="text-gray-500 mb-4">Créez votre première recette et partagez vos créations avec la communauté.</p>
                        <Link
                            :href="route('recipes.create')"
                            class="inline-flex items-center px-4 py-2 bg-green-600 text-white rounded-md hover:bg-green-700 transition"
                        >
                            <svg class="mr-2 h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
                            </svg>
                            Créer ma première recette
                        </Link>
                    </div>
                </div>
            </div>
        </div>
    </AppLayout>
</template>
