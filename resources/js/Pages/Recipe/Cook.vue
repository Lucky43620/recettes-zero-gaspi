<template>
  <AppLayout :title="t('cook.title')">
    <div v-if="recipe && recipe.title && recipe.steps && recipe.steps.length > 0" class="min-h-screen bg-gray-50 pb-20">
      <div class="max-w-4xl mx-auto px-4 py-8">
        <div class="bg-white rounded-lg shadow-lg overflow-hidden">
          <div class="bg-gradient-to-r from-green-600 to-green-700 px-4 sm:px-6 py-4">
            <div class="flex flex-col sm:flex-row items-start sm:items-center justify-between gap-3">
              <div class="flex-1 min-w-0">
                <h1 class="text-xl sm:text-2xl font-bold text-white truncate">{{ recipe.title }}</h1>
                <p class="text-green-100 text-xs sm:text-sm">{{ t('cook.step_by_step_mode') }}</p>
              </div>
              <button
                @click="exitCooking"
                class="px-4 py-2 bg-white text-green-700 rounded-lg hover:bg-gray-100 transition flex items-center gap-2 w-full sm:w-auto justify-center"
              >
                <XMarkIcon class="w-5 h-5" />
                {{ t('cook.exit') }}
              </button>
            </div>
          </div>

          <div class="px-6 py-4 bg-gray-50 border-b">
            <div class="flex items-center justify-between mb-2">
              <span class="text-sm font-medium text-gray-700">
                {{ t('cook.step_progress', { current: currentStepIndex + 1, total: totalSteps }) }}
              </span>
              <span class="text-sm text-gray-600">
                {{ t('cook.percent_completed', { percent: Math.round(progressPercentage) }) }}
              </span>
            </div>
            <div class="w-full bg-gray-200 rounded-full h-3">
              <div
                class="bg-green-600 h-3 rounded-full transition-all duration-300"
                :style="{ width: `${progressPercentage}%` }"
              ></div>
            </div>
          </div>

          <div class="p-8">
            <div v-if="currentStep && recipe && recipe.steps" class="space-y-6">
              <div class="flex items-start gap-4">
                <div class="flex-shrink-0 w-12 h-12 bg-green-600 text-white rounded-full flex items-center justify-center text-xl font-bold">
                  {{ currentStepIndex + 1 }}
                </div>
                <div class="flex-1">
                  <p class="text-xl text-gray-900 leading-relaxed">
                    {{ currentStep.text }}
                  </p>
                </div>
              </div>

              <div v-if="currentStep.timer_minutes" class="mt-8">
                <RecipeTimer
                  :minutes="currentStep.timer_minutes"
                  :step-number="currentStepIndex + 1"
                />
              </div>

              <div class="flex items-center gap-2 mt-6">
                <input
                  type="checkbox"
                  :id="`step-${currentStepIndex}`"
                  :checked="isStepCompleted(currentStepIndex)"
                  @change="toggleStepComplete(currentStepIndex)"
                  class="w-5 h-5 text-green-600 rounded focus:ring-green-500"
                />
                <label
                  :for="`step-${currentStepIndex}`"
                  class="text-sm font-medium text-gray-700 cursor-pointer"
                >
                  {{ t('cook.mark_step_complete') }}
                </label>
              </div>
            </div>
          </div>

          <div class="px-6 py-4 bg-gray-50 border-t flex items-center justify-between">
            <button
              @click="previousStep"
              :disabled="isFirstStep"
              :class="[
                'px-6 py-3 rounded-lg font-medium transition flex items-center gap-2',
                isFirstStep
                  ? 'bg-gray-200 text-gray-400 cursor-not-allowed'
                  : 'bg-gray-600 text-white hover:bg-gray-700'
              ]"
            >
              <ChevronLeftIcon class="w-5 h-5" />
              {{ t('common.previous') }}
            </button>

            <button
              v-if="!isLastStep"
              @click="nextStep"
              class="px-6 py-3 bg-green-600 text-white rounded-lg font-medium hover:bg-green-700 transition flex items-center gap-2"
            >
              {{ t('common.next') }}
              <ChevronRightIcon class="w-5 h-5" />
            </button>

            <button
              v-else
              @click="finishCooking"
              class="px-6 py-3 bg-green-600 text-white rounded-lg font-medium hover:bg-green-700 transition flex items-center gap-2"
            >
              <CheckIcon class="w-5 h-5" />
              {{ t('cook.finish') }}
            </button>
          </div>

          <div class="px-6 py-4 bg-white border-t">
            <details class="group">
              <summary class="cursor-pointer text-sm font-medium text-gray-700 hover:text-green-600 transition flex items-center gap-2">
                <ListBulletIcon class="w-5 h-5" />
                {{ t('cook.view_all_steps', { count: totalSteps }) }}
              </summary>
              <div class="mt-4 space-y-2">
                <button
                  v-for="(step, index) in (recipe && recipe.steps ? recipe.steps : [])"
                  :key="index"
                  @click="goToStep(index)"
                  :class="[
                    'w-full text-left px-4 py-3 rounded-lg transition flex items-center gap-3',
                    index === currentStepIndex
                      ? 'bg-green-100 border-2 border-green-600'
                      : isStepCompleted(index)
                      ? 'bg-gray-100 border border-gray-300'
                      : 'bg-white border border-gray-200 hover:border-green-400'
                  ]"
                >
                  <div
                    :class="[
                      'flex-shrink-0 w-8 h-8 rounded-full flex items-center justify-center text-sm font-bold',
                      index === currentStepIndex
                        ? 'bg-green-600 text-white'
                        : isStepCompleted(index)
                        ? 'bg-green-500 text-white'
                        : 'bg-gray-300 text-gray-700'
                    ]"
                  >
                    <CheckIcon v-if="isStepCompleted(index)" class="w-5 h-5" />
                    <span v-else>{{ index + 1 }}</span>
                  </div>
                  <span class="flex-1 text-sm" :class="index === currentStepIndex ? 'font-semibold text-green-900' : 'text-gray-700'">
                    {{ step.text.substring(0, 80) }}{{ step.text.length > 80 ? '...' : '' }}
                  </span>
                  <ClockIcon v-if="step.timer_minutes" class="w-5 h-5 text-gray-400" />
                </button>
              </div>
            </details>
          </div>
        </div>
      </div>
    </div>
    <div v-else class="min-h-screen bg-gray-50 flex items-center justify-center">
      <div class="max-w-md mx-auto px-4">
        <div class="bg-white rounded-lg shadow-lg p-8 text-center">
          <h2 class="text-2xl font-bold text-gray-900 mb-4">{{ t('cook.no_steps_title') }}</h2>
          <p class="text-gray-600 mb-6">{{ t('cook.no_steps_message') }}</p>
          <button
            @click="router.visit('/recipes')"
            class="px-6 py-3 bg-green-600 text-white rounded-lg hover:bg-green-700 transition"
          >
            {{ t('common.back') }}
          </button>
        </div>
      </div>
    </div>
  </AppLayout>
