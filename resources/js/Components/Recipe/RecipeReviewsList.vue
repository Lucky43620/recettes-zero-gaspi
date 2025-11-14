<script setup>
import { useI18n } from 'vue-i18n';

const { t } = useI18n();

defineProps({
    ratings: Array,
});
</script>

<template>
    <div v-if="ratings?.length" class="pt-6 border-t border-gray-200 mb-6">
        <h3 class="text-xl font-semibold text-gray-900 mb-4">
            {{ t('recipe.reviews_count', { count: ratings.length }) }}
        </h3>
        <div class="space-y-4">
            <div
                v-for="rating in ratings"
                :key="rating.user_id"
                class="border-l-2 border-gray-200 pl-4"
            >
                <div class="flex items-center gap-2 mb-2">
                    <span class="font-medium">{{ rating.user.name }}</span>
                    <div class="flex">
                        <svg
                            v-for="star in 5"
                            :key="star"
                            :class="[
                                'w-4 h-4',
                                star <= rating.rating ? 'text-yellow-400' : 'text-gray-300'
                            ]"
                            fill="currentColor"
                            viewBox="0 0 20 20"
                        >
                            <path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z" />
                        </svg>
                    </div>
                </div>
                <p v-if="rating.review" class="text-gray-700 text-sm">
                    {{ rating.review }}
                </p>
            </div>
        </div>
    </div>
</template>
