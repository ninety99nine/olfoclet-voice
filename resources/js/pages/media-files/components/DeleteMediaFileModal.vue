<template>
    <Modal
        size="sm"
        ref="modal"
        :showFooter="true"
        :dismissable="true"
        :scrollOnContent="false"
        :showApproveButton="true"
        header="Delete Media File"
        approveText="Delete"
        :approveLoading="isDeleting"
        :approveAction="deleteMediaFile"
        subheader="Are you sure you want to delete this media file?"
    >
        <template #content>
            <div class="p-4">
                <p class="text-sm text-gray-700 dark:text-neutral-400">
                    This action cannot be undone. The media file will be permanently deleted.
                </p>
            </div>
        </template>
    </Modal>
</template>

<script>
import axios from 'axios';
import Modal from '@Partials/Modal.vue';

export default {
    components: {
        Modal,
    },
    data() {
        return {
            isDeleting: false,
            mediaFile: null,
        };
    },
    methods: {
        showModal(mediaFile) {
            this.mediaFile = mediaFile;
            this.$refs.modal.showModal();
        },
        async deleteMediaFile(hideModal) {
            this.isDeleting = true;
            try {
                await axios.delete(`/api/media-files/${this.mediaFile.id}`);
                this.$notificationState.showSuccessNotification('Media file deleted successfully!');
                this.$emit('deleted', this.mediaFile.id);
                hideModal();
            } catch (error) {
                console.error('Failed to delete media file:', error);
                this.$notificationState.showWarningNotification('Failed to delete media file.');
            } finally {
                this.isDeleting = false;
            }
        },
    },
};
</script>
