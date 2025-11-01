<script setup>
import { ref, computed } from 'vue';
import AppLayout from '@/Layouts/AppLayout.vue';
import PublicLayout from '@/Layouts/PublicLayout.vue';
import BackButton from '@/Components/Common/BackButton.vue';
import { Link, usePage } from '@inertiajs/vue3';
import { router } from '@inertiajs/vue3';
import { useDifficultyLabels } from '@/composables/useDifficultyLabels';
import RatingStars from '@/Components/Social/RatingStars.vue';
import CommentSection from '@/Components/Social/CommentSection.vue';
import FavoriteButton from '@/Components/Social/FavoriteButton.vue';
import ConfirmationModal from '@/Components/ConfirmationModal.vue';
import PrimaryButton from '@/Components/Common/PrimaryButton.vue';

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

const { getDifficultyLabel } = useDifficultyLabels();
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
            <div class="flex justify-between items-center">
                <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                    {{ recipe.title }}
                </h2>
                <div v-if="canEdit || canDelete" class="flex gap-2">
                    <Link
                        v-if="canEdit"
                        :href="route('recipes.edit', recipe.slug)"
                    >
                        <PrimaryButton>
                            Modifier
                        </PrimaryButton>
                    </Link>
                    <PrimaryButton
                        v-if="canDelete"
                        @click="confirmDeleteRecipe"
                        variant="danger"
                    >
                        Supprimer
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
                <div v-if="!props.usePrivateLayout" class="mb-6">
                    <h1 class="text-3xl font-bold text-gray-900 mb-2">{{ recipe.title }}</h1>
                </div>
                <div class="bg-white overflow-hidden shadow-xl sm:rounded-lg">
                    <div v-if="recipe.media && recipe.media.length > 0" class="h-96 bg-gray-100">
                        <img
                            :src="recipe.media[0].original_url"
                            :alt="recipe.title"
                            class="w-full h-full object-cover"
                        />
                    </div>
                    <div v-else class="h-96 bg-gray-200 flex items-center justify-center">
                        <span class="text-gray-400 text-xl">Pas d'image</span>
                    </div>

                    <div class="p-6">
                        <div class="mb-6 flex items-start justify-between">
                            <div class="flex-1">
                                <p class="text-gray-700 text-lg mb-4">{{ recipe.summary }}</p>
                                <RatingStars :recipe="recipe" :readonly="true" size="md" />
                                <Link
                                    v-if="recipe.steps && recipe.steps.length > 0"
                                    :href="route('recipes.cook', recipe.slug)"
                                    class="mt-4 inline-flex items-center gap-2 px-6 py-3 bg-green-600 text-white font-medium rounded-lg hover:bg-green-700 transition"
                                >
                                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-5 h-5">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M3.75 13.5l10.5-11.25L12 10.5h8.25L9.75 21.75 12 13.5H3.75z" />
                                    </svg>
                                    Mode Cuisine
                                </Link>
                            </div>
                            <FavoriteButton
                                v-if="$page.props.auth.user"
                                :recipe="recipe"
                                :is-favorited="isFavorited"
                            />
                        </div>

                        <div class="grid grid-cols-2 md:grid-cols-4 gap-4 mb-6 p-4 bg-gray-50 rounded-lg">
                            <div>
                                <p class="text-sm text-gray-600">Portions</p>
                                <p class="text-lg font-semibold">{{ recipe.servings }}</p>
                            </div>
                            <div v-if="recipe.prep_minutes">
                                <p class="text-sm text-gray-600">Préparation</p>
                                <p class="text-lg font-semibold">{{ recipe.prep_minutes }} min</p>
                            </div>
                            <div v-if="recipe.cook_minutes">
                                <p class="text-sm text-gray-600">Cuisson</p>
                                <p class="text-lg font-semibold">{{ recipe.cook_minutes }} min</p>
                            </div>
                            <div v-if="recipe.difficulty">
                                <p class="text-sm text-gray-600">Difficulté</p>
                                <p class="text-lg font-semibold">{{ getDifficultyLabel(recipe.difficulty) }}</p>
                            </div>
                        </div>

                        <div v-if="recipe.ingredients && recipe.ingredients.length > 0" class="mb-6">
                            <h3 class="text-xl font-semibold text-gray-900 mb-4">Ingrédients</h3>
                            <div class="bg-white border border-gray-200 rounded-lg divide-y divide-gray-200">
                                <div
                                    v-for="ingredient in recipe.ingredients"
                                    :key="ingredient.id"
                                    class="px-4 py-3 flex items-center justify-between hover:bg-gray-50"
                                >
                                    <span class="text-gray-800">{{ ingredient.name }}</span>
                                    <span v-if="ingredient.pivot.quantity" class="text-gray-600 font-medium">
                                        {{ ingredient.pivot.quantity }} {{ ingredient.pivot.unit_code || '' }}
                                    </span>
                                </div>
                            </div>
                        </div>

                        <div class="mb-6">
                            <h3 class="text-xl font-semibold text-gray-900 mb-4">Étapes</h3>
                            <ol class="space-y-4">
                                <li
                                    v-for="(step, index) in recipe.steps"
                                    :key="step.id"
                                    class="flex gap-4 p-4 bg-gray-50 rounded-lg"
                                >
                                    <span class="flex-shrink-0 w-8 h-8 bg-green-600 text-white rounded-full flex items-center justify-center font-semibold">
                                        {{ index + 1 }}
                                    </span>
                                    <div class="flex-1">
                                        <p class="text-gray-800">{{ step.text }}</p>
                                        <p v-if="step.timer_minutes" class="text-sm text-green-600 mt-2">
                                            ⏱️ {{ step.timer_minutes }} min
                                        </p>
                                    </div>
                                </li>
                            </ol>
                        </div>

                        <div class="pt-6 border-t border-gray-200 mb-6">
                            <div class="flex items-center justify-between text-sm text-gray-600">
                                <Link
                                    :href="`/profile/${recipe.author.id}`"
                                    class="hover:underline"
                                >
                                    Par {{ recipe.author.name }}
                                </Link>
                                <span v-if="recipe.cuisine">{{ recipe.cuisine }}</span>
                            </div>
                        </div>

                        <div class="pt-6 border-t border-gray-200 mb-6">
                            <h3 class="text-xl font-semibold text-gray-900 mb-4">Votre avis</h3>
                            <div v-if="!$page.props.auth.user" class="bg-gray-50 border border-gray-200 rounded-lg p-4 text-center">
                                <p class="text-gray-700 mb-3">Connectez-vous pour noter cette recette</p>
                                <Link :href="route('login')">
                                    <PrimaryButton>
                                        Se connecter
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

                        <div v-if="recipe.ratings?.length" class="pt-6 border-t border-gray-200 mb-6">
                            <h3 class="text-xl font-semibold text-gray-900 mb-4">
                                Avis ({{ recipe.ratings.length }})
                            </h3>
                            <div class="space-y-4">
                                <div
                                    v-for="rating in recipe.ratings"
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

                        <div class="pt-6 border-t border-gray-200">
                            <CommentSection :recipe="recipe" :comments="recipe.comments" :comment-votes="commentVotes" />
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <ConfirmationModal :show="confirmingDeletion" @close="confirmingDeletion = false">
            <template #title>
                Supprimer la recette
            </template>

            <template #content>
                Êtes-vous sûr de vouloir supprimer cette recette ? Cette action est irréversible.
            </template>

            <template #footer>
                <PrimaryButton variant="secondary" @click="confirmingDeletion = false">
                    Annuler
                </PrimaryButton>

                <PrimaryButton
                    variant="danger"
                    class="ms-3"
                    @click="deleteRecipe"
                >
                    Supprimer
                </PrimaryButton>
            </template>
        </ConfirmationModal>
    </component>
</template>
