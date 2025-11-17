<script setup>
import { ref, computed } from 'vue';
import { router } from '@inertiajs/vue3';
import { useI18n } from 'vue-i18n';
import { useMediaConversions } from '@/composables/useMediaConversions';

const { t } = useI18n();
const { getConversionUrl } = useMediaConversions();

const props = defineProps({
    daysOfWeek: Array,
    dayLabels: Object,
    mealTypes: Array,
    mealTypeLabels: Object,
    getMealPlanRecipes: Function,
    onDragOver: Function,
    onDrop: Function,
    removeRecipe: Function,
});

const draggedMealPlanRecipe = ref(null);
const dragOverCell = ref(null);
const isDragging = ref(false);

const getRecipeImage = (recipe) => {
    return recipe.media?.[0] ? getConversionUrl(recipe.media[0], 'thumb') : '/images/placeholder-recipe.svg';
};

const getGridClasses = (recipeCount) => {
    if (recipeCount === 0) return '';
    if (recipeCount === 1) return 'grid-cols-1';
    return 'grid-cols-2';
};

const handleDragOver = (event, day, mealType) => {
    event.preventDefault();
    dragOverCell.value = `${day}-${mealType}`;
    props.onDragOver(event);
};

const handleDrop = (event, day, mealType) => {
    dragOverCell.value = null;

    if (draggedMealPlanRecipe.value) {
        router.post(route('meal-plans.recipes.move', draggedMealPlanRecipe.value.id), {
            day_of_week: day,
            meal_type: mealType,
        }, {
            preserveScroll: true,
        });
        draggedMealPlanRecipe.value = null;
    } else {
        props.onDrop(event, day, mealType);
    }
};

const onDragLeave = () => {
    dragOverCell.value = null;
};

const startDragFromCalendar = (event, mealPlanRecipe) => {
    isDragging.value = true;
    draggedMealPlanRecipe.value = mealPlanRecipe;
    event.dataTransfer.effectAllowed = 'move';
    event.dataTransfer.setData('text/plain', mealPlanRecipe.id);
};

const endDrag = () => {
    setTimeout(() => {
        isDragging.value = false;
    }, 100);
};

const handleCardClick = (slug) => {
    if (!isDragging.value) {
        router.visit(route('recipes.show', slug));
    }
};

const isDragOver = (day, mealType) => {
    return dragOverCell.value === `${day}-${mealType}`;
};
</script>

<template>
    <div class="bg-white rounded-lg shadow overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase w-32">
                            {{ t('meal_plan.meals') }}
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
                            @dragover="(event) => handleDragOver(event, day, mealType)"
                            @dragleave="onDragLeave"
                            @drop="(event) => handleDrop(event, day, mealType)"
                            :class="[
                                'px-2 py-2 align-top border-l border-gray-200 h-[180px] transition-all duration-200',
                                isDragOver(day, mealType) ? 'bg-green-50 ring-2 ring-green-400 ring-inset' : ''
                            ]"
                        >
                            <div
                                :class="[
                                    'grid gap-1.5 h-full auto-rows-fr',
                                    getGridClasses(getMealPlanRecipes(day, mealType).length)
                                ]"
                            >
                                <div
                                    v-for="mpr in getMealPlanRecipes(day, mealType)"
                                    :key="mpr.id"
                                    draggable="true"
                                    @dragstart="(event) => startDragFromCalendar(event, mpr)"
                                    @dragend="() => { draggedMealPlanRecipe = null; endDrag(); }"
                                    @click="() => handleCardClick(mpr.recipe.slug)"
                                    class="group relative cursor-move bg-gray-100 rounded-lg border-2 border-green-200 hover:border-green-400 hover:shadow-lg transition-all duration-200 hover:scale-105 overflow-hidden min-h-0"
                                    style="aspect-ratio: 1/1;"
                                    :title="`${mpr.recipe.title} (${mpr.servings} pers.)`"
                                >
                                    <button
                                        @click.stop="removeRecipe(mpr.id)"
                                        class="absolute top-1 right-1 w-6 h-6 bg-red-500 text-white rounded-full opacity-0 group-hover:opacity-100 transition-opacity duration-150 z-20 flex items-center justify-center hover:bg-red-600 shadow-md"
                                        title="Supprimer"
                                    >
                                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2.5">
                                            <path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12" />
                                        </svg>
                                    </button>

                                    <div class="absolute top-1 left-1 w-6 h-6 opacity-0 group-hover:opacity-80 transition-opacity duration-150 z-10">
                                        <svg class="w-6 h-6 text-white drop-shadow-md" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2">
                                            <path stroke-linecap="round" stroke-linejoin="round" d="M7 16V4m0 0L3 8m4-4l4 4m6 0v12m0 0l4-4m-4 4l-4-4" />
                                        </svg>
                                    </div>

                                    <img
                                        :src="getRecipeImage(mpr.recipe)"
                                        :alt="mpr.recipe.title"
                                        class="w-full h-full object-cover"
                                    />

                                    <div class="absolute inset-0 bg-gradient-to-t from-black/60 via-black/20 to-transparent opacity-0 group-hover:opacity-100 transition-opacity duration-200"></div>

                                    <div class="absolute inset-0 opacity-0 group-hover:opacity-100 transition-opacity duration-200 flex flex-col items-center justify-center p-2">
                                        <div class="text-center">
                                            <p class="text-xs font-bold text-white line-clamp-3 leading-tight drop-shadow-lg mb-1">
                                                {{ mpr.recipe.title }}
                                            </p>
                                            <p class="text-[10px] text-white/95 font-medium">
                                                {{ mpr.servings }} pers.
                                            </p>
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
</template>
