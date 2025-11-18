<script setup>
import { ref, reactive, nextTick } from 'vue';
import { useI18n } from 'vue-i18n';
import DialogModal from './DialogModal.vue';
import InputError from './InputError.vue';
import PrimaryButton from './PrimaryButton.vue';
import FormInput from '@/Components/Common/FormInput.vue';

const { t } = useI18n();
const emit = defineEmits(['confirmed']);

defineProps({
    title: {
        type: String,
        default: '',
    },
    content: {
        type: String,
        default: '',
    },
    button: {
        type: String,
        default: '',
    },
});

const confirmingPassword = ref(false);

const form = reactive({
    password: '',
    error: '',
    processing: false,
});

const passwordInput = ref(null);

const startConfirmingPassword = () => {
    axios.get(route('password.confirmation')).then(response => {
        if (response.data.confirmed) {
            emit('confirmed');
        } else {
            confirmingPassword.value = true;

            setTimeout(() => passwordInput.value.focus(), 250);
        }
    });
};

const confirmPassword = () => {
    form.processing = true;

    axios.post(route('password.confirm'), {
        password: form.password,
    }).then(() => {
        form.processing = false;

        closeModal();
        nextTick().then(() => emit('confirmed'));

    }).catch(error => {
        form.processing = false;
        form.error = error.response.data.errors.password[0];
        passwordInput.value.focus();
    });
};

const closeModal = () => {
    confirmingPassword.value = false;
    form.password = '';
    form.error = '';
};
</script>

<template>
    <span>
        <span @click="startConfirmingPassword">
            <slot />
        </span>

        <DialogModal :show="confirmingPassword" @close="closeModal">
            <template #title>
                {{ title || t('auth.secure_area') }}
            </template>

            <template #content>
                {{ content || t('auth.confirm_password_description') }}

                <div class="mt-4">
                    <FormInput
                        ref="passwordInput"
                        v-model="form.password"
                        type="password"
                        class="mt-1 block w-3/4"
                        :placeholder="t('auth.password')"
                        autocomplete="current-password"
                        @keyup.enter="confirmPassword"
                    />

                    <InputError :message="form.error" class="mt-2" />
                </div>
            </template>

            <template #footer>
                <PrimaryButton variant="secondary" @click="closeModal">
                    {{ t('common.cancel') }}
                </PrimaryButton>

                <PrimaryButton
                    class="ms-3"
                    :class="{ 'opacity-25': form.processing }"
                    :disabled="form.processing"
                    @click="confirmPassword"
                >
                    {{ button || t('auth.confirm') }}
                </PrimaryButton>
            </template>
        </DialogModal>
    </span>
</template>
