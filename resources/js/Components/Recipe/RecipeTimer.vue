<template>
  <div class="bg-white rounded-lg shadow-md p-6 border-2" :class="borderColor">
    <div class="flex items-center justify-between mb-4">
      <div class="flex items-center gap-2">
        <ClockIcon class="w-5 h-5 text-gray-600" />
        <h3 class="text-lg font-semibold text-gray-900">Minuteur</h3>
      </div>
      <span class="text-sm text-gray-500">Étape {{ stepNumber }}</span>
    </div>

    <div class="text-center mb-6">
      <div class="text-5xl font-bold mb-2" :class="textColor">
        {{ formattedTime }}
      </div>
      <div class="w-full bg-gray-200 rounded-full h-2">
        <div
          class="h-2 rounded-full transition-all duration-1000"
          :class="progressColor"
          :style="{ width: `${progress}%` }"
        ></div>
      </div>
    </div>

    <div class="flex gap-2 justify-center">
      <button
        v-if="!isRunning && !isPaused"
        @click="start"
        class="px-6 py-2 bg-green-600 text-white rounded-lg hover:bg-green-700 transition flex items-center gap-2"
      >
        <PlayIcon class="w-5 h-5" />
        Démarrer
      </button>

      <button
        v-if="isRunning"
        @click="pause"
        class="px-6 py-2 bg-yellow-600 text-white rounded-lg hover:bg-yellow-700 transition flex items-center gap-2"
      >
        <PauseIcon class="w-5 h-5" />
        Pause
      </button>

      <button
        v-if="isPaused"
        @click="resume"
        class="px-6 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition flex items-center gap-2"
      >
        <PlayIcon class="w-5 h-5" />
        Reprendre
      </button>

      <button
        @click="reset"
        class="px-6 py-2 bg-gray-600 text-white rounded-lg hover:bg-gray-700 transition flex items-center gap-2"
      >
        <ArrowPathIcon class="w-5 h-5" />
        Réinitialiser
      </button>
    </div>

    <div
      v-if="isFinished"
      class="mt-4 p-4 bg-green-50 border-2 border-green-500 rounded-lg text-center animate-pulse"
    >
      <p class="text-green-800 font-semibold flex items-center justify-center gap-2">
        <BellAlertIcon class="w-5 h-5" />
        Minuteur terminé !
      </p>
    </div>
  </div>
</template>

<script setup>
import { computed } from 'vue'
import { useTimer } from '@/composables/useTimer'
import {
  ClockIcon,
  PlayIcon,
  PauseIcon,
  ArrowPathIcon,
  BellAlertIcon
} from '@heroicons/vue/24/outline'

const props = defineProps({
  minutes: {
    type: Number,
    required: true
  },
  stepNumber: {
    type: Number,
    default: 1
  }
})

const {
  formattedTime,
  progress,
  isRunning,
  isPaused,
  isFinished,
  start,
  pause,
  resume,
  reset
} = useTimer(props.minutes)

const borderColor = computed(() => {
  if (isFinished.value) return 'border-green-500'
  if (isRunning.value) return 'border-blue-500'
  if (isPaused.value) return 'border-yellow-500'
  return 'border-gray-300'
})

const textColor = computed(() => {
  if (isFinished.value) return 'text-green-600'
  if (isRunning.value) return 'text-blue-600'
  if (isPaused.value) return 'text-yellow-600'
  return 'text-gray-900'
})

const progressColor = computed(() => {
  if (isFinished.value) return 'bg-green-500'
  if (isRunning.value) return 'bg-blue-500'
  if (isPaused.value) return 'bg-yellow-500'
  return 'bg-gray-400'
})
</script>
