export function useDateFormat() {
    const formatDate = (date, options = {}) => {
        if (!date) return '';

        const dateObj = typeof date === 'string' ? new Date(date) : date;

        const defaultOptions = {
            locale: 'fr-FR',
            dateStyle: 'medium',
            ...options
        };

        return dateObj.toLocaleDateString(defaultOptions.locale, defaultOptions);
    };

    const formatDateTime = (date, options = {}) => {
        if (!date) return '';

        const dateObj = typeof date === 'string' ? new Date(date) : date;

        const defaultOptions = {
            locale: 'fr-FR',
            dateStyle: 'medium',
            timeStyle: 'short',
            ...options
        };

        return dateObj.toLocaleDateString(defaultOptions.locale, defaultOptions);
    };

    const formatRelativeTime = (date) => {
        if (!date) return '';

        const dateObj = typeof date === 'string' ? new Date(date) : date;
        const now = new Date();
        const diffInSeconds = Math.floor((now - dateObj) / 1000);

        if (diffInSeconds < 60) {
            return 'À l\'instant';
        }

        const diffInMinutes = Math.floor(diffInSeconds / 60);
        if (diffInMinutes < 60) {
            return `Il y a ${diffInMinutes} minute${diffInMinutes > 1 ? 's' : ''}`;
        }

        const diffInHours = Math.floor(diffInMinutes / 60);
        if (diffInHours < 24) {
            return `Il y a ${diffInHours} heure${diffInHours > 1 ? 's' : ''}`;
        }

        const diffInDays = Math.floor(diffInHours / 24);
        if (diffInDays < 7) {
            return `Il y a ${diffInDays} jour${diffInDays > 1 ? 's' : ''}`;
        }

        const diffInWeeks = Math.floor(diffInDays / 7);
        if (diffInWeeks < 4) {
            return `Il y a ${diffInWeeks} semaine${diffInWeeks > 1 ? 's' : ''}`;
        }

        return formatDate(dateObj);
    };

    const formatShortDate = (date) => {
        return formatDate(date, { dateStyle: 'short' });
    };

    const formatLongDate = (date) => {
        return formatDate(date, { dateStyle: 'long' });
    };

    return {
        formatDate,
        formatDateTime,
        formatRelativeTime,
        formatShortDate,
        formatLongDate
    };
}
