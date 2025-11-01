<template>
    <PublicLayout :title="ingredient.name">
        <div class="py-12">
            <div class="max-w-[1920px] mx-auto sm:px-6 lg:px-8">
                <div class="mb-6">
                    <BackButton />
                </div>

                <div class="bg-white overflow-hidden shadow-xl sm:rounded-lg">

                    <div class="grid grid-cols-1 lg:grid-cols-3 gap-8 p-8">

                        <div class="lg:col-span-1">
                            <div v-if="ingredient.image_url" class="aspect-square rounded-lg overflow-hidden bg-gray-100 mb-6">
                                <img :src="ingredient.image_url" :alt="ingredient.name" class="w-full h-full object-cover">
                            </div>
                            <div v-else class="aspect-square rounded-lg bg-gradient-to-br from-green-100 to-green-200 flex items-center justify-center mb-6">
                                <svg class="w-32 h-32 text-green-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4" />
                                </svg>
                            </div>

                            <div v-if="ingredient.barcode" class="bg-gray-50 rounded-lg p-4 mb-4">
                                <p class="text-sm text-gray-500 mb-1">Code-barres</p>
                                <p class="font-mono text-lg">{{ ingredient.barcode }}</p>
                            </div>

                            <div v-if="ingredient.openfoodfacts_id" class="bg-blue-50 rounded-lg p-4 flex items-center gap-2">
                                <svg class="w-5 h-5 text-blue-600" fill="currentColor" viewBox="0 0 20 20">
                                    <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd" />
                                </svg>
                                <div class="flex-1">
                                    <p class="text-sm text-blue-900 font-medium">Données OpenFoodFacts</p>
                                    <p class="text-xs text-blue-700">Synchronisé le {{ formatDate(ingredient.openfoodfacts_synced_at) }}</p>
                                </div>
                            </div>
                        </div>

                        <div class="lg:col-span-2">
                            <div class="mb-6">
                                <h1 class="text-4xl font-bold text-gray-900 mb-2">{{ ingredient.name }}</h1>
                                <p v-if="ingredient.brands" class="text-xl text-gray-600">{{ ingredient.brands }}</p>
                            </div>

                            <div v-if="ingredient.category" class="mb-6">
                                <h3 class="text-sm font-semibold text-gray-700 mb-2">Catégories</h3>
                                <div class="flex flex-wrap gap-2">
                                    <span
                                        v-for="cat in ingredient.category.split(',').slice(0, 5)"
                                        :key="cat"
                                        class="px-3 py-1 bg-green-100 text-green-800 rounded-full text-sm"
                                    >
                                        {{ cat.trim() }}
                                    </span>
                                </div>
                            </div>

                            <div v-if="ingredient.labels && ingredient.labels.length > 0" class="mb-6">
                                <h3 class="text-sm font-semibold text-gray-700 mb-2">Labels & Certifications</h3>
                                <div class="flex flex-wrap gap-2">
                                    <span
                                        v-for="label in ingredient.labels"
                                        :key="label"
                                        class="px-3 py-1 bg-blue-100 text-blue-800 rounded-full text-sm inline-flex items-center gap-1"
                                    >
                                        <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 20 20">
                                            <path fill-rule="evenodd" d="M6.267 3.455a3.066 3.066 0 001.745-.723 3.066 3.066 0 013.976 0 3.066 3.066 0 001.745.723 3.066 3.066 0 012.812 2.812c.051.643.304 1.254.723 1.745a3.066 3.066 0 010 3.976 3.066 3.066 0 00-.723 1.745 3.066 3.066 0 01-2.812 2.812 3.066 3.066 0 00-1.745.723 3.066 3.066 0 01-3.976 0 3.066 3.066 0 00-1.745-.723 3.066 3.066 0 01-2.812-2.812 3.066 3.066 0 00-.723-1.745 3.066 3.066 0 010-3.976 3.066 3.066 0 00.723-1.745 3.066 3.066 0 012.812-2.812zm7.44 5.252a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd" />
                                        </svg>
                                        {{ label }}
                                    </span>
                                </div>
                            </div>

                            <div v-if="ingredient.allergens && ingredient.allergens.length > 0" class="mb-6">
                                <h3 class="text-sm font-semibold text-gray-700 mb-2">Allergènes</h3>
                                <div class="bg-red-50 border border-red-200 rounded-lg p-4">
                                    <div class="flex flex-wrap gap-2">
                                        <span
                                            v-for="allergen in ingredient.allergens"
                                            :key="allergen"
                                            class="px-3 py-1 bg-red-100 text-red-900 rounded-full text-sm font-medium inline-flex items-center gap-1"
                                        >
                                            <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 20 20">
                                                <path fill-rule="evenodd" d="M8.257 3.099c.765-1.36 2.722-1.36 3.486 0l5.58 9.92c.75 1.334-.213 2.98-1.742 2.98H4.42c-1.53 0-2.493-1.646-1.743-2.98l5.58-9.92zM11 13a1 1 0 11-2 0 1 1 0 012 0zm-1-8a1 1 0 00-1 1v3a1 1 0 002 0V6a1 1 0 00-1-1z" clip-rule="evenodd" />
                                            </svg>
                                            {{ formatAllergen(allergen) }}
                                        </span>
                                    </div>
                                </div>
                            </div>

                            <div v-if="ingredient.nutritional_info && hasNutritionalData" class="mb-6">
                                <h3 class="text-lg font-semibold text-gray-900 mb-4">Informations nutritionnelles</h3>
                                <p class="text-sm text-gray-600 mb-4">Pour 100g</p>

                                <div class="grid grid-cols-2 md:grid-cols-3 gap-4">
                                    <div v-if="ingredient.nutritional_info.energy_kcal" class="bg-gradient-to-br from-orange-50 to-orange-100 rounded-lg p-4">
                                        <p class="text-sm text-gray-600 mb-1">Énergie</p>
                                        <p class="text-2xl font-bold text-orange-900">{{ ingredient.nutritional_info.energy_kcal }}</p>
                                        <p class="text-xs text-gray-600">kcal</p>
                                    </div>

                                    <div v-if="ingredient.nutritional_info.fat" class="bg-gradient-to-br from-yellow-50 to-yellow-100 rounded-lg p-4">
                                        <p class="text-sm text-gray-600 mb-1">Lipides</p>
                                        <p class="text-2xl font-bold text-yellow-900">{{ ingredient.nutritional_info.fat }}</p>
                                        <p class="text-xs text-gray-600">g</p>
                                        <p v-if="ingredient.nutritional_info.saturated_fat" class="text-xs text-gray-500 mt-1">
                                            dont saturés: {{ ingredient.nutritional_info.saturated_fat }}g
                                        </p>
                                    </div>

                                    <div v-if="ingredient.nutritional_info.carbohydrates" class="bg-gradient-to-br from-blue-50 to-blue-100 rounded-lg p-4">
                                        <p class="text-sm text-gray-600 mb-1">Glucides</p>
                                        <p class="text-2xl font-bold text-blue-900">{{ ingredient.nutritional_info.carbohydrates }}</p>
                                        <p class="text-xs text-gray-600">g</p>
                                        <p v-if="ingredient.nutritional_info.sugars" class="text-xs text-gray-500 mt-1">
                                            dont sucres: {{ ingredient.nutritional_info.sugars }}g
                                        </p>
                                    </div>

                                    <div v-if="ingredient.nutritional_info.fiber" class="bg-gradient-to-br from-green-50 to-green-100 rounded-lg p-4">
                                        <p class="text-sm text-gray-600 mb-1">Fibres</p>
                                        <p class="text-2xl font-bold text-green-900">{{ ingredient.nutritional_info.fiber }}</p>
                                        <p class="text-xs text-gray-600">g</p>
                                    </div>

                                    <div v-if="ingredient.nutritional_info.proteins" class="bg-gradient-to-br from-purple-50 to-purple-100 rounded-lg p-4">
                                        <p class="text-sm text-gray-600 mb-1">Protéines</p>
                                        <p class="text-2xl font-bold text-purple-900">{{ ingredient.nutritional_info.proteins }}</p>
                                        <p class="text-xs text-gray-600">g</p>
                                    </div>

                                    <div v-if="ingredient.nutritional_info.salt" class="bg-gradient-to-br from-gray-50 to-gray-100 rounded-lg p-4">
                                        <p class="text-sm text-gray-600 mb-1">Sel</p>
                                        <p class="text-2xl font-bold text-gray-900">{{ ingredient.nutritional_info.salt }}</p>
                                        <p class="text-xs text-gray-600">g</p>
                                    </div>
                                </div>
                            </div>

                            <div class="flex gap-4">
                                <a
                                    v-if="ingredient.openfoodfacts_id"
                                    :href="`https://world.openfoodfacts.org/product/${ingredient.openfoodfacts_id}`"
                                    target="_blank"
                                    class="inline-flex items-center px-4 py-2 bg-green-600 text-white rounded-lg hover:bg-green-700 transition"
                                >
                                    <svg class="w-5 h-5 mr-2" fill="currentColor" viewBox="0 0 20 20">
                                        <path d="M11 3a1 1 0 100 2h2.586l-6.293 6.293a1 1 0 101.414 1.414L15 6.414V9a1 1 0 102 0V4a1 1 0 00-1-1h-5z" />
                                        <path d="M5 5a2 2 0 00-2 2v8a2 2 0 002 2h8a2 2 0 002-2v-3a1 1 0 10-2 0v3H5V7h3a1 1 0 000-2H5z" />
                                    </svg>
                                    Voir sur OpenFoodFacts
                                </a>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </PublicLayout>
