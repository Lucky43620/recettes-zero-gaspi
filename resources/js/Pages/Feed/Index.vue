<script setup>
import AppLayout from '@/Layouts/AppLayout.vue';
import RecipeCard from '@/Components/Recipe/RecipeCard.vue';
import Pagination from '@/Components/Common/Pagination.vue';
import EmptyState from '@/Components/Common/EmptyState.vue';

const props = defineProps({
    feed: Object,
});
</script>

<template>
    <AppLayout title="Mon flux">
        <template #header>
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                Mon flux personnalisé
            </h2>
        </template>

        <div class="py-12">
            <div class="max-w-[1920px] mx-auto sm:px-6 lg:px-8">
                <div v-if="feed.data.length" class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                    <RecipeCard
                        v-for="recipe in feed.data"
                        :key="recipe.id"
                        :recipe="recipe"
                    />
                </div>

                <EmptyState
                    v-else
                    icon="search"
                    title="Vous ne suivez personne pour le moment"
                    message="Commencez à suivre des cuisiniers pour voir leurs nouvelles recettes"
                    action-label="Découvrir des recettes"
                    :action-href="route('recipes.index')"
                />

                <div v-if="feed.links" class="mt-6 flex justify-center">
                    <Pagination :links="feed.links" />
                </div>
            </div>
        </div>
    </AppLayout>
</template>
