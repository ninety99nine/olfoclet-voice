<template>
    <Modal
        size="sm"
        ref="modal"
        :showFooter="true"
        :dismissable="true"
        :scrollOnContent="false"
        :showApproveButton="true"
        header="Create New Knowledge Base"
        approveText="Create Knowledge Base"
        :approveAction="createKnowledgeBase"
        :approveLoading="isCreating"
        subheader="Add a new knowledge base to the platform">
        <template #trigger="props">
            <slot :showModal="props.showModal"></slot>
        </template>
        <template #content>
            <div class="space-y-4">
                <Input
                    v-model="form.name"
                    :showAsterisk="true"
                    label="Knowledge Base Name"
                    placeholder="Enter knowledge base name"
                    :errorText="formState.getFormError('name')" />
                <Select
                    v-model="form.organization_id"
                    :options="organizationOptions"
                    label="Organization"
                    placeholder="Select organization"
                    :errorText="formState.getFormError('organization_id')" />
            </div>
        </template>
    </Modal>
</template>

<script>
import axios from 'axios';
import Modal from '@Partials/Modal.vue';
import Input from '@Partials/Input.vue';
import Select from '@Partials/Select.vue';

export default {
    components: { Modal, Input, Select },
    inject: ['formState', 'notificationState'],
    data() {
        return {
            isCreating: false,
            form: {
                name: '',
                organization_id: null
            },
            organizationOptions: []
        };
    },
    methods: {
        showModal() {
            this.reset();
            this.fetchOrganizations();
            this.$refs.modal.showModal();
        },
        reset() {
            this.form.name = '';
            this.form.organization_id = null;
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
        async createKnowledgeBase(hideModal) {
            this.formState.hideFormErrors();

            if (this.form.name.trim() === '') {
                this.formState.setFormError('name', 'Knowledge base name is required');
            }
            if (!this.form.organization_id) {
                this.formState.setFormError('organization_id', 'Organization is required');
            }

            if (this.formState.hasErrors) return;

            this.isCreating = true;

            try {
                const payload = { ...this.form };
                await axios.post('/api/knowledge-bases', payload, {
                    params: { organization_id: this.form.organization_id }
                });

                this.notificationState.showSuccessNotification('Knowledge base created');
                this.$emit('created');
                hideModal();
            } catch (error) {
                const message = error?.response?.data?.message || error?.message || 'Something went wrong trying to create the knowledge base';
                this.notificationState.showWarningNotification(message);
                this.formState.setServerFormErrors(error);
                console.error('Failed to create knowledge base:', error);
            } finally {
                this.isCreating = false;
            }
        }
    }
};
</script>
