import { ref, onUnmounted } from 'vue';
import { useI18n } from 'vue-i18n';

export function useCookingTimer() {
    const { t } = useI18n();
    const activeTimer = ref(null);
    let intervalId = null;

    const extractDuration = (content) => {
        const match = content.match(/(\d+)\s*(min|minute|minutes|h|heure|heures)/i);
        if (match) {
            const value = parseInt(match[1]);
            const unit = match[2].toLowerCase();
            return unit.startsWith('h') ? value * 60 : value;
        }
        return null;
    };

    const formatTime = (seconds) => {
        const mins = Math.floor(seconds / 60);
        const secs = seconds % 60;
        return `${mins}:${secs.toString().padStart(2, '0')}`;
    };

    const stopTimer = () => {
        if (intervalId) {
            clearInterval(intervalId);
            intervalId = null;
        }
        activeTimer.value = null;
    };

    const startTimer = (stepIndex, stepContent) => {
        stopTimer();

        const duration = extractDuration(stepContent);

        if (duration) {
            const endTime = Date.now() + duration * 60 * 1000;
            activeTimer.value = {
                stepIndex,
                endTime,
                remaining: duration * 60,
            };

            intervalId = setInterval(() => {
                const remaining = Math.max(0, Math.floor((endTime - Date.now()) / 1000));

                if (activeTimer.value) {
                    activeTimer.value.remaining = remaining;
                }

                if (remaining === 0) {
                    stopTimer();
                    if (Notification.permission === 'granted') {
                        new Notification(t('app.name'), {
                            body: t('cook.step_completed', { step: stepIndex + 1 }),
                            icon: '/favicon.ico',
                        });
                    }
                }
            }, 1000);
        }
    };

    onUnmounted(() => {
        stopTimer();
    });

    return {
        activeTimer,
        extractDuration,
        formatTime,
        startTimer,
        stopTimer
    };
}
