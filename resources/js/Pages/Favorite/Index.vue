<script setup>
import AppLayout from '@/Layouts/AppLayout.vue';
import RecipeCard from '@/Components/Recipe/RecipeCard.vue';
import PrimaryButton from '@/Components/Common/PrimaryButton.vue';
import { Link } from '@inertiajs/vue3';

const props = defineProps({
    favorites: Object,
});
</script>

<template>
    <AppLayout title="Mes favoris">
        <template #header>
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                Mes recettes favorites
            </h2>
        </template>

        <div class="py-12">
            <div class="max-w-[1920px] mx-auto sm:px-6 lg:px-8">
                <div v-if="favorites && favorites.data && favorites.data.length" class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                    <RecipeCard
                        v-for="recipe in favorites.data"
                        :key="recipe.id"
                        :recipe="recipe"
                    />
                </div>

                <div v-else class="bg-white rounded-lg shadow p-8 text-center">
                    <p class="text-gray-600 mb-4">
                        Vous n'avez pas encore de recettes favorites
                    </p>
                    <Link :href="route('home')">
                        <PrimaryButton>
                            Découvrir des recettes
                        </PrimaryButton>
                    </Link>
                </div>

                <div v-if="favorites && favorites.data && favorites.data.length && favorites.last_page > 1" class="mt-6 flex justify-center gap-2">
                    <component
                        v-for="(link, index) in favorites.links"
                        :key="index"
                        :is="link.url ? Link : 'span'"
                        :href="link.url"
                        :class="[
                            'px-3 py-2 border rounded',
                            link.active
                                ? 'bg-green-600 text-white border-green-600'
                                : link.url
                                    ? 'bg-white text-gray-700 border-gray-300 hover:bg-gray-50'
                                    : 'bg-gray-100 text-gray-400 border-gray-200 cursor-not-allowed'
                        ]"
                    >
                        <span v-if="link.label.includes('Previous')">← Précédent</span>
                        <span v-else-if="link.label.includes('Next')">Suivant →</span>
                        <span v-else v-html="link.label"></span>
                    </component>
                </div>
            </div>
        </div>
    </AppLayout>
</template>
