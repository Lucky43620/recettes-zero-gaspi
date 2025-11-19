<script setup>
import { router } from '@inertiajs/vue3';
import { useI18n } from 'vue-i18n';
import { useMediaConversions } from '@/composables/useMediaConversions';

const { t } = useI18n();
const { getRecipeImage } = useMediaConversions();

const props = defineProps({
    day: String,
    dayLabel: String,
    mealTypes: Array,
    mealTypeLabels: Object,
    getMealPlanRecipes: Function,
    onDragOver: Function,
    onDrop: Function,
    removeRecipe: Function,
});
</script>

<template>
    <div class="bg-white rounded-lg shadow p-4">
        <h3 class="font-semibold text-lg mb-3 text-gray-800">{{ dayLabel }}</h3>
        <div class="space-y-3">
            <div v-for="mealType in mealTypes" :key="`${day}-${mealType}`">
                <div
                    @dragover="(event) => onDragOver(event)"
                    @drop="(event) => onDrop(event, day, mealType)"
                    class="border-2 border-dashed border-gray-200 rounded-lg p-3 min-h-[80px]"
                >
                    <p class="text-sm font-medium text-gray-600 mb-2">{{ mealTypeLabels[mealType] }}</p>
                    <div class="space-y-2">
                        <div
                            v-for="mpr in getMealPlanRecipes(day, mealType)"
                            :key="mpr.id"
                            class="p-2 bg-green-50 rounded border border-green-200 relative group cursor-pointer hover:bg-green-100 transition"
                            @click="router.visit(route('recipes.show', mpr.recipe.slug))"
                        >
                            <button
                                @click.stop="removeRecipe(mpr.id)"
                                class="absolute top-1 right-1 text-red-500 hover:text-red-700"
                            >
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                                </svg>
                            </button>
                            <div class="flex items-start gap-2">
                                <img
                                    :src="getRecipeImage(mpr.recipe, 'thumb')"
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
                </div>
            </div>
        </div>
    </div>
</template>
