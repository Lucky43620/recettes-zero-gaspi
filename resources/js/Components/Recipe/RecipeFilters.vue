<script setup>
import { ref, watch } from 'vue';
import { router } from '@inertiajs/vue3';
import { useDebounceFn } from '@vueuse/core';
import { useI18n } from 'vue-i18n';
import FormInput from '@/Components/Common/FormInput.vue';
import FormSelect from '@/Components/Common/FormSelect.vue';
import { useDifficultyLabels } from '@/composables/useEnumLabels';

const { t } = useI18n();

const props = defineProps({
    filters: Object,
});

const search = ref(props.filters?.search || '');
const difficulty = ref(props.filters?.difficulty || '');
const sort = ref(props.filters?.sort || 'latest');

const { difficultyOptions } = useDifficultyLabels();

function applyFilters() {
    router.get('/recipes', {
        search: search.value || undefined,
        difficulty: difficulty.value || undefined,
        sort: sort.value,
    }, {
        preserveState: true,
        preserveScroll: true,
    });
}

const debouncedApplyFilters = useDebounceFn(applyFilters, 300);

watch([search, difficulty, sort], () => {
    debouncedApplyFilters();
});
</script>

<template>
    <div class="bg-white overflow-hidden shadow-xl sm:rounded-lg p-6">
        <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
            <FormInput
                v-model="search"
                :label="t('common.search')"
                type="text"
                :placeholder="t('recipe.search_placeholder')"
                @keyup.enter="applyFilters"
            />

            <FormSelect
                v-model="difficulty"
                :label="t('recipe.difficulty')"
                :placeholder="t('recipe.all_difficulties')"
                @change="applyFilters"
            >
                <option v-for="option in difficultyOptions" :key="option.value" :value="option.value">
                    {{ option.label }}
                </option>
            </FormSelect>

            <FormSelect
                v-model="sort"
                :label="t('recipe.sort_by')"
                @change="applyFilters"
            >
                <option value="latest">{{ t('recipe.sort_latest') }}</option>
                <option value="rating">{{ t('recipe.sort_rating') }}</option>
                <option value="duration">{{ t('recipe.sort_duration') }}</option>
            </FormSelect>
        </div>
    </div>
</template>
