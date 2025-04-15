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

                <Input
                    type="password"
                    v-model="form.password"
                    :showAsterisk="true"
                    label="Password"
                    placeholder="Enter password"
                    :errorText="formState.getFormError('password')" />

                <div class="flex justify-end">
                    <Button
                        size="xs"
                        type="ghost"
                        :action="generatePassword">
                        <span>Generate Password</span>
                    </Button>
                </div>

                <Select
                    label="Type"
                    :search="false"
                    v-model="form.type"
                    :showAsterisk="true"
                    :options="userTypes"
                    placeholder="Select user type"
                    :errorText="formState.getFormError('type')" />

                <Select
                    v-if="form.type === 'regular'"
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
    import Button from '@Partials/Button.vue';

    export default {
        components: { Modal, Input, Select, Button },
        inject: ['formState', 'notificationState'],
        data() {
            return {
                isCreating: false,
                form: {
                    name: '',
                    email: '',
                    password: '',
                    type: 'regular',
                    organization_id: null
                },
                userTypes: [
                    { label: 'Super Admin', value: 'super_admin' },
                    { label: 'Regular', value: 'regular' }
                ],
                organizationOptions: []
            }
        },
        methods: {
            showModal() {
                this.reset();
                this.fetchOrganizations();
                this.$refs.modal.showModal();
            },
            reset() {
                this.form.name = '';
                this.form.email = '';
                this.form.password = '';
                this.form.type = 'regular';
                this.form.organization_id = null;
            },
            generatePassword() {
                const chars = 'abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789!@#$%^&*()';
                let password = '';
                for (let i = 0; i < 12; i++) {
                    password += chars.charAt(Math.floor(Math.random() * chars.length));
                }
                this.form.password = password;
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
            async createUser(hideModal) {

                this.formState.hideFormErrors();

                if (this.form.name.trim() === '') this.formState.setFormError('name', 'Name is required');
                if (this.form.email.trim() === '') this.formState.setFormError('email', 'Email is required');
                if (this.form.password.trim() === '') this.formState.setFormError('password', 'Password is required');
                if (this.form.type === '') this.formState.setFormError('type', 'User type is required');
                if (this.form.type === 'regular' && !this.form.organization_id) {
                    this.formState.setFormError('organization_id', 'Organization is required');
                }

                if (this.formState.hasErrors) return;

                this.isCreating = true;

                try {

                    await axios.post('/api/users', this.form);
                    this.notificationState.showSuccessNotification('User created');

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
    }

</script>
