<template>
    <AdminLayout>
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
            <div class="mb-8">
                <h1 class="text-3xl font-bold text-gray-900">Gestion des Abonnements</h1>
                <p class="mt-2 text-gray-600">Tableau de bord et statistiques des abonnements</p>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 mb-8">
                <StatCard
                    title="MRR"
                    :value="formatCurrency(stats.mrr)"
                    icon="💰"
                    description="Revenu Mensuel Récurrent"
                />
                <StatCard
                    title="ARR"
                    :value="formatCurrency(stats.arr)"
                    icon="📈"
                    description="Revenu Annuel Récurrent"
                />
                <StatCard
                    title="Abonnés Actifs"
                    :value="stats.active_count"
                    icon="👥"
                    :description="`${stats.trial_count} en essai`"
                />
                <StatCard
                    title="Churn Rate"
                    :value="stats.churn_rate + '%'"
                    icon="📉"
                    description="Taux de désabonnement (30j)"
                    :alert="stats.churn_rate > 5"
                />
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 mb-8">
                <StatCard
                    title="Nouveaux (30j)"
                    :value="stats.new_subscribers"
                    icon="🆕"
                    description="Nouveaux abonnés"
                />
                <StatCard
                    title="LTV Moyenne"
                    :value="formatCurrency(stats.ltv)"
                    icon="💎"
                    description="Lifetime Value"
                />
                <StatCard
                    title="Conversion"
                    :value="stats.conversion_rate + '%'"
                    icon="🎯"
                    description="Trial → Payant"
                />
                <StatCard
                    title="Distribution"
                    :value="`${stats.plan_distribution.monthly}M / ${stats.plan_distribution.yearly}A`"
                    icon="📊"
                    description="Mensuel / Annuel"
                />
            </div>

            <div class="grid grid-cols-1 lg:grid-cols-2 gap-6 mb-8">
                <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6">
                    <h3 class="text-lg font-semibold text-gray-900 mb-4">Revenus (12 mois)</h3>
                    <div class="h-64">
                        <canvas ref="revenueChart"></canvas>
                    </div>
                </div>

                <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6">
                    <h3 class="text-lg font-semibold text-gray-900 mb-4">Croissance Abonnés</h3>
                    <div class="h-64">
                        <canvas ref="growthChart"></canvas>
                    </div>
                </div>
            </div>

            <div class="bg-white rounded-lg shadow-sm border border-gray-200">
                <div class="px-6 py-4 border-b border-gray-200">
                    <div class="flex items-center justify-between">
                        <h3 class="text-lg font-semibold text-gray-900">Liste des Abonnements</h3>
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
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Utilisateur</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Plan</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Statut</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Début</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Fin</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Actions</th>
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
                                        Détails
                                    </Link>
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>

                <div v-if="subscriptions.data.length === 0" class="px-6 py-12 text-center text-gray-500">
                    Aucun abonnement trouvé
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
import AdminLayout from '@/Layouts/AdminLayout.vue'
import StatCard from '@/Components/Admin/StatCard.vue'
import Chart from 'chart.js/auto'

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
    { value: 'all', label: 'Tous' },
    { value: 'active', label: 'Actifs' },
    { value: 'trialing', label: 'Essai' },
    { value: 'canceled', label: 'Annulés' },
    { value: 'expiring', label: 'Expirant' }
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
    if (stripePrice.includes('monthly')) return 'Mensuel'
    if (stripePrice.includes('yearly')) return 'Annuel'
    return 'Premium'
}

const getStatusLabel = (status) => {
    const labels = {
        active: 'Actif',
        trialing: 'Essai',
        canceled: 'Annulé',
        past_due: 'Impayé',
        incomplete: 'Incomplet'
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
                    label: 'Revenus (€)',
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
                    label: 'Abonnés',
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
