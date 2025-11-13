<script setup>
import { ref } from 'vue';
import { useForm } from '@inertiajs/vue3';
import { useI18n } from 'vue-i18n';
import ConfirmationModal from '@/Components/ConfirmationModal.vue';
import DangerButton from '@/Components/DangerButton.vue';
import SecondaryButton from '@/Components/SecondaryButton.vue';

const { t } = useI18n();

const props = defineProps({
    recipe: Object,
    userRating: Object,
    readonly: {
        type: Boolean,
        default: false,
    },
    size: {
        type: String,
        default: 'md',
    },
});

const showForm = ref(false);
const confirmingDeletion = ref(false);
const form = useForm({
    rating: props.userRating?.rating || 0,
    review: props.userRating?.review || '',
});

const hoverRating = ref(0);

function submitRating() {
    form.post(route('ratings.store', props.recipe.slug), {
        preserveScroll: true,
        onSuccess: () => {
            showForm.value = false;
        },
    });
}

function confirmDeleteRating() {
    confirmingDeletion.value = true;
}

function deleteRating() {
    form.delete(route('ratings.destroy', props.recipe.slug), {
        preserveScroll: true,
        onSuccess: () => {
            confirmingDeletion.value = false;
        },
    });
}

const sizeClasses = {
    sm: 'w-4 h-4',
    md: 'w-6 h-6',
    lg: 'w-8 h-8',
};
</script>

<template>
    <div>
        <div v-if="readonly" class="flex items-center gap-2">
            <div class="flex">
                <svg
                    v-for="star in 5"
                    :key="star"
                    :class="sizeClasses[size]"
                    viewBox="0 0 24 24"
                    xmlns="http://www.w3.org/2000/svg"
                >
                    <defs>
                        <linearGradient :id="`star-gradient-${star}-${recipe.id}`">
                            <stop
                                :offset="star <= (recipe.rating_avg || 0)
                                    ? '100%'
                                    : star - 1 < (recipe.rating_avg || 0)
                                        ? `${((recipe.rating_avg || 0) - (star - 1)) * 100}%`
                                        : '0%'"
                                stop-color="#FBBF24"
                            />
                            <stop
                                :offset="star <= (recipe.rating_avg || 0)
                                    ? '100%'
                                    : star - 1 < (recipe.rating_avg || 0)
                                        ? `${((recipe.rating_avg || 0) - (star - 1)) * 100}%`
                                        : '0%'"
                                stop-color="#D1D5DB"
                            />
                        </linearGradient>
                    </defs>
                    <path
                        :fill="`url(#star-gradient-${star}-${recipe.id})`"
                        d="M12 2l3.09 6.26L22 9.27l-5 4.87 1.18 6.88L12 17.77l-6.18 3.25L7 14.14 2 9.27l6.91-1.01L12 2z"
                    />
                </svg>
            </div>
            <span class="text-sm text-gray-600">
                {{ recipe.rating_avg ? Number(recipe.rating_avg).toFixed(1) : '0.0' }} ({{ recipe.rating_count || 0 }})
            </span>
        </div>

        <div v-else>
            <div v-if="!showForm" class="space-y-2">
                <div class="flex items-center gap-2">
                    <div class="flex">
                        <svg
                            v-for="star in 5"
                            :key="star"
                            :class="[
                                sizeClasses[size],
                                star <= (userRating?.rating || 0) ? 'text-yellow-400' : 'text-gray-300'
                            ]"
                            fill="currentColor"
                            viewBox="0 0 20 20"
                        >
                            <path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z" />
                        </svg>
                    </div>
                    <button
                        @click="showForm = true"
                        class="text-sm text-green-600 hover:text-green-800"
                    >
                        {{ userRating ? t('profile.edit_rating') : t('profile.rate_recipe') }}
                    </button>
                </div>

                <p v-if="userRating?.review" class="text-sm text-gray-700 italic">
                    "{{ userRating.review }}"
                </p>

                <button
                    v-if="userRating"
                    @click="confirmDeleteRating"
                    class="text-sm text-red-600 hover:text-red-800"
                >
                    {{ t('profile.delete_rating') }}
                </button>
            </div>

            <form v-else @submit.prevent="submitRating" class="space-y-4">
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">{{ t('profile.rating_label') }}</label>
                    <div class="flex gap-1">
                        <button
                            v-for="star in 5"
                            :key="star"
                            type="button"
                            @click="form.rating = star"
                            @mouseenter="hoverRating = star"
                            @mouseleave="hoverRating = 0"
                            class="focus:outline-none"
                        >
                            <svg
                                :class="[
                                    'w-8 h-8 transition',
                                    star <= (hoverRating || form.rating) ? 'text-yellow-400' : 'text-gray-300'
                                ]"
                                fill="currentColor"
                                viewBox="0 0 20 20"
                            >
                                <path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z" />
                            </svg>
                        </button>
                    </div>
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">
                        {{ t('profile.review_optional') }}
                    </label>
                    <textarea
                        v-model="form.review"
                        rows="3"
                        class="w-full border-gray-300 rounded-md shadow-sm focus:border-green-500 focus:ring-green-500"
                        :placeholder="t('profile.share_review_experience')"
                    ></textarea>
                </div>

                <div class="flex gap-2">
                    <button
                        type="submit"
                        :disabled="form.processing || form.rating === 0"
                        class="px-4 py-2 bg-green-600 text-white rounded-md hover:bg-green-700 disabled:opacity-50"
                    >
                        {{ t('common.save') }}
                    </button>
                    <button
                        type="button"
                        @click="showForm = false"
                        class="px-4 py-2 bg-gray-200 text-gray-700 rounded-md hover:bg-gray-300"
                    >
                        {{ t('common.cancel') }}
                    </button>
                </div>
            </form>

            <ConfirmationModal :show="confirmingDeletion" @close="confirmingDeletion = false">
                <template #title>
                    {{ t('profile.delete_rating') }}
                </template>

                <template #content>
                    {{ t('profile.delete_rating_confirmation') }}
                </template>

                <template #footer>
                    <SecondaryButton @click="confirmingDeletion = false">
                        {{ t('common.cancel') }}
                    </SecondaryButton>

                    <DangerButton
                        class="ms-3"
                        @click="deleteRating"
                    >
                        {{ t('common.delete') }}
                    </DangerButton>
                </template>
            </ConfirmationModal>
        </div>
    </div>
</template>
