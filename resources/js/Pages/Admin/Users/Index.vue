<script setup>
import { ref } from 'vue';
import { Head, Link, router } from '@inertiajs/vue3';
import { useI18n } from 'vue-i18n';
import AdminLayout from '@/Layouts/AdminLayout.vue';
import { useDateFormat } from '@/composables/useDateFormat';

const { t } = useI18n();

const props = defineProps({
    users: Object,
    filters: Object,
});

const search = ref(props.filters.search || '');

const searchUsers = () => {
    router.get('/admin/users', { search: search.value }, {
        preserveState: true,
        replace: true,
    });
};

const { formatRelativeTime } = useDateFormat();
</script>

<template>
    <Head :title="t('admin.users_title')" />

    <AdminLayout>
        <div class="space-y-6">
            <div class="flex items-center justify-between">
                <div>
                    <h1 class="text-3xl font-bold text-gray-900">{{ t('admin.users') }}</h1>
                    <p class="mt-2 text-gray-600">{{ t('admin.manage_users_description') }}</p>
                </div>
            </div>

            <div class="bg-white rounded-lg shadow">
                <div class="p-4 md:p-6 border-b">
                    <div class="flex flex-col sm:flex-row items-stretch sm:items-center gap-3 sm:gap-4">
                        <input
                            v-model="search"
                            type="text"
                            :placeholder="t('admin.search_by_name_email')"
                            class="flex-1 border-gray-300 rounded-lg focus:ring-green-500 focus:border-green-500"
                            @keyup.enter="searchUsers"
                        />
                        <button
                            @click="searchUsers"
                            class="px-6 py-2 bg-green-600 text-white rounded-lg hover:bg-green-700 transition whitespace-nowrap"
                        >
                            {{ t('common.search') }}
                        </button>
                    </div>
                </div>

                <div class="hidden md:block overflow-x-auto">
                    <table class="min-w-full divide-y divide-gray-200">
                        <thead class="bg-gray-50">
                            <tr>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">{{ t('admin.user_column') }}</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">{{ t('admin.email') }}</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">{{ t('admin.recipes_column') }}</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">{{ t('admin.comments_column') }}</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">{{ t('admin.followers_column') }}</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">{{ t('admin.registered_column') }}</th>
                                <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase">{{ t('common.actions') }}</th>
                            </tr>
                        </thead>
                        <tbody class="bg-white divide-y divide-gray-200">
                            <tr v-for="user in users.data" :key="user.id" class="hover:bg-gray-50">
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <div class="flex items-center">
                                        <img :src="user.profile_photo_url" :alt="user.name" class="w-10 h-10 rounded-full" />
                                        <span class="ml-3 text-sm font-medium text-gray-900">{{ user.name }}</span>
                                    </div>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                    {{ user.email }}
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                    {{ user.recipes_count || 0 }}
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                    {{ user.comments_count || 0 }}
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                    {{ user.followers_count || 0 }}
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                    {{ formatRelativeTime(user.created_at) }}
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-right text-sm">
                                    <Link :href="`/admin/users/${user.id}`" class="text-green-600 hover:text-green-700 font-medium">
                                        {{ t('admin.view_details') }}
                                    </Link>
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>

                <div class="md:hidden divide-y divide-gray-200">
                    <div v-for="user in users.data" :key="user.id" class="p-4 hover:bg-gray-50">
                        <div class="flex items-center justify-between mb-3">
                            <div class="flex items-center">
                                <img :src="user.profile_photo_url" :alt="user.name" class="w-12 h-12 rounded-full" />
                                <div class="ml-3">
                                    <div class="text-sm font-medium text-gray-900">{{ user.name }}</div>
                                    <div class="text-xs text-gray-500">{{ user.email }}</div>
                                </div>
                            </div>
                        </div>
                        <div class="grid grid-cols-3 gap-3 mb-3 text-center">
                            <div class="bg-gray-50 rounded-lg p-2">
                                <div class="text-xs text-gray-500">{{ t('admin.recipes_column') }}</div>
                                <div class="text-sm font-semibold text-gray-900">{{ user.recipes_count || 0 }}</div>
                            </div>
                            <div class="bg-gray-50 rounded-lg p-2">
                                <div class="text-xs text-gray-500">{{ t('admin.comments_column') }}</div>
                                <div class="text-sm font-semibold text-gray-900">{{ user.comments_count || 0 }}</div>
                            </div>
                            <div class="bg-gray-50 rounded-lg p-2">
                                <div class="text-xs text-gray-500">{{ t('admin.followers_column') }}</div>
                                <div class="text-sm font-semibold text-gray-900">{{ user.followers_count || 0 }}</div>
                            </div>
                        </div>
                        <div class="flex items-center justify-between text-xs">
                            <span class="text-gray-500">{{ formatRelativeTime(user.created_at) }}</span>
                            <Link :href="`/admin/users/${user.id}`" class="text-green-600 hover:text-green-700 font-medium">
                                {{ t('admin.view_details') }}
                            </Link>
                        </div>
                    </div>
                </div>

                <div v-if="users.links" class="px-4 md:px-6 py-4 border-t flex flex-col sm:flex-row items-center justify-between gap-3">
                    <div class="text-xs sm:text-sm text-gray-500">
                        {{ users.from }} - {{ users.to }} {{ t('admin.pagination_of') }} {{ users.total }} {{ t('admin.users_count') }}
                    </div>
                    <div class="flex flex-wrap gap-1 sm:gap-2 justify-center">
                        <Link
                            v-for="(link, index) in users.links"
                            :key="index"
                            :href="link.url"
                            :class="[
                                'px-2 sm:px-3 py-1.5 sm:py-2 rounded-lg text-xs sm:text-sm transition',
                                link.active
                                    ? 'bg-green-600 text-white'
                                    : link.url
                                    ? 'bg-gray-100 text-gray-700 hover:bg-gray-200'
                                    : 'bg-gray-50 text-gray-400 cursor-not-allowed'
                            ]"
                        >
                            <span v-if="link.label === 'pagination.previous'">{{ t('common.previous') }}</span>
                            <span v-else-if="link.label === 'pagination.next'">{{ t('common.next') }}</span>
                            <span v-else v-html="link.label"></span>
                        </Link>
                    </div>
                </div>
            </div>
        </div>
    </AdminLayout>
</template>