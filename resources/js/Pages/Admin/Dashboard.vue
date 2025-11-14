<script setup>
import { Head } from '@inertiajs/vue3';
import { useI18n } from 'vue-i18n';
import AdminLayout from '@/Layouts/AdminLayout.vue';
import StatCard from '@/Components/Admin/StatCard.vue';
import RecentReportsCard from '@/Components/Admin/RecentReportsCard.vue';
import TopRecipesCard from '@/Components/Admin/TopRecipesCard.vue';
import RecentUsersTable from '@/Components/Admin/RecentUsersTable.vue';

const { t } = useI18n();

const props = defineProps({
    stats: Object,
    recentReports: Array,
    topRecipes: Array,
    recentUsers: Array,
});
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
                <RecentReportsCard :reports="recentReports" />
                <TopRecipesCard :recipes="topRecipes" />
            </div>

            <RecentUsersTable :users="recentUsers" />
        </div>
    </AdminLayout>
</template>
