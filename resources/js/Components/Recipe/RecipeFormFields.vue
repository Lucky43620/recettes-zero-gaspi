<script setup>
import { useI18n } from 'vue-i18n';
import { UserGroupIcon, ClockIcon, FireIcon, GlobeAltIcon } from '@heroicons/vue/24/outline';
import FormInput from '@/Components/Common/FormInput.vue';
import FormTextarea from '@/Components/Common/FormTextarea.vue';
import FormSelect from '@/Components/Common/FormSelect.vue';
import { useDifficultyLabels } from '@/composables/useEnumLabels';

const { t } = useI18n();

const props = defineProps({
    form: Object,
});

const { difficultyOptions } = useDifficultyLabels();
</script>

<template>
    <div class="space-y-6">
        <FormInput
            v-model="form.title"
            :label="t('recipe.title')"
            type="text"
            required
            :placeholder="t('recipe.title_placeholder')"
            :error="form.errors.title"
        />

        <FormTextarea
            v-model="form.summary"
            :label="t('recipe.short_description')"
            :rows="4"
            :placeholder="t('recipe.description_placeholder')"
            :error="form.errors.summary"
        />

        <div class="bg-gray-50 rounded-lg p-4 border border-gray-200">
            <h4 class="text-sm font-semibold text-gray-700 mb-4">{{ t('recipe.practical_info') }}</h4>
            <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                <FormInput
                    v-model.number="form.servings"
                    type="number"
                    min="1"
                    required
                    placeholder="4"
                    :error="form.errors.servings"
                >
                    <template #label>
                        <span class="flex items-center gap-2">
                            <UserGroupIcon class="h-4 w-4 text-gray-500" />
                            {{ t('recipe.servings') }}
                        </span>
                    </template>
                </FormInput>

                <FormInput
                    v-model.number="form.prep_minutes"
                    type="number"
                    min="0"
                    placeholder="15"
                    :error="form.errors.prep_minutes"
                >
                    <template #label>
                        <span class="flex items-center gap-2">
                            <ClockIcon class="h-4 w-4 text-gray-500" />
                            {{ t('recipe.preparation_min') }}
                        </span>
                    </template>
                </FormInput>

                <FormInput
                    v-model.number="form.cook_minutes"
                    type="number"
                    min="0"
                    placeholder="30"
                    :error="form.errors.cook_minutes"
                >
                    <template #label>
                        <span class="flex items-center gap-2">
                            <FireIcon class="h-4 w-4 text-gray-500" />
                            {{ t('recipe.cooking_min') }}
                        </span>
                    </template>
                </FormInput>
            </div>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
            <FormSelect
                v-model="form.difficulty"
                :label="t('recipe.difficulty_level')"
                :placeholder="t('recipe.select_level')"
                :error="form.errors.difficulty"
            >
                <option v-for="option in difficultyOptions" :key="option.value" :value="option.value">
                    {{ option.label }}
                </option>
            </FormSelect>

            <FormInput
                v-model="form.cuisine"
                type="text"
                :placeholder="t('recipe.cuisine_type_placeholder')"
                :error="form.errors.cuisine"
            >
                <template #label>
                    <span class="flex items-center gap-2">
                        <GlobeAltIcon class="h-4 w-4 text-gray-500" />
                        {{ t('recipe.cuisine_type') }}
                    </span>
                </template>
            </FormInput>
        </div>
    </div>
</template>
