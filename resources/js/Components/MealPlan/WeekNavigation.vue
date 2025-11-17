<script setup>
import PrimaryButton from '@/Components/PrimaryButton.vue';
import { useI18n } from 'vue-i18n';

const { t } = useI18n();

defineProps({
    weekStart: String,
    prevWeek: String,
    nextWeek: String,
});

const emit = defineEmits(['navigate']);
</script>

<template>
    <div class="mb-6 flex justify-between items-center">
        <PrimaryButton variant="secondary" @click="emit('navigate', prevWeek)">
            ← {{ t('meal_plan.previous_week') }}
        </PrimaryButton>
        <div class="text-lg font-semibold">
            {{ t('meal_plan.week_of') }} {{ new Date(weekStart).toLocaleDateString('fr-FR', { day: 'numeric', month: 'long', year: 'numeric' }) }}
        </div>
        <PrimaryButton variant="secondary" @click="emit('navigate', nextWeek)">
            {{ t('meal_plan.next_week') }} →
        </PrimaryButton>
    </div>
</template>
