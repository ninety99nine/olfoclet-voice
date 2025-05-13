<template>
    <Modal ref="modal" title="Update Content" size="md">
        <form @submit.prevent="submit" class="space-y-4">
            <div>
                <label for="type" class="block text-sm font-medium text-gray-700">Type</label>
                <select
                    id="type"
                    v-model="form.type"
                    class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm"
                    disabled>
                    <option value="folder">Folder</option>
                    <option value="article">Article</option>
                    <option value="snippet">Snippet</option>
                    <option value="webpage">Webpage</option>
                </select>
            </div>
            <div>
                <label for="title" class="block text-sm font-medium text-gray-700">Title</label>
                <input
                    id="title"
                    type="text"
                    v-model="form.title"
                    placeholder="Content Title"
                    class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm"
                />
            </div>
            <div v-if="form.type !== 'webpage' && form.type !== 'folder'">
                <label for="content" class="block text-sm font-medium text-gray-700">Content</label>
                <textarea
                    id="content"
                    v-model="form.content"
                    rows="4"
                    placeholder="Content"
                    class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm"
                ></textarea>
            </div>
            <div v-if="form.type === 'webpage'">
                <label for="url" class="block text-sm font-medium text-gray-700">URL</label>
                <input
                    id="url"
                    type="text"
                    v-model="form.url"
                    placeholder="https://example.com"
                    class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm"
                />
            </div>
            <div v-if="form.type !== 'folder'">
                <label for="visibility" class="block text-sm font-medium text-gray-700">Visibility</label>
                <select
                    id="visibility"
                    v-model="form.visibility"
                    class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm">
                    <option value="public">Public</option>
                    <option value="internal">Internal</option>
                </select>
            </div>
            <div v-if="form.type !== 'folder'">
                <label for="help_center_collection_id" class="block text-sm font-medium text-gray-700">Help Center Collection</label>
                <select
                    id="help_center_collection_id"
                    v-model="form.help_center_collection_id"
                    class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm">
                    <option value="">No Collection</option>
                    <option v-for="collection in helpCenterCollections" :key="collection.id" :value="collection.id">{{ collection.name }}</option>
                </select>
            </div>
            <div v-if="form.type !== 'folder'" class="flex space-x-4">
                <div class="flex items-center space-x-2">
                    <Input type="checkbox" v-model="form.ai_agent_enabled" />
                    <label class="text-sm text-gray-700">AI Agent</label>
                </div>
                <div class="flex items-center space-x-2">
                    <Input type="checkbox" v-model="form.copilot_enabled" />
                    <label class="text-sm text-gray-700">Copilot</label>
                </div>
                <div class="flex items-center space-x-2">
                    <Input type="checkbox" v-model="form.help_center_enabled" />
                    <label class="text-sm text-gray-700">Help Center</label>
                </div>
            </div>
            <div class="flex justify-end space-x-2">
                <Button type="light" size="sm" :action="closeModal">Cancel</Button>
                <Button type="primary" size="sm" :loading="formState.isLoading">Update</Button>
            </div>
        </form>
    </Modal>
</template>

<script>
import axios from 'axios';
import Modal from '@Partials/Modal.vue';
import Input from '@Partials/Input.vue';
import Button from '@Partials/Button.vue';

export default {
    inject: ['formState', 'notificationState'],
    components: {
        Modal, Input, Button
    },
    data() {
        return {
            contentItem: null,
            helpCenterCollections: [],
            form: {
                type: '',
                title: '',
                content: '',
                url: '',
                visibility: 'internal',
                ai_agent_enabled: true,
                copilot_enabled: true,
                help_center_enabled: false,
                help_center_collection_id: ''
            }
        };
    },
    methods: {
        showModal(contentItem) {
            this.contentItem = contentItem;
            this.form.type = contentItem.type;
            this.form.title = contentItem.title;
            this.form.content = contentItem.content || '';
            this.form.url = contentItem.url || '';
            this.form.visibility = contentItem.visibility;
            this.form.ai_agent_enabled = contentItem.ai_agent_enabled;
            this.form.copilot_enabled = contentItem.copilot_enabled;
            this.form.help_center_enabled = contentItem.help_center_enabled;
            this.form.help_center_collection_id = contentItem.help_center_collection_id || '';
            this.$refs.modal.showModal();
            this.fetchHelpCenterCollections();
        },
        closeModal() {
            this.$refs.modal.closeModal();
            this.form.type = '';
            this.form.title = '';
            this.form.content = '';
            this.form.url = '';
            this.form.visibility = 'internal';
            this.form.ai_agent_enabled = true;
            this.form.copilot_enabled = true;
            this.form.help_center_enabled = false;
            this.form.help_center_collection_id = '';
        },
        async fetchHelpCenterCollections() {
            try {
                const response = await axios.get(`/api/help-center-collections`, {
                    params: {
                        'knowledge_base_id': this.contentItem.knowledge_base_id
                    }
                });
                this.helpCenterCollections = response.data.data || [];
            } catch (error) {
                const message = error?.response?.data?.message || error?.message || 'Failed to fetch Help Center collections';
                this.notificationState.showWarningNotification(message);
                console.error('Failed to fetch Help Center collections:', error);
            }
        },
        async submit() {
            try {
                this.formState.isLoading = true;
                await axios.put(`/api/knowledge-bases/${this.contentItem.knowledge_base_id}/content-items/${this.contentItem.id}`, this.form);
                this.notificationState.showSuccessNotification('Content updated successfully.');
                this.$emit('updated');
                this.closeModal();
            } catch (error) {
                const message = error?.response?.data?.message || error?.message || 'Failed to update content';
                this.notificationState.showWarningNotification(message);
                console.error('Failed to update content:', error);
            } finally {
                this.formState.isLoading = false;
            }
        }
    }
};
</script>
