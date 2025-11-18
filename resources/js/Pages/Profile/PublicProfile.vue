<script setup>
import PublicLayout from '@/Layouts/PublicLayout.vue';
import RecipeCard from '@/Components/Recipe/RecipeCard.vue';
import FollowButton from '@/Components/Social/FollowButton.vue';
import BackButton from '@/Components/Common/BackButton.vue';
import Badge from '@/Components/UI/Badge.vue';
import { Link } from '@inertiajs/vue3';
import { useI18n } from 'vue-i18n';

const { t } = useI18n();

const props = defineProps({
    profileUser: Object,
    topRecipes: Array,
    recentRecipes: Array,
    averageRating: Number,
    isFollowing: Boolean,
    isOwnProfile: Boolean,
});
</script>

<template>
    <PublicLayout :title="profileUser.name">
        <div class="pt-12 pb-8">
            <div class="max-w-[1920px] mx-auto px-4 sm:px-6 lg:px-8">
                <BackButton class="mb-6" />
                <div class="bg-white overflow-hidden shadow-xl rounded-xl">
                    <div class="relative h-48 bg-gradient-to-r from-green-600 to-orange-500">
                        <div class="absolute inset-0 bg-[url('data:image/svg+xml;base64,PHN2ZyB3aWR0aD0iNjAiIGhlaWdodD0iNjAiIHZpZXdCb3g9IjAgMCA2MCA2MCIgeG1sbnM9Imh0dHA6Ly93d3cudzMub3JnLzIwMDAvc3ZnIj48ZyBmaWxsPSJub25lIiBmaWxsLXJ1bGU9ImV2ZW5vZGQiPjxnIGZpbGw9IiNmZmZmZmYiIGZpbGwtb3BhY2l0eT0iMC4wNSI+PHBhdGggZD0iTTM2IDE0YzYuMDc1IDAgMTEgNC45MjUgMTEgMTEtMCA2LjA3NS00LjkyNSAxMS0xMSAxMS02LjA3NSAwLTExLTQuOTI1LTExLTExIDAtNi4wNzUgNC45MjUtMTEgMTEtMTF6Ii8+PC9nPjwvZz48L3N2Zz4=')] opacity-20"></div>
                    </div>

                    <div class="px-4 md:px-6 pb-6">
                        <div class="flex flex-col md:flex-row md:items-end md:justify-between -mt-16 md:-mt-20 mb-6">
                            <div class="flex flex-col sm:flex-row items-center sm:items-end gap-3 md:gap-4 w-full md:w-auto">
                                <img
                                    :src="profileUser.profile_photo_url"
                                    :alt="profileUser.name"
                                    class="w-24 h-24 md:w-32 md:h-32 rounded-full border-4 border-white shadow-lg bg-white relative z-10"
                                />
                                <div class="pb-2 text-center sm:text-left">
                                    <div class="flex flex-col sm:flex-row items-center gap-2">
                                        <h1 class="text-2xl md:text-3xl font-bold text-gray-900">
                                            {{ profileUser.name }}
                                        </h1>
                                        <Badge v-if="profileUser.is_premium" variant="premium" size="md">{{ t('common.premium') }}</Badge>
                                    </div>
                                    <p v-if="profileUser.bio" class="text-sm md:text-base text-gray-600 mt-1">
                                        {{ profileUser.bio }}
                                    </p>
                                </div>
                            </div>

                            <FollowButton
                                v-if="!isOwnProfile && $page.props.auth.user"
                                :user="profileUser"
                                :is-following="isFollowing"
                                class="mt-4 md:mt-0"
                            />
                        </div>

                        <div class="grid grid-cols-2 md:grid-cols-4 gap-6 mb-8 p-6 bg-gray-50 rounded-lg">
                            <div class="text-center">
                                <div class="text-2xl font-bold text-green-600">{{ profileUser.recipes_count }}</div>
                                <div class="text-sm text-gray-600">{{ t('nav.recipes') }}</div>
                            </div>
                            <Link
                                :href="`/profile/${profileUser.id}/followers`"
                                class="text-center hover:bg-gray-100 rounded-lg transition-colors p-2"
                            >
                                <div class="text-2xl font-bold text-green-600">{{ profileUser.followers_count }}</div>
                                <div class="text-sm text-gray-600">{{ t('profile.followers') }}</div>
                            </Link>
                            <Link
                                :href="`/profile/${profileUser.id}/following`"
                                class="text-center hover:bg-gray-100 rounded-lg transition-colors p-2"
                            >
                                <div class="text-2xl font-bold text-green-600">{{ profileUser.following_count }}</div>
                                <div class="text-sm text-gray-600">{{ t('profile.following') }}</div>
                            </Link>
                            <div v-if="averageRating" class="text-center">
                                <div class="text-2xl font-bold text-yellow-500">⭐ {{ averageRating }}</div>
                                <div class="text-sm text-gray-600">{{ t('profile.average_rating') }}</div>
                            </div>
                        </div>

                        <!-- Top 3 recettes -->
                        <div v-if="topRecipes && topRecipes.length" class="mb-8">
                            <h2 class="text-lg md:text-xl font-semibold text-gray-900 mb-3 md:mb-4">
                                {{ t('profile.top_3_recipes') }}
                            </h2>
                            <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                                <RecipeCard
                                    v-for="recipe in topRecipes"
                                    :key="recipe.id"
                                    :recipe="recipe"
                                />
                            </div>
                        </div>

                        <!-- Recettes récentes -->
                        <div>
                            <h2 class="text-xl font-semibold text-gray-900 mb-4">
                                {{ t('profile.recent_recipes') }}
                            </h2>
                            <div v-if="recentRecipes && recentRecipes.length" class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                                <RecipeCard
                                    v-for="recipe in recentRecipes"
                                    :key="recipe.id"
                                    :recipe="recipe"
                                />
                            </div>
                            <p v-else class="text-gray-500">
                                {{ t('profile.no_public_recipes') }}
                            </p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </PublicLayout>
</template>
