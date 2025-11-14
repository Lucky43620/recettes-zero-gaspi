<script setup>
import { router } from '@inertiajs/vue3';
import { useI18n } from 'vue-i18n';

const { t } = useI18n();

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

const getRecipeImage = (recipe) => {
    return recipe.media?.[0]?.original_url || '/images/placeholder-recipe.svg';
};

const handleDragOver = (event) => {
    props.onDragOver(event);
};

const handleDrop = (event, day, mealType) => {
    props.onDrop(event, day, mealType);
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
                            @dragover="handleDragOver"
                            @drop="(event) => handleDrop(event, day, mealType)"
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
                                            <p class="text-xs text-gray-500">{{ mpr.servings }} {{ t('recipe.servings').toLowerCase() }}</p>
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
