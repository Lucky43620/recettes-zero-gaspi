import { ref } from 'vue';

export function useConfirmation() {
    const showModal = ref(false);
    const modalConfig = ref({
        title: 'Confirmation',
        message: '',
        onConfirm: () => {},
        danger: false,
    });

    function confirm({ title, message, onConfirm, danger = false }) {
        modalConfig.value = { title, message, onConfirm, danger };
        showModal.value = true;
    }

    function handleConfirm() {
        modalConfig.value.onConfirm();
        showModal.value = false;
    }

    function handleCancel() {
        showModal.value = false;
    }

    return {
        showModal,
        modalConfig,
        confirm,
        handleConfirm,
        handleCancel,
    };
}
