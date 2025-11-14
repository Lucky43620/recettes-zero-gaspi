<template>
    <Link
        :href="route('ingredients.show', product.id || product.openfoodfacts_id)"
        class="bg-white overflow-hidden shadow-sm sm:rounded-lg transition-all duration-200 block group hover:shadow-lg cursor-pointer"
    >
        <div class="relative">
            <div v-if="product.image_url" class="h-48 overflow-hidden bg-gray-100">
                <img
                    :src="product.image_url"
                    :alt="product.name"
                    class="w-full h-full object-contain transition-transform duration-200 group-hover:scale-105"
                >
            </div>
            <div v-else class="h-48 bg-gradient-to-br from-green-100 to-green-200 flex items-center justify-center">
                <svg class="w-20 h-20 text-green-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4" />
                </svg>
            </div>

            <div v-if="product.barcode" class="absolute top-2 right-2 bg-white/90 backdrop-blur-sm px-2 py-1 rounded-md text-xs font-mono text-gray-600">
                {{ product.barcode }}
            </div>
        </div>

        <div class="p-4">
            <h3 class="font-semibold text-lg mb-2 line-clamp-2 transition-colors text-gray-900 group-hover:text-green-600">
                {{ product.name }}
            </h3>

            <p v-if="product.brands" class="text-sm text-gray-600 mb-2">
                {{ product.brands }}
            </p>

            <p v-if="product.category" class="text-xs text-gray-500 line-clamp-1 mb-3">
                {{ truncateCategory(product.category) }}
            </p>

            <div v-if="showNutrition && product.nutritional_info" class="flex flex-wrap gap-2 mb-3">
                <span v-if="product.nutritional_info.energy_kcal" class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium bg-orange-100 text-orange-800">
                    {{ product.nutritional_info.energy_kcal }} kcal
                </span>
                <span v-if="product.nutritional_info.proteins" class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium bg-blue-100 text-blue-800">
                    {{ t('common.proteins_g', { g: product.nutritional_info.proteins }) }}
                </span>
            </div>

            <div v-if="showLabels && product.labels && product.labels.length > 0" class="flex flex-wrap gap-1">
                <span
                    v-for="label in product.labels.slice(0, 3)"
                    :key="label"
                    class="inline-flex items-center px-2 py-0.5 rounded text-xs font-medium bg-green-100 text-green-800"
                >
                    {{ label }}
                </span>
                <span v-if="product.labels.length > 3" class="inline-flex items-center px-2 py-0.5 rounded text-xs font-medium bg-gray-100 text-gray-600">
                    +{{ product.labels.length - 3 }}
                </span>
            </div>

            <div class="mt-3 flex items-center text-sm text-green-600 group-hover:text-green-700">
                <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                </svg>
                {{ t('common.see_details') }}
            </div>
        </div>
    </Link>
</template>

<script setup>
import { Link } from '@inertiajs/vue3';
import { useI18n } from 'vue-i18n';

const { t } = useI18n();

defineProps({
    product: {
        type: Object,
        required: true,
    },
    showNutrition: {
        type: Boolean,
        default: true,
    },
    showLabels: {
        type: Boolean,
        default: true,
    },
});

const truncateCategory = (category) => {
    if (!category) return '';
    const parts = category.split(',');
    return parts.length > 2 ? `${parts[0]}, ${parts[1]}...` : category;
};
</script>
