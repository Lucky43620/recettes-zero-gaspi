/**
 * Composable for handling Spatie MediaLibrary conversions
 * Provides helper functions to get conversion URLs from media objects
 */
export function useMediaConversions() {
    /**
     * Get the URL for a specific conversion
     * @param {Object} media - The media object from Spatie MediaLibrary
     * @param {string} conversion - The conversion name (thumb, medium, large)
     * @returns {string} The conversion URL
     */
    const getConversionUrl = (media, conversion) => {
        if (!media?.original_url) return '';

        const originalUrl = media.original_url;
        const lastSlashIndex = originalUrl.lastIndexOf('/');
        const basePath = originalUrl.substring(0, lastSlashIndex);
        const fileName = originalUrl.substring(lastSlashIndex + 1);
        const fileNameWithoutExt = fileName.substring(0, fileName.lastIndexOf('.'));

        // Spatie MediaLibrary conversion URL pattern: /path/conversions/filename-conversion.webp
        return `${basePath}/conversions/${fileNameWithoutExt}-${conversion}.webp`;
    };

    /**
     * Get a srcset string for responsive images
     * @param {Object} media - The media object from Spatie MediaLibrary
     * @returns {string} The srcset attribute value
     */
    const getSrcset = (media) => {
        if (!media?.original_url) return '';

        return [
            `${getConversionUrl(media, 'thumb')} 300w`,
            `${getConversionUrl(media, 'medium')} 800w`,
            `${getConversionUrl(media, 'large')} 1200w`,
        ].join(', ');
    };

    /**
     * Get sizes attribute for different screen sizes
     * @param {string} size - The size preset (card, header, full)
     * @returns {string} The sizes attribute value
     */
    const getSizes = (size = 'card') => {
        const presets = {
            card: '(max-width: 640px) 300px, (max-width: 1024px) 400px, 300px',
            header: '(max-width: 640px) 100vw, (max-width: 1024px) 800px, 1200px',
            full: '100vw',
            cooksnap: '(max-width: 640px) 300px, (max-width: 1024px) 400px, 300px',
        };

        return presets[size] || presets.card;
    };

    return {
        getConversionUrl,
        getSrcset,
        getSizes,
    };
}
