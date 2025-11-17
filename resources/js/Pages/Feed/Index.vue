<script setup>
import AppLayout from '@/Layouts/AppLayout.vue';
import RecipeCard from '@/Components/Recipe/RecipeCard.vue';
import Pagination from '@/Components/Common/Pagination.vue';
import EmptyState from '@/Components/Common/EmptyState.vue';
import { useI18n } from 'vue-i18n';

const { t } = useI18n();

const props = defineProps({
    feed: Object,
});
</script>

<template>
    <AppLayout :title="t('feed.title')">
        <template #header>
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                {{ t('feed.personalized_feed') }}
            </h2>
        </template>

        <div class="py-12">
            <div class="max-w-[1920px] mx-auto px-4 sm:px-6 lg:px-8">
                <div v-if="feed && feed.data && feed.data.length" class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                    <RecipeCard
                        v-for="recipe in feed.data"
                        :key="recipe.id"
                        :recipe="recipe"
                    />
                </div>

                <EmptyState
                    v-else
                    icon="search"
                    :title="t('feed.not_following_anyone')"
                    :message="t('feed.follow_chefs_message')"
                    :action-label="t('feed.discover_recipes')"
                    :action-href="route('recipes.index')"
                />

                <div v-if="feed.links" class="mt-6 flex justify-center">
                    <Pagination :links="feed.links" />
                </div>
            </div>
        </div>
    </AppLayout>
</template>
