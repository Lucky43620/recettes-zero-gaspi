<script setup>
import { ref } from 'vue';
import { router } from '@inertiajs/vue3';
import { useI18n } from 'vue-i18n';
import { XMarkIcon } from '@heroicons/vue/24/outline';

const { t } = useI18n();

const props = defineProps({
    show: Boolean,
});

const emit = defineEmits(['close', 'product-found']);

const barcode = ref('');
const loading = ref(false);
const error = ref('');
const product = ref(null);

const lookupBarcode = async () => {
    if (!barcode.value || barcode.value.length < 8) {
        error.value = t('pantry.barcode_invalid');
        return;
    }

    loading.value = true;
    error.value = '';
    product.value = null;

    try {
        const response = await fetch(route('barcode.lookup'), {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
            },
            body: JSON.stringify({ barcode: barcode.value }),
        });

        const data = await response.json();

        if (data.success) {
            product.value = data.product;
            emit('product-found', data.product);
        } else {
            error.value = data.message || t('pantry.product_not_found');
        }
    } catch (err) {
        error.value = t('pantry.barcode_error');
    } finally {
        loading.value = false;
    }
};

const reset = () => {
    barcode.value = '';
    error.value = '';
    product.value = null;
};

const close = () => {
    reset();
    emit('close');
};
</script>

<template>
    <div v-if="show" class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50 p-4" @click.self="close">
        <div class="bg-white rounded-lg shadow-xl max-w-2xl w-full max-h-[90vh] overflow-y-auto">
            <div class="p-6">
                <div class="flex items-center justify-between mb-6">
                    <h2 class="text-2xl font-bold text-gray-900">{{ t('pantry.scan_barcode') }}</h2>
                    <button @click="close" class="p-2 hover:bg-gray-100 rounded-lg transition">
                        <XMarkIcon class="w-6 h-6" />
                    </button>
                </div>

                <div class="space-y-6">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">
                            {{ t('pantry.barcode') }}
                        </label>
                        <div class="flex gap-2">
                            <input
                                v-model="barcode"
                                type="text"
                                inputmode="numeric"
                                pattern="[0-9]*"
                                :placeholder="t('pantry.barcode_placeholder')"
                                class="flex-1 px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-green-500 focus:border-transparent"
                                @keyup.enter="lookupBarcode"
                            />
                            <button
                                @click="lookupBarcode"
                                :disabled="loading || !barcode"
                                class="px-6 py-2 bg-green-600 text-white rounded-lg hover:bg-green-700 transition disabled:opacity-50 disabled:cursor-not-allowed"
                            >
                                {{ loading ? t('common.searching') : t('common.search') }}
                            </button>
                        </div>
                        <p class="mt-2 text-sm text-gray-500">
                            {{ t('pantry.barcode_hint') }}
                        </p>
                    </div>

                    <div v-if="error" class="bg-red-50 border border-red-200 rounded-lg p-4">
                        <p class="text-red-800">{{ error }}</p>
                    </div>

                    <div v-if="product" class="bg-green-50 border border-green-200 rounded-lg p-6">
                        <div class="flex items-start gap-4">
                            <img
                                v-if="product.image_url"
                                :src="product.image_url"
                                :alt="product.name"
                                class="w-24 h-24 object-cover rounded-lg"
                            />
                            <div class="flex-1">
                                <h3 class="text-xl font-bold text-gray-900 mb-2">{{ product.name }}</h3>
                                <div v-if="product.brand" class="text-sm text-gray-600 mb-2">
                                    {{ t('pantry.brand') }}: {{ product.brand }}
                                </div>
                                <div v-if="product.quantity" class="text-sm text-gray-600 mb-2">
                                    {{ t('pantry.quantity') }}: {{ product.quantity }}
                                </div>
                                <div v-if="product.categories" class="text-sm text-gray-600">
                                    {{ t('pantry.categories') }}: {{ product.categories }}
                                </div>
                            </div>
                        </div>

                        <div v-if="product.nutriments" class="mt-4 pt-4 border-t border-green-300">
                            <h4 class="font-semibold text-gray-900 mb-2">{{ t('pantry.nutrition_info') }}</h4>
                            <div class="grid grid-cols-2 gap-2 text-sm">
                                <div v-if="product.nutriments.energy">{{ t('pantry.energy') }}: {{ product.nutriments.energy }} kcal</div>
                                <div v-if="product.nutriments.fat">{{ t('pantry.fat') }}: {{ product.nutriments.fat }}g</div>
                                <div v-if="product.nutriments.carbohydrates">{{ t('pantry.carbs') }}: {{ product.nutriments.carbohydrates }}g</div>
                                <div v-if="product.nutriments.proteins">{{ t('pantry.proteins') }}: {{ product.nutriments.proteins }}g</div>
                            </div>
                        </div>

                        <div class="mt-4 text-xs text-gray-500">
                            {{ t('pantry.data_source') }}
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</template>
