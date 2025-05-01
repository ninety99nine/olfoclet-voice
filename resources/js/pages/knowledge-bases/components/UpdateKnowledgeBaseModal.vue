<template>
    <Modal
        size="sm"
        ref="modal"
        :showFooter="true"
        :dismissable="true"
        :scrollOnContent="false"
        :showApproveButton="true"
        :approveLoading="isUpdating"
        header="Update Knowledge Base"
        approveText="Update Knowledge Base"
        :approveAction="updateKnowledgeBase"
        subheader="Modify the knowledge base's details">
        <template #content>
            <div class="space-y-4">
                <Input
                    v-model="form.name"
                    :showAsterisk="true"
                    label="Knowledge Base Name"
                    placeholder="Enter knowledge base name"
                    :errorText="formState.getFormError('name')" />
            </div>
        </template>
    </Modal>
</template>

<script>
import axios from 'axios';
import Modal from '@Partials/Modal.vue';
import Input from '@Partials/Input.vue';

export default {
    name: 'UpdateKnowledgeBaseModal',
    inject: ['formState', 'notificationState'],
    components: { Modal, Input },
    data() {
        return {
            isUpdating: false,
            knowledgeBase: null,
            form: {
                name: ''
            }
        };
    },
    methods: {
        showModal(knowledgeBase) {
            this.reset();
            this.knowledgeBase = knowledgeBase;
            this.form.name = knowledgeBase.name;
            this.$refs.modal.showModal();
        },
        reset() {
            this.form.name = '';
            this.knowledgeBase = null;
        },
        async updateKnowledgeBase(hideModal) {
            this.formState.hideFormErrors();

            if (this.form.name.trim() === '') {
                this.formState.setFormError('name', 'Knowledge base name is required');
            }

            if (this.formState.hasErrors) return;

            this.isUpdating = true;

            try {
                const url = this.knowledgeBase._links.update;
                const payload = {
                    name: this.form.name
                };

                await axios.put(url, payload);

                this.notificationState.showSuccessNotification('Knowledge base updated');
                this.$emit('updated');
                hideModal();
            } catch (error) {
                const message = error?.response?.data?.message || error?.message || 'Something went wrong updating the knowledge base';
                this.notificationState.showWarningNotification(message);
                this.formState.setServerFormErrors(error);
                console.error('Failed to update knowledge base:', error);
            } finally {
                this.isUpdating = false;
            }
        }
    }
};
</script>
