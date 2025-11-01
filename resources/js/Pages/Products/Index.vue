<template>
    <PublicLayout title="Recherche de Produits">
        <div class="py-12 bg-gray-50 min-h-screen">
            <div class="max-w-[1920px] mx-auto sm:px-6 lg:px-8">
                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg mb-8 p-8">
                    <div class="text-center mb-8">
                        <h1 class="text-4xl font-bold text-gray-900 mb-4">
                            Recherche de Produits Alimentaires
                        </h1>
                        <p class="text-lg text-gray-600 max-w-2xl mx-auto">
                            Explorez notre base de données de produits alimentaires et découvrez leurs informations nutritionnelles complètes
                        </p>
                    </div>

                    <div class="max-w-2xl mx-auto">
                        <div class="relative">
                            <input
                                v-model="searchQuery"
                                @keyup.enter="performSearch(searchQuery)"
                                type="text"
                                placeholder="Rechercher un produit par nom, marque ou code-barres..."
                                class="w-full border-gray-300 focus:border-green-500 focus:ring-green-500 rounded-md shadow-sm pr-32 py-3"
                            >
                            <PrimaryButton
                                @click="performSearch(searchQuery)"
                                class="absolute right-2 top-1/2 -translate-y-1/2"
                            >
                                Rechercher
                            </PrimaryButton>
                        </div>
                    </div>
                </div>

                <div v-if="isSearching" class="text-center py-12">
                    <svg class="animate-spin h-12 w-12 mx-auto text-green-600" fill="none" viewBox="0 0 24 24">
                        <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                        <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                    </svg>
                    <p class="mt-4 text-gray-600">Recherche en cours...</p>
                </div>

                <div v-else-if="allProducts.length > 0">
                    <div class="mb-6 flex items-center justify-between">
                        <p class="text-gray-600">
                            <span class="font-semibold text-gray-900">{{ allProducts.length }}</span> produit(s) trouvé(s)
                        </p>
                    </div>

                    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-6">
                        <ProductCard
                            v-for="product in allProducts"
                            :key="product.id"
                            :product="product"
                        />
                    </div>

                    <div v-if="hasMore" class="mt-8 flex justify-center">
                        <PrimaryButton variant="secondary" @click="loadMore" :loading="isSearching">
                            Afficher plus
                        </PrimaryButton>
                    </div>
                </div>

                <div v-else-if="hasSearched" class="bg-white overflow-hidden shadow-sm sm:rounded-lg p-12 text-center">
                    <svg class="w-20 h-20 text-gray-300 mx-auto mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                    </svg>
                    <h3 class="text-lg font-medium text-gray-900 mb-2">Aucun produit trouvé</h3>
                    <p class="text-gray-500">Essayez avec d'autres termes de recherche</p>
                </div>

                <div v-else class="bg-white overflow-hidden shadow-sm sm:rounded-lg p-12 text-center">
                    <svg class="w-20 h-20 text-gray-300 mx-auto mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                    </svg>
                    <h3 class="text-lg font-medium text-gray-900 mb-2">Commencez votre recherche</h3>
                    <p class="text-gray-500">Recherchez un produit pour voir les résultats</p>
                </div>

                <div class="mt-12 bg-white overflow-hidden shadow-sm sm:rounded-lg p-6">
                    <h2 class="text-2xl font-bold text-gray-900 mb-4">À propos de notre base de données</h2>
                    <div class="grid md:grid-cols-3 gap-6 text-center">
                        <div>
                            <div class="flex justify-center mb-3">
                                <div class="bg-green-100 rounded-full p-3">
                                    <svg class="w-8 h-8 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                                    </svg>
                                </div>
                            </div>
                            <h3 class="font-semibold text-gray-900 mb-2">Données vérifiées</h3>
                            <p class="text-sm text-gray-600">Base de données collaborative OpenFoodFacts</p>
                        </div>
                        <div>
                            <div class="flex justify-center mb-3">
                                <div class="bg-blue-100 rounded-full p-3">
                                    <svg class="w-8 h-8 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z" />
                                    </svg>
                                </div>
                            </div>
                            <h3 class="font-semibold text-gray-900 mb-2">Informations complètes</h3>
                            <p class="text-sm text-gray-600">Nutrition, allergènes, labels et origine</p>
                        </div>
                        <div>
                            <div class="flex justify-center mb-3">
                                <div class="bg-purple-100 rounded-full p-3">
                                    <svg class="w-8 h-8 text-purple-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15" />
                                    </svg>
                                </div>
                            </div>
                            <h3 class="font-semibold text-gray-900 mb-2">Mise à jour régulière</h3>
                            <p class="text-sm text-gray-600">Données synchronisées automatiquement</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </PublicLayout>
</template>

<script setup>
import PublicLayout from '@/Layouts/PublicLayout.vue';
import ProductCard from '@/Components/Ingredients/ProductCard.vue';
import PrimaryButton from '@/Components/Common/PrimaryButton.vue';
import { ref } from 'vue';
import { router } from '@inertiajs/vue3';

const props = defineProps({
    products: {
        type: Array,
        default: () => [],
    },
    query: {
        type: String,
        default: '',
    },
    currentPage: {
        type: Number,
        default: 1,
    },
    hasMore: {
        type: Boolean,
        default: false,
    },
});

const searchQuery = ref(props.query || '');
const isSearching = ref(false);
const hasSearched = ref(props.products.length > 0 || props.query);
const allProducts = ref([...props.products]);

const performSearch = (query) => {
    if (!query || query.length < 2) return;

    isSearching.value = true;
    hasSearched.value = true;
    allProducts.value = [];

    router.get(route('products.index'), { q: query }, {
        preserveState: true,
        preserveScroll: false,
        onSuccess: (page) => {
            allProducts.value = page.props.products;
        },
        onFinish: () => {
            isSearching.value = false;
        },
    });
};

const loadMore = () => {
    isSearching.value = true;

    router.get(route('products.index'), {
        q: props.query,
        page: props.currentPage + 1
    }, {
        only: ['products', 'currentPage', 'hasMore'],
        preserveState: true,
        preserveScroll: true,
        onSuccess: (page) => {
            const newProducts = page.props.products || [];
            allProducts.value = [...allProducts.value, ...newProducts];
        },
        onFinish: () => {
            isSearching.value = false;
        },
    });
};
</script>
