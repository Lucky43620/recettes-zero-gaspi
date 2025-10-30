<script setup>
import PublicLayout from '@/Layouts/PublicLayout.vue';
import RecipeCard from '@/Components/Recipe/RecipeCard.vue';
import FollowButton from '@/Components/Social/FollowButton.vue';
import { Link } from '@inertiajs/vue3';

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
        <div class="py-8">
            <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
                <div class="bg-white overflow-hidden shadow-xl sm:rounded-lg">
                    <div class="p-6">
                        <div class="flex items-start gap-6 mb-8">
                            <img
                                :src="profileUser.profile_photo_url"
                                :alt="profileUser.name"
                                class="w-24 h-24 rounded-full"
                            />
                            <div class="flex-1">
                                <h1 class="text-2xl font-bold text-gray-900 mb-2">
                                    {{ profileUser.name }}
                                </h1>

                                <p v-if="profileUser.bio" class="text-gray-600 mb-4">
                                    {{ profileUser.bio }}
                                </p>

                                <div class="flex gap-6 mb-4">
                                    <div>
                                        <span class="font-semibold">{{ profileUser.recipes_count }}</span>
                                        <span class="text-gray-600"> recettes</span>
                                    </div>
                                    <Link
                                        :href="`/profile/${profileUser.id}/followers`"
                                        class="hover:underline"
                                    >
                                        <span class="font-semibold">{{ profileUser.followers_count }}</span>
                                        <span class="text-gray-600"> abonnés</span>
                                    </Link>
                                    <Link
                                        :href="`/profile/${profileUser.id}/following`"
                                        class="hover:underline"
                                    >
                                        <span class="font-semibold">{{ profileUser.following_count }}</span>
                                        <span class="text-gray-600"> abonnements</span>
                                    </Link>
                                    <div v-if="averageRating">
                                        <span class="font-semibold">⭐ {{ averageRating }}</span>
                                        <span class="text-gray-600"> moyenne</span>
                                    </div>
                                </div>

                                <FollowButton
                                    v-if="!isOwnProfile && $page.props.auth.user"
                                    :user="profileUser"
                                    :is-following="isFollowing"
                                />
                            </div>
                        </div>

                        <!-- Top 3 recettes -->
                        <div v-if="topRecipes && topRecipes.length" class="mb-8">
                            <h2 class="text-xl font-semibold text-gray-900 mb-4">
                                🏆 Top 3 recettes
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
                                Recettes récentes
                            </h2>
                            <div v-if="recentRecipes && recentRecipes.length" class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                                <RecipeCard
                                    v-for="recipe in recentRecipes"
                                    :key="recipe.id"
                                    :recipe="recipe"
                                />
                            </div>
                            <p v-else class="text-gray-500">
                                Aucune recette publique pour le moment
                            </p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </PublicLayout>
</template>
