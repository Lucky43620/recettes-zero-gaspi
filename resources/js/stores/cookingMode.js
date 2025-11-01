import { defineStore } from 'pinia'
import { ref, computed } from 'vue'

export const useCookingModeStore = defineStore('cookingMode', () => {
  const recipe = ref(null)
  const currentStepIndex = ref(0)
  const completedSteps = ref(new Set())
  const activeTimers = ref(new Map())
  const isFullscreen = ref(false)

  const currentStep = computed(() => {
    if (!recipe.value?.steps) return null
    return recipe.value.steps[currentStepIndex.value]
  })

  const totalSteps = computed(() => recipe.value?.steps?.length || 0)

  const isFirstStep = computed(() => currentStepIndex.value === 0)

  const isLastStep = computed(() => currentStepIndex.value === totalSteps.value - 1)

  const progressPercentage = computed(() => {
    if (totalSteps.value === 0) return 0
    return ((currentStepIndex.value + 1) / totalSteps.value) * 100
  })

  const startCooking = (recipeData) => {
    recipe.value = recipeData
    currentStepIndex.value = 0
    completedSteps.value.clear()
    activeTimers.value.clear()

    const savedState = localStorage.getItem(`cooking-${recipeData.id}`)
    if (savedState) {
      try {
        const state = JSON.parse(savedState)
        currentStepIndex.value = state.currentStepIndex || 0
        completedSteps.value = new Set(state.completedSteps || [])
      } catch (e) {
        console.error('Failed to restore cooking state', e)
      }
    }
  }

  const nextStep = () => {
    if (isLastStep.value) return

    completedSteps.value.add(currentStepIndex.value)
    currentStepIndex.value++
    saveState()
  }

  const previousStep = () => {
    if (isFirstStep.value) return

    currentStepIndex.value--
    saveState()
  }

  const goToStep = (index) => {
    if (index >= 0 && index < totalSteps.value) {
      currentStepIndex.value = index
      saveState()
    }
  }

  const toggleStepComplete = (index) => {
    if (completedSteps.value.has(index)) {
      completedSteps.value.delete(index)
    } else {
      completedSteps.value.add(index)
    }
    saveState()
  }

  const isStepCompleted = (index) => {
    return completedSteps.value.has(index)
  }

  const saveState = () => {
    if (!recipe.value) return

    const state = {
      currentStepIndex: currentStepIndex.value,
      completedSteps: Array.from(completedSteps.value),
      timestamp: Date.now()
    }

    localStorage.setItem(`cooking-${recipe.value.id}`, JSON.stringify(state))
  }

  const clearState = () => {
    if (!recipe.value) return

    localStorage.removeItem(`cooking-${recipe.value.id}`)
    completedSteps.value.clear()
    currentStepIndex.value = 0
  }

  const exitCooking = () => {
    recipe.value = null
    currentStepIndex.value = 0
    completedSteps.value.clear()
    activeTimers.value.clear()
    isFullscreen.value = false
  }

  return {
    recipe,
    currentStepIndex,
    currentStep,
    totalSteps,
    isFirstStep,
    isLastStep,
    progressPercentage,
    completedSteps,
    activeTimers,
    isFullscreen,
    startCooking,
    nextStep,
    previousStep,
    goToStep,
    toggleStepComplete,
    isStepCompleted,
    saveState,
    clearState,
    exitCooking
  }
})
