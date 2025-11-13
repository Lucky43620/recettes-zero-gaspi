<script setup>
import { Head, Link } from '@inertiajs/vue3';
import { useI18n } from 'vue-i18n';
import AdminLayout from '@/Layouts/AdminLayout.vue';
import StatCard from '@/Components/Admin/StatCard.vue';
import { useDateFormat } from '@/composables/useDateFormat';

const { t } = useI18n();

const props = defineProps({
    stats: Object,
    recentReports: Array,
    topRecipes: Array,
    recentUsers: Array,
});

const { formatRelativeTime } = useDateFormat();

const getStatusColor = (status) => {
    const colors = {
        pending: 'bg-yellow-100 text-yellow-800',
        reviewing: 'bg-blue-100 text-blue-800',
        resolved: 'bg-green-100 text-green-800',
        dismissed: 'bg-gray-100 text-gray-800',
    };
    return colors[status] || colors.pending;
};

const getStatusLabel = (status) => {
    return t(`admin.report_status.${status}`);
};

const getReasonLabel = (reason) => {
    return t(`admin.report_reason.${reason}`);
};
</script>

<template>
    <Head :title="t('admin.dashboard_title')" />

    <AdminLayout>
        <div class="space-y-8">
            <div>
                <h1 class="text-3xl font-bold text-gray-900">{{ t('admin.dashboard') }}</h1>
                <p class="mt-2 text-gray-600">{{ t('admin.platform_overview') }}</p>
            </div>

            <div>
                <h2 class="text-lg font-semibold text-gray-900 mb-4">{{ t('admin.users') }}</h2>
                <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                    <StatCard
                        :title="t('admin.total_users')"
                        :value="stats.users.total"
                        icon="👥"
                        color="blue"
                    />
                    <StatCard
                        :title="t('admin.new_this_month')"
                        :value="stats.users.new_this_month"
                        icon="🆕"
                        color="green"
                    />
                    <StatCard
                        :title="t('admin.active_this_week')"
                        :value="stats.users.active_this_week"
                        icon="⚡"
                        color="purple"
                    />
                </div>
            </div>

            <div>
                <h2 class="text-lg font-semibold text-gray-900 mb-4">{{ t('admin.recipes') }}</h2>
                <div class="grid grid-cols-1 md:grid-cols-4 gap-6">
                    <StatCard
                        :title="t('admin.total_recipes')"
                        :value="stats.recipes.total"
                        icon="📖"
                        color="blue"
                    />
                    <StatCard
                        :title="t('admin.public_recipes')"
                        :value="stats.recipes.public"
                        icon="🌐"
                        color="green"
                    />
                    <StatCard
                        :title="t('admin.private_recipes')"
                        :value="stats.recipes.private"
                        icon="🔒"
                        color="orange"
                    />
                    <StatCard
                        :title="t('admin.this_month')"
                        :value="stats.recipes.new_this_month"
                        icon="📅"
                        color="purple"
                    />
                </div>
            </div>

            <div>
                <h2 class="text-lg font-semibold text-gray-900 mb-4">{{ t('admin.engagement') }}</h2>
                <div class="grid grid-cols-1 md:grid-cols-4 gap-6">
                    <StatCard
                        :title="t('admin.comments')"
                        :value="stats.engagement.comments"
                        icon="💬"
                        color="blue"
                    />
                    <StatCard
                        :title="t('admin.cooksnaps')"
                        :value="stats.engagement.cooksnaps"
                        icon="📸"
                        color="purple"
                    />
                    <StatCard
                        :title="t('admin.total_ratings')"
                        :value="stats.engagement.total_ratings"
                        icon="⭐"
                        color="orange"
                    />
                    <StatCard
                        :title="t('admin.average_rating')"
                        :value="stats.engagement.avg_rating || 0"
                        icon="📊"
                        color="green"
                    />
                </div>
            </div>

            <div>
                <h2 class="text-lg font-semibold text-gray-900 mb-4">{{ t('admin.moderation') }}</h2>
                <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                    <StatCard
                        :title="t('admin.pending_reports')"
                        :value="stats.moderation.pending_reports"
                        icon="⏳"
                        color="orange"
                    />
                    <StatCard
                        :title="t('admin.reviewing_reports')"
                        :value="stats.moderation.reviewing_reports"
                        icon="🔍"
                        color="blue"
                    />
                    <StatCard
                        :title="t('admin.total_reports')"
                        :value="stats.moderation.total_reports"
                        icon="🚨"
                        color="red"
                    />
                </div>
            </div>

            <div>
                <h2 class="text-lg font-semibold text-gray-900 mb-4">{{ t('admin.events') }}</h2>
                <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                    <StatCard
                        :title="t('admin.active_events')"
                        :value="stats.events.active"
                        icon="🎯"
                        color="green"
                    />
                    <StatCard
                        :title="t('admin.upcoming_events')"
                        :value="stats.events.upcoming"
                        icon="📅"
                        color="blue"
                    />
                    <StatCard
                        :title="t('admin.total_events')"
                        :value="stats.events.total"
                        icon="🎪"
                        color="purple"
                    />
                </div>
            </div>

            <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
                <div class="bg-white rounded-lg shadow">
                    <div class="p-6 border-b">
                        <div class="flex items-center justify-between">
                            <h2 class="text-lg font-semibold text-gray-900">{{ t('admin.recent_reports') }}</h2>
                            <Link href="/admin/reports" class="text-sm text-green-600 hover:text-green-700">
                                {{ t('admin.view_all') }}
                            </Link>
                        </div>
                    </div>
                    <div class="divide-y">
                        <div
                            v-for="report in recentReports.slice(0, 5)"
                            :key="report.id"
                            class="p-4 hover:bg-gray-50"
                        >
                            <div class="flex items-start justify-between">
                                <div class="flex-1">
                                    <p class="text-sm font-medium text-gray-900">
                                        {{ report.reporter.name }}
                                    </p>
                                    <p class="text-sm text-gray-500 mt-1">
                                        {{ getReasonLabel(report.reason) }}
                                    </p>
                                    <p class="text-xs text-gray-400 mt-1">
                                        {{ formatRelativeTime(report.created_at) }}
                                    </p>
                                </div>
                                <span :class="['px-2 py-1 rounded-full text-xs font-medium', getStatusColor(report.status)]">
                                    {{ getStatusLabel(report.status) }}
                                </span>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="bg-white rounded-lg shadow">
                    <div class="p-6 border-b">
                        <h2 class="text-lg font-semibold text-gray-900">{{ t('admin.top_recipes') }}</h2>
                    </div>
                    <div class="divide-y">
                        <div
                            v-for="recipe in topRecipes.slice(0, 5)"
                            :key="recipe.id"
                            class="p-4 hover:bg-gray-50"
                        >
                            <div class="flex items-center justify-between">
                                <div class="flex-1">
                                    <Link :href="`/recipes/${recipe.slug}`" class="text-sm font-medium text-gray-900 hover:text-green-600">
                                        {{ recipe.title }}
                                    </Link>
                                    <p class="text-sm text-gray-500 mt-1">
                                        {{ t('admin.by_author', { author: recipe.author.name }) }}
                                    </p>
                                </div>
                                <div class="text-right">
                                    <div class="flex items-center gap-1">
                                        <span class="text-yellow-500">⭐</span>
                                        <span class="text-sm font-medium">{{ recipe.rating_avg || 0 }}</span>
                                    </div>
                                    <p class="text-xs text-gray-400">{{ t('admin.ratings_count', { count: recipe.rating_count }) }}</p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

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
                            <tr v-for="user in recentUsers" :key="user.id" class="hover:bg-gray-50">
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
        </div>
    </AdminLayout>
</template>
