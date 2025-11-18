<template>
    <AdminLayout>
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
            <div class="mb-8">
                <h1 class="text-3xl font-bold text-gray-900">{{ t('admin.subscriptions_management') }}</h1>
                <p class="mt-2 text-gray-600">{{ t('admin.subscriptions_dashboard') }}</p>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 mb-8">
                <StatCard
                    :title="t('admin.subscriptions_mrr')"
                    :value="formatCurrency(stats.mrr)"
                    icon="💰"
                    :description="t('admin.subscriptions_mrr_desc')"
                />
                <StatCard
                    :title="t('admin.subscriptions_arr')"
                    :value="formatCurrency(stats.arr)"
                    icon="📈"
                    :description="t('admin.subscriptions_arr_desc')"
                />
                <StatCard
                    :title="t('admin.subscriptions_active_subscribers')"
                    :value="stats.active_count"
                    icon="👥"
                    :description="t('admin.subscriptions_trial_count', { count: stats.trial_count })"
                />
                <StatCard
                    :title="t('admin.subscriptions_churn_rate')"
                    :value="stats.churn_rate + '%'"
                    icon="📉"
                    :description="t('admin.subscriptions_churn_rate_desc')"
                    :alert="stats.churn_rate > 5"
                />
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 mb-8">
                <StatCard
                    :title="t('admin.subscriptions_new_30d')"
                    :value="stats.new_subscribers"
                    icon="🆕"
                    :description="t('admin.subscriptions_new_subscribers')"
                />
                <StatCard
                    :title="t('admin.subscriptions_ltv_average')"
                    :value="formatCurrency(stats.ltv)"
                    icon="💎"
                    :description="t('admin.subscriptions_lifetime_value')"
                />
                <StatCard
                    :title="t('admin.subscriptions_conversion')"
                    :value="stats.conversion_rate + '%'"
                    icon="🎯"
                    :description="t('admin.subscriptions_trial_to_paid')"
                />
                <StatCard
                    :title="t('admin.subscriptions_distribution')"
                    :value="`${stats.plan_distribution.monthly}M / ${stats.plan_distribution.yearly}A`"
                    icon="📊"
                    :description="t('admin.subscriptions_monthly_yearly')"
                />
            </div>

            <div class="grid grid-cols-1 lg:grid-cols-2 gap-6 mb-8">
                <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6">
                    <h3 class="text-lg font-semibold text-gray-900 mb-4">{{ t('admin.subscriptions_revenue_12m') }}</h3>
                    <div class="h-64">
                        <canvas ref="revenueChart"></canvas>
                    </div>
                </div>

                <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6">
                    <h3 class="text-lg font-semibold text-gray-900 mb-4">{{ t('admin.subscriptions_subscriber_growth') }}</h3>
                    <div class="h-64">
                        <canvas ref="growthChart"></canvas>
                    </div>
                </div>
            </div>

            <div class="bg-white rounded-lg shadow-sm border border-gray-200">
                <div class="px-6 py-4 border-b border-gray-200">
                    <div class="flex items-center justify-between">
                        <h3 class="text-lg font-semibold text-gray-900">{{ t('admin.subscriptions_list') }}</h3>
                        <div class="flex gap-2">
                            <button
                                v-for="filter in filters"
                                :key="filter.value"
                                @click="changeFilter(filter.value)"
                                :class="[
                                    'px-3 py-1.5 rounded-lg text-sm font-medium transition',
                                    currentFilter === filter.value
                                        ? 'bg-orange-600 text-white'
                                        : 'bg-gray-100 text-gray-700 hover:bg-gray-200'
                                ]"
                            >
                                {{ filter.label }}
                            </button>
                        </div>
                    </div>
                </div>

                <div class="overflow-x-auto">
                    <table class="w-full">
                        <thead class="bg-gray-50">
                            <tr>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">{{ t('admin.user') }}</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">{{ t('subscription.plan') }}</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">{{ t('subscription.status') }}</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">{{ t('admin.subscriptions_start_date') }}</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">{{ t('admin.subscriptions_end_date') }}</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">{{ t('common.actions') }}</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-200">
                            <tr v-for="sub in subscriptions.data" :key="sub.id" class="hover:bg-gray-50">
                                <td class="px-6 py-4">
                                    <Link :href="`/admin/users/${sub.user.id}`" class="text-orange-600 hover:text-orange-700 font-medium">
                                        {{ sub.user.name }}
                                    </Link>
                                    <div class="text-sm text-gray-500">{{ sub.user.email }}</div>
                                </td>
                                <td class="px-6 py-4">
                                    <span class="text-sm">{{ getPlanName(sub.stripe_price) }}</span>
                                </td>
                                <td class="px-6 py-4">
                                    <span :class="[
                                        'px-2 py-1 text-xs font-medium rounded-full',
                                        getStatusClass(sub.stripe_status)
                                    ]">
                                        {{ getStatusLabel(sub.stripe_status) }}
                                    </span>
                                </td>
                                <td class="px-6 py-4 text-sm text-gray-500">
                                    {{ formatDate(sub.created_at) }}
                                </td>
                                <td class="px-6 py-4 text-sm text-gray-500">
                                    {{ sub.ends_at ? formatDate(sub.ends_at) : '-' }}
                                </td>
                                <td class="px-6 py-4">
                                    <Link
                                        :href="`/admin/subscriptions/${sub.user.id}`"
                                        class="text-orange-600 hover:text-orange-700 text-sm font-medium"
                                    >
                                        {{ t('common.details') }}
                                    </Link>
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>

                <div v-if="subscriptions.data.length === 0" class="px-6 py-12 text-center text-gray-500">
                    {{ t('admin.subscriptions_no_subscriptions') }}
                </div>

                <div v-if="subscriptions.links.length > 3" class="px-6 py-4 border-t border-gray-200">
                    <div class="flex justify-center gap-2">
                        <Link
                            v-for="(link, index) in subscriptions.links"
                            :key="index"
                            :href="link.url"
                            :class="[
                                'px-3 py-1.5 rounded-lg text-sm transition',
                                link.active
                                    ? 'bg-orange-600 text-white'
                                    : link.url
                                        ? 'bg-gray-100 text-gray-700 hover:bg-gray-200'
                                        : 'bg-gray-50 text-gray-400 cursor-not-allowed'
                            ]"
                            :disabled="!link.url"
                            v-html="link.label"
                        />
                    </div>
                </div>
            </div>
        </div>
    </AdminLayout>
