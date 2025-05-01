<template>
    <Modal
        size="md"
        ref="modal"
        :showFooter="true"
        :dismissable="true"
        :scrollOnContent="false"
        :showApproveButton="true"
        :approveLoading="isUpdating"
        header="Update Snippet"
        approveText="Update Snippet"
        :approveAction="updateSnippet"
        subheader="Modify the snippet's details">
        <template #content>
            <div class="space-y-4">
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
import Switch from '@Partials/Switch.vue';

export default {
    name: 'UpdateSnippetModal',
    inject: ['formState', 'notificationState'],
    components: { Modal, Input, Switch },
    data() {
        return {
            isUpdating: false,
            snippet: null,
            form: {
                title: '',
                content: '',
                ai_searchable: true
            }
        };
    },
    methods: {
        showModal(snippet) {
            this.reset();
            this.snippet = snippet;
            this.form.title = snippet.title;
            this.form.content = snippet.content;
            this.form.ai_searchable = snippet.ai_searchable;
            this.$refs.modal.showModal();
        },
        reset() {
            this.form.title = '';
            this.form.content = '';
            this.form.ai_searchable = true;
            this.snippet = null;
        },
        async updateSnippet(hideModal) {
            this.formState.hideFormErrors();

            if (this.form.title.trim() === '') {
                this.formState.setFormError('title', 'Snippet title is required');
            }
            if (this.form.content.trim() === '') {
                this.formState.setFormError('content', 'Snippet content is required');
            }

            if (this.formState.hasErrors) return;

            this.isUpdating = true;

            try {
                const url = this.snippet._links.update;
                const payload = {
                    title: this.form.title,
                    content: this.form.content,
                    ai_searchable: this.form.ai_searchable
                };

                await axios.put(url, payload);

                this.notificationState.showSuccessNotification('Snippet updated');
                this.$emit('updated');
                hideModal();
            } catch (error) {
                const message = error?.response?.data?.message || error?.message || 'Something went wrong updating the snippet';
                this.notificationState.showWarningNotification(message);
                this.formState.setServerFormErrors(error);
                console.error('Failed to update snippet:', error);
            } finally {
                this.isUpdating = false;
            }
        }
    }
};
</script>
