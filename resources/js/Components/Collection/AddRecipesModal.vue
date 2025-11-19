<script setup>
import { ref, computed } from 'vue';
import { useForm } from '@inertiajs/vue3';
import { useI18n } from 'vue-i18n';
import { useMediaConversions } from '@/composables/useMediaConversions';
import DialogModal from '@/Components/DialogModal.vue';
import PrimaryButton from '@/Components/PrimaryButton.vue';
import FormInput from '@/Components/Common/FormInput.vue';
import InputLabel from '@/Components/InputLabel.vue';

const { t } = useI18n();
const { getRecipeImage } = useMediaConversions();

const props = defineProps({
    show: Boolean,
    collection: Object,
    userRecipes: {
        type: Array,
        default: () => []
    }
});

const emit = defineEmits(['close']);

const searchQuery = ref('');
const selectedRecipes = ref([]);

const filteredRecipes = computed(() => {
    // Exclure les recettes déjà dans la collection
    const existingRecipeIds = props.collection?.recipes?.map(r => r.id) || [];
    const availableRecipes = props.userRecipes.filter(recipe =>
        !existingRecipeIds.includes(recipe.id)
    );

    if (!searchQuery.value) {
        return availableRecipes;
    }

    const query = searchQuery.value.toLowerCase().trim();

    return availableRecipes.filter(recipe => {
        const title = (recipe.title || '').toLowerCase();
        const description = (recipe.description || '').toLowerCase();
        const summary = (recipe.summary || '').toLowerCase();

        return title.includes(query) ||
               description.includes(query) ||
               summary.includes(query);
    });
});

const toggleRecipe = (recipeId) => {
    const index = selectedRecipes.value.indexOf(recipeId);
    if (index > -1) {
        selectedRecipes.value.splice(index, 1);
    } else {
        selectedRecipes.value.push(recipeId);
    }
};

const isSelected = (recipeId) => {
    return selectedRecipes.value.includes(recipeId);
};

const form = useForm({
    recipe_ids: []
});

const addRecipes = () => {
    if (selectedRecipes.value.length === 0) return;

    form.recipe_ids = selectedRecipes.value;

    form.post(route('collections.recipes.add-multiple', props.collection.id), {
        preserveScroll: true,
        onSuccess: () => {
            selectedRecipes.value = [];
            searchQuery.value = '';
            emit('close');
            form.reset();
        },
    });
};

const close = () => {
    selectedRecipes.value = [];
    searchQuery.value = '';
    emit('close');
};
</script>

<template>
    <DialogModal :show="show" @close="close">
        <template #title>
            {{ t('collections.add_recipes_title') }}
        </template>

        <template #content>
            <div class="space-y-4">
                <div>
                    <InputLabel for="search" :value="t('collections.search_recipes')" />
                    <FormInput
                        id="search"
                        v-model="searchQuery"
                        type="text"
                        :placeholder="t('collections.search_placeholder')"
                        class="mt-1 block w-full"
                    />
                </div>

                <div class="max-h-96 overflow-y-auto space-y-2">
                    <div
                        v-for="recipe in filteredRecipes"
                        :key="recipe.id"
                        @click="toggleRecipe(recipe.id)"
                        :class="[
                            'flex items-center gap-3 p-3 rounded-lg cursor-pointer transition',
                            isSelected(recipe.id)
                                ? 'bg-green-100 border-2 border-green-500'
                                : 'bg-gray-50 hover:bg-gray-100 border-2 border-transparent'
                        ]"
                    >
                        <div
                            :class="[
                                'w-5 h-5 rounded border-2 flex items-center justify-center',
                                isSelected(recipe.id)
                                    ? 'bg-green-500 border-green-500'
                                    : 'border-gray-300'
                            ]"
                        >
                            <svg v-if="isSelected(recipe.id)" class="w-3 h-3 text-white" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"/>
                            </svg>
                        </div>

                        <div class="flex-shrink-0">
                            <img
                                :src="getRecipeImage(recipe, 'thumb')"
                                :alt="recipe.title"
                                class="w-12 h-12 object-cover rounded"
                            />
                        </div>

                        <div class="flex-1 min-w-0">
                            <p class="font-medium text-gray-900 truncate">{{ recipe.title }}</p>
                            <p v-if="recipe.description" class="text-sm text-gray-500 truncate">{{ recipe.description }}</p>
                        </div>
                    </div>

                    <div v-if="filteredRecipes.length === 0" class="text-center py-8 text-gray-500">
                        <p v-if="searchQuery">{{ t('collections.no_results') }}</p>
                        <p v-else>{{ t('collections.no_recipes_available') }}</p>
                    </div>
                </div>

                <div v-if="selectedRecipes.length > 0" class="bg-green-50 p-3 rounded-lg">
                    <p class="text-sm text-green-800">
                        {{ t('collections.selected_count', { count: selectedRecipes.length }) }}
                    </p>
                </div>
            </div>
        </template>

        <template #footer>
            <PrimaryButton variant="secondary" @click="close">
                {{ t('common.cancel') }}
            </PrimaryButton>

            <PrimaryButton
                @click="addRecipes"
                :disabled="selectedRecipes.length === 0 || form.processing"
                :loading="form.processing"
                class="ms-3"
            >
                {{ t('collections.add_selected', { count: selectedRecipes.length }) }}
            </PrimaryButton>
        </template>
    </DialogModal>
</template>
