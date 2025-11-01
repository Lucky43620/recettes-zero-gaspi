import { ref, computed, onUnmounted } from 'vue'

export function useTimer(initialMinutes = 0) {
  const totalSeconds = ref(Math.round(initialMinutes * 60))
  const remainingSeconds = ref(totalSeconds.value)
  const isRunning = ref(false)
  const isPaused = ref(false)
  const isFinished = ref(false)
  let intervalId = null

  const minutes = computed(() => Math.floor(remainingSeconds.value / 60))
  const seconds = computed(() => remainingSeconds.value % 60)

  const formattedTime = computed(() => {
    const m = String(minutes.value).padStart(2, '0')
    const s = String(seconds.value).padStart(2, '0')
    return `${m}:${s}`
  })

  const progress = computed(() => {
    if (totalSeconds.value === 0) return 0
    return ((totalSeconds.value - remainingSeconds.value) / totalSeconds.value) * 100
  })

  const start = () => {
    if (isRunning.value) return

    isRunning.value = true
    isPaused.value = false
    isFinished.value = false

    intervalId = setInterval(() => {
      if (remainingSeconds.value > 0) {
        remainingSeconds.value--
      } else {
        finish()
      }
    }, 1000)
  }

  const pause = () => {
    if (!isRunning.value) return

    clearInterval(intervalId)
    isRunning.value = false
    isPaused.value = true
  }

  const resume = () => {
    if (!isPaused.value) return
    start()
  }

  const reset = () => {
    clearInterval(intervalId)
    remainingSeconds.value = totalSeconds.value
    isRunning.value = false
    isPaused.value = false
    isFinished.value = false
  }

  const finish = () => {
    clearInterval(intervalId)
    isRunning.value = false
    isPaused.value = false
    isFinished.value = true
    playNotificationSound()
  }

  const playNotificationSound = () => {
    const audio = new Audio('/sounds/timer-finished.mp3')
    audio.play().catch(() => {})
  }

  onUnmounted(() => {
    if (intervalId) {
      clearInterval(intervalId)
    }
  })

  return {
    totalSeconds,
    remainingSeconds,
    minutes,
    seconds,
    formattedTime,
    progress,
    isRunning,
    isPaused,
    isFinished,
    start,
    pause,
    resume,
    reset
  }
}
