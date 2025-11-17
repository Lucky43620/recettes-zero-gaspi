<script setup>
import { Head, useForm } from '@inertiajs/vue3';
import { useI18n } from 'vue-i18n';
import AuthenticationCard from '@/Components/AuthenticationCard.vue';
import AuthenticationCardLogo from '@/Components/AuthenticationCardLogo.vue';
import InputError from '@/Components/InputError.vue';
import InputLabel from '@/Components/InputLabel.vue';
import PrimaryButton from '@/Components/PrimaryButton.vue';
import TextInput from '@/Components/TextInput.vue';

const { t } = useI18n();

defineProps({
    status: String,
});

const form = useForm({
    email: '',
});

const submit = () => {
    form.post(route('password.email'));
};
</script>

<template>
    <Head :title="t('auth.forgot_password')" />

    <AuthenticationCard>
        <template #logo>
            <AuthenticationCardLogo />
        </template>

        <div class="text-center mb-6">
            <h1 class="text-2xl font-bold text-gray-900 mb-2">{{ t('auth.forgot_password_title') }}</h1>
            <p class="text-sm text-gray-600">
                {{ t('auth.forgot_password_description') }}
            </p>
        </div>

        <div v-if="status" class="mb-4 font-medium text-sm text-green-600 bg-green-50 p-3 rounded-md">
            {{ status }}
        </div>

        <form @submit.prevent="submit">
            <div>
                <InputLabel for="email" :value="t('auth.email')" />
                <TextInput
                    id="email"
                    v-model="form.email"
                    type="email"
                    class="mt-1 block w-full"
                    required
                    autofocus
                    autocomplete="username"
                />
                <InputError class="mt-2" :message="form.errors.email" />
            </div>

            <PrimaryButton type="submit" class="w-full justify-center mt-6 bg-green-600 hover:bg-green-700 focus:bg-green-700 active:bg-green-900 focus:ring-green-500" :class="{ 'opacity-25': form.processing }" :disabled="form.processing">
                {{ t('auth.send_reset_link') }}
            </PrimaryButton>
        </form>
    </AuthenticationCard>
</template>
