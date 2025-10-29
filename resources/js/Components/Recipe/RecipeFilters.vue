<script setup>
import { ref, watch } from 'vue';
import { router } from '@inertiajs/vue3';
import { useDebounceFn } from '@vueuse/core';

const props = defineProps({
    filters: Object,
});

const search = ref(props.filters?.search || '');
const difficulty = ref(props.filters?.difficulty || '');
const sort = ref(props.filters?.sort || 'latest');

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
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">
                    Rechercher
                </label>
                <input
                    v-model="search"
                    type="text"
                    placeholder="Titre ou description..."
                    class="w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500"
                    @keyup.enter="applyFilters"
                />
            </div>

            <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">
                    Difficulté
                </label>
                <select
                    v-model="difficulty"
                    class="w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500"
                    @change="applyFilters"
                >
                    <option value="">Toutes</option>
                    <option value="easy">Facile</option>
                    <option value="medium">Moyen</option>
                    <option value="hard">Difficile</option>
                </select>
            </div>

            <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">
                    Trier par
                </label>
                <select
                    v-model="sort"
                    class="w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500"
                    @change="applyFilters"
                >
                    <option value="latest">Plus récent</option>
                    <option value="rating">Note</option>
                    <option value="duration">Durée</option>
                </select>
            </div>
        </div>
    </div>
</template>