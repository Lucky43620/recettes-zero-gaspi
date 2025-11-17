<script setup>
import { ref } from 'vue';
import { useForm, usePage } from '@inertiajs/vue3';
import { useI18n } from 'vue-i18n';
import AppLayout from '@/Layouts/AppLayout.vue';
import PrimaryButton from '@/Components/PrimaryButton.vue';
import SecondaryButton from '@/Components/SecondaryButton.vue';
import DangerButton from '@/Components/DangerButton.vue';
import ConfirmationModal from '@/Components/ConfirmationModal.vue';

const { t } = useI18n();
const page = usePage();

const props = defineProps({
    subscription: Object,
});

const showSuccessAlert = ref(!!page.props.flash?.success);
const showErrorAlert = ref(!!page.props.flash?.error);

const showCancelModal = ref(false);
const showResumeModal = ref(false);

const cancelForm = useForm({});
const resumeForm = useForm({});

const cancelSubscription = () => {
    if (cancelForm.processing) return;

    cancelForm.post(route('subscription.cancel'), {
        preserveState: false,
        preserveScroll: false,
        onSuccess: () => {
            showCancelModal.value = false;
            window.location.reload();
        },
        onError: () => {
            showCancelModal.value = false;
        },
    });
};

const resumeSubscription = () => {
    if (resumeForm.processing) return;

    resumeForm.post(route('subscription.resume'), {
        preserveState: false,
        preserveScroll: false,
        onSuccess: () => {
            showResumeModal.value = false;
            window.location.reload();
        },
        onError: () => {
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
                <div v-if="showSuccessAlert && $page.props.flash?.success" class="mb-6 bg-green-50 border border-green-200 rounded-lg p-4 flex items-start justify-between">
                    <div class="flex items-start gap-3">
                        <svg class="w-5 h-5 text-green-600 flex-shrink-0 mt-0.5" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/>
                        </svg>
                        <div>
                            <p class="text-sm font-medium text-green-900">{{ $page.props.flash.success }}</p>
                        </div>
                    </div>
                    <button @click="showSuccessAlert = false" class="text-green-600 hover:text-green-800">
                        <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd" d="M4.293 4.293a1 1 0 011.414 0L10 8.586l4.293-4.293a1 1 0 111.414 1.414L11.414 10l4.293 4.293a1 1 0 01-1.414 1.414L10 11.414l-4.293 4.293a1 1 0 01-1.414-1.414L8.586 10 4.293 5.707a1 1 0 010-1.414z" clip-rule="evenodd"/>
                        </svg>
                    </button>
                </div>

                <div v-if="showErrorAlert && $page.props.flash?.error" class="mb-6 bg-red-50 border border-red-200 rounded-lg p-4 flex items-start justify-between">
                    <div class="flex items-start gap-3">
                        <svg class="w-5 h-5 text-red-600 flex-shrink-0 mt-0.5" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z" clip-rule="evenodd"/>
                        </svg>
                        <div>
                            <p class="text-sm font-medium text-red-900">{{ $page.props.flash.error }}</p>
                        </div>
                    </div>
                    <button @click="showErrorAlert = false" class="text-red-600 hover:text-red-800">
                        <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd" d="M4.293 4.293a1 1 0 011.414 0L10 8.586l4.293-4.293a1 1 0 111.414 1.414L11.414 10l4.293 4.293a1 1 0 01-1.414 1.414L10 11.414l-4.293 4.293a1 1 0 01-1.414-1.414L8.586 10 4.293 5.707a1 1 0 010-1.414z" clip-rule="evenodd"/>
                        </svg>
                    </button>
                </div>

                <div class="bg-white shadow rounded-lg">
                    <!-- Subscription Info -->
                    <div class="p-4 md:p-6 border-b border-gray-200">
                        <h3 class="text-base md:text-lg font-semibold text-gray-900 mb-4">
                            {{ t('subscription.subscription_details') }}
                        </h3>

                        <div class="grid md:grid-cols-2 gap-4 md:gap-6 mb-4 md:mb-6">
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

                        <div class="bg-gradient-to-br from-green-50 to-emerald-50 rounded-lg p-4 md:p-6 border border-green-200">
                            <h4 class="text-base md:text-md font-semibold text-gray-900 mb-3 flex items-center gap-2">
                                <svg class="w-5 h-5 text-green-600 flex-shrink-0" fill="currentColor" viewBox="0 0 20 20">
                                    <path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"/>
                                </svg>
                                {{ t('subscription.premium_benefits') }}
                            </h4>
                            <ul class="grid md:grid-cols-2 gap-2 md:gap-3">
                                <li class="flex items-start gap-2">
                                    <svg class="w-5 h-5 text-green-600 flex-shrink-0 mt-0.5" fill="currentColor" viewBox="0 0 20 20">
                                        <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"/>
                                    </svg>
                                    <span class="text-sm text-gray-700">{{ t('subscription.features.ai_menu_generator') }}</span>
                                </li>
                                <li class="flex items-start gap-2">
                                    <svg class="w-5 h-5 text-green-600 flex-shrink-0 mt-0.5" fill="currentColor" viewBox="0 0 20 20">
                                        <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"/>
                                    </svg>
                                    <span class="text-sm text-gray-700">{{ t('subscription.features.advanced_pantry') }}</span>
                                </li>
                                <li class="flex items-start gap-2">
                                    <svg class="w-5 h-5 text-green-600 flex-shrink-0 mt-0.5" fill="currentColor" viewBox="0 0 20 20">
                                        <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"/>
                                    </svg>
                                    <span class="text-sm text-gray-700">{{ t('subscription.features.expiry_alerts') }}</span>
                                </li>
                                <li class="flex items-start gap-2">
                                    <svg class="w-5 h-5 text-green-600 flex-shrink-0 mt-0.5" fill="currentColor" viewBox="0 0 20 20">
                                        <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"/>
                                    </svg>
                                    <span class="text-sm text-gray-700">{{ t('subscription.features.recipe_suggestions') }}</span>
                                </li>
                                <li class="flex items-start gap-2">
                                    <svg class="w-5 h-5 text-green-600 flex-shrink-0 mt-0.5" fill="currentColor" viewBox="0 0 20 20">
                                        <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"/>
                                    </svg>
                                    <span class="text-sm text-gray-700">{{ t('subscription.features.advanced_statistics') }}</span>
                                </li>
                                <li class="flex items-start gap-2">
                                    <svg class="w-5 h-5 text-green-600 flex-shrink-0 mt-0.5" fill="currentColor" viewBox="0 0 20 20">
                                        <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"/>
                                    </svg>
                                    <span class="text-sm text-gray-700">{{ t('subscription.features.no_ads') }}</span>
                                </li>
                            </ul>
                        </div>
                    </div>

                    <!-- Actions -->
                    <div class="p-4 md:p-6">
                        <h3 class="text-base md:text-lg font-semibold text-gray-900 mb-4">
                            {{ t('subscription.actions') }}
                        </h3>

                        <div class="space-y-4">
                            <!-- Resume Subscription -->
                            <div v-if="subscription.on_grace_period" class="flex flex-col sm:flex-row items-start sm:items-center justify-between gap-3 sm:gap-4 bg-yellow-50 border border-yellow-200 rounded-lg p-4">
                                <div class="flex-1">
                                    <p class="font-semibold text-yellow-900 text-sm md:text-base">
                                        {{ t('subscription.cancelled_info') }}
                                    </p>
                                    <p class="text-xs md:text-sm text-yellow-700 mt-1">
                                        {{ t('subscription.reactivate_info') }}
                                    </p>
                                </div>
                                <PrimaryButton @click="showResumeModal = true" class="w-full sm:w-auto whitespace-nowrap">
                                    {{ t('subscription.resume') }}
                                </PrimaryButton>
                            </div>

                            <!-- Cancel Subscription -->
                            <div v-else class="flex flex-col sm:flex-row items-start sm:items-center justify-between gap-3 sm:gap-4 bg-gray-50 border border-gray-200 rounded-lg p-4">
                                <div class="flex-1">
                                    <p class="font-semibold text-gray-900 text-sm md:text-base">
                                        {{ t('subscription.cancel_title') }}
                                    </p>
                                    <p class="text-xs md:text-sm text-gray-600 mt-1">
                                        {{ t('subscription.cancel_info') }}
                                    </p>
                                </div>
                                <DangerButton @click="showCancelModal = true" class="w-full sm:w-auto whitespace-nowrap">
                                    {{ t('subscription.cancel') }}
                                </DangerButton>
                            </div>

                            <!-- Update Payment Method -->
                            <div class="flex flex-col sm:flex-row items-start sm:items-center justify-between gap-3 sm:gap-4 bg-gray-50 border border-gray-200 rounded-lg p-4">
                                <div class="flex-1">
                                    <p class="font-semibold text-gray-900 text-sm md:text-base">
                                        {{ t('subscription.payment_method') }}
                                    </p>
                                    <p class="text-xs md:text-sm text-gray-600 mt-1">
                                        {{ t('subscription.update_payment_info') }}
                                    </p>
                                </div>
                                <PrimaryButton @click="$inertia.visit(route('subscription.payment-method'))" class="w-full sm:w-auto whitespace-nowrap">
                                    {{ t('subscription.update_payment') }}
                                </PrimaryButton>
                            </div>
                        </div>
                    </div>

                    <!-- Billing History -->
                    <div class="p-4 md:p-6 border-t border-gray-200">
                        <h3 class="text-base md:text-lg font-semibold text-gray-900 mb-4">
                            {{ t('subscription.billing_history') }}
                        </h3>
                        <p class="text-sm md:text-base text-gray-600">
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
