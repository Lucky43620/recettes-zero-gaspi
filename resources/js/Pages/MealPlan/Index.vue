<script setup>
import AppLayout from '@/Layouts/AppLayout.vue';
import MealPlanMobileCard from '@/Components/MealPlanMobileCard.vue';
import PrimaryButton from '@/Components/Common/PrimaryButton.vue';
import { ref, computed } from 'vue';
import { Link, router, usePage } from '@inertiajs/vue3';

const props = defineProps({
    mealPlan: Object,
    weekStart: String,
    userRecipes: Array,
    favoriteRecipes: Array,
    isPremium: Boolean,
    recipeLimit: Number,
});

const page = usePage();

const daysOfWeek = ['monday', 'tuesday', 'wednesday', 'thursday', 'friday', 'saturday', 'sunday'];
const dayLabels = {
    monday: 'Lundi',
    tuesday: 'Mardi',
    wednesday: 'Mercredi',
    thursday: 'Jeudi',
    friday: 'Vendredi',
    saturday: 'Samedi',
    sunday: 'Dimanche',
};

const mealTypes = ['breakfast', 'lunch', 'dinner', 'snack'];
const mealTypeLabels = {
    breakfast: 'Petit-déjeuner',
    lunch: 'Déjeuner',
    dinner: 'Dîner',
    snack: 'Collation',
};

const draggedRecipe = ref(null);
const showDuplicateModal = ref(false);
const duplicateWeekStart = ref('');
const isGeneratingShoppingList = ref(false);
const isDuplicating = ref(false);

const currentWeekStart = computed(() => new Date(props.weekStart));

const prevWeek = computed(() => {
    const date = new Date(currentWeekStart.value);
    date.setDate(date.getDate() - 7);
    return date.toISOString().split('T')[0];
});

const nextWeek = computed(() => {
    const date = new Date(currentWeekStart.value);
    date.setDate(date.getDate() + 7);
    return date.toISOString().split('T')[0];
});

const getMealPlanRecipes = (day, mealType) => {
    return props.mealPlan.meal_plan_recipes?.filter(
        mpr => mpr.day_of_week === day && mpr.meal_type === mealType
    ) || [];
};

const onDragStart = (recipe) => {
    draggedRecipe.value = recipe;
};

const onDragOver = (event) => {
    event.preventDefault();
};

const onDrop = (day, mealType) => {
    if (!draggedRecipe.value) return;

    router.post(route('meal-plans.recipes.add', props.mealPlan.id), {
        recipe_id: draggedRecipe.value.id,
        day_of_week: day,
        meal_type: mealType,
        servings: draggedRecipe.value.servings || 1,
    }, {
        preserveScroll: true,
    });

    draggedRecipe.value = null;
};

const removeRecipe = (mealPlanRecipeId) => {
    router.delete(route('meal-plans.recipes.remove', mealPlanRecipeId), {
        preserveScroll: true,
    });
};

const navigateWeek = (weekStart) => {
    router.get(route('meal-plans.index', { week: weekStart }));
};

const openDuplicateModal = () => {
    showDuplicateModal.value = true;
    duplicateWeekStart.value = nextWeek.value;
};

const duplicateWeek = () => {
    isDuplicating.value = true;
    router.post(route('meal-plans.duplicate', props.mealPlan.id), {
        week_start: duplicateWeekStart.value,
    }, {
        onFinish: () => {
            isDuplicating.value = false;
            showDuplicateModal.value = false;
        },
    });
};

const generateShoppingList = () => {
    isGeneratingShoppingList.value = true;
    router.post(route('shopping-lists.generate', props.mealPlan.id), {
        onFinish: () => {
            isGeneratingShoppingList.value = false;
        },
    });
};

const getRecipeImage = (recipe) => {
    return recipe.media?.[0]?.original_url || '/images/placeholder-recipe.jpg';
};
</script>

