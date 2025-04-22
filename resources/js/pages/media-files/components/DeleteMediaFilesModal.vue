<template>
    <Modal
        size="sm"
        ref="modal"
        :showFooter="true"
        :dismissable="true"
        :scrollOnContent="false"
        :showApproveButton="true"
        header="Delete Media Files"
        approveText="Delete"
        :approveLoading="isDeleting"
        :approveAction="deleteMediaFiles"
        subheader="Are you sure you want to delete the selected media files?"
    >
        <template #content>
            <div class="p-4">
                <p class="text-sm text-gray-700 dark:text-neutral-400">
                    This action cannot be undone. {{ mediaFileIds.length }} media file(s) will be permanently deleted.
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
            mediaFileIds: [],
        };
    },
    methods: {
        showModal(mediaFileIds) {
            this.mediaFileIds = mediaFileIds;
            this.$refs.modal.showModal();
        },
        async deleteMediaFiles(hideModal) {
            this.isDeleting = true;
            try {
                await axios.delete('/api/media-files', {
                    params: {
                        media_file_ids: this.mediaFileIds,
                    },
                });
                this.$notificationState.showSuccessNotification(`${this.mediaFileIds.length} media file(s) deleted successfully!`);
                this.$emit('deleted', this.mediaFileIds);
                hideModal();
            } catch (error) {
                console.error('Failed to delete media files:', error);
                this.$notificationState.showWarningNotification('Failed to delete media files.');
            } finally {
                this.isDeleting = false;
            }
        },
    },
};
</script>
