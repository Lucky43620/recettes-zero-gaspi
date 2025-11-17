import { computed } from 'vue';
import { usePage } from '@inertiajs/vue3';

export function useAlerts() {
    const page = usePage();

    const successMessage = computed(() => {
        return page.props.flash?.success || page.props.jetstream?.flash?.banner || '';
    });

    const errorMessage = computed(() => {
        const errors = page.props.errors;
        if (errors && Object.keys(errors).length > 0) {
            return Object.values(errors)[0];
        }
        return page.props.flash?.error || '';
    });

    const hasSuccess = computed(() => !!successMessage.value);
    const hasError = computed(() => !!errorMessage.value);
    const hasAnyAlert = computed(() => hasSuccess.value || hasError.value);

    return {
        successMessage,
        errorMessage,
        hasSuccess,
        hasError,
        hasAnyAlert,
    };
}