</template>

<script setup>
import PublicLayout from '@/Layouts/PublicLayout.vue';
import BackButton from '@/Components/Common/BackButton.vue';
import { computed } from 'vue';

const props = defineProps({
    ingredient: Object,
});

const hasNutritionalData = computed(() => {
    if (!props.ingredient.nutritional_info) return false;
    const nutri = props.ingredient.nutritional_info;
    return nutri.energy_kcal || nutri.fat || nutri.carbohydrates || nutri.proteins;
});

const formatDate = (dateString) => {
    if (!dateString) return '';
    const date = new Date(dateString);
    return date.toLocaleDateString('fr-FR', { year: 'numeric', month: 'long', day: 'numeric' });
};

const formatAllergen = (allergen) => {
    const allergenMap = {
        'en:milk': 'Lait',
        'en:nuts': 'Fruits à coque',
        'en:soybeans': 'Soja',
        'en:eggs': 'Œufs',
        'en:gluten': 'Gluten',
        'en:fish': 'Poisson',
        'en:crustaceans': 'Crustacés',
        'en:molluscs': 'Mollusques',
        'en:peanuts': 'Arachides',
        'en:sesame-seeds': 'Graines de sésame',
        'en:sulphur-dioxide-and-sulphites': 'Sulfites',
        'en:celery': 'Céleri',
        'en:mustard': 'Moutarde',
        'en:lupin': 'Lupin',
    };

    return allergenMap[allergen] || allergen.replace('en:', '').replace(/-/g, ' ');
};
</script>