</template>

<script setup>
import { onMounted, onUnmounted } from 'vue'
import { useI18n } from 'vue-i18n'
import { router } from '@inertiajs/vue3'
import { useCookingModeStore } from '@/stores/cookingMode'
import { storeToRefs } from 'pinia'
import AppLayout from '@/Layouts/AppLayout.vue'
import RecipeTimer from '@/Components/Recipe/RecipeTimer.vue'
import {
  XMarkIcon,
  ChevronLeftIcon,
  ChevronRightIcon,
  CheckIcon,
  ListBulletIcon,
  ClockIcon
} from '@heroicons/vue/24/outline'

const { t } = useI18n()

const props = defineProps({
  recipe: {
    type: Object,
    required: true
  }
})

const cookingStore = useCookingModeStore()
const {
  currentStepIndex,
  currentStep,
  totalSteps,
  isFirstStep,
  isLastStep,
  progressPercentage
} = storeToRefs(cookingStore)

const {
  startCooking,
  nextStep,
  previousStep,
  goToStep,
  toggleStepComplete,
  isStepCompleted,
  exitCooking: exitStore
} = cookingStore

onMounted(() => {
  startCooking(props.recipe)

  if ('wakeLock' in navigator) {
    requestWakeLock()
  }
})

let wakeLock = null

const requestWakeLock = async () => {
  try {
    wakeLock = await navigator.wakeLock.request('screen')
  } catch (err) {
    console.error('Wake Lock error:', err)
  }
}

const releaseWakeLock = async () => {
  if (wakeLock) {
    await wakeLock.release()
    wakeLock = null
  }
}

const exitCooking = () => {
  if (confirm(t('cook.exit_confirmation'))) {
    releaseWakeLock()
    exitStore()
    router.visit(`/recipes/${props.recipe.slug}`)
  }
}

const finishCooking = () => {
  releaseWakeLock()
  exitStore()
  router.visit(`/recipes/${props.recipe.slug}`, {
    onSuccess: () => {
      alert(t('cook.congratulations_message'))
    }
  })
}

onUnmounted(() => {
  releaseWakeLock()
})
</script>
