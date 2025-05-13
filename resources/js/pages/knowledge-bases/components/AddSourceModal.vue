<template>
    <Modal ref="modal" title="Add Source" size="md">
        <form @submit.prevent="submit" class="space-y-4">
            <div>
                <label for="type" class="block text-sm font-medium text-gray-700">Type</label>
                <select
                    id="type"
                    v-model="form.type"
                    class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm">
                    <option value="website">Website</option>
                    <option value="snippet">Snippet</option>
                </select>
            </div>
            <div v-if="form.type === 'website'">
                <label for="name" class="block text-sm font-medium text-gray-700">URL</label>
                <input
                    id="name"
                    type="text"
                    v-model="form.name"
                    placeholder="https://example.com"
                    class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm"
                />
            </div>
            <div v-if="form.type === 'snippet'">
                <label for="name" class="block text-sm font-medium text-gray-700">Title</label>
                <input
                    id="name"
                    type="text"
                    v-model="form.name"
                    placeholder="Snippet Title"
                    class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm"
                />
                <label for="content" class="block text-sm font-medium text-gray-700">Content</label>
                <textarea
                    id="content"
                    v-model="form.content"
                    rows="4"
                    placeholder="Snippet Content"
                    class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm"
                ></textarea>
            </div>
            <div class="flex justify-end space-x-2">
                <Button type="light" size="sm" :action="closeModal">Cancel</Button>
                <Button type="primary" size="sm" :loading="formState.isLoading">Add</Button>
            </div>
        </form>
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
    props: {
        knowledgeBaseId: {
            type: String,
            required: true
        }
    },
    data() {
        return {
            form: {
                type: 'website',
                name: '',
                content: '',
                knowledge_base_id: this.knowledgeBaseId
            }
        };
    },
    methods: {
        showModal(type = null) {
            if (type) this.form.type = type;
            this.$refs.modal.showModal();
        },
        closeModal() {
            this.$refs.modal.closeModal();
            this.form.name = '';
            this.form.content = '';
        },
        async submit() {
            try {
                this.formState.isLoading = true;
                await axios.post('/api/content-sources', this.form);
                this.notificationState.showSuccessNotification('Source added successfully.');
                this.$emit('created');
                this.closeModal();
            } catch (error) {
                const message = error?.response?.data?.message || error?.message || 'Failed to add source';
                this.notificationState.showWarningNotification(message);
                console.error('Failed to add source:', error);
            } finally {
                this.formState.isLoading = false;
            }
        }
    }
};
</script>
