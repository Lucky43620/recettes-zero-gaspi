<script setup>
import { ref, computed } from 'vue';
import { useI18n } from 'vue-i18n';
import AppLayout from '@/Layouts/AppLayout.vue';
import PublicLayout from '@/Layouts/PublicLayout.vue';
import BackButton from '@/Components/Common/BackButton.vue';
import { Link, usePage } from '@inertiajs/vue3';
import { router } from '@inertiajs/vue3';
import RatingStars from '@/Components/Social/RatingStars.vue';
import CommentSection from '@/Components/Social/CommentSection.vue';
import CooksnapSection from '@/Components/Social/CooksnapSection.vue';
import FavoriteButton from '@/Components/Social/FavoriteButton.vue';
import ConfirmationModal from '@/Components/ConfirmationModal.vue';
import PrimaryButton from '@/Components/Common/PrimaryButton.vue';
import RecipeHeader from '@/Components/Recipe/RecipeHeader.vue';
import RecipeStats from '@/Components/Recipe/RecipeStats.vue';
import RecipeIngredientsList from '@/Components/Recipe/RecipeIngredientsList.vue';
import RecipeStepsList from '@/Components/Recipe/RecipeStepsList.vue';
import RecipeReviewsList from '@/Components/Recipe/RecipeReviewsList.vue';

const { t } = useI18n();

const props = defineProps({
    recipe: Object,
    userRating: Object,
    isFavorited: Boolean,
    commentVotes: Object,
    canEdit: Boolean,
    canDelete: Boolean,
    usePrivateLayout: {
        type: Boolean,
        default: false
    },
});

const confirmingDeletion = ref(false);
const page = usePage();
const isAuthenticated = computed(() => !!page.props.auth.user);

function confirmDeleteRecipe() {
    confirmingDeletion.value = true;
}

function deleteRecipe() {
    router.delete(route('recipes.destroy', props.recipe.slug));
    confirmingDeletion.value = false;
}
</script>

<template>
    <component :is="props.usePrivateLayout ? AppLayout : PublicLayout" :title="recipe.title">
        <template v-if="props.usePrivateLayout" #header>
            <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-3">
                <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                    {{ recipe.title }}
                </h2>
                <div v-if="canEdit || canDelete" class="flex flex-col sm:flex-row gap-2 w-full sm:w-auto">
                    <Link
                        v-if="canEdit"
                        :href="route('recipes.edit', recipe.slug)"
                        class="w-full sm:w-auto"
                    >
                        <PrimaryButton class="w-full">
                            {{ t('common.edit') }}
                        </PrimaryButton>
                    </Link>
                    <PrimaryButton
                        v-if="canDelete"
                        @click="confirmDeleteRecipe"
                        variant="danger"
                        class="w-full sm:w-auto"
                    >
                        {{ t('common.delete') }}
                    </PrimaryButton>
                </div>
            </div>
        </template>

        <div :class="props.usePrivateLayout ? 'py-12' : 'py-8'">
            <div class="max-w-4xl mx-auto sm:px-6 lg:px-8">
                <BackButton
                    :href="props.usePrivateLayout ? route('recipes.my') : null"
                    class="mb-6"
                />
                <div class="bg-white overflow-hidden shadow-xl sm:rounded-lg">
                    <RecipeHeader :recipe="recipe" :show-title="!props.usePrivateLayout" />

                    <div class="p-4 md:p-6">
                        <div class="mb-6 flex flex-col sm:flex-row items-start justify-between gap-4">
                            <div class="flex-1 w-full">
                                <p class="text-gray-700 text-base md:text-lg mb-4">{{ recipe.summary }}</p>
                                <RatingStars :recipe="recipe" :readonly="true" size="md" />
                                <Link
                                    v-if="recipe.steps && recipe.steps.length > 0"
                                    :href="route('recipes.cook', recipe.slug)"
                                    class="mt-4 w-full sm:w-auto inline-flex items-center justify-center gap-2 px-6 py-3 bg-green-600 text-white font-medium rounded-lg hover:bg-green-700 active:bg-green-800 transition"
                                >
                                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-5 h-5 flex-shrink-0">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M3.75 13.5l10.5-11.25L12 10.5h8.25L9.75 21.75 12 13.5H3.75z" />
                                    </svg>
                                    {{ t('cook.title') }}
                                </Link>
                            </div>
                            <FavoriteButton
                                v-if="$page.props.auth.user"
                                :recipe="recipe"
                                :is-favorited="isFavorited"
                                class="sm:mt-0"
                            />
                        </div>

                        <RecipeStats :recipe="recipe" />

                        <RecipeIngredientsList :ingredients="recipe.ingredients" />

                        <RecipeStepsList :steps="recipe.steps" />

                        <div class="pt-6 border-t border-gray-200 mb-6">
                            <div class="flex items-center justify-between text-sm text-gray-600">
                                <Link
                                    :href="`/profile/${recipe.author.id}`"
                                    class="hover:underline"
                                >
                                    {{ t('recipe.by_author', { author: recipe.author.name }) }}
                                </Link>
                                <span v-if="recipe.cuisine">{{ recipe.cuisine }}</span>
                            </div>
                        </div>

                        <div class="pt-6 border-t border-gray-200 mb-6">
                            <h3 class="text-xl font-semibold text-gray-900 mb-4">{{ t('recipe.your_rating') }}</h3>
                            <div v-if="!$page.props.auth.user" class="bg-gray-50 border border-gray-200 rounded-lg p-4 text-center">
                                <p class="text-gray-700 mb-3">{{ t('recipe.login_to_rate') }}</p>
                                <Link :href="route('login')">
                                    <PrimaryButton>
                                        {{ t('auth.login') }}
                                    </PrimaryButton>
                                </Link>
                            </div>
                            <RatingStars
                                v-else
                                :recipe="recipe"
                                :user-rating="userRating"
                                :readonly="false"
                            />
                        </div>

                        <RecipeReviewsList :ratings="recipe.ratings" />

                        <div class="pt-6 border-t border-gray-200 mb-6">
                            <CooksnapSection :recipe="recipe" :cooksnaps="recipe.cooksnaps" />
                        </div>

                        <div class="pt-6 border-t border-gray-200">
                            <CommentSection :recipe="recipe" :comments="recipe.comments" :comment-votes="commentVotes" />
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <ConfirmationModal :show="confirmingDeletion" @close="confirmingDeletion = false">
            <template #title>
                {{ t('recipe.delete_recipe') }}
            </template>

            <template #content>
                {{ t('recipe.delete_recipe_message') }}
            </template>

            <template #footer>
                <PrimaryButton variant="secondary" @click="confirmingDeletion = false">
                    {{ t('common.cancel') }}
                </PrimaryButton>

                <PrimaryButton
                    variant="danger"
                    class="ms-3"
                    @click="deleteRecipe"
                >
                    {{ t('common.delete') }}
                </PrimaryButton>
            </template>
        </ConfirmationModal>
    </component>
</template>
