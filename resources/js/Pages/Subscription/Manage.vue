<script setup>
import { ref } from 'vue';
import { useForm, usePage } from '@inertiajs/vue3';
import { useI18n } from 'vue-i18n';
import AppLayout from '@/Layouts/AppLayout.vue';
import PrimaryButton from '@/Components/PrimaryButton.vue';
import DialogModal from '@/Components/DialogModal.vue';

const { t } = useI18n();
const page = usePage();

const props = defineProps({
    subscription: Object,
    invoices: {
        type: Array,
        default: () => [],
    },
    plans: Object,
});

const showSuccessAlert = ref(!!page.props.flash?.success);
const showErrorAlert = ref(!!page.props.flash?.error);

const showCancelModal = ref(false);
const showResumeModal = ref(false);
const showSwapModal = ref(false);
const showCancelNowModal = ref(false);

const cancelForm = useForm({});
const resumeForm = useForm({});
const swapForm = useForm({
    plan: null,
});
const cancelNowForm = useForm({});

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

const swapPlan = (newPlan) => {
    swapForm.plan = newPlan;
    showSwapModal.value = true;
};

const confirmSwap = () => {
    if (swapForm.processing) return;

    swapForm.post(route('subscription.swap'), {
        preserveState: false,
        preserveScroll: false,
        onSuccess: () => {
            showSwapModal.value = false;
            window.location.reload();
        },
        onError: () => {
            showSwapModal.value = false;
        },
    });
};

const cancelNow = () => {
    if (cancelNowForm.processing) return;

    cancelNowForm.post(route('subscription.cancel-now'), {
        preserveState: false,
        preserveScroll: false,
        onSuccess: () => {
            showCancelNowModal.value = false;
            window.location.href = route('subscription.index');
        },
        onError: () => {
            showCancelNowModal.value = false;
        },
    });
};

const openBillingPortal = () => {
    window.location.href = route('subscription.billing-portal');
};

const formatDate = (date) => {
    if (!date) return '';
    return new Date(date).toLocaleDateString('fr-FR', {
        year: 'numeric',
        month: 'long',
        day: 'numeric',
    });
};

const formatCurrency = (value) => {
    return new Intl.NumberFormat('fr-FR', {
        style: 'currency',
        currency: 'EUR',
    }).format(value);
};

const getStatusLabel = (status) => {
    const labels = {
        paid: 'Payée',
        open: 'En attente',
        draft: 'Brouillon',
        void: 'Annulée',
        uncollectible: 'Non recouvrable',
    };
    return labels[status] || status;
};

const getStatusClass = (status) => {
    const classes = {
        paid: 'bg-green-100 text-green-800',
        open: 'bg-yellow-100 text-yellow-800',
        draft: 'bg-gray-100 text-gray-800',
        void: 'bg-red-100 text-red-800',
        uncollectible: 'bg-red-100 text-red-800',
    };
    return classes[status] || 'bg-gray-100 text-gray-800';
};

const getOtherPlan = () => {
    return props.subscription.plan === 'monthly' ? 'yearly' : 'monthly';
};

