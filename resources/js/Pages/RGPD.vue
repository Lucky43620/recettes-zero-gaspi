<template>
  <PublicLayout :title="t('rgpd.title')">
    <div class="py-12 bg-gray-50 min-h-screen">
      <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg p-6 sm:p-8">
          <h1 class="text-3xl sm:text-4xl font-bold text-gray-900 mb-6">
            {{ t('rgpd.title') }}
          </h1>

          <div class="prose prose-green max-w-none">
            <section class="mb-8">
              <h2 class="text-2xl font-semibold text-gray-900 mb-4">
                {{ t('rgpd.data_protection') }}
              </h2>
              <div class="bg-green-50 border-l-4 border-green-600 p-4 mb-4">
                <p class="text-gray-700">
                  <strong>{{ t('rgpd.responsible') }} :</strong> {{ t('rgpd.responsible_desc') }}
                </p>
              </div>
            </section>

            <section class="mb-8">
              <h2 class="text-2xl font-semibold text-gray-900 mb-4">
                {{ t('rgpd.data_collected_title') }}
              </h2>
              <p class="text-gray-700 mb-4">{{ t('rgpd.data_collected_desc') }}</p>
              <ul class="list-disc list-inside space-y-2 text-gray-700">
                <li>{{ t('rgpd.data_email') }}</li>
                <li>{{ t('rgpd.data_name') }}</li>
                <li>{{ t('rgpd.data_profile') }}</li>
                <li>{{ t('rgpd.data_recipes') }}</li>
                <li>{{ t('rgpd.data_usage') }}</li>
              </ul>
            </section>

            <section class="mb-8">
              <h2 class="text-2xl font-semibold text-gray-900 mb-4">
                {{ t('rgpd.purpose_title') }}
              </h2>
              <ul class="list-disc list-inside space-y-2 text-gray-700">
                <li>{{ t('rgpd.purpose_account') }}</li>
                <li>{{ t('rgpd.purpose_features') }}</li>
                <li>{{ t('rgpd.purpose_communication') }}</li>
                <li>{{ t('rgpd.purpose_improvement') }}</li>
              </ul>
            </section>

            <section class="mb-8">
              <h2 class="text-2xl font-semibold text-gray-900 mb-4">
                {{ t('rgpd.your_rights_title') }}
              </h2>
              <p class="text-gray-700 mb-4">{{ t('rgpd.your_rights_desc') }}</p>
              <ul class="list-disc list-inside space-y-2 text-gray-700 mb-4">
                <li>{{ t('rgpd.right_access') }}</li>
                <li>{{ t('rgpd.right_rectification') }}</li>
                <li>{{ t('rgpd.right_erasure') }}</li>
                <li>{{ t('rgpd.right_portability') }}</li>
                <li>{{ t('rgpd.right_opposition') }}</li>
                <li>{{ t('rgpd.right_limitation') }}</li>
              </ul>
              <div class="bg-blue-50 border-l-4 border-blue-600 p-4">
                <p class="font-semibold text-gray-900 mb-2">{{ t('rgpd.exercise_rights') }}</p>
                <ul class="list-disc list-inside space-y-1 text-gray-700">
                  <li>{{ t('rgpd.export_data') }}</li>
                  <li>{{ t('rgpd.delete_account') }}</li>
                  <li>{{ t('rgpd.contact_us') }}</li>
                </ul>
              </div>
            </section>

            <section class="mb-8">
              <h2 class="text-2xl font-semibold text-gray-900 mb-4">
                {{ t('rgpd.data_retention_title') }}
              </h2>
              <p class="text-gray-700">{{ t('rgpd.data_retention_desc') }}</p>
            </section>

            <section class="mb-8">
              <h2 class="text-2xl font-semibold text-gray-900 mb-4">
                {{ t('rgpd.security_title') }}
              </h2>
              <p class="text-gray-700">{{ t('rgpd.security_desc') }}</p>
            </section>

            <section class="mb-8">
              <h2 class="text-2xl font-semibold text-gray-900 mb-4">
                {{ t('rgpd.cookies_title') }}
              </h2>
              <p class="text-gray-700">{{ t('rgpd.cookies_desc') }}</p>
            </section>

            <section class="mb-8">
              <h2 class="text-2xl font-semibold text-gray-900 mb-4">
                {{ t('rgpd.third_party_title') }}
              </h2>
              <p class="text-gray-700 mb-4">{{ t('rgpd.third_party_desc') }}</p>
              <ul class="list-disc list-inside space-y-2 text-gray-700">
                <li>{{ t('rgpd.third_party_stripe') }}</li>
                <li>{{ t('rgpd.third_party_openfoodfacts') }}</li>
              </ul>
            </section>

            <section class="mb-8">
              <h2 class="text-2xl font-semibold text-gray-900 mb-4">
                {{ t('rgpd.contact_title') }}
              </h2>
              <p class="text-gray-700">{{ t('rgpd.contact_desc') }}</p>
            </section>

            <div class="mt-12 pt-6 border-t border-gray-200 text-sm text-gray-500 text-center">
              {{ t('rgpd.last_updated', { date: '18 novembre 2025' }) }}
            </div>
          </div>
        </div>
      </div>
    </div>
  </PublicLayout>
</template>

<script setup>
import PublicLayout from '@/Layouts/PublicLayout.vue'
import { useI18n } from 'vue-i18n'

const { t } = useI18n()
</script>
