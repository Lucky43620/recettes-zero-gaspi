<script setup>
import { Link } from '@inertiajs/vue3';
import { useI18n } from 'vue-i18n';
import { useDateFormat } from '@/composables/useDateFormat';

const { t } = useI18n();
const { formatRelativeTime } = useDateFormat();

defineProps({
    users: Array,
});
</script>

<template>
    <div class="bg-white rounded-lg shadow">
        <div class="p-6 border-b">
            <div class="flex items-center justify-between">
                <h2 class="text-lg font-semibold text-gray-900">{{ t('admin.recent_users') }}</h2>
                <Link href="/admin/users" class="text-sm text-green-600 hover:text-green-700">
                    {{ t('admin.view_all') }}
                </Link>
            </div>
        </div>
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">{{ t('admin.user') }}</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">{{ t('admin.email') }}</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">{{ t('admin.registered_on') }}</th>
                        <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase">{{ t('common.actions') }}</th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200">
                    <tr v-for="user in users" :key="user.id" class="hover:bg-gray-50">
                        <td class="px-6 py-4 whitespace-nowrap">
                            <div class="flex items-center">
                                <img :src="user.profile_photo_url" :alt="user.name" class="w-8 h-8 rounded-full" />
                                <span class="ml-3 text-sm font-medium text-gray-900">{{ user.name }}</span>
                            </div>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                            {{ user.email }}
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                            {{ formatRelativeTime(user.created_at) }}
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-right text-sm">
                            <Link :href="`/admin/users/${user.id}`" class="text-green-600 hover:text-green-700">
                                {{ t('common.view') }}
                            </Link>
                        </td>
                    </tr>
                </tbody>
            </table>
        </div>
    </div>
</template>
