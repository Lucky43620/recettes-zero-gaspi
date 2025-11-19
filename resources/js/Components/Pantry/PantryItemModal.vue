<template>
    <div class="fixed inset-0 z-50 overflow-y-auto" @click.self="$emit('close')">
        <div class="flex items-center justify-center min-h-screen px-4">
            <div class="fixed inset-0 bg-black opacity-50"></div>

            <div class="relative bg-white rounded-lg shadow-xl max-w-2xl w-full p-6">
                <div class="flex justify-between items-center mb-6">
                    <h3 class="text-lg font-semibold text-gray-900">
                        {{ isEditing ? t('pantry.edit_item') : t('pantry.add_to_pantry') }}
                    </h3>
                    <button
                        @click="$emit('close')"
                        class="text-gray-400 hover:text-gray-600"
                    >
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                        </svg>
                    </button>
                </div>

                <form @submit.prevent="submit" class="space-y-4">
                    <div v-if="!isEditing">
                        <label class="block text-sm font-medium text-gray-700 mb-2">
                            {{ t('pantry.search_ingredient') }}
                        </label>
                        <div class="relative">
                            <input
                                v-model="searchQuery"
                                @input="handleSearch"
                                type="text"
                                :placeholder="t('pantry.search_product')"
                                class="w-full border-gray-300 focus:border-green-500 focus:ring-green-500 rounded-md shadow-sm"
                            >
                        </div>

                        <div v-if="isSearching" class="mt-2 p-4 text-center text-gray-500">
                            <svg class="animate-spin h-5 w-5 mx-auto" fill="none" viewBox="0 0 24 24">
                                <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                                <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                            </svg>
                        </div>

                        <div v-else-if="searchResults.length > 0" class="mt-2 max-h-64 overflow-y-auto border border-gray-200 rounded-md bg-white shadow-lg">
                            <button
                                v-for="ingredient in searchResults"
                                :key="ingredient.id"
                                type="button"
                                @click="selectIngredient(ingredient)"
                                class="w-full text-left px-4 py-3 hover:bg-gray-50 flex items-center gap-3 border-b border-gray-100 last:border-b-0"
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
                                </div>
                            </button>
                        </div>

                        <div v-if="form.ingredient_id && selectedIngredient" class="mt-3 p-3 bg-green-50 border border-green-200 rounded-md flex items-center gap-3">
                            <img
                                v-if="selectedIngredient.image_url"
                                :src="selectedIngredient.image_url"
                                :alt="selectedIngredient.name"
                                class="w-12 h-12 object-cover rounded"
                            >
                            <div class="flex-1">
                                <p class="font-medium text-gray-900">{{ selectedIngredient.name }}</p>
                                <p v-if="selectedIngredient.brands" class="text-sm text-gray-600">{{ selectedIngredient.brands }}</p>
                            </div>
                            <button
                                type="button"
                                @click="clearSelection"
                                class="text-gray-400 hover:text-gray-600"
                            >
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                                </svg>
                            </button>
                        </div>

                        <p v-if="form.errors.ingredient_id" class="mt-2 text-sm text-red-600">{{ form.errors.ingredient_id }}</p>
                    </div>

                    <div v-else>
                        <label class="block text-sm font-medium text-gray-700 mb-2">
                            {{ t('pantry.ingredient') }}
                        </label>
                        <div class="p-3 bg-gray-50 border border-gray-200 rounded-md flex items-center gap-3">
                            <img
                                v-if="item.ingredient?.image_url"
                                :src="item.ingredient.image_url"
                                :alt="item.ingredient?.name"
                                class="w-12 h-12 object-cover rounded"
                            >
                            <div v-else class="w-12 h-12 bg-gradient-to-br from-green-100 to-green-200 rounded flex items-center justify-center">
                                <svg class="w-6 h-6 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4" />
                                </svg>
                            </div>
                            <div class="flex-1">
                                <p class="font-medium text-gray-900">{{ item.ingredient?.name }}</p>
                                <p v-if="item.ingredient?.brands" class="text-sm text-gray-600">{{ item.ingredient.brands }}</p>
                            </div>
                        </div>
                    </div>

                    <div class="grid grid-cols-2 gap-4">
                        <FormInput
                            v-model="form.quantity"
                            :label="t('pantry.quantity')"
                            type="number"
                            step="0.01"
                            min="0.01"
                            required
                            :error="form.errors.quantity"
                        />

                        <FormSelect
                            v-model="form.unit_code"
                            :label="t('pantry.unit')"
                            :placeholder="t('pantry.select_unit')"
                            required
                            :error="form.errors.unit_code"
                        >
                            <option v-for="unit in units" :key="unit.code" :value="unit.code">
                                {{ unit.name }}
                            </option>
                        </FormSelect>
                    </div>

                    <FormInput
                        v-model="form.expiration_date"
                        :label="t('pantry.expiration_date')"
                        type="date"
                        :min="isEditing ? undefined : today"
                        :error="form.errors.expiration_date"
                    />

                    <FormSelect
                        v-model="form.storage_location"
                        :label="t('pantry.storage_location_label')"
                        :placeholder="t('pantry.select_location')"
                        :error="form.errors.storage_location"
                    >
                        <option v-for="option in storageLocationOptions" :key="option.value" :value="option.value">
                            {{ option.label }}
                        </option>
                    </FormSelect>

                    <div class="flex items-center">
                        <input
                            v-model="form.opened"
                            type="checkbox"
                            :id="isEditing ? 'opened-edit' : 'opened'"
                            class="rounded border-gray-300 text-green-600 shadow-sm focus:ring-green-500"
                        >
                        <label :for="isEditing ? 'opened-edit' : 'opened'" class="ml-2 text-sm text-gray-700">
                            {{ t('pantry.item_opened') }}
                        </label>
                    </div>

                    <div class="flex justify-end gap-3 pt-4 border-t">
                        <PrimaryButton variant="secondary" @click="$emit('close')">
                            {{ t('common.cancel') }}
                        </PrimaryButton>

                        <PrimaryButton
                            type="submit"
                            :disabled="!isEditing && !form.ingredient_id"
                            :loading="form.processing"
                        >
                            {{ isEditing ? t('common.save') : t('shopping_list.add') }}
                        </PrimaryButton>
                    </div>
                </form>
            </div>
        </div>
    </div>
