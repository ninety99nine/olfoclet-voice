<template>

    <Modal
        size="md"
        ref="modal"
        :showFooter="true"
        :dismissable="true"
        :scrollOnContent="false"
        :showApproveButton="true"
        :approveLoading="isUpdating"
        header="Update Copilot"
        approveText="Update Copilot"
        :approveAction="updateCopilot"
        subheader="Modify the Copilot's details">

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
    import SelectTags from '@Partials/SelectTags.vue';
    import Switch from '@Partials/Switch.vue';

    export default {
        name: 'UpdateCopilotModal',
        inject: ['formState', 'notificationState'],
        components: { Modal, Input, SelectTags, Switch },
        data() {
            return {
                isUpdating: false,
                copilot: null,
                form: {
                    name: '',
                    description: '',
                    knowledge_base_ids: [],
                    user_ids: [],
                    is_active: true
                },
                knowledgeBaseOptions: [],
                userOptions: []
            };
        },
        methods: {
            showModal(copilot) {
                this.reset();
                this.copilot = copilot;
                this.form.name = copilot.name;
                this.form.description = copilot.description || '';
                this.form.knowledge_base_ids = copilot.knowledge_bases?.map(kb => kb.id) || [];
                this.form.user_ids = copilot.users?.map(user => user.id) || [];
                this.form.is_active = copilot.is_active;
                this.fetchKnowledgeBases(copilot.organization_id);
                this.fetchUsers();
                this.$refs.modal.showModal();
            },
            reset() {
                this.form.name = '';
                this.form.description = '';
                this.form.knowledge_base_ids = [];
                this.form.user_ids = [];
                this.form.is_active = true;
                this.knowledgeBaseOptions = [];
                this.userOptions = [];
                this.copilot = null;
            },
            async fetchKnowledgeBases(organizationId) {
                if (!organizationId) {
                    this.knowledgeBaseOptions = [];
                    this.form.knowledge_base_ids = [];
                    return;
                }
                try {
                    const response = await axios.get('/api/knowledge-bases', {
                        params: { organization_id: organizationId }
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
            async updateCopilot(hideModal) {
                this.formState.hideFormErrors();

                if (this.form.name.trim() === '') {
                    this.formState.setFormError('name', 'Copilot name is required');
                }

                if (this.formState.hasErrors) return;

                this.isUpdating = true;

                try {
                    const url = this.copilot._links.update;
                    const payload = {
                        name: this.form.name,
                        description: this.form.description,
                        knowledge_base_ids: Array.isArray(this.form.knowledge_base_ids) ? this.form.knowledge_base_ids : (this.form.knowledge_base_ids ? [this.form.knowledge_base_ids] : []),
                        user_ids: Array.isArray(this.form.user_ids) ? this.form.user_ids : (this.form.user_ids ? [this.form.user_ids] : []),
                        is_active: this.form.is_active
                    };

                    await axios.put(url, payload);

                    this.notificationState.showSuccessNotification('Copilot updated');
                    this.$emit('updated');
                    hideModal();
                } catch (error) {
                    const message = error?.response?.data?.message || error?.message || 'Something went wrong updating the copilot';
                    this.notificationState.showWarningNotification(message);
                    this.formState.setServerFormErrors(error);

                    console.error('Failed to update copilot:', error);
                } finally {
                    this.isUpdating = false;
                }
            }
        }
    };

</script>
