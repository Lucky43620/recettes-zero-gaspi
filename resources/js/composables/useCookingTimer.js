import { ref, onUnmounted } from 'vue';
import { useI18n } from 'vue-i18n';

export function useCookingTimer() {
    const { t } = useI18n();
    const timers = ref({});
    const intervals = new Map();

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

    const stopTimer = (stepIndex) => {
        const interval = intervals.get(stepIndex);
        if (interval) {
            clearInterval(interval);
            intervals.delete(stepIndex);
        }
        delete timers.value[stepIndex];
    };

    const startTimer = (stepIndex, stepContent) => {
        stopTimer(stepIndex);

        const duration = extractDuration(stepContent);

        if (duration) {
            const endTime = Date.now() + duration * 60 * 1000;
            timers.value[stepIndex] = {
                endTime,
                remaining: duration * 60,
            };

            const interval = setInterval(() => {
                const remaining = Math.max(0, Math.floor((endTime - Date.now()) / 1000));
                if (timers.value[stepIndex]) {
                    timers.value[stepIndex].remaining = remaining;
                }

                if (remaining === 0) {
                    stopTimer(stepIndex);
                    if (Notification.permission === 'granted') {
                        new Notification(t('app.name'), {
                            body: t('cook.step_completed', { step: stepIndex + 1 }),
                            icon: '/favicon.ico',
                        });
                    }
                }
            }, 1000);

            intervals.set(stepIndex, interval);
        }
    };

    const stopAllTimers = () => {
        intervals.forEach(interval => clearInterval(interval));
        intervals.clear();
        timers.value = {};
    };

    onUnmounted(() => {
        stopAllTimers();
    });

    return {
        timers,
        extractDuration,
        formatTime,
        startTimer,
        stopTimer,
        stopAllTimers
    };
}
