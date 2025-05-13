<template>
    <Modal ref="modal" title="Delete Content Items" size="md">
        <div class="space-y-4">
            <p class="text-sm text-gray-600">
                Are you sure you want to delete {{ contentItemIds.length }} content {{ contentItemIds.length === 1 ? 'item' : 'items' }}?
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
            contentItemIds: []
        };
    },
    methods: {
        showModal(contentItemIds) {
            this.contentItemIds = contentItemIds;
            this.$refs.modal.showModal();
        },
        closeModal() {
            this.$refs.modal.closeModal();
            this.contentItemIds = [];
        },
        async submit() {
            try {
                this.formState.isLoading = true;
                await axios.delete(`/api/knowledge-bases`, {
                    params: {
                        content_item_ids: this.contentItemIds
                    }
                });
                this.notificationState.showSuccessNotification('Content items deleted successfully.');
                this.$emit('deleted', this.contentItemIds);
                this.closeModal();
            } catch (error) {
                const message = error?.response?.data?.message || error?.message || 'Failed to delete content items';
                this.notificationState.showWarningNotification(message);
                console.error('Failed to delete content items:', error);
            } finally {
                this.formState.isLoading = false;
            }
        }
    }
};
</script>
