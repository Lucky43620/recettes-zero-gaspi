<script setup>
import { ref, onMounted } from 'vue';
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

const sectionsVisible = ref([]);

onMounted(() => {
    [0, 1, 2, 3, 4, 5, 6].forEach((index) => {
        setTimeout(() => {
            sectionsVisible.value[index] = true;
        }, index * 100);
    });
});
</script>

<template>
    <Head :title="t('admin.dashboard_title')" />

    <AdminLayout>
        <div class="space-y-6">
            <div class="transition-all duration-500 opacity-100 transform translate-y-0">
                <h1 class="text-3xl font-bold text-gray-900">{{ t('admin.dashboard') }}</h1>
                <p class="mt-2 text-gray-600">{{ t('admin.platform_overview') }}</p>
            </div>

            <div
                class="bg-white rounded-lg shadow-sm p-6 border border-gray-100 transition-all duration-500"
                :class="sectionsVisible[0] ? 'opacity-100 translate-y-0' : 'opacity-0 translate-y-4'"
            >
                <h2 class="text-lg font-semibold text-gray-900 mb-4 pb-3 border-b border-gray-200">{{ t('admin.users') }}</h2>
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

            <div
                class="bg-white rounded-lg shadow-sm p-6 border border-gray-100 transition-all duration-500"
                :class="sectionsVisible[1] ? 'opacity-100 translate-y-0' : 'opacity-0 translate-y-4'"
            >
                <h2 class="text-lg font-semibold text-gray-900 mb-4 pb-3 border-b border-gray-200">{{ t('admin.recipes') }}</h2>
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

            <div
                class="bg-white rounded-lg shadow-sm p-6 border border-gray-100 transition-all duration-500"
                :class="sectionsVisible[2] ? 'opacity-100 translate-y-0' : 'opacity-0 translate-y-4'"
            >
                <h2 class="text-lg font-semibold text-gray-900 mb-4 pb-3 border-b border-gray-200">{{ t('admin.engagement') }}</h2>
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

            <div
                class="bg-white rounded-lg shadow-sm p-6 border border-gray-100 transition-all duration-500"
                :class="sectionsVisible[3] ? 'opacity-100 translate-y-0' : 'opacity-0 translate-y-4'"
            >
                <h2 class="text-lg font-semibold text-gray-900 mb-4 pb-3 border-b border-gray-200">{{ t('admin.moderation') }}</h2>
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

            <div
                class="bg-white rounded-lg shadow-sm p-6 border border-gray-100 transition-all duration-500"
                :class="sectionsVisible[4] ? 'opacity-100 translate-y-0' : 'opacity-0 translate-y-4'"
            >
                <h2 class="text-lg font-semibold text-gray-900 mb-4 pb-3 border-b border-gray-200">{{ t('admin.events') }}</h2>
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

            <div class="h-px bg-gradient-to-r from-transparent via-gray-300 to-transparent my-8"></div>

            <div
                class="grid grid-cols-1 lg:grid-cols-2 gap-6 transition-all duration-500"
                :class="sectionsVisible[5] ? 'opacity-100 translate-y-0' : 'opacity-0 translate-y-4'"
            >
                <RecentReportsCard :reports="recentReports" />
                <TopRecipesCard :recipes="topRecipes" />
            </div>

            <div
                class="transition-all duration-500"
                :class="sectionsVisible[6] ? 'opacity-100 translate-y-0' : 'opacity-0 translate-y-4'"
            >
                <RecentUsersTable :users="recentUsers" />
            </div>
        </div>
    </AdminLayout>
</template>
