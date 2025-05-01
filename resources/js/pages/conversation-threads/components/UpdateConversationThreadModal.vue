<template>
    <Modal ref="modal" @closed="onClose">
        <template #header>
            <div class="flex items-center space-x-2">
                <Pencil size="24" class="text-indigo-500" />
                <h3 class="text-lg font-semibold text-gray-900">Update Conversation Thread</h3>
            </div>
        </template>

        <template #body>
            <div class="space-y-4">
                <div>
                    <label for="title" class="block text-sm font-medium text-gray-700">Title</label>
                    <Input
                        id="title"
                        type="text"
                        v-model="form.title"
                        placeholder="Enter thread title"
                        :disabled="isLoading"
                        class="mt-1 w-full"
                        @input="clearError('title')" />
                    <p v-if="errors.title" class="mt-1 text-sm text-red-600">{{ errors.title }}</p>
                </div>
            </div>
        </template>

        <template #footer>
            <div class="flex justify-end space-x-2">
                <Button type="light" size="md" :action="hideModal">
                    <span>Cancel</span>
                </Button>
                <Button type="primary" size="md" :action="updateThread" :disabled="isLoading">
                    <Loader v-if="isLoading" size="20" class="animate-spin mr-2" />
                    <span>Update</span>
                </Button>
            </div>
        </template>
    </Modal>
</template>

<script>
    import axios from 'axios';
    import Modal from '@Partials/Modal.vue';
    import Input from '@Partials/Input.vue';
    import Button from '@Partials/Button.vue';
    import Loader from 'lucide-vue-next/dist/esm/icons/Loader';
    import Pencil from 'lucide-vue-next/dist/esm/icons/Pencil';

    export default {
        components: { Modal, Input, Button, Loader, Pencil },
        inject: ['formState', 'notificationState'],
        data() {
            return {
                thread: null,
                form: {
                    title: ''
                },
                errors: {
                    title: null
                },
                isLoading: false,
            };
        },
        methods: {
            showModal(thread) {
                this.thread = thread;
                this.form.title = thread.title || '';
                this.$refs.modal.showModal();
            },
            hideModal() {
                this.$refs.modal.hideModal();
            },
            clearError(field) {
                this.errors[field] = null;
            },
            async updateThread() {
                if (!this.thread) return;

                try {
                    this.isLoading = true;
                    this.errors = { title: null };

                    const response = await axios.put(`/api/conversation-threads/${this.thread.id}`, {
                        title: this.form.title
                    });

                    if (response.data.updated) {
                        this.notificationState.showSuccessNotification('Conversation thread updated successfully');
                        this.$emit('updated', { ...this.thread, title: this.form.title });
                        this.hideModal();
                    } else {
                        this.notificationState.showWarningNotification('Failed to update conversation thread');
                    }
                } catch (error) {
                    if (error.response?.status === 422) {
                        const validationErrors = error.response.data.errors;
                        if (validationErrors.title) {
                            this.errors.title = validationErrors.title[0];
                        }
                    } else {
                        const message = error?.response?.data?.message || error?.message || 'Failed to update conversation thread';
                        this.notificationState.showWarningNotification(message);
                        console.error('Failed to update conversation thread:', error);
                    }
                } finally {
                    this.isLoading = false;
                }
            },
            onClose() {
                this.thread = null;
                this.form.title = '';
                this.errors = { title: null };
                this.isLoading = false;
            }
        }
    };
</script>
