import { ref, watch } from 'vue';
import axios from 'axios';

export function useIngredientSearch(options = {}) {
    const {
        minSearchLength = 2,
        debounceDelay = 300,
        autoSearch = true,
    } = options;

    const searchQuery = ref('');
    const results = ref([]);
    const isSearching = ref(false);
    const error = ref(null);
    let searchTimeout = null;

    const search = async (query) => {
        if (query.length < minSearchLength) {
            results.value = [];
            return;
        }

        isSearching.value = true;
        error.value = null;

        try {
            const response = await axios.get('/api/ingredients/search', {
                params: { q: query }
            });
            results.value = response.data.data || [];
        } catch (err) {
            error.value = err.response?.data?.message || 'Erreur lors de la recherche';
            results.value = [];
        } finally {
            isSearching.value = false;
        }
    };

    const debouncedSearch = (query) => {
        if (searchTimeout) {
            clearTimeout(searchTimeout);
        }

        searchTimeout = setTimeout(() => {
            search(query);
        }, debounceDelay);
    };

    const handleInput = (query) => {
        searchQuery.value = query;
        if (autoSearch) {
            debouncedSearch(query);
        }
    };

    const clearResults = () => {
        results.value = [];
        searchQuery.value = '';
        error.value = null;
    };

    const searchByBarcode = async (barcode) => {
        isSearching.value = true;
        error.value = null;

        try {
            const response = await axios.get('/api/ingredients/search', {
                params: { barcode }
            });
            results.value = response.data.data || [];
            return results.value[0] || null;
        } catch (err) {
            error.value = err.response?.data?.message || 'Produit non trouvé';
            results.value = [];
            return null;
        } finally {
            isSearching.value = false;
        }
    };

    if (autoSearch) {
        watch(searchQuery, (newQuery) => {
            debouncedSearch(newQuery);
        });
    }

    return {
        searchQuery,
        results,
        isSearching,
        error,
        search,
        handleInput,
        clearResults,
        searchByBarcode,
    };
}