</template>

<script setup>
import { ref, computed } from 'vue';
import { useForm } from '@inertiajs/vue3';
import { useI18n } from 'vue-i18n';
import axios from 'axios';
import FormInput from '@/Components/Common/FormInput.vue';
import FormSelect from '@/Components/Common/FormSelect.vue';
import PrimaryButton from '@/Components/PrimaryButton.vue';
import { useStorageLocationLabels } from '@/composables/useEnumLabels';

const { t } = useI18n();

const props = defineProps({
    units: {
        type: Array,
        required: true
    },
    item: {
        type: Object,
        default: null
    }
});

const emit = defineEmits(['close']);

const isEditing = computed(() => !!props.item);

const form = useForm(isEditing.value ? {
    quantity: props.item.quantity,
    unit_code: props.item.unit?.code || props.item.unit_code,
    expiration_date: props.item.expiration_date || '',
    storage_location: props.item.storage_location || '',
    opened: props.item.opened || false,
} : {
    ingredient_id: null,
    quantity: 1,
    unit_code: '',
    expiration_date: '',
    storage_location: '',
    opened: false,
});

const searchQuery = ref('');
const searchResults = ref([]);
const isSearching = ref(false);
const selectedIngredient = ref(null);
let searchTimeout = null;

const { storageLocationOptions } = useStorageLocationLabels();

const today = computed(() => {
    return new Date().toISOString().split('T')[0];
});

const handleSearch = () => {
    if (searchTimeout) {
        clearTimeout(searchTimeout);
    }

    if (searchQuery.value.length < 2) {
        searchResults.value = [];
        return;
    }

    isSearching.value = true;

    searchTimeout = setTimeout(async () => {
        try {
            const response = await axios.get('/api/ingredients/search', {
                params: { q: searchQuery.value }
            });
            searchResults.value = response.data.data || [];
        } catch (error) {
            searchResults.value = [];
        } finally {
            isSearching.value = false;
        }
    }, 300);
};

const selectIngredient = (ingredient) => {
    form.ingredient_id = ingredient.id;
    selectedIngredient.value = ingredient;
    searchResults.value = [];
    searchQuery.value = '';
};

const clearSelection = () => {
    form.ingredient_id = null;
    selectedIngredient.value = null;
};

const submit = () => {
    if (isEditing.value) {
        form.put(route('pantry.update', props.item.id), {
            preserveScroll: true,
            onSuccess: () => {
                emit('close');
            },
        });
    } else {
        form.post(route('pantry.store'), {
            preserveScroll: true,
            onSuccess: () => {
                emit('close');
            },
        });
    }
};
</script>
