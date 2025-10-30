<script setup>
import PublicLayout from '@/Layouts/PublicLayout.vue';
import RecipeGrid from '@/Components/Recipe/RecipeGrid.vue';
import RecipeFilters from '@/Components/Recipe/RecipeFilters.vue';
import PageHeader from '@/Components/Common/PageHeader.vue';
import Pagination from '@/Components/Common/Pagination.vue';

defineProps({
    recipes: Object,
    filters: Object,
});
</script>

<template>
    <PublicLayout title="Recettes">
        <div class="py-8">
            <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 min-h-[60vh]">
                <PageHeader
                    title="Toutes les recettes"
                    subtitle="Découvrez toutes les recettes partagées par notre communauté"
                />

                <RecipeFilters :filters="filters" class="mb-6" />

                <RecipeGrid :recipes="recipes.data" />

                <div v-if="recipes.links" class="mt-6 flex justify-center">
                    <Pagination :links="recipes.links" />
                </div>
            </div>
        </div>
    </PublicLayout>
</template>
