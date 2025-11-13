<script setup>
import { ref } from 'vue';
import { useForm } from '@inertiajs/vue3';
import { useI18n } from 'vue-i18n';
import AppLayout from '@/Layouts/AppLayout.vue';
import PrimaryButton from '@/Components/PrimaryButton.vue';
import SecondaryButton from '@/Components/SecondaryButton.vue';
import DangerButton from '@/Components/DangerButton.vue';
import ConfirmationModal from '@/Components/ConfirmationModal.vue';

const { t } = useI18n();

const props = defineProps({
    subscription: Object,
});

const showCancelModal = ref(false);
const showResumeModal = ref(false);

const cancelForm = useForm({});
const resumeForm = useForm({});

const cancelSubscription = () => {
    cancelForm.post(route('subscription.cancel'), {
        preserveScroll: true,
        onSuccess: () => {
            showCancelModal.value = false;
        },
    });
};

const resumeSubscription = () => {
    resumeForm.post(route('subscription.resume'), {
        preserveScroll: true,
        onSuccess: () => {
            showResumeModal.value = false;
        },
    });
};

const formatDate = (date) => {
    if (!date) return '';
    return new Date(date).toLocaleDateString('fr-FR', {
        year: 'numeric',
        month: 'long',
        day: 'numeric',
    });
};
</script>

<template>
    <AppLayout :title="t('subscription.manage_title')">
        <template #header>
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                {{ t('subscription.manage_title') }}
            </h2>
        </template>

        <div class="py-12">
            <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
                <div class="bg-white shadow rounded-lg">
                    <!-- Subscription Info -->
                    <div class="p-6 border-b border-gray-200">
                        <h3 class="text-lg font-semibold text-gray-900 mb-4">
                            {{ t('subscription.subscription_details') }}
                        </h3>

                        <div class="grid md:grid-cols-2 gap-6">
                            <div>
                                <p class="text-sm text-gray-600 mb-1">{{ t('subscription.plan') }}</p>
                                <p class="text-lg font-semibold text-gray-900">
                                    {{ t(`subscription.plans.${subscription.plan}`) }}
                                </p>
                            </div>

                            <div>
                                <p class="text-sm text-gray-600 mb-1">{{ t('subscription.status') }}</p>
                                <span :class="[
                                    'inline-flex px-3 py-1 rounded-full text-sm font-semibold',
                                    subscription.status === 'active' ? 'bg-green-100 text-green-800' : 'bg-yellow-100 text-yellow-800'
                                ]">
                                    {{ t(`subscription.status_${subscription.status}`) }}
                                </span>
                            </div>

                            <div v-if="subscription.ends_at && subscription.on_grace_period">
                                <p class="text-sm text-gray-600 mb-1">{{ t('subscription.ends_at') }}</p>
                                <p class="text-lg font-semibold text-yellow-700">
                                    {{ formatDate(subscription.ends_at) }}
                                </p>
                                <p class="text-sm text-yellow-600 mt-1">
                                    {{ t('subscription.grace_period_info') }}
                                </p>
                            </div>
                        </div>
                    </div>

                    <!-- Actions -->
                    <div class="p-6">
                        <h3 class="text-lg font-semibold text-gray-900 mb-4">
                            {{ t('subscription.actions') }}
                        </h3>

                        <div class="space-y-4">
                            <!-- Resume Subscription -->
                            <div v-if="subscription.on_grace_period" class="flex items-center justify-between bg-yellow-50 border border-yellow-200 rounded-lg p-4">
                                <div>
                                    <p class="font-semibold text-yellow-900">
                                        {{ t('subscription.cancelled_info') }}
                                    </p>
                                    <p class="text-sm text-yellow-700 mt-1">
                                        {{ t('subscription.reactivate_info') }}
                                    </p>
                                </div>
                                <PrimaryButton @click="showResumeModal = true">
                                    {{ t('subscription.resume') }}
                                </PrimaryButton>
                            </div>

                            <!-- Cancel Subscription -->
                            <div v-else class="flex items-center justify-between bg-gray-50 border border-gray-200 rounded-lg p-4">
                                <div>
                                    <p class="font-semibold text-gray-900">
                                        {{ t('subscription.cancel_title') }}
                                    </p>
                                    <p class="text-sm text-gray-600 mt-1">
                                        {{ t('subscription.cancel_info') }}
                                    </p>
                                </div>
                                <DangerButton @click="showCancelModal = true">
                                    {{ t('subscription.cancel') }}
                                </DangerButton>
                            </div>

                            <!-- Update Payment Method -->
                            <div class="flex items-center justify-between bg-gray-50 border border-gray-200 rounded-lg p-4">
                                <div>
                                    <p class="font-semibold text-gray-900">
                                        {{ t('subscription.payment_method') }}
                                    </p>
                                    <p class="text-sm text-gray-600 mt-1">
                                        {{ t('subscription.update_payment_info') }}
                                    </p>
                                </div>
                                <PrimaryButton @click="$inertia.visit(route('subscription.payment-method'))">
                                    {{ t('subscription.update_payment') }}
                                </PrimaryButton>
                            </div>
                        </div>
                    </div>

                    <!-- Billing History -->
                    <div class="p-6 border-t border-gray-200">
                        <h3 class="text-lg font-semibold text-gray-900 mb-4">
                            {{ t('subscription.billing_history') }}
                        </h3>
                        <p class="text-gray-600">
                            {{ t('subscription.view_invoices_stripe') }}
                        </p>
                    </div>
                </div>
            </div>
        </div>

        <!-- Cancel Confirmation Modal -->
        <ConfirmationModal :show="showCancelModal" @close="showCancelModal = false">
            <template #title>
                {{ t('subscription.confirm_cancel') }}
            </template>

            <template #content>
                {{ t('subscription.cancel_confirmation_message') }}
            </template>

            <template #footer>
                <SecondaryButton @click="showCancelModal = false">
                    {{ t('common.cancel') }}
                </SecondaryButton>

                <DangerButton
                    class="ms-3"
                    :class="{ 'opacity-25': cancelForm.processing }"
                    :disabled="cancelForm.processing"
                    @click="cancelSubscription"
                >
                    {{ t('subscription.confirm_cancel_button') }}
                </DangerButton>
            </template>
        </ConfirmationModal>

        <!-- Resume Confirmation Modal -->
        <ConfirmationModal :show="showResumeModal" @close="showResumeModal = false">
            <template #title>
                {{ t('subscription.confirm_resume') }}
            </template>

            <template #content>
                {{ t('subscription.resume_confirmation_message') }}
            </template>

            <template #footer>
                <SecondaryButton @click="showResumeModal = false">
                    {{ t('common.cancel') }}
                </SecondaryButton>

                <PrimaryButton
                    class="ms-3"
                    :class="{ 'opacity-25': resumeForm.processing }"
                    :disabled="resumeForm.processing"
                    @click="resumeSubscription"
                >
                    {{ t('subscription.confirm_resume_button') }}
                </PrimaryButton>
            </template>
        </ConfirmationModal>
    </AppLayout>
</template>
