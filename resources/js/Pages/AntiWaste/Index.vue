<template>
    <AppLayout :title="t('anti_waste.title')">
        <template #header>
            <div class="flex justify-between items-center">
                <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                    {{ t('anti_waste.what_to_cook') }}
                </h2>
            </div>
        </template>

        <div class="py-12">
            <div class="max-w-[1920px] mx-auto sm:px-6 lg:px-8">
                <div v-if="loading" class="text-center py-12">
                    <svg class="animate-spin h-12 w-12 mx-auto text-green-600" fill="none" viewBox="0 0 24 24">
                        <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                        <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                    </svg>
                    <p class="mt-4 text-gray-600">{{ t('anti_waste.searching') }}</p>
                </div>

                <div v-else-if="message" class="bg-white overflow-hidden shadow-sm sm:rounded-lg p-12 text-center">
                    <svg class="w-20 h-20 text-gray-300 mx-auto mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4" />
                    </svg>
                    <h3 class="text-lg font-medium text-gray-900 mb-2">{{ message }}</h3>
                    <Link :href="route('pantry.index')">
                        <PrimaryButton class="mt-4">
                            {{ t('anti_waste.go_to_pantry') }}
                        </PrimaryButton>
                    </Link>
                </div>

                <div v-else>
                    <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg p-6 mb-6">
                        <div class="flex items-center justify-between">
                            <div>
                                <h3 class="text-lg font-semibold text-gray-900">
                                    {{ t('anti_waste.recipes_found', { count: recipes.length }) }}
                                </h3>
                                <p class="text-sm text-gray-600 mt-1">
                                    {{ t('anti_waste.based_on_ingredients', { count: pantryIngredientsCount }) }}
                                </p>
                            </div>
                            <div class="flex items-center gap-4">
                                <label class="flex items-center">
                                    <input
                                        v-model="showOnlyCompletable"
                                        type="checkbox"
                                        class="rounded border-gray-300 text-green-600 shadow-sm focus:ring-green-500"
                                    >
                                    <span class="ml-2 text-sm text-gray-700">{{ t('anti_waste.fully_completable') }}</span>
                                </label>
                            </div>
                        </div>
                    </div>

                    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-6">
                        <div
                            v-for="recipe in filteredRecipes"
                            :key="recipe.id"
                            class="bg-white overflow-hidden shadow-sm sm:rounded-lg hover:shadow-md transition-shadow"
                        >
                            <Link :href="route('recipes.show', recipe.slug)" class="block">
                                <div class="h-48 bg-gray-200">
                                    <img
                                        v-if="recipe.image"
                                        :src="recipe.image"
                                        :alt="recipe.title"
                                        class="w-full h-full object-cover"
                                    >
                                    <div v-else class="w-full h-full flex items-center justify-center text-gray-400">
                                        <svg class="w-16 h-16" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z" />
                                        </svg>
                                    </div>
                                </div>
                            </Link>

                            <div class="p-4">
                                <Link :href="route('recipes.show', recipe.slug)">
                                    <h3 class="font-semibold text-gray-900 hover:text-green-600 mb-2">
                                        {{ recipe.title }}
                                    </h3>
                                </Link>

                                <div class="mb-3">
                                    <div class="flex items-center gap-2 mb-2">
                                        <div class="flex-1 bg-gray-200 rounded-full h-2">
                                            <div
                                                class="h-2 rounded-full transition-all"
                                                :class="recipe.match_percentage === 100 ? 'bg-green-600' : 'bg-yellow-500'"
                                                :style="{ width: recipe.match_percentage + '%' }"
                                            ></div>
                                        </div>
                                        <span class="text-sm font-medium" :class="recipe.match_percentage === 100 ? 'text-green-600' : 'text-yellow-600'">
                                            {{ recipe.match_percentage }}%
                                        </span>
                                    </div>

                                    <div class="text-xs text-gray-600 space-y-1">
                                        <div class="flex items-center gap-1">
                                            <svg class="w-3 h-3 text-green-600" fill="currentColor" viewBox="0 0 20 20">
                                                <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd" />
                                            </svg>
                                            <span>{{ t('anti_waste.ingredients_you_have', { count: recipe.matching_ingredients_count }) }}</span>
                                        </div>
                                        <div v-if="recipe.missing_ingredients_count > 0" class="flex items-center gap-1">
                                            <svg class="w-3 h-3 text-yellow-600" fill="currentColor" viewBox="0 0 20 20">
                                                <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z" clip-rule="evenodd" />
                                            </svg>
                                            <span>{{ t('anti_waste.missing_ingredients', { count: recipe.missing_ingredients_count }) }}</span>
                                        </div>
                                    </div>
                                </div>

                                <div v-if="recipe.missing_ingredients && recipe.missing_ingredients.length > 0" class="border-t pt-3 mt-3">
                                    <p class="text-xs font-medium text-gray-700 mb-1">{{ t('anti_waste.to_buy') }}</p>
                                    <div class="flex flex-wrap gap-1">
                                        <span
                                            v-for="ingredient in recipe.missing_ingredients.slice(0, 3)"
                                            :key="ingredient.id"
                                            class="inline-block px-2 py-1 text-xs bg-yellow-100 text-yellow-800 rounded"
                                        >
                                            {{ ingredient.name }}
                                        </span>
                                        <span
                                            v-if="recipe.missing_ingredients && recipe.missing_ingredients.length > 3"
                                            class="inline-block px-2 py-1 text-xs bg-gray-100 text-gray-600 rounded"
                                        >
                                            +{{ recipe.missing_ingredients.length - 3 }}
                                        </span>
                                    </div>
                                </div>

                                <div v-if="recipe.can_make_with_pantry" class="mt-3 flex items-center gap-2 text-green-600 text-sm font-medium">
                                    <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 20 20">
                                        <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd" />
                                    </svg>
                                    <span>{{ t('anti_waste.can_make_now') }}</span>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div v-if="filteredRecipes && filteredRecipes.length === 0 && showOnlyCompletable" class="bg-white overflow-hidden shadow-sm sm:rounded-lg p-12 text-center mt-6">
                        <p class="text-gray-600">{{ t('anti_waste.no_fully_completable') }}</p>
                        <p class="text-gray-500 text-sm mt-2">{{ t('anti_waste.disable_filter_hint') }}</p>
                    </div>
                </div>
            </div>
        </div>
    </AppLayout>
</template>

<script setup>
import AppLayout from '@/Layouts/AppLayout.vue';
import PrimaryButton from '@/Components/PrimaryButton.vue';
import { Link } from '@inertiajs/vue3';
import { ref, computed, onMounted } from 'vue';
import axios from 'axios';
import { useI18n } from 'vue-i18n';

const { t } = useI18n();

const loading = ref(true);
const recipes = ref([]);
const message = ref('');
const pantryIngredientsCount = ref(0);
const showOnlyCompletable = ref(false);

const filteredRecipes = computed(() => {
    if (!showOnlyCompletable.value) {
        return recipes.value;
    }
    return recipes.value.filter(r => r.can_make_with_pantry);
});

onMounted(async () => {
    try {
        const response = await axios.get('/api/recipes/search-with-pantry');
        recipes.value = response.data.data;
        message.value = response.data.message || '';
        pantryIngredientsCount.value = response.data.pantry_ingredients_count || 0;
    } catch (error) {
        message.value = t('anti_waste.search_error');
    } finally {
        loading.value = false;
    }
});
</script>
