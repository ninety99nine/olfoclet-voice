<template>

    <Modal
        size="md"
        ref="modal"
        :showFooter="true"
        :dismissable="true"
        :scrollOnContent="false"
        :showApproveButton="true"
        header="Create New Copilot"
        approveText="Create Copilot"
        :approveAction="createCopilot"
        :approveLoading="isCreating"
        subheader="Add a new AI Copilot to the platform">

        <template #trigger="props">
            <slot :showModal="props.showModal"></slot>
        </template>

        <template #content>

            <div class="space-y-4">

                <Input
                    v-model="form.name"
                    :showAsterisk="true"
                    label="Copilot Name"
                    placeholder="Support Copilot"
                    :errorText="formState.getFormError('name')" />

                <Input
                    type="textarea"
                    v-model="form.description"
                    label="Description"
                    placeholder="Enter description"
                    rows="3"
                    :errorText="formState.getFormError('description')" />

                <Select
                    v-model="form.organization_id"
                    :options="organizationOptions"
                    :showAsterisk="true"
                    label="Organization"
                    placeholder="Select organization"
                    :errorText="formState.getFormError('organization_id')"
                    @change="fetchKnowledgeBases" />

                <SelectTags
                    isDraggable
                    label="Knowledge Bases"
                    v-model="form.knowledge_base_ids"
                    :options="knowledgeBaseOptions"
                    placeholder="Select knowledge bases"
                    :searchableFields="['label', 'value']"
                    :errorText="formState.getFormError('knowledge_base_ids')" />

                <SelectTags
                    isDraggable
                    label="Assigned Users"
                    v-model="form.user_ids"
                    :options="userOptions"
                    placeholder="Select users"
                    :searchableFields="['label', 'value']"
                    :errorText="formState.getFormError('user_ids')" />

                <Switch
                    size="sm"
                    v-model="form.is_active"
                    suffixText="Active" />

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
    import SelectTags from '@Partials/SelectTags.vue';

    export default {
        components: { Modal, Input, Select, Switch, SelectTags },
        inject: ['formState', 'notificationState'],
        data() {
            return {
                isCreating: false,
                form: {
                    name: '',
                    description: '',
                    organization_id: null,
                    knowledge_base_ids: [],
                    user_ids: [],
                    is_active: true
                },
                organizationOptions: [],
                knowledgeBaseOptions: [],
                userOptions: []
            };
        },
        methods: {
            showModal() {
                this.reset();
                this.fetchOrganizations();
                this.fetchUsers();
                this.$refs.modal.showModal();
            },
            reset() {
                this.form.name = '';
                this.form.description = '';
                this.form.organization_id = null;
                this.form.knowledge_base_ids = [];
                this.form.user_ids = [];
                this.form.is_active = true;
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
                    this.form.knowledge_base_ids = [];
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
            async fetchUsers() {
                try {
                    const response = await axios.get('/api/users');
                    this.userOptions = (response.data?.data ?? []).map(user => ({
                        label: user.name,
                        value: user.id
                    }));
                } catch (error) {
                    console.error('Failed to load users:', error);
                }
            },
            async createCopilot(hideModal) {
                this.formState.hideFormErrors();

                if (this.form.name.trim() === '') {
                    this.formState.setFormError('name', 'Copilot name is required');
                }
                if (!this.form.organization_id) {
                    this.formState.setFormError('organization_id', 'Organization is required');
                }

                if (this.formState.hasErrors) return;

                this.isCreating = true;

                try {
                    // Ensure knowledge_base_ids and user_ids are arrays
                    const payload = {
                        ...this.form,
                        knowledge_base_ids: Array.isArray(this.form.knowledge_base_ids) ? this.form.knowledge_base_ids : (this.form.knowledge_base_ids ? [this.form.knowledge_base_ids] : []),
                        user_ids: Array.isArray(this.form.user_ids) ? this.form.user_ids : (this.form.user_ids ? [this.form.user_ids] : [])
                    };

                    await axios.post('/api/copilots', payload);
                    this.notificationState.showSuccessNotification('Copilot created');

                    this.$emit('created');
                    hideModal();
                } catch (error) {
                    const message = error?.response?.data?.message || error?.message || 'Something went wrong trying to create the copilot';
                    this.notificationState.showWarningNotification(message);
                    this.formState.setServerFormErrors(error);

                    console.error('Failed to create copilot:', error);
                } finally {
                    this.isCreating = false;
                }
            }
        }
    };

</script>
