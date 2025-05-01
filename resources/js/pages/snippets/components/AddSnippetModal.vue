<template>
    <Modal
        size="md"
        ref="modal"
        :showFooter="true"
        :dismissable="true"
        :scrollOnContent="false"
        :showApproveButton="true"
        header="Create New Snippet"
        approveText="Create Snippet"
        :approveAction="createSnippet"
        :approveLoading="isCreating"
        subheader="Add a new snippet to a knowledge base">
        <template #trigger="props">
            <slot :showModal="props.showModal"></slot>
        </template>
        <template #content>
            <div class="space-y-4">
                <Select
                    v-model="form.organization_id"
                    :options="organizationOptions"
                    :showAsterisk="true"
                    label="Organization"
                    placeholder="Select organization"
                    :errorText="formState.getFormError('organization_id')"
                    @change="fetchKnowledgeBases" />
                <Select
                    v-model="form.knowledge_base_id"
                    :options="knowledgeBaseOptions"
                    :showAsterisk="true"
                    label="Knowledge Base"
                    placeholder="Select knowledge base"
                    :errorText="formState.getFormError('knowledge_base_id')" />
                <Input
                    v-model="form.title"
                    :showAsterisk="true"
                    label="Snippet Title"
                    placeholder="Enter snippet title"
                    :errorText="formState.getFormError('title')" />
                <Input
                    type="textarea"
                    v-model="form.content"
                    :showAsterisk="true"
                    label="Content"
                    placeholder="Enter snippet content"
                    :errorText="formState.getFormError('content')"
                    rows="6" />
                <Switch
                    size="sm"
                    v-model="form.ai_searchable"
                    suffixText="AI Searchable" />
            </div>
        </template>
    </Modal>
</template>

<script>
import axios from 'axios';
import Modal from '@Partials/Modal.vue';
import Input from '@Partials/Input.vue';
import Select from '@Partials/Select.vue';
import Switch from '@Partials/Switch.vue';

export default {
    components: { Modal, Input, Select, Switch },
    inject: ['formState', 'notificationState'],
    data() {
        return {
            isCreating: false,
            form: {
                organization_id: null,
                knowledge_base_id: null,
                title: '',
                content: '',
                ai_searchable: true
            },
            organizationOptions: [],
            knowledgeBaseOptions: []
        };
    },
    methods: {
        showModal() {
            this.reset();
            this.fetchOrganizations();
            this.$refs.modal.showModal();
        },
        reset() {
            this.form.organization_id = null;
            this.form.knowledge_base_id = null;
            this.form.title = '';
            this.form.content = '';
            this.form.ai_searchable = true;
            this.knowledgeBaseOptions = [];
        },
        async fetchOrganizations() {
            try {
                const response = await axios.get('/api/organizations');
                this.organizationOptions = (response.data?.data ?? []).map(org => ({
                    label: org.name,
                    value: org.id
                }));
            } catch (error) {
                console.error('Failed to load organizations:', error);
            }
        },
        async fetchKnowledgeBases() {
            if (!this.form.organization_id) {
                this.knowledgeBaseOptions = [];
                this.form.knowledge_base_id = null;
                return;
            }
            try {
                const response = await axios.get('/api/knowledge-bases', {
                    params: { organization_id: this.form.organization_id }
                });
                this.knowledgeBaseOptions = (response.data?.data ?? []).map(kb => ({
                    label: kb.name,
                    value: kb.id
                }));
            } catch (error) {
                console.error('Failed to load knowledge bases:', error);
            }
        },
        async createSnippet(hideModal) {
            this.formState.hideFormErrors();

            if (!this.form.organization_id) {
                this.formState.setFormError('organization_id', 'Organization is required');
            }
            if (!this.form.knowledge_base_id) {
                this.formState.setFormError('knowledge_base_id', 'Knowledge base is required');
            }
            if (this.form.title.trim() === '') {
                this.formState.setFormError('title', 'Snippet title is required');
            }
            if (this.form.content.trim() === '') {
                this.formState.setFormError('content', 'Snippet content is required');
            }

            if (this.formState.hasErrors) return;

            this.isCreating = true;

            try {
                const payload = { ...this.form };
                await axios.post('/api/snippets', payload);

                this.notificationState.showSuccessNotification('Snippet created');
                this.$emit('created');
                hideModal();
            } catch (error) {
                const message = error?.response?.data?.message || error?.message || 'Something went wrong trying to create the snippet';
                this.notificationState.showWarningNotification(message);
                this.formState.setServerFormErrors(error);
                console.error('Failed to create snippet:', error);
            } finally {
                this.isCreating = false;
            }
        }
    }
};
</script>
