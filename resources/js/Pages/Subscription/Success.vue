<script setup>
import { useI18n } from 'vue-i18n';
import AppLayout from '@/Layouts/AppLayout.vue';
import PrimaryButton from '@/Components/PrimaryButton.vue';

const { t } = useI18n();
</script>

<template>
    <AppLayout :title="t('subscription.success_title')">
        <div class="py-12">
            <div class="max-w-3xl mx-auto sm:px-6 lg:px-8">
                <div class="bg-white rounded-lg shadow-lg p-12 text-center">
                    <!-- Success Icon -->
                    <div class="mx-auto flex items-center justify-center h-24 w-24 rounded-full bg-green-100 mb-8">
                        <svg class="h-12 w-12 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                        </svg>
                    </div>

                    <h2 class="text-3xl font-bold text-gray-900 mb-4">
                        {{ t('subscription.success_title') }}
                    </h2>

                    <p class="text-lg text-gray-700 mb-8">
                        {{ t('subscription.success_message') }}
                    </p>

                    <div class="bg-green-50 border border-green-200 rounded-lg p-6 mb-8">
                        <h3 class="font-semibold text-green-900 mb-3">
                            {{ t('subscription.whats_next') }}
                        </h3>
                        <ul class="text-left text-green-800 space-y-2">
                            <li class="flex items-start">
                                <svg class="w-5 h-5 text-green-600 mr-2 mt-0.5" fill="currentColor" viewBox="0 0 20 20">
                                    <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"/>
                                </svg>
                                {{ t('subscription.benefit_1') }}
                            </li>
                            <li class="flex items-start">
                                <svg class="w-5 h-5 text-green-600 mr-2 mt-0.5" fill="currentColor" viewBox="0 0 20 20">
                                    <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"/>
                                </svg>
                                {{ t('subscription.benefit_2') }}
                            </li>
                            <li class="flex items-start">
                                <svg class="w-5 h-5 text-green-600 mr-2 mt-0.5" fill="currentColor" viewBox="0 0 20 20">
                                    <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"/>
                                </svg>
                                {{ t('subscription.benefit_3') }}
                            </li>
                        </ul>
                    </div>

                    <div class="flex gap-4 justify-center">
                        <PrimaryButton @click="$inertia.visit(route('dashboard'))">
                            {{ t('subscription.go_to_dashboard') }}
                        </PrimaryButton>
                        <PrimaryButton @click="$inertia.visit(route('subscription.manage'))" class="bg-gray-600 hover:bg-gray-700">
                            {{ t('subscription.manage_subscription') }}
                        </PrimaryButton>
                    </div>
                </div>
            </div>
        </div>
    </AppLayout>
</template>
