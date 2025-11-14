<script setup>
import { Link } from '@inertiajs/vue3';
import { useI18n } from 'vue-i18n';

const { t } = useI18n();

defineProps({
    recipes: Array,
});
</script>

<template>
    <div class="bg-white rounded-lg shadow">
        <div class="p-6 border-b">
            <h2 class="text-lg font-semibold text-gray-900">{{ t('admin.top_recipes') }}</h2>
        </div>
        <div class="divide-y">
            <div
                v-for="recipe in recipes.slice(0, 5)"
                :key="recipe.id"
                class="p-4 hover:bg-gray-50"
            >
                <div class="flex items-center justify-between">
                    <div class="flex-1">
                        <Link :href="`/recipes/${recipe.slug}`" class="text-sm font-medium text-gray-900 hover:text-green-600">
                            {{ recipe.title }}
                        </Link>
                        <p class="text-sm text-gray-500 mt-1">
                            {{ t('admin.by_author', { author: recipe.author.name }) }}
                        </p>
                    </div>
                    <div class="text-right">
                        <div class="flex items-center gap-1">
                            <span class="text-yellow-500">⭐</span>
                            <span class="text-sm font-medium">{{ recipe.rating_avg || 0 }}</span>
                        </div>
                        <p class="text-xs text-gray-400">{{ t('admin.ratings_count', { count: recipe.rating_count }) }}</p>
                    </div>
                </div>
            </div>
        </div>
    </div>
</template>
