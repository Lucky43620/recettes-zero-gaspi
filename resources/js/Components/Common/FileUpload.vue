<script setup>
import { ref, computed } from 'vue';
import { PhotoIcon, XMarkIcon } from '@heroicons/vue/24/outline';
import { useI18n } from 'vue-i18n';

const { t } = useI18n();

const props = defineProps({
    modelValue: [Array, File],
    multiple: {
        type: Boolean,
        default: false
    },
    accept: {
        type: String,
        default: 'image/*'
    },
    maxSize: {
        type: Number,
        default: 10240
    },
    label: {
        type: String,
        default: null
    },
    hint: String,
    error: String,
});

const emit = defineEmits(['update:modelValue']);

const fileInput = ref(null);
const isDragging = ref(false);
const previews = ref([]);

const files = computed({
    get: () => props.modelValue,
    set: (value) => emit('update:modelValue', value),
});

const displayLabel = computed(() => props.label || t('common.images'));
const defaultHint = computed(() => t('common.file_size_limit', { size: props.maxSize / 1024 }));

function handleFileSelect(event) {
    const selectedFiles = Array.from(event.target.files);
    processFiles(selectedFiles);
}

function handleDrop(event) {
    isDragging.value = false;
    const selectedFiles = Array.from(event.dataTransfer.files);
    processFiles(selectedFiles);
}

function processFiles(selectedFiles) {
    if (!props.multiple && selectedFiles.length > 1) {
        selectedFiles = [selectedFiles[0]];
    }

    const validFiles = selectedFiles.filter(file => {
        const sizeMB = file.size / 1024;
        return sizeMB <= props.maxSize;
    });

    if (props.multiple) {
        files.value = validFiles;
    } else {
        files.value = validFiles[0] || null;
    }

    generatePreviews(validFiles);
}

function generatePreviews(fileList) {
    previews.value = [];
    fileList.forEach(file => {
        const reader = new FileReader();
        reader.onload = (e) => {
            previews.value.push({
                url: e.target.result,
                name: file.name
            });
        };
        reader.readAsDataURL(file);
    });
}

function removeFile(index) {
    if (props.multiple) {
        const newFiles = Array.from(files.value);
        newFiles.splice(index, 1);
        files.value = newFiles;
        previews.value.splice(index, 1);
    } else {
        files.value = null;
        previews.value = [];
        if (fileInput.value) {
            fileInput.value.value = '';
        }
    }
}

function openFilePicker() {
    fileInput.value?.click();
}

function handleDragEnter() {
    isDragging.value = true;
}

function handleDragLeave() {
    isDragging.value = false;
}
</script>

<template>
    <div>
        <label v-if="displayLabel" class="block text-sm font-medium text-gray-700 mb-2">
            {{ displayLabel }}
        </label>

        <div
            @click="openFilePicker"
            @dragenter.prevent="handleDragEnter"
            @dragover.prevent
            @dragleave.prevent="handleDragLeave"
            @drop.prevent="handleDrop"
            :class="[
                'relative border-2 border-dashed rounded-lg p-8 transition-all cursor-pointer',
                isDragging
                    ? 'border-green-500 bg-green-50'
                    : 'border-gray-300 hover:border-green-400 hover:bg-gray-50',
                error ? 'border-red-300' : ''
            ]"
        >
            <input
                ref="fileInput"
                type="file"
                :multiple="multiple"
                :accept="accept"
                @change="handleFileSelect"
                class="hidden"
            />

            <div class="text-center">
                <PhotoIcon class="mx-auto h-12 w-12 text-gray-400" />
                <div class="mt-4">
                    <p class="text-sm font-medium text-gray-900">
                        {{ t('common.click_or_drag_drop') }}
                    </p>
                    <p class="text-xs text-gray-500 mt-1">
                        {{ hint || defaultHint }}
                    </p>
                </div>
            </div>
        </div>

        <div v-if="error" class="text-red-600 text-sm mt-1">
            {{ error }}
        </div>

        <div v-if="previews.length > 0" class="mt-4 grid grid-cols-2 md:grid-cols-3 gap-4">
            <div
                v-for="(preview, index) in previews"
                :key="index"
                class="relative group"
            >
                <img
                    :src="preview.url"
                    :alt="preview.name"
                    class="w-full h-32 object-cover rounded-lg border-2 border-gray-200"
                />
                <button
                    type="button"
                    @click.stop="removeFile(index)"
                    class="absolute -top-2 -right-2 p-1 bg-red-500 text-white rounded-full hover:bg-red-600 shadow-lg transition-all opacity-0 group-hover:opacity-100"
                >
                    <XMarkIcon class="h-4 w-4" />
                </button>
                <p class="text-xs text-gray-600 mt-1 truncate">
                    {{ preview.name }}
                </p>
            </div>
        </div>
    </div>
</template>
