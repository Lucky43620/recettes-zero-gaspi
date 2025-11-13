<script setup>
import { ref, watch } from 'vue';
import { useI18n } from 'vue-i18n';
import { PlusIcon, TrashIcon } from '@heroicons/vue/24/outline';

const { t } = useI18n();

const props = defineProps({
    modelValue: {
        type: Array,
        default: () => []
    },
    units: {
        type: Array,
        default: () => []
    }
});

const emit = defineEmits(['update:modelValue']);

const ingredients = ref(props.modelValue.length > 0 ? props.modelValue : [
    { name: '', quantity: '', unit_code: '', position: 0 }
]);

watch(ingredients, (newVal) => {
    emit('update:modelValue', newVal);
}, { deep: true });

function addIngredient() {
    ingredients.value.push({
        name: '',
        quantity: '',
        unit_code: '',
        position: ingredients.value.length
    });
}

function removeIngredient(index) {
    ingredients.value.splice(index, 1);
    ingredients.value.forEach((ing, idx) => {
        ing.position = idx;
    });
}
</script>

<template>
    <div class="space-y-4">
        <div class="flex items-center justify-between mb-4">
            <h3 class="text-lg font-semibold text-gray-900">{{ t('recipe.ingredients') }}</h3>
            <button
                type="button"
                @click="addIngredient"
                class="inline-flex items-center gap-2 px-4 py-2 bg-green-600 text-white rounded-lg hover:bg-green-700 transition-colors"
            >
                <PlusIcon class="h-5 w-5" />
                {{ t('recipe.add_ingredient') }}
            </button>
        </div>

        <div v-if="ingredients.length === 0" class="text-center py-8 bg-gray-50 rounded-lg border-2 border-dashed border-gray-300">
            <p class="text-gray-500 mb-3">{{ t('recipe.no_ingredients_added') }}</p>
            <button
                type="button"
                @click="addIngredient"
                class="inline-flex items-center gap-2 px-4 py-2 bg-green-600 text-white rounded-lg hover:bg-green-700 transition-colors"
            >
                <PlusIcon class="h-5 w-5" />
                {{ t('recipe.add_first_ingredient') }}
            </button>
        </div>

        <div v-else class="space-y-3">
            <div
                v-for="(ingredient, index) in ingredients"
                :key="index"
                class="flex gap-3 items-start p-4 bg-white border border-gray-200 rounded-lg hover:border-green-300 transition-colors"
            >
                <div class="flex-shrink-0 w-8 h-8 bg-green-100 text-green-700 rounded-full flex items-center justify-center font-semibold text-sm">
                    {{ index + 1 }}
                </div>

                <div class="flex-1 grid grid-cols-1 md:grid-cols-12 gap-3">
                    <div class="md:col-span-6">
                        <input
                            v-model="ingredient.name"
                            type="text"
                            :placeholder="t('recipe.ingredient_name_placeholder')"
                            class="w-full rounded-md border-gray-300 shadow-sm focus:border-green-500 focus:ring-green-500"
                            required
                        />
                    </div>

                    <div class="md:col-span-3">
                        <input
                            v-model="ingredient.quantity"
                            type="number"
                            step="0.01"
                            min="0"
                            :placeholder="t('pantry.quantity')"
                            class="w-full rounded-md border-gray-300 shadow-sm focus:border-green-500 focus:ring-green-500"
                        />
                    </div>

                    <div class="md:col-span-3">
                        <select
                            v-model="ingredient.unit_code"
                            class="w-full rounded-md border-gray-300 shadow-sm focus:border-green-500 focus:ring-green-500"
                        >
                            <option value="">{{ t('pantry.unit') }}</option>
                            <option v-for="unit in units" :key="unit.code" :value="unit.code">
                                {{ unit.label }}
                            </option>
                        </select>
                    </div>
                </div>

                <button
                    type="button"
                    @click="removeIngredient(index)"
                    class="flex-shrink-0 p-2 text-red-600 hover:bg-red-50 rounded-lg transition-colors"
                    :title="t('recipe.delete_ingredient')"
                >
                    <TrashIcon class="h-5 w-5" />
                </button>
            </div>
        </div>
    </div>
</template>
