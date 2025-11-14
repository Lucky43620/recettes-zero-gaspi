<script setup>
import { useI18n } from 'vue-i18n';
import { Link } from '@inertiajs/vue3';

const { t } = useI18n();

defineProps({
    recipes: Array,
    title: String,
    emptyMessage: String,
    emptyLinkHref: String,
    emptyLinkText: String,
    bgClass: {
        type: String,
        default: 'bg-gray-50'
    },
    hoverClass: {
        type: String,
        default: 'hover:bg-gray-100'
    },
});

const emit = defineEmits(['dragstart']);

const getRecipeImage = (recipe) => {
    return recipe.media?.[0]?.original_url || '/images/placeholder-recipe.jpg';
};
</script>

<template>
    <div class="bg-white rounded-lg shadow p-4">
        <h3 class="font-semibold text-lg mb-4">{{ title }}</h3>
        <p v-if="!recipes || !recipes.length" class="text-sm text-gray-500 mb-4">
            {{ emptyMessage }}
            <Link v-if="emptyLinkHref" :href="emptyLinkHref" class="text-green-600 hover:underline">
                {{ emptyLinkText }}
            </Link>
        </p>
        <p v-else class="text-sm text-gray-500 mb-4">
            {{ t('meal_plan.drag_drop_recipes') }}
        </p>
        <div class="space-y-2 max-h-[400px] overflow-y-auto">
            <div
                v-for="recipe in recipes"
                :key="recipe.id"
                draggable="true"
                @dragstart="emit('dragstart', recipe)"
                :class="[bgClass, hoverClass]"
                class="p-2 rounded cursor-move transition"
            >
                <div class="flex items-center gap-2">
                    <img
                        :src="getRecipeImage(recipe)"
                        :alt="recipe.title"
                        class="w-12 h-12 object-cover rounded"
                    />
                    <div class="flex-1 min-w-0">
                        <p class="text-sm font-medium truncate">{{ recipe.title }}</p>
                        <p class="text-xs text-gray-500">{{ recipe.servings }} {{ t('recipe.servings').toLowerCase() }}</p>
                    </div>
                </div>
            </div>
        </div>
    </div>
</template>
