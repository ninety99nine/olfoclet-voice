<template>
    <Modal ref="modal" @closed="onClose">
        <template #header>
            <div class="flex items-center space-x-2">
                <Trash2 size="24" class="text-red-500" />
                <h3 class="text-lg font-semibold text-gray-900">Delete Conversation Thread</h3>
            </div>
        </template>

        <template #body>
            <div class="space-y-4">
                <p class="text-sm text-gray-600">
                    Are you sure you want to delete the conversation thread titled "{{ thread?.title }}"?
                    This action cannot be undone.
                </p>
            </div>
        </template>

        <template #footer>
            <div class="flex justify-end space-x-2">
                <Button type="light" size="md" :action="hideModal">
                    <span>Cancel</span>
                </Button>
                <Button type="danger" size="md" :action="deleteThread" :disabled="isLoading">
                    <Loader v-if="isLoading" size="20" class="animate-spin mr-2" />
                    <span>Delete</span>
                </Button>
            </div>
        </template>
    </Modal>
</template>

<script>
    import axios from 'axios';
    import Modal from '@Partials/Modal.vue';
    import Button from '@Partials/Button.vue';
    import { Loader, Trash2 } from 'lucide-vue-next';

    export default {
        components: { Modal, Button, Loader, Trash2 },
        inject: ['notificationState'],
        data() {
            return {
                thread: null,
                isLoading: false,
            };
        },
        methods: {
            showModal(thread) {
                this.thread = thread;
                this.$refs.modal.showModal();
            },
            hideModal() {
                this.$refs.modal.hideModal();
            },
            async deleteThread() {
                if (!this.thread) return;

                try {
                    this.isLoading = true;
                    const response = await axios.delete('/api/conversation-threads', {
                        data: { thread_ids: [this.thread.id] }
                    });

                    if (response.data.deleted) {
                        this.notificationState.showSuccessNotification(response.data.message);
                        this.$emit('deleted', this.thread.id);
                        this.hideModal();
                    } else {
                        this.notificationState.showWarningNotification(response.data.message);
                    }
                } catch (error) {
                    const message = error?.response?.data?.message || error?.message || 'Failed to delete conversation thread';
                    this.notificationState.showWarningNotification(message);
                    console.error('Failed to delete conversation thread:', error);
                } finally {
                    this.isLoading = false;
                }
            },
            onClose() {
                this.thread = null;
                this.isLoading = false;
            }
        }
    };
</script>
