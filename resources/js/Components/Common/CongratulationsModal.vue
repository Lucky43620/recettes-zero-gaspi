<template>
  <Teleport to="body">
    <Transition
      enter-active-class="transition ease-out duration-200"
      enter-from-class="opacity-0"
      enter-to-class="opacity-100"
      leave-active-class="transition ease-in duration-150"
      leave-from-class="opacity-100"
      leave-to-class="opacity-0"
    >
      <div
        v-if="show"
        class="fixed inset-0 z-50 overflow-y-auto px-4 py-6 sm:px-0 flex items-center justify-center"
        @click.self="close"
      >
        <div class="fixed inset-0 bg-black/60 backdrop-blur-sm"></div>

        <Transition
          enter-active-class="transition ease-out duration-200"
          enter-from-class="opacity-0 scale-95"
          enter-to-class="opacity-100 scale-100"
          leave-active-class="transition ease-in duration-150"
          leave-from-class="opacity-100 scale-100"
          leave-to-class="opacity-0 scale-95"
        >
          <div
            v-if="show"
            class="relative bg-white rounded-2xl shadow-2xl max-w-md w-full mx-auto overflow-hidden"
          >
            <div class="absolute inset-0 bg-gradient-to-br from-green-50 via-orange-50 to-yellow-50 opacity-40"></div>

            <div class="relative p-8 text-center">
              <div class="mb-6 flex justify-center">
                <div class="w-24 h-24 bg-gradient-to-br from-green-500 to-green-600 rounded-full flex items-center justify-center shadow-lg animate-bounce">
                  <CheckCircleIcon class="w-16 h-16 text-white" />
                </div>
              </div>

              <h3 class="text-3xl font-bold text-gray-900 mb-4">
                {{ displayTitle }}
              </h3>

              <p class="text-lg text-gray-700 mb-8 leading-relaxed">
                {{ displayMessage }}
              </p>

              <div class="flex flex-col sm:flex-row gap-3 justify-center">
                <button
                  v-if="showShareButton"
                  @click="$emit('share')"
                  class="px-6 py-3 bg-orange-600 text-white font-semibold rounded-lg hover:bg-orange-700 active:bg-orange-800 transition-all transform hover:scale-105 flex items-center justify-center gap-2"
                >
                  <ShareIcon class="w-5 h-5" />
                  {{ displayShareButtonText }}
                </button>
                <button
                  @click="close"
                  class="px-6 py-3 bg-green-600 text-white font-semibold rounded-lg hover:bg-green-700 active:bg-green-800 transition-all transform hover:scale-105 flex items-center justify-center gap-2"
                >
                  <ArrowLeftIcon class="w-5 h-5" />
                  {{ displayCloseButtonText }}
                </button>
              </div>
            </div>

            <div class="absolute top-0 left-0 w-full h-2 bg-gradient-to-r from-green-500 via-orange-500 to-yellow-500"></div>
          </div>
        </Transition>
      </div>
    </Transition>
  </Teleport>
</template>

<script setup>
import { computed } from 'vue';
import { CheckCircleIcon, ShareIcon, ArrowLeftIcon } from '@heroicons/vue/24/solid';
import { useI18n } from 'vue-i18n';

const { t } = useI18n();

const props = defineProps({
  show: {
    type: Boolean,
    default: false
  },
  title: {
    type: String,
    default: ''
  },
  message: {
    type: String,
    default: ''
  },
  closeButtonText: {
    type: String,
    default: ''
  },
  shareButtonText: {
    type: String,
    default: ''
  },
  showShareButton: {
    type: Boolean,
    default: true
  }
});

const displayTitle = computed(() => props.title || t('cook.congratulations_title'));
const displayMessage = computed(() => props.message || t('cook.congratulations_message'));
const displayCloseButtonText = computed(() => props.closeButtonText || t('cook.back_to_recipe'));
const displayShareButtonText = computed(() => props.shareButtonText || t('cook.share_cooksnap'));

const emit = defineEmits(['close', 'share']);

const close = () => {
  emit('close');
};
</script>
