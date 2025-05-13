<template>
    <Modal
        size="sm"
        ref="modal"
        :showFooter="true"
        :dismissable="true"
        :scrollOnContent="false"
        :showApproveButton="true"
        header="Create New User"
        approveText="Create User"
        :approveAction="createUser"
        :approveLoading="isCreating"
        subheader="Add a new user to the platform">

        <template #trigger="props">
            <slot :showModal="props.showModal"></slot>
        </template>

        <template #content>
            <div class="space-y-4">
                <Input
                    v-model="form.name"
                    :showAsterisk="true"
                    label="Full Name"
                    placeholder="Jane Doe"
                    :errorText="formState.getFormError('name')" />

                <Input
                    type="email"
                    v-model="form.email"
                    :showAsterisk="true"
                    label="Email Address"
                    placeholder="jane@example.com"
                    :errorText="formState.getFormError('email')" />

                <Select
                    v-if="!organizationId"
                    label="Type"
                    :search="false"
                    v-model="form.type"
                    :showAsterisk="true"
                    :options="userTypes"
                    placeholder="Select user type"
                    :errorText="formState.getFormError('type')" />

                <Select
                    v-if="!organizationId && form.type === 'regular'"
                    v-model="form.organization_id"
                    :options="organizationOptions"
                    :showAsterisk="true"
                    label="Organization"
                    placeholder="Select organization"
                    :errorText="formState.getFormError('organization_id')"
                    :disabled="isLoadingOrganizations" />

                <Select
                    v-if="form.type === 'regular'"
                    v-model="form.role_id"
                    :options="roleOptions"
                    :showAsterisk="true"
                    label="Role"
                    placeholder="Select role"
                    :errorText="formState.getFormError('role_id')"
                    :disabled="isLoadingRoles || !form.organization_id" />
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

export default {
    components: { Modal, Input, Select, Button },
    inject: ['formState', 'notificationState'],
    data() {
        return {
            isCreating: false,
            isLoadingOrganizations: false,
            isLoadingRoles: false,
            form: {
                name: '',
                email: '',
                type: '', // Will be set in reset
                organization_id: null, // Will be set in reset
                role_id: null // New field for role selection
            },
            userTypes: [
                { label: 'Super Admin', value: 'super_admin' },
                { label: 'Regular', value: 'regular' }
            ],
            organizationOptions: [],
            roleOptions: []
        };
    },
    computed: {
        organizationId() {
            return this.$route.query.organization_id || null;
        }
    },
    watch: {
        organizationId(newValue) {
            // Update form values when organizationId changes
            if (newValue) {
                this.form.type = 'regular';
                this.form.organization_id = newValue;
                this.fetchRoles(newValue); // Fetch roles for the organization
            } else {
                this.form.type = '';
                this.form.organization_id = null;
                this.form.role_id = null;
                this.roleOptions = [];
            }
        },
        'form.type'(newValue) {
            // Reset organization_id and role_id when type changes
            if (newValue !== 'regular') {
                if (!this.organizationId) {
                    this.form.organization_id = null;
                }
                this.form.role_id = null;
                this.roleOptions = [];
            }
        },
        'form.organization_id'(newValue) {
            // Fetch roles when organization_id changes (in super admin mode)
            if (newValue && this.form.type === 'regular') {
                this.fetchRoles(newValue);
            } else {
                this.form.role_id = null;
                this.roleOptions = [];
            }
        }
    },
    methods: {
        showModal() {
            this.reset();
            if (!this.organizationId) {
                this.fetchOrganizations();
            } else {
                this.fetchRoles(this.organizationId);
            }
            this.$refs.modal.showModal();
        },
        reset() {
            this.form.name = '';
            this.form.email = '';
            this.form.type = this.organizationId ? 'regular' : '';
            this.form.organization_id = this.organizationId || null;
            this.form.role_id = null;
            this.roleOptions = [];
        },
        async fetchOrganizations() {
            try {
                this.isLoadingOrganizations = true;
                const response = await axios.get('/api/organizations');
                this.organizationOptions = (response.data?.data ?? []).map(org => ({
                    label: org.name,
                    value: org.id
                }));
            } catch (error) {
                const message = error?.response?.data?.message || error?.message || 'Failed to load organizations';
                this.notificationState.showWarningNotification(message);
                console.error('Failed to load organizations:', error);
            } finally {
                this.isLoadingOrganizations = false;
            }
        },
        async fetchRoles(organizationId) {
            if (!organizationId) return;

            try {
                this.isLoadingRoles = true;
                const response = await axios.get('/api/roles', {
                    params: {
                        organization_id: organizationId
                    }
                });
                this.roleOptions = (response.data?.data ?? []).map(role => ({
                    label: role.name,
                    value: role.id
                }));
            } catch (error) {
                const message = error?.response?.data?.message || error?.message || 'Failed to load roles';
                this.notificationState.showWarningNotification(message);
                console.error('Failed to load roles:', error);
            } finally {
                this.isLoadingRoles = false;
            }
        },
        async createUser(hideModal) {
            this.formState.hideFormErrors();

            // Validate required fields
            if (this.form.name.trim() === '') this.formState.setFormError('name', 'Name is required');
            if (this.form.email.trim() === '') this.formState.setFormError('email', 'Email is required');

            // Validate type and organization_id if organizationId is not set
            if (!this.organizationId) {
                if (this.form.type === '') this.formState.setFormError('type', 'User type is required');
                if (this.form.type === 'regular' && !this.form.organization_id) {
                    this.formState.setFormError('organization_id', 'Organization is required');
                }
            }

            // Validate role_id if type is regular
            if (this.form.type === 'regular' && !this.form.role_id) {
                this.formState.setFormError('role_id', 'Role is required');
            }

            if (this.formState.hasErrors) return;

            this.isCreating = true;

            try {
                // Ensure type and organization_id are correct before submission
                const payload = {
                    ...this.form,
                    type: this.organizationId ? 'regular' : this.form.type,
                    organization_id: this.organizationId || this.form.organization_id
                };

                await axios.post('/api/users', payload);
                this.notificationState.showSuccessNotification('User created. An email has been sent to the user to set their password.');

                this.$emit('created');
                hideModal();
            } catch (error) {
                const message = error?.response?.data?.message || error?.message || 'Something went wrong trying to create the user';
                this.notificationState.showWarningNotification(message);
                this.formState.setServerFormErrors(error);

                console.error('Failed to create user:', error);
            } finally {
                this.isCreating = false;
            }
        }
    }
};
</script>
