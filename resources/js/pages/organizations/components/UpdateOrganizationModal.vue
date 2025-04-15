<template>
    <Modal
        size="sm"
        ref="modal"
        :showFooter="true"
        :dismissable="true"
        :scrollOnContent="false"
        :showApproveButton="true"
        :approveLoading="isUpdating"
        header="Update Organization"
        approveText="Update Organization"
        :approveAction="updateOrganization"
        subheader="Modify the organization's details">

        <template #content>

            <div class="space-y-4">

                <Input
                    v-model="form.name"
                    :showAsterisk="true"
                    label="Organization Name"
                    placeholder="Acme Telecom"
                    :errorText="formState.getFormError('name')"
                />

                <SelectCountry
                    label="Country"
                    :showAsterisk="true"
                    v-model="form.country"
                    :errorText="formState.getFormError('country')"
                />

            </div>

        </template>

    </Modal>

</template>

<script>

    import axios from 'axios';
    import Modal from '@Partials/Modal.vue';
    import Input from '@Partials/Input.vue';
    import SelectCountry from '@Partials/SelectCountry.vue';

    export default {
        name: 'UpdateOrganizationModal',
        inject: ['formState', 'notificationState'],
        components: { Modal, Input, SelectCountry },
        data() {
            return {
                isUpdating: false,
                organization: null,
                form: {
                name: '',
                country: ''
                }
            };
        },
        methods: {
            showModal(organization) {
                this.reset();
                this.organization = organization;
                this.form.name = organization.name;
                this.form.country = organization.country;
                this.$refs.modal.showModal();
            },
            reset() {
                this.form.name = '';
                this.form.country = '';
                this.organization = null;
            },
            async updateOrganization(hideModal) {
            this.formState.hideFormErrors();

            if (this.form.name.trim() === '') this.formState.setFormError('name', 'Organization name is required');
            if (!this.form.country) this.formState.setFormError('country', 'Select a country');

            if (this.formState.hasErrors) return;

            this.isUpdating = true;

            try {
                const url = this.organization._links.update_organization;
                await axios.put(url, this.form);

                this.notificationState.showSuccessNotification('Organization updated');
                this.$emit('updated');
                hideModal();

            } catch (error) {
                const message = error?.response?.data?.message || error?.message || 'Something went wrong updating the organization';
                this.notificationState.showWarningNotification(message);
                this.formState.setServerFormErrors(error);

                console.error('Failed to update organization:', error);
            } finally {
                this.isUpdating = false;
            }
        }
        }
    };

</script>
