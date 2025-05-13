<template>
    <Modal ref="modal" title="Delete Content" size="md">
        <div class="space-y-4">
            <p class="text-sm text-gray-600">
                Are you sure you want to delete "{{ contentItem?.title }}"?
            </p>
            <div class="flex justify-end space-x-2">
                <Button type="light" size="sm" :action="closeModal">Cancel</Button>
                <Button type="danger" size="sm" :loading="formState.isLoading" :action="submit">Delete</Button>
            </div>
        </div>
    </Modal>
</template>

<script>
import axios from 'axios';
import Modal from '@Partials/Modal.vue';
import Button from '@Partials/Button.vue';

export default {
    inject: ['formState', 'notificationState'],
    components: {
        Modal, Button
    },
    data() {
        return {
            contentItem: null
        };
    },
    methods: {
        showModal(contentItem) {
            this.contentItem = contentItem;
            this.$refs.modal.showModal();
        },
        closeModal() {
            this.$refs.modal.closeModal();
            this.contentItem = null;
        },
        async submit() {
            try {
                this.formState.isLoading = true;
                await axios.delete(`/api/knowledge-bases/${this.contentItem.knowledge_base_id}/content-items/${this.contentItem.id}`);
                this.notificationState.showSuccessNotification('Content deleted successfully.');
                this.$emit('deleted', this.contentItem.id);
                this.closeModal();
            } catch (error) {
                const message = error?.response?.data?.message || error?.message || 'Failed to delete content';
                this.notificationState.showWarningNotification(message);
                console.error('Failed to delete content:', error);
            } finally {
                this.formState.isLoading = false;
            }
        }
    }
};
</script>
