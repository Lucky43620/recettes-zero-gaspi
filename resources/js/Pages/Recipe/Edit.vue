<script setup>
import { useForm } from '@inertiajs/vue3';
import { useI18n } from 'vue-i18n';
import AppLayout from '@/Layouts/AppLayout.vue';
import RecipeStepEditor from '@/Components/Recipe/RecipeStepEditor.vue';
import RecipeFormFields from '@/Components/Recipe/RecipeFormFields.vue';
import RecipeIngredientEditor from '@/Components/Recipe/RecipeIngredientEditor.vue';
import FileUpload from '@/Components/Common/FileUpload.vue';

const { t } = useI18n();

const props = defineProps({
    recipe: Object,
    units: Array,
});

const form = useForm({
    title: props.recipe.title,
    summary: props.recipe.summary,
    servings: props.recipe.servings,
    prep_minutes: props.recipe.prep_minutes,
    cook_minutes: props.recipe.cook_minutes,
    difficulty: props.recipe.difficulty || '',
    cuisine: props.recipe.cuisine || '',
    is_public: props.recipe.is_public,
    calories: props.recipe.calories,
    steps: props.recipe.steps.map(step => ({
        text: step.text,
        timer_minutes: step.timer_minutes,
    })),
    ingredients: props.recipe.ingredients?.map(ing => ({
        name: ing.name,
        quantity: ing.pivot.quantity,
        unit_code: ing.pivot.unit_code || '',
        position: ing.pivot.position
    })) || [],
    images: [],
});

function submit() {
    form.transform((data) => ({
        ...data,
        _method: 'PUT',
    })).post(route('recipes.update', props.recipe.slug), {
        preserveScroll: true,
    });
}
</script>

<template>
    <AppLayout :title="t('recipe.edit_recipe')">
        <template #header>
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                {{ t('recipe.edit_recipe') }}
            </h2>
        </template>

        <div class="py-8 md:py-12">
            <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8">
                <div class="bg-white overflow-hidden shadow-xl sm:rounded-lg">
                    <div class="p-4 md:p-6 lg:p-8">
                        <form @submit.prevent="submit" class="space-y-6 md:space-y-8">
                            <div class="space-y-6">
                                <div>
                                    <h3 class="text-base md:text-lg font-semibold text-gray-900 mb-3 md:mb-4 flex items-center gap-2">
                                        <span class="flex items-center justify-center w-7 h-7 md:w-8 md:h-8 bg-green-100 text-green-700 rounded-full text-sm font-bold">1</span>
                                        {{ t('recipe.general_information') }}
                                    </h3>
                                    <div class="pl-0 md:pl-10">
                                        <RecipeFormFields :form="form" />
                                    </div>
                                </div>

                                <div class="border-t pt-6">
                                    <h3 class="text-base md:text-lg font-semibold text-gray-900 mb-3 md:mb-4 flex items-center gap-2">
                                        <span class="flex items-center justify-center w-7 h-7 md:w-8 md:h-8 bg-green-100 text-green-700 rounded-full text-sm font-bold">2</span>
                                        {{ t('recipe.ingredients') }}
                                    </h3>
                                    <div class="pl-0 md:pl-10">
                                        <RecipeIngredientEditor
                                            v-model="form.ingredients"
                                            :units="units"
                                        />
                                    </div>
                                </div>

                                <div class="border-t pt-6">
                                    <h3 class="text-base md:text-lg font-semibold text-gray-900 mb-3 md:mb-4 flex items-center gap-2">
                                        <span class="flex items-center justify-center w-7 h-7 md:w-8 md:h-8 bg-green-100 text-green-700 rounded-full text-sm font-bold">3</span>
                                        {{ t('recipe.preparation_steps') }}
                                    </h3>
                                    <div class="pl-0 md:pl-10">
                                        <RecipeStepEditor
                                            v-model="form.steps"
                                            :errors="form.errors"
                                        />
                                    </div>
                                </div>

                                <div class="border-t pt-6">
                                    <h3 class="text-base md:text-lg font-semibold text-gray-900 mb-3 md:mb-4 flex items-center gap-2">
                                        <span class="flex items-center justify-center w-7 h-7 md:w-8 md:h-8 bg-green-100 text-green-700 rounded-full text-sm font-bold">4</span>
                                        {{ t('recipe.recipe_photos') }}
                                    </h3>
                                    <div class="pl-0 md:pl-10">
                                        <div v-if="recipe.media && recipe.media.length > 0" class="mb-4">
                                            <p class="text-sm text-gray-600 mb-2">{{ t('recipe.current_images') }}</p>
                                            <div class="grid grid-cols-2 md:grid-cols-3 gap-3 md:gap-4">
                                                <div
                                                    v-for="media in recipe.media"
                                                    :key="media.id"
                                                    class="relative"
                                                >
                                                    <img
                                                        :src="media.original_url"
                                                        :alt="t('recipe.recipe_image_alt')"
                                                        class="w-full h-24 md:h-32 object-cover rounded-lg border-2 border-gray-200"
                                                    />
                                                </div>
                                            </div>
                                        </div>

                                        <FileUpload
                                            v-model="form.images"
                                            multiple
                                            :label="t('recipe.add_new_images')"
                                            :hint="t('recipe.image_upload_hint')"
                                            :error="form.errors.images"
                                        />
                                    </div>
                                </div>

                                <div class="border-t pt-6">
                                    <div class="pl-0 md:pl-10">
                                        <div class="bg-gray-50 rounded-lg p-3 md:p-4 border border-gray-200">
                                            <label class="flex items-start md:items-center cursor-pointer">
                                                <input
                                                    v-model="form.is_public"
                                                    type="checkbox"
                                                    class="rounded border-gray-300 text-green-600 shadow-sm focus:border-green-500 focus:ring-green-500 h-5 w-5 mt-0.5 md:mt-0 flex-shrink-0"
                                                />
                                                <div class="ml-3">
                                                    <span class="text-sm font-medium text-gray-900">{{ t('recipe.make_public') }}</span>
                                                    <p class="text-xs text-gray-500 mt-0.5">
                                                        {{ t('recipe.public_recipe_description') }}
                                                    </p>
                                                </div>
                                            </label>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <div class="border-t pt-6 flex flex-col sm:flex-row justify-end gap-3 sm:gap-4">
                                <a
                                    :href="route('recipes.show', recipe.slug)"
                                    class="px-6 py-2.5 bg-white border-2 border-gray-300 text-gray-700 rounded-lg hover:bg-gray-50 font-medium transition-colors text-center"
                                >
                                    {{ t('common.cancel') }}
                                </a>
                                <button
                                    type="submit"
                                    :disabled="form.processing"
                                    class="px-6 py-2.5 bg-green-600 text-white rounded-lg hover:bg-green-700 disabled:opacity-50 disabled:cursor-not-allowed font-medium shadow-sm transition-all"
                                >
                                    {{ form.processing ? t('common.updating') : t('common.update') }}
                                </button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </AppLayout>
</template>