<script setup>
import { ref } from 'vue';
import AppLayout from '@/Layouts/AppLayout.vue';
import RecipeCard from '@/Components/Recipe/RecipeCard.vue';
import PrimaryButton from '@/Components/PrimaryButton.vue';
import AddRecipesModal from '@/Components/Collection/AddRecipesModal.vue';
import { useForm, router } from '@inertiajs/vue3';
import { useI18n } from 'vue-i18n';

const { t } = useI18n();

const props = defineProps({
    collection: Object,
    canEdit: Boolean,
    userRecipes: {
        type: Array,
        default: () => []
    }
});

const deleteForm = useForm({});
const showAddRecipesModal = ref(false);

function deleteCollection() {
    if (confirm(t('collections.delete_confirmation'))) {
        deleteForm.delete(route('collections.destroy', props.collection.id));
    }
}

function removeRecipe(recipeId) {
    if (confirm(t('collections.remove_recipe_confirmation'))) {
        router.delete(route('collections.recipes.remove', [props.collection.id, recipeId]), {
            preserveScroll: true,
        });
    }
}
</script>

<template>
    <AppLayout :title="collection.name">
        <template #header>
            <div class="flex justify-between items-center">
                <div>
                    <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                        {{ collection.name }}
                    </h2>
                    <p v-if="collection.description" class="text-sm text-gray-600 mt-1">
                        {{ collection.description }}
                    </p>
                </div>
                <div v-if="canEdit" class="flex gap-3">
                    <PrimaryButton @click="showAddRecipesModal = true">
                        {{ t('collections.add_recipes') }}
                    </PrimaryButton>
                    <PrimaryButton
                        @click="deleteCollection"
                        variant="danger"
                    >
                        {{ t('common.delete') }}
                    </PrimaryButton>
                </div>
            </div>
        </template>

        <div class="py-12">
            <div class="max-w-[1920px] mx-auto px-4 sm:px-6 lg:px-8">
                <div class="mb-6 flex items-center justify-between">
                    <div class="text-sm text-gray-600">
                        {{ collection.recipes.length }} {{ t('collections.recipes_count') }} •
                        <span v-if="collection.is_public" class="text-green-600">{{ t('collections.public') }}</span>
                        <span v-else>{{ t('collections.private') }}</span>
                    </div>
                    <div class="text-sm text-gray-600">
                        {{ t('collections.by_user', { name: collection.user.name }) }}
                    </div>
                </div>

                <div v-if="collection.recipes.length" class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                    <div v-for="recipe in collection.recipes" :key="recipe.id" class="relative group">
                        <RecipeCard :recipe="recipe" />
                        <button
                            v-if="canEdit"
                            @click="removeRecipe(recipe.id)"
                            class="absolute top-2 right-2 bg-red-600 text-white p-2 rounded-full opacity-0 group-hover:opacity-100 transition-opacity hover:bg-red-700"
                            :title="t('collections.remove_recipe')"
                        >
                            <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd" d="M4.293 4.293a1 1 0 011.414 0L10 8.586l4.293-4.293a1 1 0 111.414 1.414L11.414 10l4.293 4.293a1 1 0 01-1.414 1.414L10 11.414l-4.293 4.293a1 1 0 01-1.414-1.414L8.586 10 4.293 5.707a1 1 0 010-1.414z" clip-rule="evenodd"/>
                            </svg>
                        </button>
                    </div>
                </div>

                <div v-else class="bg-white rounded-lg shadow p-8 text-center">
                    <p class="text-gray-600 mb-4">
                        {{ t('collections.empty') }}
                    </p>
                    <PrimaryButton v-if="canEdit" @click="showAddRecipesModal = true">
                        {{ t('collections.add_first_recipe') }}
                    </PrimaryButton>
                </div>
            </div>
        </div>

        <AddRecipesModal
            :show="showAddRecipesModal"
            :collection="collection"
            :user-recipes="userRecipes"
            @close="showAddRecipesModal = false"
        />
    </AppLayout>
</template>
