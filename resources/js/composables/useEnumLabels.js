export function useDifficultyLabels() {
    const difficulties = {
        easy: {
            label: 'Facile',
            emoji: '😊',
            color: 'green',
            classes: 'bg-green-100 text-green-800'
        },
        medium: {
            label: 'Moyen',
            emoji: '👨‍🍳',
            color: 'yellow',
            classes: 'bg-yellow-100 text-yellow-800'
        },
        hard: {
            label: 'Difficile',
            emoji: '⭐',
            color: 'red',
            classes: 'bg-red-100 text-red-800'
        }
    };

    const getDifficultyLabel = (difficulty) => {
        return difficulties[difficulty]?.label || difficulty;
    };

    const getDifficultyEmoji = (difficulty) => {
        return difficulties[difficulty]?.emoji || '';
    };

    const getDifficultyClasses = (difficulty) => {
        return difficulties[difficulty]?.classes || 'bg-gray-100 text-gray-800';
    };

    const difficultyOptions = Object.entries(difficulties).map(([value, data]) => ({
        value,
        label: `${data.emoji} ${data.label}`
    }));

    return {
        difficulties,
        getDifficultyLabel,
        getDifficultyEmoji,
        getDifficultyClasses,
        difficultyOptions
    };
}

export function useMealTypeLabels() {
    const mealTypes = {
        breakfast: {
            label: 'Petit-déjeuner',
            emoji: '🌅',
            icon: 'sun'
        },
        lunch: {
            label: 'Déjeuner',
            emoji: '🍽️',
            icon: 'utensils'
        },
        dinner: {
            label: 'Dîner',
            emoji: '🌙',
            icon: 'moon'
        },
        snack: {
            label: 'Collation',
            emoji: '🍪',
            icon: 'cookie'
        }
    };

    const getMealTypeLabel = (type) => {
        return mealTypes[type]?.label || type;
    };

    const getMealTypeEmoji = (type) => {
        return mealTypes[type]?.emoji || '';
    };

    const mealTypeOptions = Object.entries(mealTypes).map(([value, data]) => ({
        value,
        label: data.label,
        emoji: data.emoji
    }));

    return {
        mealTypes,
        getMealTypeLabel,
        getMealTypeEmoji,
        mealTypeOptions
    };
}

export function useDayOfWeekLabels() {
    const daysOfWeek = {
        monday: { label: 'Lundi', short: 'Lun' },
        tuesday: { label: 'Mardi', short: 'Mar' },
        wednesday: { label: 'Mercredi', short: 'Mer' },
        thursday: { label: 'Jeudi', short: 'Jeu' },
        friday: { label: 'Vendredi', short: 'Ven' },
        saturday: { label: 'Samedi', short: 'Sam' },
        sunday: { label: 'Dimanche', short: 'Dim' }
    };

    const getDayLabel = (day) => {
        return daysOfWeek[day]?.label || day;
    };

    const getDayShort = (day) => {
        return daysOfWeek[day]?.short || day;
    };

    const dayOptions = Object.entries(daysOfWeek).map(([value, data]) => ({
        value,
        label: data.label,
        short: data.short
    }));

    return {
        daysOfWeek,
        getDayLabel,
        getDayShort,
        dayOptions
    };
}

export function useStorageLocationLabels() {
    const storageLocations = {
        'Réfrigérateur': { emoji: '❄️', color: 'blue' },
        'Congélateur': { emoji: '🧊', color: 'cyan' },
        'Placard': { emoji: '🚪', color: 'gray' },
        'Cave': { emoji: '🏚️', color: 'stone' },
        'Garde-manger': { emoji: '🥫', color: 'yellow' }
    };

    const getLocationEmoji = (location) => {
        return storageLocations[location]?.emoji || '';
    };

    const storageLocationOptions = Object.entries(storageLocations).map(([value, data]) => ({
        value,
        label: `${data.emoji} ${value}`,
        emoji: data.emoji
    }));

    return {
        storageLocations,
        getLocationEmoji,
        storageLocationOptions
    };
}
