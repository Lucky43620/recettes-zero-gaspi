<script setup>
import { Head, Link } from '@inertiajs/vue3';
import AdminLayout from '@/Layouts/AdminLayout.vue';
import StatCard from '@/Components/Admin/StatCard.vue';
import { useDateFormat } from '@/composables/useDateFormat';

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

const getReasonLabel = (reason) => {
    const labels = {
        spam: 'Spam',
        inappropriate: 'Inapproprié',
        offensive: 'Offensant',
        misleading: 'Trompeur',
        copyright: 'Copyright',
        other: 'Autre',
    };
    return labels[reason] || reason;
};
</script>

<template>
    <Head title="Administration - Dashboard" />

    <AdminLayout>
        <div class="space-y-8">
            <div>
                <h1 class="text-3xl font-bold text-gray-900">Dashboard</h1>
                <p class="mt-2 text-gray-600">Vue d'ensemble de la plateforme</p>
            </div>

            <div>
                <h2 class="text-lg font-semibold text-gray-900 mb-4">Utilisateurs</h2>
                <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                    <StatCard
                        title="Total utilisateurs"
                        :value="stats.users.total"
                        icon="👥"
                        color="blue"
                    />
                    <StatCard
                        title="Nouveaux ce mois"
                        :value="stats.users.new_this_month"
                        icon="🆕"
                        color="green"
                    />
                    <StatCard
                        title="Actifs cette semaine"
                        :value="stats.users.active_this_week"
                        icon="⚡"
                        color="purple"
                    />
                </div>
            </div>

            <div>
                <h2 class="text-lg font-semibold text-gray-900 mb-4">Recettes</h2>
                <div class="grid grid-cols-1 md:grid-cols-4 gap-6">
                    <StatCard
                        title="Total recettes"
                        :value="stats.recipes.total"
                        icon="📖"
                        color="blue"
                    />
                    <StatCard
                        title="Publiques"
                        :value="stats.recipes.public"
                        icon="🌐"
                        color="green"
                    />
                    <StatCard
                        title="Privées"
                        :value="stats.recipes.private"
                        icon="🔒"
                        color="orange"
                    />
                    <StatCard
                        title="Ce mois"
                        :value="stats.recipes.new_this_month"
                        icon="📅"
                        color="purple"
                    />
                </div>
            </div>

            <div>
                <h2 class="text-lg font-semibold text-gray-900 mb-4">Engagement</h2>
                <div class="grid grid-cols-1 md:grid-cols-4 gap-6">
                    <StatCard
                        title="Commentaires"
                        :value="stats.engagement.comments"
                        icon="💬"
                        color="blue"
                    />
                    <StatCard
                        title="Cooksnaps"
                        :value="stats.engagement.cooksnaps"
                        icon="📸"
                        color="purple"
                    />
                    <StatCard
                        title="Notes totales"
                        :value="stats.engagement.total_ratings"
                        icon="⭐"
                        color="orange"
                    />
                    <StatCard
                        title="Note moyenne"
                        :value="stats.engagement.avg_rating || 0"
                        icon="📊"
                        color="green"
                    />
                </div>
            </div>

            <div>
                <h2 class="text-lg font-semibold text-gray-900 mb-4">Modération</h2>
                <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                    <StatCard
                        title="Signalements en attente"
                        :value="stats.moderation.pending_reports"
                        icon="⏳"
                        color="orange"
                    />
                    <StatCard
                        title="En cours"
                        :value="stats.moderation.reviewing_reports"
                        icon="🔍"
                        color="blue"
                    />
                    <StatCard
                        title="Total signalements"
                        :value="stats.moderation.total_reports"
                        icon="🚨"
                        color="red"
                    />
                </div>
            </div>

            <div>
                <h2 class="text-lg font-semibold text-gray-900 mb-4">Événements</h2>
                <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                    <StatCard
                        title="Actifs"
                        :value="stats.events.active"
                        icon="🎯"
                        color="green"
                    />
                    <StatCard
                        title="À venir"
                        :value="stats.events.upcoming"
                        icon="📅"
                        color="blue"
                    />
                    <StatCard
                        title="Total"
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
                            <h2 class="text-lg font-semibold text-gray-900">Signalements récents</h2>
                            <Link href="/admin/reports" class="text-sm text-green-600 hover:text-green-700">
                                Voir tout
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
                                    {{ report.status }}
                                </span>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="bg-white rounded-lg shadow">
                    <div class="p-6 border-b">
                        <h2 class="text-lg font-semibold text-gray-900">Top recettes</h2>
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
                                        Par {{ recipe.author.name }}
                                    </p>
                                </div>
                                <div class="text-right">
                                    <div class="flex items-center gap-1">
                                        <span class="text-yellow-500">⭐</span>
                                        <span class="text-sm font-medium">{{ recipe.rating_avg || 0 }}</span>
                                    </div>
                                    <p class="text-xs text-gray-400">{{ recipe.rating_count }} notes</p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="bg-white rounded-lg shadow">
                <div class="p-6 border-b">
                    <div class="flex items-center justify-between">
                        <h2 class="text-lg font-semibold text-gray-900">Utilisateurs récents</h2>
                        <Link href="/admin/users" class="text-sm text-green-600 hover:text-green-700">
                            Voir tout
                        </Link>
                    </div>
                </div>
                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-gray-200">
                        <thead class="bg-gray-50">
                            <tr>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Utilisateur</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Email</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Inscrit le</th>
                                <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase">Actions</th>
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
                                        Voir
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
