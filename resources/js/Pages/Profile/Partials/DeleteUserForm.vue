<script setup>
import { ref } from 'vue';
import { useForm } from '@inertiajs/vue3';
import ActionSection from '@/Components/ActionSection.vue';
import PrimaryButton from '@/Components/PrimaryButton.vue';
import DialogModal from '@/Components/DialogModal.vue';
import InputError from '@/Components/InputError.vue';
import FormInput from '@/Components/Common/FormInput.vue';
import { useI18n } from 'vue-i18n';

const { t } = useI18n();

const confirmingUserDeletion = ref(false);
const passwordInput = ref(null);

const form = useForm({
    password: '',
});

const confirmUserDeletion = () => {
    confirmingUserDeletion.value = true;

    setTimeout(() => passwordInput.value.focus(), 250);
};

const deleteUser = () => {
    form.delete(route('current-user.destroy'), {
        preserveScroll: true,
        onSuccess: () => closeModal(),
        onError: () => passwordInput.value.focus(),
        onFinish: () => form.reset(),
    });
};

const closeModal = () => {
    confirmingUserDeletion.value = false;

    form.reset();
};
</script>

<template>
    <ActionSection>
        <template #title>
            {{ t('profile.delete_account_title') }}
        </template>

        <template #description>
            {{ t('profile.delete_account_subtitle') }}
        </template>

        <template #content>
            <div class="max-w-xl text-sm text-gray-600">
                {{ t('profile.delete_account_description') }}
            </div>

            <div class="mt-5">
                <PrimaryButton variant="danger" @click="confirmUserDeletion">
                    {{ t('profile.delete_account_title') }}
                </PrimaryButton>
            </div>

            <!-- Delete Account Confirmation Modal -->
            <DialogModal :show="confirmingUserDeletion" @close="closeModal">
                <template #title>
                    {{ t('profile.delete_account_title') }}
                </template>

                <template #content>
                    {{ t('profile.delete_account_confirmation') }}

                    <div class="mt-4">
                        <FormInput
                            ref="passwordInput"
                            v-model="form.password"
                            type="password"
                            class="mt-1 block w-3/4"
                            :placeholder="t('auth.password')"
                            autocomplete="current-password"
                            @keyup.enter="deleteUser"
                        />

                        <InputError :message="form.errors.password" class="mt-2" />
                    </div>
                </template>

                <template #footer>
                    <PrimaryButton variant="secondary" class="w-full sm:w-auto" @click="closeModal">
                        {{ t('common.cancel') }}
                    </PrimaryButton>

                    <PrimaryButton
                        variant="danger"
                        class="w-full sm:w-auto"
                        :class="{ 'opacity-25': form.processing }"
                        :disabled="form.processing"
                        @click="deleteUser"
                    >
                        {{ t('profile.delete_account_title') }}
                    </PrimaryButton>
                </template>
            </DialogModal>
        </template>
    </ActionSection>
</template>