const getSwapPlanName = () => {
    const otherPlan = getOtherPlan();
    return t(`subscription.plans.${otherPlan}`);
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
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
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

                            <!-- Swap Plan -->
                            <div v-if="!subscription.on_grace_period && subscription.status === 'active'" class="flex flex-col sm:flex-row items-start sm:items-center justify-between gap-3 sm:gap-4 bg-blue-50 border border-blue-200 rounded-lg p-4">
                                <div class="flex-1">
                                    <p class="font-semibold text-blue-900 text-sm md:text-base">
                                        Changer de formule
                                    </p>
                                    <p class="text-xs md:text-sm text-blue-700 mt-1">
                                        Passez à la formule {{ getSwapPlanName() }}
                                    </p>
                                </div>
                                <PrimaryButton @click="swapPlan(getOtherPlan())" class="w-full sm:w-auto whitespace-nowrap">
                                    Changer de plan
                                </PrimaryButton>
                            </div>

                            <!-- Cancel Subscription -->
                            <div v-if="!subscription.on_grace_period" class="flex flex-col sm:flex-row items-start sm:items-center justify-between gap-3 sm:gap-4 bg-gray-50 border border-gray-200 rounded-lg p-4">
                                <div class="flex-1">
                                    <p class="font-semibold text-gray-900 text-sm md:text-base">
                                        {{ t('subscription.cancel_title') }}
                                    </p>
                                    <p class="text-xs md:text-sm text-gray-600 mt-1">
                                        {{ t('subscription.cancel_info') }}
                                    </p>
                                </div>
                                <PrimaryButton variant="danger" @click="showCancelModal = true" class="w-full sm:w-auto whitespace-nowrap">
                                    {{ t('subscription.cancel') }}
                                </PrimaryButton>
                            </div>

                            <!-- Cancel Now -->
                            <div v-if="subscription.on_grace_period" class="flex flex-col sm:flex-row items-start sm:items-center justify-between gap-3 sm:gap-4 bg-red-50 border border-red-200 rounded-lg p-4">
                                <div class="flex-1">
                                    <p class="font-semibold text-red-900 text-sm md:text-base">
                                        Annuler immédiatement
                                    </p>
                                    <p class="text-xs md:text-sm text-red-700 mt-1">
                                        Mettre fin à votre abonnement dès maintenant sans attendre la fin de la période
                                    </p>
                                </div>
                                <PrimaryButton variant="danger" @click="showCancelNowModal = true" class="w-full sm:w-auto whitespace-nowrap">
                                    Annuler maintenant
                                </PrimaryButton>
                            </div>

                            <!-- Billing Portal -->
                            <div class="flex flex-col sm:flex-row items-start sm:items-center justify-between gap-3 sm:gap-4 bg-gray-50 border border-gray-200 rounded-lg p-4">
                                <div class="flex-1">
                                    <p class="font-semibold text-gray-900 text-sm md:text-base">
                                        {{ t('subscription.payment_method') }}
                                    </p>
                                    <p class="text-xs md:text-sm text-gray-600 mt-1">
                                        Gérez votre moyen de paiement et vos factures sur Stripe
                                    </p>
                                </div>
                                <PrimaryButton @click="openBillingPortal" class="w-full sm:w-auto whitespace-nowrap">
                                    Portail de facturation
                                </PrimaryButton>
                            </div>
                        </div>
                    </div>

                    <!-- Billing History -->
                    <div class="p-4 md:p-6 border-t border-gray-200">
                        <h3 class="text-base md:text-lg font-semibold text-gray-900 mb-4">
                            {{ t('subscription.billing_history') }}
                        </h3>

                        <div v-if="invoices.length > 0" class="space-y-3">
                            <div
                                v-for="invoice in invoices"
                                :key="invoice.id"
                                class="flex flex-col sm:flex-row items-start sm:items-center justify-between gap-3 p-4 bg-gray-50 border border-gray-200 rounded-lg hover:bg-gray-100 transition"
                            >
                                <div class="flex-1">
                                    <div class="flex items-center gap-3 mb-2">
                                        <p class="font-semibold text-gray-900">
                                            {{ formatCurrency(invoice.total) }}
                                        </p>
                                        <span :class="[
                                            'px-2 py-1 text-xs font-medium rounded-full',
                                            getStatusClass(invoice.status)
                                        ]">
                                            {{ getStatusLabel(invoice.status) }}
                                        </span>
                                    </div>
                                    <p class="text-sm text-gray-600">
                                        {{ formatDate(invoice.date) }}
                                    </p>
                                </div>

                                <div class="flex gap-2 w-full sm:w-auto">
                                    <a
                                        v-if="invoice.download_url"
                                        :href="invoice.download_url"
                                        target="_blank"
                                        class="flex-1 sm:flex-none inline-flex items-center justify-center gap-2 px-4 py-2 bg-white border border-gray-300 rounded-lg text-sm font-medium text-gray-700 hover:bg-gray-50 transition"
                                    >
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/>
                                        </svg>
                                        Voir
                                    </a>
                                    <a
                                        v-if="invoice.invoice_pdf"
                                        :href="invoice.invoice_pdf"
                                        target="_blank"
                                        class="flex-1 sm:flex-none inline-flex items-center justify-center gap-2 px-4 py-2 bg-orange-600 text-white rounded-lg text-sm font-medium hover:bg-orange-700 transition"
                                    >
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                                        </svg>
                                        Télécharger PDF
                                    </a>
                                </div>
                            </div>
                        </div>

                        <div v-else class="text-center py-8 bg-gray-50 rounded-lg">
                            <svg class="w-12 h-12 text-gray-400 mx-auto mb-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                            </svg>
                            <p class="text-gray-600 text-sm">
                                Aucune facture disponible
                            </p>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Cancel Confirmation Modal -->
        <DialogModal variant="danger" :show="showCancelModal" @close="showCancelModal = false">
            <template #title>
                {{ t('subscription.confirm_cancel') }}
            </template>

            <template #content>
                {{ t('subscription.cancel_confirmation_message') }}
            </template>

            <template #footer>
                <PrimaryButton variant="secondary" @click="showCancelModal = false">
                    {{ t('common.cancel') }}
                </PrimaryButton>

                <PrimaryButton
                    variant="danger"
                    class="ms-3"
                    :class="{ 'opacity-25': cancelForm.processing }"
                    :disabled="cancelForm.processing"
                    @click="cancelSubscription"
                >
                    {{ t('subscription.confirm_cancel_button') }}
                </PrimaryButton>
            </template>
        </DialogModal>

        <!-- Resume Confirmation Modal -->
        <DialogModal variant="danger" :show="showResumeModal" @close="showResumeModal = false">
            <template #title>
                {{ t('subscription.confirm_resume') }}
            </template>

            <template #content>
                {{ t('subscription.resume_confirmation_message') }}
            </template>

            <template #footer>
                <PrimaryButton variant="secondary" @click="showResumeModal = false">
                    {{ t('common.cancel') }}
                </PrimaryButton>

                <PrimaryButton
                    class="ms-3"
                    :class="{ 'opacity-25': resumeForm.processing }"
                    :disabled="resumeForm.processing"
                    @click="resumeSubscription"
                >
                    {{ t('subscription.confirm_resume_button') }}
                </PrimaryButton>
            </template>
        </DialogModal>

        <!-- Swap Plan Confirmation Modal -->
        <DialogModal variant="danger" :show="showSwapModal" @close="showSwapModal = false">
            <template #title>
                Changer de formule
            </template>

            <template #content>
                <div v-if="swapForm.plan">
                    <p class="mb-4">Êtes-vous sûr de vouloir passer à la formule {{ getSwapPlanName() }} ?</p>
                    <div class="bg-blue-50 border border-blue-200 rounded-lg p-4">
                        <p class="text-sm text-blue-900">
                            <strong>Note:</strong> Le changement sera effectué immédiatement. Votre facturation sera ajustée au prorata.
                        </p>
                    </div>
                </div>
            </template>

            <template #footer>
                <PrimaryButton variant="secondary" @click="showSwapModal = false">
                    Annuler
                </PrimaryButton>

                <PrimaryButton
                    class="ms-3"
                    :class="{ 'opacity-25': swapForm.processing }"
                    :disabled="swapForm.processing"
                    @click="confirmSwap"
                >
                    Confirmer le changement
                </PrimaryButton>
            </template>
        </DialogModal>

        <!-- Cancel Now Confirmation Modal -->
        <DialogModal variant="danger" :show="showCancelNowModal" @close="showCancelNowModal = false">
            <template #title>
                Annuler immédiatement
            </template>

            <template #content>
                <div>
                    <p class="mb-4">Êtes-vous sûr de vouloir annuler votre abonnement immédiatement ?</p>
                    <div class="bg-red-50 border border-red-200 rounded-lg p-4">
                        <p class="text-sm text-red-900">
                            <strong>Attention:</strong> Votre accès aux fonctionnalités Premium sera révoqué immédiatement et vous ne serez pas remboursé pour la période restante.
                        </p>
                    </div>
                </div>
            </template>

            <template #footer>
                <PrimaryButton variant="secondary" @click="showCancelNowModal = false">
                    Annuler
                </PrimaryButton>

                <PrimaryButton
                    variant="danger"
                    class="ms-3"
                    :class="{ 'opacity-25': cancelNowForm.processing }"
                    :disabled="cancelNowForm.processing"
                    @click="cancelNow"
                >
                    Confirmer l'annulation immédiate
                </PrimaryButton>
            </template>
        </DialogModal>
    </AppLayout>
</template>
