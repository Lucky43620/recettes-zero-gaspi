<script setup>
import { ref } from 'vue';
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

const getRecipeImage = (recipe) => {
    return recipe.media?.[0] ? getConversionUrl(recipe.media[0], 'thumb') : '/images/placeholder-recipe.svg';
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
    draggedMealPlanRecipe.value = mealPlanRecipe;
    event.dataTransfer.effectAllowed = 'move';
    event.dataTransfer.setData('text/plain', mealPlanRecipe.id);
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
                            <div class="space-y-2 h-full overflow-y-auto">
                                <div
                                    v-for="mpr in getMealPlanRecipes(day, mealType)"
                                    :key="mpr.id"
                                    draggable="true"
                                    @dragstart="(event) => startDragFromCalendar(event, mpr)"
                                    @dragend="draggedMealPlanRecipe = null"
                                    class="p-2 bg-green-50 rounded border border-green-200 relative group cursor-move hover:bg-green-100 hover:shadow-md transition-all duration-200 hover:scale-105"
                                    @click="router.visit(route('recipes.show', mpr.recipe.slug))"
                                >
                                    <button
                                        @click.stop="removeRecipe(mpr.id)"
                                        class="absolute top-1 right-1 text-red-500 hover:text-red-700 opacity-0 group-hover:opacity-100 transition-opacity duration-150 z-10"
                                        title="Supprimer"
                                    >
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                                        </svg>
                                    </button>
                                    <div class="flex items-start gap-2">
                                        <img
                                            :src="getRecipeImage(mpr.recipe)"
                                            :alt="mpr.recipe.title"
                                            class="w-10 h-10 object-cover rounded flex-shrink-0"
                                        />
                                        <div class="flex-1 min-w-0">
                                            <p class="text-xs font-medium truncate">{{ mpr.recipe.title }}</p>
                                            <p class="text-xs text-gray-500">{{ mpr.servings }} {{ t('recipe.servings').toLowerCase() }}</p>
                                        </div>
                                    </div>
                                    <div class="absolute top-1 left-1 opacity-0 group-hover:opacity-60 transition-opacity duration-150">
                                        <svg class="w-4 h-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 16V4m0 0L3 8m4-4l4 4m6 0v12m0 0l4-4m-4 4l-4-4" />
                                        </svg>
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
