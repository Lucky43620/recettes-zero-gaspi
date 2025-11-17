<template>
    <div class="fixed inset-0 z-50 overflow-y-auto" @click.self="$emit('close')">
        <div class="flex items-center justify-center min-h-screen px-4">
            <div class="fixed inset-0 bg-black opacity-50"></div>

            <div class="relative bg-white rounded-lg shadow-xl max-w-2xl w-full p-6">
                <div class="flex justify-between items-center mb-6">
                    <h3 class="text-lg font-semibold text-gray-900">
                        {{ t('pantry.edit_item') }}
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
                    <div>
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
                            id="opened-edit"
                            class="rounded border-gray-300 text-green-600 shadow-sm focus:ring-green-500"
                        >
                        <label for="opened-edit" class="ml-2 text-sm text-gray-700">
                            {{ t('pantry.item_opened') }}
                        </label>
                    </div>

                    <div class="flex justify-end gap-3 pt-4 border-t">
                        <SecondaryButton @click="$emit('close')">
                            {{ t('common.cancel') }}
                        </SecondaryButton>

                        <PrimaryButton
                            type="submit"
                            :loading="form.processing"
                        >
                            {{ t('common.save') }}
                        </PrimaryButton>
                    </div>
                </form>
            </div>
        </div>
    </div>
</template>

<script setup>
import { useForm } from '@inertiajs/vue3';
import { useI18n } from 'vue-i18n';
import FormInput from '@/Components/Common/FormInput.vue';
import FormSelect from '@/Components/Common/FormSelect.vue';
import PrimaryButton from '@/Components/PrimaryButton.vue';
import SecondaryButton from '@/Components/SecondaryButton.vue';
import { useStorageLocationLabels } from '@/composables/useEnumLabels';

const { t } = useI18n();

const props = defineProps({
    item: {
        type: Object,
        required: true
    },
    units: {
        type: Array,
        required: true
    },
});

const emit = defineEmits(['close']);

const { storageLocationOptions } = useStorageLocationLabels();

const form = useForm({
    quantity: props.item.quantity,
    unit_code: props.item.unit?.code || props.item.unit_code,
    expiration_date: props.item.expiration_date || '',
    storage_location: props.item.storage_location || '',
    opened: props.item.opened || false,
});

const submit = () => {
    form.put(route('pantry.update', props.item.id), {
        preserveScroll: true,
        onSuccess: () => {
            emit('close');
        },
    });
};
</script>
