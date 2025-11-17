import { computed, ref } from 'vue';
import { router } from '@inertiajs/vue3';

export function useWeekNavigation(initialWeekStart) {
    const weekStart = ref(initialWeekStart || getTodayWeekStart());

    const currentWeekStart = computed(() => new Date(weekStart.value));

    const prevWeek = computed(() => {
        const date = new Date(currentWeekStart.value);
        date.setDate(date.getDate() - 7);
        return date.toISOString().split('T')[0];
    });

    const nextWeek = computed(() => {
        const date = new Date(currentWeekStart.value);
        date.setDate(date.getDate() + 7);
        return date.toISOString().split('T')[0];
    });

    const currentMonth = computed(() => {
        return currentWeekStart.value.toLocaleDateString('fr-FR', {
            month: 'long',
            year: 'numeric'
        });
    });

    const currentWeekNumber = computed(() => {
        const onejan = new Date(currentWeekStart.value.getFullYear(), 0, 1);
        const millisecsInDay = 86400000;
        return Math.ceil((((currentWeekStart.value - onejan) / millisecsInDay) + onejan.getDay() + 1) / 7);
    });

    const navigateToWeek = (date, routeName = 'meal-plans.index') => {
        router.get(route(routeName), { week_start: date }, {
            preserveState: true,
            preserveScroll: true,
        });
    };

    const goToPrevWeek = (routeName) => {
        navigateToWeek(prevWeek.value, routeName);
    };

    const goToNextWeek = (routeName) => {
        navigateToWeek(nextWeek.value, routeName);
    };

    const goToToday = (routeName) => {
        navigateToWeek(getTodayWeekStart(), routeName);
    };

    const isCurrentWeek = computed(() => {
        return weekStart.value === getTodayWeekStart();
    });

    return {
        weekStart,
        currentWeekStart,
        prevWeek,
        nextWeek,
        currentMonth,
        currentWeekNumber,
        isCurrentWeek,
        navigateToWeek,
        goToPrevWeek,
        goToNextWeek,
        goToToday,
    };
}

function getTodayWeekStart() {
    const today = new Date();
    const dayOfWeek = today.getDay();
    const diff = dayOfWeek === 0 ? -6 : 1 - dayOfWeek;
    const monday = new Date(today);
    monday.setDate(today.getDate() + diff);
    return monday.toISOString().split('T')[0];
}
