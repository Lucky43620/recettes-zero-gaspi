<script setup>
import { Head, Link, useForm } from '@inertiajs/vue3';
import { useI18n } from 'vue-i18n';
import AuthenticationCard from '@/Components/AuthenticationCard.vue';
import AuthenticationCardLogo from '@/Components/AuthenticationCardLogo.vue';
import Checkbox from '@/Components/Checkbox.vue';
import InputError from '@/Components/InputError.vue';
import InputLabel from '@/Components/InputLabel.vue';
import PrimaryButton from '@/Components/PrimaryButton.vue';
import TextInput from '@/Components/TextInput.vue';

const { t } = useI18n();

defineProps({
    canResetPassword: Boolean,
    status: String,
});

const form = useForm({
    email: '',
    password: '',
    remember: false,
});

const submit = () => {
    form.transform(data => ({
        ...data,
        remember: form.remember ? 'on' : '',
    })).post(route('login'), {
        onFinish: () => form.reset('password'),
    });
};
</script>

<template>
    <Head :title="t('auth.login')" />

    <AuthenticationCard>
        <template #logo>
            <AuthenticationCardLogo />
        </template>

        <div class="text-center mb-6">
            <h1 class="text-2xl font-bold text-gray-900 mb-2">{{ t('auth.login') }}</h1>
            <p class="text-gray-600">{{ t('auth.login_subtitle') }}</p>
        </div>

        <div v-if="status" class="mb-4 font-medium text-sm text-green-600">
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

            <div class="mt-4">
                <InputLabel for="password" :value="t('auth.password')" />
                <TextInput
                    id="password"
                    v-model="form.password"
                    type="password"
                    class="mt-1 block w-full"
                    required
                    autocomplete="current-password"
                />
                <InputError class="mt-2" :message="form.errors.password" />
            </div>

            <div class="block mt-4">
                <label class="flex items-center">
                    <Checkbox v-model:checked="form.remember" name="remember" />
                    <span class="ms-2 text-sm text-gray-600">{{ t('auth.remember_me') }}</span>
                </label>
            </div>

            <div class="flex flex-col sm:flex-row items-stretch sm:items-center justify-end gap-3 sm:gap-4 mt-4">
                <Link v-if="canResetPassword" :href="route('password.request')" class="underline text-sm text-center sm:text-left text-green-600 hover:text-green-800 rounded-md focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-green-500">
                    {{ t('auth.forgot_password') }}
                </Link>

                <PrimaryButton class="bg-green-600 hover:bg-green-700 focus:bg-green-700 active:bg-green-900 focus:ring-green-500 w-full sm:w-auto" :class="{ 'opacity-25': form.processing }" :disabled="form.processing">
                    {{ t('auth.login_button') }}
                </PrimaryButton>
            </div>
        </form>

        <div class="mt-6 text-center">
            <p class="text-sm text-gray-600">
                {{ t('auth.not_registered') }}
                <Link :href="route('register')" class="text-green-600 hover:text-green-800 font-medium">
                    {{ t('auth.create_account') }}
                </Link>
            </p>
        </div>
    </AuthenticationCard>
</template>