</template>

<script setup>
import { onMounted, ref } from 'vue'
import { Link, router } from '@inertiajs/vue3'
import { useI18n } from 'vue-i18n'
import AdminLayout from '@/Layouts/AdminLayout.vue'
import StatCard from '@/Components/Admin/StatCard.vue'
import Chart from 'chart.js/auto'

const { t } = useI18n()

const props = defineProps({
    subscriptions: Object,
    stats: Object,
    revenueChart: Array,
    growthChart: Array,
    currentFilter: String
})

const revenueChart = ref(null)
const growthChart = ref(null)

const filters = [
    { value: 'all', label: t('admin.subscriptions_filter_all') },
    { value: 'active', label: t('admin.subscriptions_filter_active') },
    { value: 'trialing', label: t('admin.subscriptions_filter_trialing') },
    { value: 'canceled', label: t('admin.subscriptions_filter_canceled') },
    { value: 'expiring', label: t('admin.subscriptions_filter_expiring') }
]

const formatCurrency = (value) => {
    return new Intl.NumberFormat('fr-FR', {
        style: 'currency',
        currency: 'EUR'
    }).format(value)
}

const formatDate = (date) => {
    return new Date(date).toLocaleDateString('fr-FR')
}

const getPlanName = (stripePrice) => {
    if (stripePrice.includes('monthly')) return t('subscription.plans.monthly')
    if (stripePrice.includes('yearly')) return t('subscription.plans.yearly')
    return t('common.premium')
}

const getStatusLabel = (status) => {
    const labels = {
        active: t('subscription.status_active'),
        trialing: t('admin.subscriptions_status_trialing'),
        canceled: t('subscription.status_canceled'),
        past_due: t('subscription.status_past_due'),
        incomplete: t('admin.subscriptions_status_incomplete')
    }
    return labels[status] || status
}

const getStatusClass = (status) => {
    const classes = {
        active: 'bg-green-100 text-green-800',
        trialing: 'bg-blue-100 text-blue-800',
        canceled: 'bg-red-100 text-red-800',
        past_due: 'bg-orange-100 text-orange-800',
        incomplete: 'bg-gray-100 text-gray-800'
    }
    return classes[status] || 'bg-gray-100 text-gray-800'
}

const changeFilter = (filter) => {
    router.get('/admin/subscriptions', { filter }, { preserveState: true })
}

onMounted(() => {
    if (revenueChart.value) {
        new Chart(revenueChart.value, {
            type: 'line',
            data: {
                labels: props.revenueChart.map(d => d.month),
                datasets: [{
                    label: t('admin.subscriptions_revenue_label'),
                    data: props.revenueChart.map(d => d.revenue),
                    borderColor: '#ea580c',
                    backgroundColor: 'rgba(234, 88, 12, 0.1)',
                    tension: 0.4,
                    fill: true
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: {
                        display: false
                    }
                },
                scales: {
                    y: {
                        beginAtZero: true,
                        ticks: {
                            callback: (value) => value + '€'
                        }
                    }
                }
            }
        })
    }

    if (growthChart.value) {
        new Chart(growthChart.value, {
            type: 'bar',
            data: {
                labels: props.growthChart.map(d => d.month),
                datasets: [{
                    label: t('admin.subscriptions_subscribers_label'),
                    data: props.growthChart.map(d => d.subscribers),
                    backgroundColor: '#10b981',
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: {
                        display: false
                    }
                },
                scales: {
                    y: {
                        beginAtZero: true
                    }
                }
            }
        })
    }
})
</script>
