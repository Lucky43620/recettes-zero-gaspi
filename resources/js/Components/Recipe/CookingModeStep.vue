<template>
    <div class="bg-white rounded-xl shadow-lg p-4 md:p-8">
        <div class="flex items-start justify-between mb-4 md:mb-6">
            <h3 class="text-xl md:text-3xl font-bold text-gray-900">{{ t('cook.step') }} {{ stepNumber }}</h3>
            <button
                @click="$emit('toggle-completion')"
                :class="[
                    'p-3 md:p-4 rounded-full transition flex-shrink-0 ml-2 active:scale-95',
                    isCompleted
                        ? 'bg-green-600 text-white active:bg-green-700'
                        : 'bg-gray-200 text-gray-600 hover:bg-gray-300 active:bg-gray-400'
                ]"
                :aria-label="t('cook.mark_completed')"
            >
                <CheckIcon class="w-6 h-6 md:w-8 md:h-8" />
            </button>
        </div>

        <p class="text-base md:text-2xl text-gray-800 leading-relaxed mb-4 md:mb-6" style="line-height: 1.6;">
            {{ content }}
        </p>

        <div v-if="duration" class="mt-4 md:mt-6 bg-orange-50 rounded-lg p-4 md:p-6">
            <div class="flex flex-col sm:flex-row items-start sm:items-center justify-between gap-3">
                <div class="flex items-center">
                    <ClockIcon class="w-6 h-6 md:w-8 md:h-8 text-orange-600 mr-2 md:mr-3 flex-shrink-0" />
                    <span class="text-base md:text-xl font-semibold text-orange-900">
                        {{ duration }} {{ t('cook.minutes') }}
                    </span>
                </div>
                <button
                    v-if="!timerActive"
                    @click="$emit('start-timer')"
                    class="w-full sm:w-auto px-5 py-3 md:px-6 md:py-3 bg-orange-600 text-white rounded-lg hover:bg-orange-700 active:bg-orange-800 transition text-base md:text-lg font-medium"
                >
                    {{ t('cook.launch_timer') }}
                </button>
                <div v-else class="text-2xl md:text-3xl font-bold text-orange-600">
                    {{ formattedTime }}
                </div>
            </div>
        </div>
    </div>
</template>

<script setup>
import { useI18n } from 'vue-i18n';
import { ClockIcon, CheckIcon } from '@heroicons/vue/24/outline';

const { t } = useI18n();

defineProps({
    stepNumber: {
        type: Number,
        required: true
    },
    content: {
        type: String,
        required: true
    },
    isCompleted: {
        type: Boolean,
        default: false
    },
    duration: {
        type: Number,
        default: null
    },
    timerActive: {
        type: Boolean,
        default: false
    },
    formattedTime: {
        type: String,
        default: ''
    }
});

defineEmits(['toggle-completion', 'start-timer']);
</script>
