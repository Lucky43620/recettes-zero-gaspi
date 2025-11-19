<script setup>
import { ref, computed } from 'vue';
import AppLayout from '@/Layouts/AppLayout.vue';
import RecipeCard from '@/Components/Recipe/RecipeCard.vue';
import PrimaryButton from '@/Components/PrimaryButton.vue';
import AddRecipesModal from '@/Components/Collection/AddRecipesModal.vue';
import BackButton from '@/Components/Common/BackButton.vue';
import FormInput from '@/Components/Common/FormInput.vue';
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
const searchQuery = ref('');

const filteredRecipes = computed(() => {
    if (!props.collection?.recipes) return [];

    if (!searchQuery.value) {
        return props.collection.recipes;
    }

    const query = searchQuery.value.toLowerCase().trim();
    return props.collection.recipes.filter(recipe => {
        const title = (recipe.title || '').toLowerCase();
        const description = (recipe.description || '').toLowerCase();
        const summary = (recipe.summary || '').toLowerCase();

        return title.includes(query) ||
               description.includes(query) ||
               summary.includes(query);
    });
});

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
            <div class="space-y-4">
                <BackButton :href="route('collections.public')" />

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
            </div>
        </template>

        <div class="py-12">
            <div class="max-w-[1920px] mx-auto px-4 sm:px-6 lg:px-8 space-y-6">
                <div class="flex flex-col sm:flex-row items-start sm:items-center justify-between gap-4">
                    <div class="text-sm text-gray-600">
                        {{ collection.recipes.length }} {{ t('collections.recipes_count') }} •
                        <span v-if="collection.is_public" class="text-green-600">{{ t('collections.public') }}</span>
                        <span v-else>{{ t('collections.private') }}</span>
                        •
                        {{ t('collections.by_user', { name: collection.user.name }) }}
                    </div>
                </div>

                <!-- Search Bar -->
                <div v-if="collection.recipes.length > 0" class="bg-white rounded-lg shadow p-4">
                    <FormInput
                        v-model="searchQuery"
                        type="text"
                        :placeholder="t('collections.search_recipes')"
                        class="w-full"
                    >
                        <template #prefix>
                            <svg class="w-5 h-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
                            </svg>
                        </template>
                    </FormInput>
                </div>

                <div v-if="filteredRecipes.length" class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                    <div v-for="recipe in filteredRecipes" :key="recipe.id" class="relative group">
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
