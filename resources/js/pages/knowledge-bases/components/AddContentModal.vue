<template>
    <Modal
        size="md"
        ref="modal"
        :showFooter="true"
        :dismissable="true"
        :scrollOnContent="false"
        :showApproveButton="true"
        header="Add Content"
        approveText="Add"
        :approveLoading="isSubmitting"
        :approveAction="submit"
        subheader="Add a new content item to the knowledge base">

        <template #content>
            <div class="space-y-4">
                <!-- Breadcrumbs -->
                <Breadcrumbs :path="breadcrumbPath" />

                <Select
                    label="Type"
                    :search="false"
                    v-model="form.type"
                    :showAsterisk="true"
                    :options="contentTypes"
                    placeholder="Select content type"
                    :errorText="formState.getFormError('type')" />

                <Input
                    label="Title"
                    v-model="form.title"
                    :showAsterisk="true"
                    placeholder="Content Title"
                    :errorText="formState.getFormError('title')" />

                <Input
                    rows="4"
                    type="textarea"
                    label="Content"
                    placeholder="Content"
                    v-model="form.content"
                    :errorText="formState.getFormError('content')"/>

                <Select
                    label="State"
                    :search="false"
                    v-model="form.state"
                    :showAsterisk="true"
                    :options="stateOptions"
                    placeholder="Select state"
                    v-if="form.type === 'article'"
                    :errorText="formState.getFormError('state')" />

                <Select
                    :search="false"
                    label="Visibility"
                    :showAsterisk="true"
                    v-model="form.visibility"
                    :options="visibilityOptions"
                    v-if="form.type === 'article'"
                    placeholder="Select visibility"
                    :errorText="formState.getFormError('visibility')" />

                <Select
                    label="Help Center Collection"
                    placeholder="Select collection"
                    :options="helpCenterCollectionOptions"
                    v-model="form.help_center_collection_id"
                    v-if="form.type === 'article' && form.visibility === 'public'"
                    :errorText="formState.getFormError('help_center_collection_id')" />

                <div class="flex space-x-4">
                    <Input
                        type="checkbox"
                        inputLabel="Copilot"
                        v-model="form.copilot_enabled" />

                    <Input
                        type="checkbox"
                        inputLabel="AI Agent"
                        v-model="form.ai_agent_enabled" />

                    <Input
                        type="checkbox"
                        inputLabel="Help Center"
                        v-if="form.type === 'article'"
                        v-model="form.help_center_enabled" />
                </div>
            </div>
        </template>
    </Modal>
</template>

<script>
import axios from 'axios';
import Modal from '@Partials/Modal.vue';
import Input from '@Partials/Input.vue';
import Select from '@Partials/Select.vue';
import Button from '@Partials/Button.vue';
import Breadcrumbs from '@Partials/Breadcrumbs.vue';

