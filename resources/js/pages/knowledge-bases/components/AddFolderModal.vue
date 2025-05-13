<template>
    <Modal
        size="md"
        ref="modal"
        :showFooter="true"
        :dismissable="true"
        :scrollOnContent="false"
        :showApproveButton="true"
        header="Add Folder"
        approveText="Add"
        :approveLoading="isSubmitting"
        :approveAction="submit"
        subheader="Add a new folder to the knowledge base">

        <template #content>
            <div class="space-y-4">
                <!-- Breadcrumbs -->
                <Breadcrumbs :path="breadcrumbPath" />

                <Input
                    v-model="form.title"
                    :showAsterisk="true"
                    label="Folder Name"
                    placeholder="Folder Name"
                    :errorText="formState.getFormError('title')" />
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
        parentId: {
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
            form: {
                title: '',
                type: 'folder',
                parent_id: this.parentId,
                knowledge_base_id: this.knowledgeBaseId
            }
        };
    },
    computed: {
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
        parentId(newParentId) {
            this.form.parent_id = newParentId;
        }
    },
    methods: {
        showModal() {
            this.reset();
            this.$refs.modal.showModal();
        },
        reset() {
            this.form.title = '';
            this.formState.hideFormErrors();
        },
        async submit(hideModal) {
            this.formState.hideFormErrors();

            if (!this.form.title.trim()) {
                this.formState.setFormError('title', 'Folder name is required');
                return;
            }

            this.isSubmitting = true;
            try {
                const payload = {
                    type: this.form.type,
                    title: this.form.title,
                    parent_id: this.form.parent_id,
                    knowledge_base_id: this.form.knowledge_base_id
                };

                await axios.post(`/api/content-items`, payload);
                this.notificationState.showSuccessNotification('Folder added successfully.');
                this.$emit('created');
                hideModal();
            } catch (error) {
                const message = error?.response?.data?.message || error?.message || 'Failed to add folder';
                this.notificationState.showWarningNotification(message);
                this.formState.setServerFormErrors(error);
                console.error('Failed to add folder:', error);
            } finally {
                this.isSubmitting = false;
            }
        }
    }
};
</script>
