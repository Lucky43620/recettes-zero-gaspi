<script setup>
import AppLayout from '@/Layouts/AppLayout.vue';
import { Link } from '@inertiajs/vue3';
import { useI18n } from 'vue-i18n';
import { useMediaConversions } from '@/composables/useMediaConversions';
import { ref, computed } from 'vue';
import FormInput from '@/Components/Common/FormInput.vue';

const { t } = useI18n();
const { getRecipeImage } = useMediaConversions();

const props = defineProps({
    collections: Object,
});

const searchQuery = ref('');

const filteredCollections = computed(() => {
    if (!props.collections?.data) return [];

    if (!searchQuery.value) {
        return props.collections.data;
    }

    const query = searchQuery.value.toLowerCase().trim();
    return props.collections.data.filter(collection => {
        const name = (collection.name || '').toLowerCase();
        const description = (collection.description || '').toLowerCase();
        const userName = (collection.user?.name || '').toLowerCase();

        return name.includes(query) ||
               description.includes(query) ||
               userName.includes(query);
    });
});
</script>

<template>
    <AppLayout :title="t('collections.title')">
        <template #header>
            <div class="flex flex-col sm:flex-row sm:justify-between sm:items-center gap-4">
                <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                    {{ t('collections.title') }}
                </h2>

                <div class="flex gap-3">
                    <Link
                        :href="route('collections.index')"
                        class="inline-flex items-center px-4 py-2 bg-white border border-gray-300 rounded-lg hover:bg-gray-50 transition"
                    >
                        <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10"/>
                        </svg>
                        {{ t('collections.my_collections') }}
                    </Link>
                </div>
            </div>
        </template>

        <div class="py-12">
            <div class="max-w-[1920px] mx-auto px-4 sm:px-6 lg:px-8 space-y-6">
                <!-- Search Bar -->
                <div class="bg-white rounded-lg shadow p-4">
                    <FormInput
                        v-model="searchQuery"
                        type="text"
                        :placeholder="t('collections.search_placeholder')"
                        class="w-full"
                    >
                        <template #prefix>
                            <svg class="w-5 h-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
                            </svg>
                        </template>
                    </FormInput>
                </div>

                <div v-if="filteredCollections.length" class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-6">
                    <Link
                        v-for="collection in filteredCollections"
                        :key="collection.id"
                        :href="route('collections.show', collection.id)"
                        class="bg-white rounded-lg shadow hover:shadow-lg transition-all duration-200 overflow-hidden group"
                    >
                        <!-- Preview Images (first 4 recipes) -->
                        <div class="grid grid-cols-2 gap-1 h-48 bg-gray-100">
                            <template v-if="collection.recipes && collection.recipes.length > 0">
                                <div
                                    v-for="(recipe, index) in collection.recipes.slice(0, 4)"
                                    :key="recipe.id"
                                    class="relative overflow-hidden"
                                >
                                    <img
                                        :src="getRecipeImage(recipe, 'thumb')"
                                        :alt="recipe.title"
                                        class="w-full h-full object-cover group-hover:scale-110 transition-transform duration-200"
                                    />
                                </div>
                                <!-- Fill empty slots if less than 4 recipes -->
                                <div
                                    v-for="i in (4 - Math.min(collection.recipes.length, 4))"
                                    :key="`empty-${i}`"
                                    class="bg-gradient-to-br from-gray-100 to-gray-200"
                                ></div>
                            </template>
                            <template v-else>
                                <div
                                    v-for="i in 4"
                                    :key="`placeholder-${i}`"
                                    class="bg-gradient-to-br from-gray-100 to-gray-200"
                                ></div>
                            </template>
                        </div>

                        <!-- Collection Info -->
                        <div class="p-4">
                            <h3 class="text-lg font-semibold text-gray-900 mb-1 line-clamp-1">
                                {{ collection.name }}
                            </h3>

                            <p v-if="collection.description" class="text-gray-600 text-sm mb-3 line-clamp-2">
                                {{ collection.description }}
                            </p>

                            <div class="flex items-center justify-between text-sm">
                                <div class="flex items-center gap-2 text-gray-500">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"/>
                                    </svg>
                                    <span>{{ collection.recipes_count }} {{ t('collections.recipes_count') }}</span>
                                </div>

                                <div class="flex items-center gap-2 text-gray-500">
                                    <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 20 20">
                                        <path fill-rule="evenodd" d="M10 9a3 3 0 100-6 3 3 0 000 6zm-7 9a7 7 0 1114 0H3z" clip-rule="evenodd"/>
                                    </svg>
                                    <span class="text-sm truncate max-w-[100px]">{{ collection.user?.name || 'Anonyme' }}</span>
                                </div>
                            </div>
                        </div>
                    </Link>
                </div>

                <div v-else class="bg-white rounded-lg shadow p-12 text-center">
                    <svg class="mx-auto h-12 w-12 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10"/>
                    </svg>
                    <h3 class="mt-4 text-lg font-medium text-gray-900">{{ t('collections.no_collections') }}</h3>
                    <p class="mt-2 text-gray-600">{{ t('collections.empty') }}</p>
                </div>

                <!-- Pagination -->
                <div v-if="collections.data && collections.data.length && (collections.prev_page_url || collections.next_page_url)" class="flex justify-center gap-2 mt-8">
                    <Link
                        v-if="collections.prev_page_url"
                        :href="collections.prev_page_url"
                        class="px-4 py-2 bg-white border border-gray-300 rounded-lg hover:bg-gray-50 transition"
                    >
                        {{ t('common.previous') }}
                    </Link>
                    <Link
                        v-if="collections.next_page_url"
                        :href="collections.next_page_url"
                        class="px-4 py-2 bg-orange-600 text-white rounded-lg hover:bg-orange-700 transition"
                    >
                        {{ t('common.next') }}
                    </Link>
                </div>
            </div>
        </div>
    </AppLayout>
</template>
