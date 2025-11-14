<script setup>
import { useI18n } from 'vue-i18n';
import { Link } from '@inertiajs/vue3';
import PrimaryButton from '@/Components/Common/PrimaryButton.vue';

const { t } = useI18n();

defineProps({
    form: Object,
    isAuthenticated: Boolean,
    isReply: {
        type: Boolean,
        default: false,
    },
});

const emit = defineEmits(['submit', 'cancel']);
</script>

<template>
    <div v-if="!isAuthenticated" class="bg-gray-50 border border-gray-200 rounded-lg p-6 text-center">
        <p class="text-gray-700 mb-4">{{ t('profile.login_to_comment') }}</p>
        <Link :href="route('login')">
            <PrimaryButton>
                {{ t('auth.login') }}
            </PrimaryButton>
        </Link>
    </div>

    <form v-else @submit.prevent="emit('submit')" :class="isReply ? 'space-y-2' : 'space-y-4'">
        <div>
            <textarea
                v-model="form.content"
                :rows="isReply ? 2 : 3"
                class="w-full border-gray-300 rounded-md shadow-sm focus:border-green-500 focus:ring-green-500"
                :placeholder="isReply ? t('profile.your_reply') : t('profile.share_experience')"
                required
            ></textarea>
        </div>
        <div class="flex gap-2">
            <PrimaryButton type="submit" :loading="form.processing" :size="isReply ? 'sm' : undefined">
                {{ isReply ? t('profile.reply') : t('profile.publish') }}
            </PrimaryButton>
            <PrimaryButton
                v-if="isReply"
                type="button"
                @click="emit('cancel')"
                variant="secondary"
                :size="isReply ? 'sm' : undefined"
            >
                {{ t('common.cancel') }}
            </PrimaryButton>
        </div>
    </form>
</template>
