<script setup>
import AppLayout from '@/Layouts/AppLayout.vue';
import MealPlanMobileCard from '@/Components/MealPlanMobileCard.vue';
import RecipeDraggableList from '@/Components/MealPlan/RecipeDraggableList.vue';
import WeekNavigation from '@/Components/MealPlan/WeekNavigation.vue';
import FreeLimitBanner from '@/Components/MealPlan/FreeLimitBanner.vue';
import MealPlanGrid from '@/Components/MealPlan/MealPlanGrid.vue';
import DuplicateWeekModal from '@/Components/MealPlan/DuplicateWeekModal.vue';
import PrimaryButton from '@/Components/Common/PrimaryButton.vue';
import { ref, computed } from 'vue';
import { router } from '@inertiajs/vue3';
import { useI18n } from 'vue-i18n';

const { t } = useI18n();

const props = defineProps({
    mealPlan: Object,
    weekStart: String,
    userRecipes: Array,
    favoriteRecipes: Array,
    isPremium: Boolean,
    recipeLimit: Number,
});

const daysOfWeek = ['monday', 'tuesday', 'wednesday', 'thursday', 'friday', 'saturday', 'sunday'];
const dayLabels = {
    monday: t('time.days.monday'),
    tuesday: t('time.days.tuesday'),
    wednesday: t('time.days.wednesday'),
    thursday: t('time.days.thursday'),
    friday: t('time.days.friday'),
    saturday: t('time.days.saturday'),
    sunday: t('time.days.sunday'),
};

const mealTypes = ['breakfast', 'lunch', 'dinner', 'snack'];
const mealTypeLabels = {
    breakfast: t('meal_plan.breakfast'),
    lunch: t('meal_plan.lunch'),
    dinner: t('meal_plan.dinner'),
    snack: t('meal_plan.snack'),
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
    <AppLayout :title="t('meal_plan.title')">
        <template #header>
            <div class="flex justify-between items-center">
                <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                    {{ t('meal_plan.title') }}
                </h2>
                <div class="flex gap-3">
                    <PrimaryButton
                        @click="generateShoppingList"
                        :loading="isGeneratingShoppingList"
                        variant="warning"
                    >
                        {{ t('meal_plan.generate_shopping_list') }}
                    </PrimaryButton>
                    <PrimaryButton @click="openDuplicateModal">
                        {{ t('meal_plan.duplicate_week') }}
                    </PrimaryButton>
                </div>
            </div>
        </template>

        <div class="py-12">
            <div class="max-w-[1920px] mx-auto sm:px-6 lg:px-8">
                <WeekNavigation
                    :week-start="weekStart"
                    :prev-week="prevWeek"
                    :next-week="nextWeek"
                    @navigate="navigateWeek"
                />

                <FreeLimitBanner
                    v-if="!isPremium && recipeLimit"
                    :current-count="mealPlan.meal_plan_recipes.length"
                    :limit="recipeLimit"
                />

                <div class="grid grid-cols-1 lg:grid-cols-4 gap-6">
                    <div class="lg:col-span-1 space-y-6">
                        <RecipeDraggableList
                            :recipes="userRecipes"
                            :title="t('profile.my_recipes')"
                            :empty-message="t('meal_plan.no_recipes_yet')"
                            :empty-link-href="route('recipes.create')"
                            :empty-link-text="t('recipe.create_recipe')"
                            @dragstart="onDragStart"
                        />

                        <RecipeDraggableList
                            v-if="favoriteRecipes && favoriteRecipes.length"
                            :recipes="favoriteRecipes"
                            :title="t('profile.my_favorites')"
                            bg-class="bg-orange-50"
                            hover-class="hover:bg-orange-100"
                            @dragstart="onDragStart"
                        />
                    </div>

                    <div class="lg:col-span-3">
                        <div class="hidden md:block">
                            <MealPlanGrid
                                :days-of-week="daysOfWeek"
                                :day-labels="dayLabels"
                                :meal-types="mealTypes"
                                :meal-type-labels="mealTypeLabels"
                                :get-meal-plan-recipes="getMealPlanRecipes"
                                :on-drag-over="onDragOver"
                                :on-drop="onDrop"
                                :remove-recipe="removeRecipe"
                            />
                        </div>

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

        <DuplicateWeekModal
            :show="showDuplicateModal"
            v-model:week-start="duplicateWeekStart"
            :is-duplicating="isDuplicating"
            @close="showDuplicateModal = false"
            @duplicate="duplicateWeek"
        />
    </AppLayout>
</template>