<template>
    <AppLayout title="Planning des repas">
        <template #header>
            <div class="flex justify-between items-center">
                <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                    Planning des repas
                </h2>
                <div class="flex gap-3">
                    <PrimaryButton
                        @click="generateShoppingList"
                        :loading="isGeneratingShoppingList"
                        variant="warning"
                    >
                        Générer liste de courses
                    </PrimaryButton>
                    <PrimaryButton @click="openDuplicateModal">
                        Dupliquer cette semaine
                    </PrimaryButton>
                </div>
            </div>
        </template>

        <div class="py-12">
            <div class="max-w-[1920px] mx-auto sm:px-6 lg:px-8">
                <div class="mb-6 flex justify-between items-center">
                    <PrimaryButton variant="secondary" @click="navigateWeek(prevWeek)">
                        ← Semaine précédente
                    </PrimaryButton>
                    <div class="text-lg font-semibold">
                        Semaine du {{ new Date(weekStart).toLocaleDateString('fr-FR', { day: 'numeric', month: 'long', year: 'numeric' }) }}
                    </div>
                    <PrimaryButton variant="secondary" @click="navigateWeek(nextWeek)">
                        Semaine suivante →
                    </PrimaryButton>
                </div>

                <!-- Free user limit banner -->
                <div v-if="!isPremium && recipeLimit" class="bg-gradient-to-r from-yellow-50 to-orange-50 border-l-4 border-yellow-400 p-4 mb-6 rounded-r-lg shadow-sm">
                    <div class="flex items-center justify-between">
                        <div class="flex items-center">
                            <svg class="w-5 h-5 text-yellow-600 mr-3" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd" d="M8.257 3.099c.765-1.36 2.722-1.36 3.486 0l5.58 9.92c.75 1.334-.213 2.98-1.742 2.98H4.42c-1.53 0-2.493-1.646-1.743-2.98l5.58-9.92zM11 13a1 1 0 11-2 0 1 1 0 012 0zm-1-8a1 1 0 00-1 1v3a1 1 0 002 0V6a1 1 0 00-1-1z" clip-rule="evenodd" />
                            </svg>
                            <div>
                                <p class="text-sm font-semibold text-gray-900">
                                    Limite Gratuite : {{ mealPlan.meal_plan_recipes.length }}/{{ recipeLimit }} recettes par semaine
                                </p>
                                <p class="text-xs text-gray-600 mt-0.5">
                                    Passez à Premium pour des plans de repas illimités avec générateur de menus IA
                                </p>
                            </div>
                        </div>
                        <Link :href="route('subscription.index')" class="ml-4 px-4 py-2 bg-gradient-to-r from-yellow-500 to-orange-500 hover:from-yellow-600 hover:to-orange-600 text-white text-sm font-semibold rounded-lg transition-all">
                            Passer à Premium
                        </Link>
                    </div>
                </div>

                <div class="grid grid-cols-1 lg:grid-cols-4 gap-6">
                    <div class="lg:col-span-1">
                        <div class="bg-white rounded-lg shadow p-4 mb-6">
                            <h3 class="font-semibold text-lg mb-4">Mes recettes</h3>
                            <p v-if="!userRecipes.length" class="text-sm text-gray-500 mb-4">
                                Vous n'avez pas encore de recettes publiques.
                                <Link :href="route('recipes.create')" class="text-green-600 hover:underline">
                                    Créer une recette
                                </Link>
                            </p>
                            <p v-else class="text-sm text-gray-500 mb-4">
                                Glissez-déposez vos recettes dans le planning
                            </p>
                            <div class="space-y-2 max-h-[400px] overflow-y-auto">
                                <div
                                    v-for="recipe in userRecipes"
                                    :key="recipe.id"
                                    draggable="true"
                                    @dragstart="onDragStart(recipe)"
                                    class="p-2 bg-gray-50 rounded cursor-move hover:bg-gray-100 transition"
                                >
                                    <div class="flex items-center gap-2">
                                        <img
                                            :src="getRecipeImage(recipe)"
                                            :alt="recipe.title"
                                            class="w-12 h-12 object-cover rounded"
                                        />
                                        <div class="flex-1 min-w-0">
                                            <p class="text-sm font-medium truncate">{{ recipe.title }}</p>
                                            <p class="text-xs text-gray-500">{{ recipe.servings }} portions</p>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div v-if="favoriteRecipes.length" class="bg-white rounded-lg shadow p-4">
                            <h3 class="font-semibold text-lg mb-4">Mes favoris</h3>
                            <p class="text-sm text-gray-500 mb-4">
                                Glissez-déposez vos recettes favorites dans le planning
                            </p>
                            <div class="space-y-2 max-h-[400px] overflow-y-auto">
                                <div
                                    v-for="recipe in favoriteRecipes"
                                    :key="'fav-' + recipe.id"
                                    draggable="true"
                                    @dragstart="onDragStart(recipe)"
                                    class="p-2 bg-orange-50 rounded cursor-move hover:bg-orange-100 transition"
                                >
                                    <div class="flex items-center gap-2">
                                        <img
                                            :src="getRecipeImage(recipe)"
                                            :alt="recipe.title"
                                            class="w-12 h-12 object-cover rounded"
                                        />
                                        <div class="flex-1 min-w-0">
                                            <p class="text-sm font-medium truncate">{{ recipe.title }}</p>
                                            <p class="text-xs text-gray-500">{{ recipe.servings }} portions</p>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="lg:col-span-3">
                        <!-- Desktop view: Table -->
                        <div class="hidden md:block bg-white rounded-lg shadow overflow-hidden">
                            <div class="overflow-x-auto">
                                <table class="w-full">
                                    <thead class="bg-gray-50">
                                        <tr>
                                            <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase w-32">
                                                Repas
                                            </th>
                                            <th
                                                v-for="day in daysOfWeek"
                                                :key="day"
                                                class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase"
                                            >
                                                {{ dayLabels[day] }}
                                            </th>
                                        </tr>
                                    </thead>
                                    <tbody class="divide-y divide-gray-200">
                                        <tr v-for="mealType in mealTypes" :key="mealType">
                                            <td class="px-4 py-3 text-sm font-medium text-gray-700 bg-gray-50">
                                                {{ mealTypeLabels[mealType] }}
                                            </td>
                                            <td
                                                v-for="day in daysOfWeek"
                                                :key="`${day}-${mealType}`"
                                                @dragover="onDragOver"
                                                @drop="onDrop(day, mealType)"
                                                class="px-2 py-2 align-top border-l border-gray-200 min-h-[120px]"
                                            >
                                                <div class="space-y-2 min-h-[100px]">
                                                    <div
                                                        v-for="mpr in getMealPlanRecipes(day, mealType)"
                                                        :key="mpr.id"
                                                        class="p-2 bg-green-50 rounded border border-green-200 relative group cursor-pointer hover:bg-green-100 transition"
                                                        @click="router.visit(route('recipes.show', mpr.recipe.slug))"
                                                    >
                                                        <button
                                                            @click.stop="removeRecipe(mpr.id)"
                                                            class="absolute top-1 right-1 text-red-500 hover:text-red-700 opacity-0 group-hover:opacity-100 transition"
                                                        >
                                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                                                            </svg>
                                                        </button>
                                                        <div class="flex items-start gap-2">
                                                            <img
                                                                :src="getRecipeImage(mpr.recipe)"
                                                                :alt="mpr.recipe.title"
                                                                class="w-10 h-10 object-cover rounded"
                                                            />
                                                            <div class="flex-1 min-w-0">
                                                                <p class="text-xs font-medium truncate">{{ mpr.recipe.title }}</p>
                                                                <p class="text-xs text-gray-500">{{ mpr.servings }} portions</p>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                            </td>
                                        </tr>
                                    </tbody>
                                </table>
                            </div>
                        </div>

                        <!-- Mobile view: Cards -->
                        <div class="md:hidden space-y-4">
                            <MealPlanMobileCard
                                v-for="day in daysOfWeek"
                                :key="day"
                                :day="day"
                                :day-label="dayLabels[day]"
                                :meal-types="mealTypes"
                                :meal-type-labels="mealTypeLabels"
                                :get-meal-plan-recipes="getMealPlanRecipes"
                                :on-drag-over="onDragOver"
                                :on-drop="onDrop"
                                :remove-recipe="removeRecipe"
                                :get-recipe-image="getRecipeImage"
                            />
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div
            v-if="showDuplicateModal"
            class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50"
            @click.self="showDuplicateModal = false"
        >
            <div class="bg-white rounded-lg p-6 max-w-md w-full mx-4">
                <h3 class="text-lg font-semibold mb-4">Dupliquer la semaine</h3>
                <div class="mb-4">
                    <label class="block text-sm font-medium text-gray-700 mb-2">
                        Vers quelle semaine ?
                    </label>
                    <input
                        v-model="duplicateWeekStart"
                        type="date"
                        class="w-full px-3 py-2 border rounded-md"
                    />
                </div>
                <div class="flex gap-3 justify-end">
                    <PrimaryButton
                        variant="secondary"
                        @click="showDuplicateModal = false"
                        :disabled="isDuplicating"
                    >
                        Annuler
                    </PrimaryButton>
                    <PrimaryButton
                        @click="duplicateWeek"
                        :loading="isDuplicating"
                    >
                        Dupliquer
                    </PrimaryButton>
                </div>
            </div>
        </div>
    </AppLayout>
</template>
