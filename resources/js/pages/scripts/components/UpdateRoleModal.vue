<!-- UpdateRoleModal.vue -->
<template>

    <Modal
        size="sm"
        ref="modal"
        :showFooter="true"
        :dismissable="true"
        :scrollOnContent="false"
        :showApproveButton="true"
        :approveLoading="isUpdating"
        header="Update Role"
        approveText="Update Role"
        :approveAction="updateRole"
        subheader="Modify the role's details">

        <template #content>

            <div class="space-y-4">

                <Input
                    v-model="form.name"
                    :showAsterisk="true"
                    label="Role Name"
                    placeholder="e.g. Supervisor"
                    :errorText="formState.getFormError('name')" />

                <Select
                    v-model="form.organization_id"
                    :options="organizationOptions"
                    :showAsterisk="true"
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
        name: 'UpdateRoleModal',
        inject: ['formState', 'notificationState'],
        components: { Modal, Input, Select },
        data() {
            return {
                isUpdating: false,
                role: null,
                form: {
                    name: '',
                    organization_id: null,
                },
                organizationOptions: []
            };
        },
        methods: {
            showModal(role) {
                this.reset();
                this.role = role;
                this.form.name = role.name;
                this.form.organization_id = role.organization_id;
                this.fetchOrganizations();
                this.$refs.modal.showModal();
            },
            reset() {
                this.form.name = '';
                this.form.organization_id = null;
                this.role = null;
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
            async updateRole(hideModal) {
                this.formState.hideFormErrors();

                if (this.form.name.trim() === '') this.formState.setFormError('name', 'Role name is required');
                if (!this.form.organization_id) this.formState.setFormError('organization_id', 'Organization is required');

                if (this.formState.hasErrors) return;

                this.isUpdating = true;

                try {
                    const url = this.role._links.update_role;
                    const payload = {
                        name: this.form.name,
                        organization_id: this.form.organization_id
                    };

                    await axios.put(url, payload);

                    this.notificationState.showSuccessNotification('Role updated');
                    this.$emit('updated');
                    hideModal();

                } catch (error) {
                    const message = error?.response?.data?.message || error?.message || 'Something went wrong updating the role';
                    this.notificationState.showWarningNotification(message);
                    this.formState.setServerFormErrors(error);

                    console.error('Failed to update role:', error);
                } finally {
                    this.isUpdating = false;
                }
            }
        }
    };

</script>
