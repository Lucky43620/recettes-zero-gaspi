<template>
    <div class="relative">
        <label v-if="label" class="block text-sm font-medium text-gray-700 mb-2">
            {{ label }}
        </label>

        <div class="relative">
            <input
                v-model="searchQuery"
                @input="handleSearch"
                type="text"
                :placeholder="placeholder || t('common.search_ingredient_placeholder')"
                class="w-full border-gray-300 focus:border-green-500 focus:ring-green-500 rounded-md shadow-sm"
            >
            <button
                v-if="showBarcodeButton"
                type="button"
                @click="$emit('scan')"
                class="absolute right-2 top-2 text-gray-400 hover:text-gray-600"
            >
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v1m6 11h2m-6 0h-2v4m0-11v3m0 0h.01M12 12h4.01M16 20h4M4 12h4m12 0h.01M5 8h2a1 1 0 001-1V5a1 1 0 00-1-1H5a1 1 0 00-1 1v2a1 1 0 001 1zm12 0h2a1 1 0 001-1V5a1 1 0 00-1-1h-2a1 1 0 00-1 1v2a1 1 0 001 1zM5 20h2a1 1 0 001-1v-2a1 1 0 00-1-1H5a1 1 0 00-1 1v2a1 1 0 001 1z" />
                </svg>
            </button>
        </div>

        <div v-if="isSearching" class="mt-2 p-4 text-center text-gray-500">
            <svg class="animate-spin h-5 w-5 mx-auto" fill="none" viewBox="0 0 24 24">
                <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
            </svg>
        </div>

        <div v-else-if="results.length > 0" class="mt-2 max-h-64 overflow-y-auto border border-gray-200 rounded-md bg-white shadow-lg">
            <button
                v-for="ingredient in results"
                :key="ingredient.id"
                type="button"
                @click="selectIngredient(ingredient)"
                class="w-full text-left px-4 py-3 hover:bg-gray-50 flex items-center gap-3 border-b border-gray-100 last:border-b-0 transition-colors"
            >
                <div class="flex-shrink-0">
                    <img
                        v-if="ingredient.image_url"
                        :src="ingredient.image_url"
                        :alt="ingredient.name"
                        class="w-12 h-12 object-cover rounded"
                    >
                    <div v-else class="w-12 h-12 bg-gradient-to-br from-green-100 to-green-200 rounded flex items-center justify-center">
                        <svg class="w-6 h-6 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6" />
                        </svg>
                    </div>
                </div>
                <div class="flex-1 min-w-0">
                    <p class="font-medium text-gray-900 truncate">{{ ingredient.name }}</p>
                    <p v-if="ingredient.brands" class="text-sm text-gray-500 truncate">{{ ingredient.brands }}</p>
                    <p v-if="ingredient.category" class="text-xs text-gray-400 truncate">{{ ingredient.category }}</p>
                </div>
                <svg class="w-5 h-5 text-gray-400 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" />
                </svg>
            </button>
        </div>

        <div v-else-if="searchQuery.length >= minSearchLength && !isSearching" class="mt-2 p-4 text-center text-gray-500 border border-gray-200 rounded-md bg-gray-50">
            {{ t('common.no_ingredient_found') }}
        </div>
    </div>
</template>

<script setup>
import { ref, watch } from 'vue';
import { useI18n } from 'vue-i18n';
import axios from 'axios';

const { t } = useI18n();

const props = defineProps({
    label: {
        type: String,
        default: '',
    },
    placeholder: {
        type: String,
        default: '',
    },
    showBarcodeButton: {
        type: Boolean,
        default: false,
    },
    minSearchLength: {
        type: Number,
        default: 2,
    },
    modelValue: {
        type: String,
        default: '',
    },
});

const emit = defineEmits(['select', 'scan', 'update:modelValue']);

const searchQuery = ref(props.modelValue);
const results = ref([]);
const isSearching = ref(false);
let searchTimeout = null;

watch(() => props.modelValue, (newValue) => {
    searchQuery.value = newValue;
});

watch(searchQuery, (newValue) => {
    emit('update:modelValue', newValue);
});

const handleSearch = () => {
    if (searchTimeout) {
        clearTimeout(searchTimeout);
    }

    if (searchQuery.value.length < props.minSearchLength) {
        results.value = [];
        return;
    }

    isSearching.value = true;

    searchTimeout = setTimeout(async () => {
        try {
            const response = await axios.get('/api/ingredients/search', {
                params: { q: searchQuery.value }
            });
            results.value = response.data.data || [];
        } catch (error) {
            results.value = [];
        } finally {
            isSearching.value = false;
        }
    }, 300);
};

const selectIngredient = (ingredient) => {
    emit('select', ingredient);
    results.value = [];
    searchQuery.value = '';
};
</script>
