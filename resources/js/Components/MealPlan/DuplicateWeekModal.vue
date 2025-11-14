<script setup>
import PrimaryButton from '@/Components/Common/PrimaryButton.vue';
import { useI18n } from 'vue-i18n';

const { t } = useI18n();

defineProps({
    show: Boolean,
    weekStart: String,
    isDuplicating: Boolean,
});

const emit = defineEmits(['close', 'duplicate', 'update:weekStart']);
</script>

<template>
    <div
        v-if="show"
        class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50"
        @click.self="emit('close')"
    >
        <div class="bg-white rounded-lg p-6 max-w-md w-full mx-4">
            <h3 class="text-lg font-semibold mb-4">{{ t('meal_plan.duplicate_week') }}</h3>
            <div class="mb-4">
                <label class="block text-sm font-medium text-gray-700 mb-2">
                    {{ t('meal_plan.duplicate_to_week') }}
                </label>
                <input
                    :value="weekStart"
                    @input="emit('update:weekStart', $event.target.value)"
                    type="date"
                    class="w-full px-3 py-2 border rounded-md"
                />
            </div>
            <div class="flex gap-3 justify-end">
                <PrimaryButton
                    variant="secondary"
                    @click="emit('close')"
                    :disabled="isDuplicating"
                >
                    {{ t('common.cancel') }}
                </PrimaryButton>
                <PrimaryButton
                    @click="emit('duplicate')"
                    :loading="isDuplicating"
                >
                    {{ t('meal_plan.duplicate') }}
                </PrimaryButton>
            </div>
        </div>
    </div>
</template>
