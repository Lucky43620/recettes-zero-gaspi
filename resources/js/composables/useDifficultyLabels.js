export function useDifficultyLabels() {
    function getDifficultyLabel(level) {
        const labels = {
            easy: 'Facile',
            medium: 'Moyen',
            hard: 'Difficile',
        };
        return labels[level] || level;
    }

    return {
        getDifficultyLabel,
    };
}
