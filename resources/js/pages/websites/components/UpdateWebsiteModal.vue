<template>
    <Modal
        size="md"
        ref="modal"
        :showFooter="true"
        :dismissable="true"
        :scrollOnContent="false"
        :showApproveButton="true"
        :approveLoading="isUpdating"
        header="Update Website"
        approveText="Update Website"
        :approveAction="updateWebsite"
        subheader="Modify the website's details">
        <template #content>
            <div class="space-y-4">
                <Input
                    v-model="form.url"
                    :showAsterisk="true"
                    label="Website URL"
                    placeholder="https://example.com"
                    :errorText="formState.getFormError('url')" />
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
    name: 'UpdateWebsiteModal',
    inject: ['formState', 'notificationState'],
    components: { Modal, Input, Switch },
    data() {
        return {
            isUpdating: false,
            website: null,
            form: {
                url: '',
                ai_searchable: true
            }
        };
    },
    methods: {
        showModal(website) {
            this.reset();
            this.website = website;
            this.form.url = website.url;
            this.form.ai_searchable = website.ai_searchable;
            this.$refs.modal.showModal();
        },
        reset() {
            this.form.url = '';
            this.form.ai_searchable = true;
            this.website = null;
        },
        async updateWebsite(hideModal) {
            this.formState.hideFormErrors();

            if (this.form.url.trim() === '') {
                this.formState.setFormError('url', 'Website URL is required');
            }

            if (this.formState.hasErrors) return;

            this.isUpdating = true;

            try {
                const url = this.website._links.update;
                const payload = {
                    url: this.form.url,
                    ai_searchable: this.form.ai_searchable
                };

                await axios.put(url, payload);

                this.notificationState.showSuccessNotification('Website updated');
                this.$emit('updated');
                hideModal();
            } catch (error) {
                const message = error?.response?.data?.message || error?.message || 'Something went wrong updating the website';
                this.notificationState.showWarningNotification(message);
                this.formState.setServerFormErrors(error);
                console.error('Failed to update website:', error);
            } finally {
                this.isUpdating = false;
            }
        }
    }
};
</script>
