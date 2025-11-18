<template>
    <AdminLayout>
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
            <div class="mb-8">
                <Link href="/admin/subscriptions" class="text-orange-600 hover:text-orange-700 text-sm font-medium mb-4 inline-flex items-center">
                    ← Retour aux abonnements
                </Link>
                <h1 class="text-3xl font-bold text-gray-900 mt-4">Abonnement de {{ user.name }}</h1>
                <p class="mt-2 text-gray-600">{{ user.email }}</p>
            </div>

            <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
                <div class="lg:col-span-2 space-y-6">
                    <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6">
                        <h2 class="text-lg font-semibold text-gray-900 mb-4">Plan Actuel</h2>

                        <div class="space-y-4">
                            <div class="flex items-center justify-between py-3 border-b">
                                <span class="text-gray-600">Statut</span>
                                <span :class="[
                                    'px-3 py-1 rounded-full text-sm font-medium',
                                    subscription.is_premium
                                        ? 'bg-green-100 text-green-800'
                                        : 'bg-gray-100 text-gray-800'
                                ]">
                                    {{ subscription.is_premium ? 'Premium' : 'Gratuit' }}
                                </span>
                            </div>

                            <div class="flex items-center justify-between py-3 border-b">
                                <span class="text-gray-600">Plan</span>
                                <span class="font-medium">{{ subscription.current_plan }}</span>
                            </div>

                            <div v-if="subscription.subscriptions.length > 0">
                                <div v-for="sub in subscription.subscriptions" :key="sub.id" class="mt-4 p-4 bg-gray-50 rounded-lg">
                                    <div class="space-y-2">
                                        <div class="flex justify-between">
                                            <span class="text-sm text-gray-600">Stripe ID</span>
                                            <span class="text-sm font-mono">{{ sub.stripe_id }}</span>
                                        </div>
                                        <div class="flex justify-between">
                                            <span class="text-sm text-gray-600">Statut</span>
                                            <span class="text-sm">{{ sub.stripe_status }}</span>
                                        </div>
                                        <div class="flex justify-between">
                                            <span class="text-sm text-gray-600">Créé le</span>
                                            <span class="text-sm">{{ formatDate(sub.created_at) }}</span>
                                        </div>
                                        <div v-if="sub.trial_ends_at" class="flex justify-between">
                                            <span class="text-sm text-gray-600">Fin essai</span>
                                            <span class="text-sm">{{ formatDate(sub.trial_ends_at) }}</span>
                                        </div>
                                        <div v-if="sub.ends_at" class="flex justify-between">
                                            <span class="text-sm text-gray-600">Fin prévue</span>
                                            <span class="text-sm text-red-600">{{ formatDate(sub.ends_at) }}</span>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <div v-else class="text-center py-8 text-gray-500">
                                Aucun abonnement actif
                            </div>
                        </div>
                    </div>

                    <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6">
                        <h2 class="text-lg font-semibold text-gray-900 mb-4">Factures</h2>

                        <div v-if="invoices.length > 0" class="space-y-3">
                            <div
                                v-for="invoice in invoices"
                                :key="invoice.id"
                                class="flex items-center justify-between p-4 bg-gray-50 rounded-lg"
                            >
                                <div>
                                    <div class="font-medium">{{ formatCurrency(invoice.total) }}</div>
                                    <div class="text-sm text-gray-500">{{ formatDate(invoice.date) }}</div>
                                </div>
                                <div class="flex items-center gap-3">
                                    <span :class="[
                                        'px-2 py-1 text-xs font-medium rounded-full',
                                        invoice.status === 'paid'
                                            ? 'bg-green-100 text-green-800'
                                            : 'bg-red-100 text-red-800'
                                    ]">
                                        {{ invoice.status === 'paid' ? 'Payée' : 'Non payée' }}
                                    </span>
                                </div>
                            </div>
                        </div>

                        <div v-else class="text-center py-8 text-gray-500">
                            Aucune facture
                        </div>
                    </div>
                </div>

                <div class="space-y-6">
                    <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6">
                        <h2 class="text-lg font-semibold text-gray-900 mb-4">Actions Admin</h2>

                        <div class="space-y-3">
                            <button
                                v-if="subscription.is_premium && !hasEndDate"
                                @click="cancelSubscription"
                                class="w-full px-4 py-2 bg-red-600 text-white rounded-lg hover:bg-red-700 transition"
                            >
                                Annuler l'abonnement
                            </button>

                            <button
                                v-if="hasEndDate"
                                @click="resumeSubscription"
                                class="w-full px-4 py-2 bg-green-600 text-white rounded-lg hover:bg-green-700 transition"
                            >
                                Réactiver l'abonnement
                            </button>

                            <Link
                                :href="`/admin/users/${user.id}`"
                                class="w-full block text-center px-4 py-2 bg-gray-100 text-gray-700 rounded-lg hover:bg-gray-200 transition"
                            >
                                Voir le profil complet
                            </Link>
                        </div>
                    </div>

                    <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6">
                        <h2 class="text-lg font-semibold text-gray-900 mb-4">Informations</h2>

                        <div class="space-y-3 text-sm">
                            <div>
                                <span class="text-gray-600">Inscription</span>
                                <div class="font-medium">{{ formatDate(user.created_at) }}</div>
                            </div>

                            <div v-if="user.email_verified_at">
                                <span class="text-gray-600">Email vérifié</span>
                                <div class="font-medium">{{ formatDate(user.email_verified_at) }}</div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </AdminLayout>
</template>

<script setup>
import { computed } from 'vue'
import { Link, router } from '@inertiajs/vue3'
import AdminLayout from '@/Layouts/AdminLayout.vue'

const props = defineProps({
    user: Object,
    subscription: Object,
    invoices: Array
})

const hasEndDate = computed(() => {
    return props.subscription.subscriptions.some(sub => sub.ends_at)
})

const formatCurrency = (value) => {
    return new Intl.NumberFormat('fr-FR', {
        style: 'currency',
        currency: 'EUR'
    }).format(value)
}

const formatDate = (date) => {
    if (!date) return '-'
    return new Date(date).toLocaleDateString('fr-FR')
}

const cancelSubscription = () => {
    if (confirm('Êtes-vous sûr de vouloir annuler cet abonnement ?')) {
        router.post(`/admin/subscriptions/${props.user.id}/cancel`, {}, {
            onSuccess: () => {
                router.reload()
            }
        })
    }
}

const resumeSubscription = () => {
    router.post(`/admin/subscriptions/${props.user.id}/resume`, {}, {
        onSuccess: () => {
            router.reload()
        }
    })
}
</script>
