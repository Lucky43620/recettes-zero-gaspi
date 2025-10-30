<script setup>
import PublicLayout from '@/Layouts/PublicLayout.vue';
import { Link } from '@inertiajs/vue3';
import RecipeGrid from '@/Components/Recipe/RecipeGrid.vue';
import RecipeFilters from '@/Components/Recipe/RecipeFilters.vue';
import PageHeader from '@/Components/Common/PageHeader.vue';

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
    </PublicLayout>
</template>
