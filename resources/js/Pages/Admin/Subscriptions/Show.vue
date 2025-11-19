<template>
    <AdminLayout>
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
            <div class="mb-8">
                <Link href="/admin/subscriptions" class="text-orange-600 hover:text-orange-700 text-sm font-medium mb-4 inline-flex items-center">
                    ← {{ t('admin.subscriptions_back') }}
                </Link>
                <h1 class="text-3xl font-bold text-gray-900 mt-4">{{ t('admin.subscriptions_user_subscription', { name: user.name }) }}</h1>
                <p class="mt-2 text-gray-600">{{ user.email }}</p>
            </div>

            <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
                <div class="lg:col-span-2 space-y-6">
                    <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6">
                        <h2 class="text-lg font-semibold text-gray-900 mb-4">{{ t('admin.subscriptions_current_plan') }}</h2>

                        <div class="space-y-4">
                            <div class="flex items-center justify-between py-3 border-b">
                                <span class="text-gray-600">{{ t('subscription.status') }}</span>
                                <span :class="[
                                    'px-3 py-1 rounded-full text-sm font-medium',
                                    subscription.is_premium
                                        ? 'bg-green-100 text-green-800'
                                        : 'bg-gray-100 text-gray-800'
                                ]">
                                    {{ subscription.is_premium ? t('common.premium') : t('common.free') }}
                                </span>
                            </div>

                            <div class="flex items-center justify-between py-3 border-b">
                                <span class="text-gray-600">{{ t('subscription.plan') }}</span>
                                <span class="font-medium">{{ subscription.current_plan }}</span>
                            </div>

                            <div v-if="subscription.subscriptions.length > 0">
                                <div v-for="sub in subscription.subscriptions" :key="sub.id" class="mt-4 p-4 bg-gray-50 rounded-lg">
                                    <div class="space-y-2">
                                        <div class="flex justify-between">
                                            <span class="text-sm text-gray-600">{{ t('admin.subscriptions_stripe_id') }}</span>
                                            <span class="text-sm font-mono">{{ sub.stripe_id }}</span>
                                        </div>
                                        <div class="flex justify-between">
                                            <span class="text-sm text-gray-600">{{ t('subscription.status') }}</span>
                                            <span class="text-sm">{{ sub.stripe_status }}</span>
                                        </div>
                                        <div v-if="sub.created_at" class="flex justify-between">
                                            <span class="text-sm text-gray-600">{{ t('admin.subscriptions_created_on') }}</span>
                                            <span class="text-sm">{{ sub.created_at }}</span>
                                        </div>
                                        <div v-if="sub.trial_ends_at" class="flex justify-between">
                                            <span class="text-sm text-gray-600">{{ t('admin.subscriptions_trial_end') }}</span>
                                            <span class="text-sm">{{ sub.trial_ends_at }}</span>
                                        </div>
                                        <div v-if="sub.ends_at" class="flex justify-between">
                                            <span class="text-sm text-gray-600">{{ t('admin.subscriptions_scheduled_end') }}</span>
                                            <span class="text-sm text-red-600">{{ sub.ends_at }}</span>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <div v-else class="text-center py-8 text-gray-500">
                                {{ t('admin.subscriptions_no_active') }}
                            </div>
                        </div>
                    </div>

                    <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6">
                        <h2 class="text-lg font-semibold text-gray-900 mb-4">{{ t('admin.subscriptions_invoices') }}</h2>

                        <div v-if="invoices && invoices.length > 0" class="space-y-3">
                            <div
                                v-for="invoice in invoices"
                                :key="invoice.id"
                                class="flex items-center justify-between p-4 bg-gray-50 rounded-lg"
                            >
                                <div>
                                    <div class="font-medium">{{ invoice.total_formatted }}</div>
                                    <div class="text-sm text-gray-500">{{ invoice.date_formatted }}</div>
                                </div>
                                <div class="flex items-center gap-3">
                                    <span :class="[
                                        'px-2 py-1 text-xs font-medium rounded-full',
                                        invoice.status === 'paid'
                                            ? 'bg-green-100 text-green-800'
                                            : 'bg-red-100 text-red-800'
                                    ]">
                                        {{ invoice.status === 'paid' ? t('subscription.invoice_status.paid') : t('admin.subscriptions_invoice_unpaid') }}
                                    </span>
                                </div>
                            </div>
                        </div>

                        <div v-else class="text-center py-8 text-gray-500">
                            {{ t('subscription.no_invoices') }}
                        </div>
                    </div>
                </div>

                <div class="space-y-6">
                    <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6">
                        <h2 class="text-lg font-semibold text-gray-900 mb-4">{{ t('admin.subscriptions_admin_actions') }}</h2>

                        <div class="space-y-3">
                            <button
                                v-if="subscription.is_premium && !hasEndDate"
                                @click="showCancelModal = true"
                                class="w-full px-4 py-2 bg-red-600 text-white rounded-lg hover:bg-red-700 transition"
                            >
                                {{ t('subscription.cancel') }}
                            </button>

                            <button
                                v-if="hasEndDate"
                                @click="resumeSubscription"
                                class="w-full px-4 py-2 bg-green-600 text-white rounded-lg hover:bg-green-700 transition"
                            >
                                {{ t('subscription.resume') }}
                            </button>

                            <Link
                                :href="`/admin/users/${user.id}`"
                                class="w-full block text-center px-4 py-2 bg-gray-100 text-gray-700 rounded-lg hover:bg-gray-200 transition"
                            >
                                {{ t('admin.subscriptions_view_full_profile') }}
                            </Link>
                        </div>
                    </div>

                    <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6">
                        <h2 class="text-lg font-semibold text-gray-900 mb-4">{{ t('admin.subscriptions_info') }}</h2>

                        <div class="space-y-3 text-sm">
                            <div>
                                <span class="text-gray-600">{{ t('admin.subscriptions_registration') }}</span>
                                <div class="font-medium">{{ formatDate(user.created_at) }}</div>
                            </div>

                            <div v-if="user.email_verified_at">
                                <span class="text-gray-600">{{ t('admin.subscriptions_email_verified') }}</span>
                                <div class="font-medium">{{ formatDate(user.email_verified_at) }}</div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Cancel Confirmation Modal -->
        <DialogModal variant="danger" :show="showCancelModal" @close="showCancelModal = false">
            <template #title>
                {{ t('admin.subscriptions_confirm_cancel') }}
            </template>

            <template #content>
                {{ t('admin.subscriptions_confirm_cancel_message', { name: user.name }) }}
            </template>

            <template #footer>
                <button
                    @click="showCancelModal = false"
                    class="px-4 py-2 bg-gray-100 text-gray-700 rounded-lg hover:bg-gray-200 transition"
                >
                    {{ t('common.cancel') }}
                </button>

                <button
                    @click="cancelSubscription"
                    class="ms-3 px-4 py-2 bg-red-600 text-white rounded-lg hover:bg-red-700 transition"
                >
                    {{ t('admin.subscriptions_confirm_cancel_button') }}
                </button>
            </template>
        </DialogModal>
    </AdminLayout>
</template>

<script setup>
import { ref, computed } from 'vue'
import { Link, router } from '@inertiajs/vue3'
import { useI18n } from 'vue-i18n'
import AdminLayout from '@/Layouts/AdminLayout.vue'
import DialogModal from '@/Components/DialogModal.vue'

const { t } = useI18n()

const props = defineProps({
    user: Object,
    subscription: Object,
    invoices: Array
})

const showCancelModal = ref(false)

const hasEndDate = computed(() => {
    if (!props.subscription.subscriptions || !Array.isArray(props.subscription.subscriptions)) {
        return false
    }
    return props.subscription.subscriptions.some(sub => sub.ends_at)
})

const cancelSubscription = () => {
    router.post(`/admin/subscriptions/${props.user.id}/cancel`, {}, {
        preserveState: false,
        onSuccess: () => {
            showCancelModal.value = false
            router.reload()
        }
    })
}

const resumeSubscription = () => {
    router.post(`/admin/subscriptions/${props.user.id}/resume`, {}, {
        preserveState: false,
        onSuccess: () => {
            router.reload()
        }
    })
}
</script>
