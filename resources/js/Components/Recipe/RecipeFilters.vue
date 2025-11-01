<script setup>
import { ref, watch } from 'vue';
import { router } from '@inertiajs/vue3';
import { useDebounceFn } from '@vueuse/core';
import FormInput from '@/Components/Common/FormInput.vue';
import FormSelect from '@/Components/Common/FormSelect.vue';
import { useDifficultyLabels } from '@/composables/useEnumLabels';

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
                label="Rechercher"
                type="text"
                placeholder="Titre ou description..."
                @keyup.enter="applyFilters"
            />

            <FormSelect
                v-model="difficulty"
                label="Difficulté"
                placeholder="Toutes"
                @change="applyFilters"
            >
                <option v-for="option in difficultyOptions" :key="option.value" :value="option.value">
                    {{ option.label }}
                </option>
            </FormSelect>

            <FormSelect
                v-model="sort"
                label="Trier par"
                @change="applyFilters"
            >
                <option value="latest">Plus récent</option>
                <option value="rating">Note</option>
                <option value="duration">Durée</option>
            </FormSelect>
        </div>
    </div>
</template>