export default {
    inject: ['formState', 'notificationState'],
    components: {
        Modal, Input, Select, Button, Breadcrumbs
    },
    props: {
        knowledgeBaseId: {
            type: String,
            required: true
        },
        folderId: {
            type: String,
            default: null
        },
        treeNodes: {
            type: Array,
            default: () => []
        },
        selectedNode: {
            type: Object,
            default: () => ({ id: 'all', title: 'All' })
        }
    },
    data() {
        return {
            isSubmitting: false,
            helpCenterCollections: [],
            contentTypes: [
                { label: 'Article', value: 'article' },
                { label: 'Snippet', value: 'snippet' }
            ],
            visibilityOptions: [
                { label: 'Public', value: 'public' },
                { label: 'Internal', value: 'internal' }
            ],
            stateOptions: [
                { label: 'Draft', value: 'draft' },
                { label: 'Active', value: 'active' },
                { label: 'Archived', value: 'archived' }
            ],
            form: {
                type: 'article',
                title: '',
                content: '',
                state: 'active',
                visibility: 'internal',
                ai_agent_enabled: true,
                copilot_enabled: true,
                help_center_enabled: false,
                help_center_collection_id: '',
                parent_id: this.folderId,
                knowledge_base_id: this.knowledgeBaseId
            }
        };
    },
    computed: {
        helpCenterCollectionOptions() {
            return [
                { label: 'No Collection', value: '' },
                ...this.helpCenterCollections.map(collection => ({
                    label: collection.name,
                    value: collection.id
                }))
            ];
        },
        breadcrumbPath() {
            if (!this.selectedNode || this.selectedNode.id === 'all') {
                return [{ id: 'all', title: 'All' }];
            }

            // Build the path by finding the selected node in the tree
            const path = [{ id: 'all', title: 'All' }];

            // Function to recursively find the path to the selected node
            const findPathToNode = (nodes, targetId, currentPath = []) => {
                for (const node of nodes) {
                    const newPath = [...currentPath, { id: node.id, title: node.title }];
                    if (node.id === targetId) {
                        return newPath;
                    }
                    if (node.subfolders && node.subfolders.length > 0) {
                        const foundPath = findPathToNode(node.subfolders, targetId, newPath);
                        if (foundPath) {
                            return foundPath;
                        }
                    }
                }
                return null;
            };

            const foundPath = findPathToNode(this.treeNodes, this.selectedNode.id);
            if (foundPath) {
                return [...path, ...foundPath];
            }

            return path; // Fallback to "All" if the path isn't found
        }
    },
    watch: {
        folderId(newFolderId) {
            this.form.parent_id = newFolderId;
        },
        'form.type'(newType) {
            // Reset visibility and help center fields for non-articles
            if (newType !== 'article') {
                this.form.visibility = null;
                this.form.help_center_enabled = false;
                this.form.help_center_collection_id = '';
            }
        },
        'form.visibility'(newVisibility) {
            // Reset help center fields if visibility changes to internal
            if (newVisibility === 'internal') {
                this.form.help_center_enabled = false;
                this.form.help_center_collection_id = '';
            }
        }
    },
    methods: {
        showModal() {
            this.reset();
            this.$refs.modal.showModal();
            this.fetchHelpCenterCollections();
        },
        reset() {
            this.form.type = 'article';
            this.form.title = '';
            this.form.content = '';
            this.form.visibility = 'internal';
            this.form.state = 'active';
            this.form.ai_agent_enabled = true;
            this.form.copilot_enabled = true;
            this.form.help_center_enabled = false;
            this.form.help_center_collection_id = '';
            this.formState.hideFormErrors();
        },
        async fetchHelpCenterCollections() {
            try {
                const response = await axios.get(`/api/help-center-collections`, {
                    params: {
                        knowledge_base_id: this.knowledgeBaseId
                    }
                });
                this.helpCenterCollections = response.data.data || [];
            } catch (error) {
                const message = error?.response?.data?.message || error?.message || 'Failed to fetch Help Center collections';
                this.notificationState.showWarningNotification(message);
                console.error('Failed to fetch Help Center collections:', error);
            }
        },
        async submit(hideModal) {
            this.formState.hideFormErrors();

            // Validate required fields
            if (!this.form.title.trim()) {
                this.formState.setFormError('title', 'Title is required');
            }
            if (this.form.type === 'article' && !this.form.visibility) {
                this.formState.setFormError('visibility', 'Visibility is required for articles');
            }
            if (this.form.type === 'article' && !this.form.state) {
                this.formState.setFormError('state', 'State is required');
            }
            if (this.form.type === 'article' && this.form.visibility === 'public' && this.form.help_center_enabled && !this.form.help_center_collection_id) {
                this.formState.setFormError('help_center_collection_id', 'Help Center Collection is required for public articles added to Help Center');
            }

            if (this.formState.hasErrors) return;

            this.isSubmitting = true;
            try {
                const payload = {
                    type: this.form.type,
                    title: this.form.title,
                    content: this.form.content || '',
                    state: this.form.type === 'article' ? this.form.state : null,
                    ai_agent_enabled: this.form.ai_agent_enabled,
                    copilot_enabled: this.form.copilot_enabled,
                    help_center_enabled: this.form.help_center_enabled,
                    help_center_collection_id: this.form.help_center_collection_id || null,
                    parent_id: this.form.parent_id,
                    knowledge_base_id: this.form.knowledge_base_id
                };

                // Only include visibility for articles
                if (this.form.type === 'article') {
                    payload.visibility = this.form.visibility;
                }

                await axios.post(`/api/content-items`, payload);
                this.notificationState.showSuccessNotification('Content added successfully.');
                this.$emit('created');
                hideModal();
            } catch (error) {
                const message = error?.response?.data?.message || error?.message || 'Failed to add content';
                this.notificationState.showWarningNotification(message);
                this.formState.setServerFormErrors(error);
                console.error('Failed to add content:', error);
            } finally {
                this.isSubmitting = false;
            }
        }
    }
};
</script>
