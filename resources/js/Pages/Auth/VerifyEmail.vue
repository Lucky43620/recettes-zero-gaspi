<script setup>
import { computed } from 'vue';
import { Head, Link, useForm } from '@inertiajs/vue3';
import { useI18n } from 'vue-i18n';
import AuthenticationCard from '@/Components/AuthenticationCard.vue';
import AuthenticationCardLogo from '@/Components/AuthenticationCardLogo.vue';
import PrimaryButton from '@/Components/PrimaryButton.vue';

const { t } = useI18n();

const props = defineProps({
    status: String,
});

const form = useForm({});

const submit = () => {
    form.post(route('verification.send'));
};

const verificationLinkSent = computed(() => props.status === 'verification-link-sent');
</script>

<template>
    <Head :title="t('auth.verify_email')" />

    <AuthenticationCard>
        <template #logo>
            <AuthenticationCardLogo />
        </template>

        <div class="mb-4 text-sm text-gray-600">
            {{ t('auth.verify_email_description') }}
        </div>

        <div v-if="verificationLinkSent" class="mb-4 font-medium text-sm text-green-600">
            {{ t('auth.verification_link_sent') }}
        </div>

        <form @submit.prevent="submit">
            <div class="mt-4 flex flex-col sm:flex-row items-stretch sm:items-center justify-between gap-3">
                <PrimaryButton class="w-full sm:w-auto text-center" :class="{ 'opacity-25': form.processing }" :disabled="form.processing">
                    {{ t('auth.resend_verification_email') }}
                </PrimaryButton>

                <div class="flex flex-col sm:flex-row gap-2 text-center sm:text-left">
                    <Link
                        :href="route('user.profile')"
                        class="underline text-sm text-gray-600 hover:text-gray-900 rounded-md focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500"
                    >
                        {{ t('auth.edit_profile') }}</Link>

                    <Link
                        :href="route('logout')"
                        method="post"
                        as="button"
                        class="underline text-sm text-gray-600 hover:text-gray-900 rounded-md focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500"
                    >
                        {{ t('auth.log_out') }}
                    </Link>
                </div>
            </div>
        </form>
    </AuthenticationCard>
</template>
