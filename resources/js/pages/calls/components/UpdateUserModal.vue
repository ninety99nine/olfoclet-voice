<template>

    <Modal
        size="sm"
        ref="modal"
        :showFooter="true"
        :dismissable="true"
        :scrollOnContent="false"
        :showApproveButton="true"
        :approveLoading="isUpdating"
        header="Update User"
        approveText="Update User"
        :approveAction="updateUser"
        subheader="Modify the user's details">

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
                    label="Type"
                    :search="false"
                    v-model="form.type"
                    :showAsterisk="true"
                    :options="userTypes"
                    placeholder="Select user type"
                    :errorText="formState.getFormError('type')" />

                <div class="border-t border-gray-200 border-dashed space-y-4 pt-4">

                    <Switch
                        size="sm"
                        v-model="resetPassword"
                        suffixText="Reset Password" />

                    <template v-if="resetPassword">

                        <Input
                            :copy="true"
                            class="w-full"
                            type="password"
                            label="New Password"
                            v-model="form.password"
                            placeholder="Enter new password"
                            :errorText="formState.getFormError('password')" />

                        <div class="flex justify-end">
                            <Button
                                size="xs"
                                type="ghost"
                                :action="generatePassword">
                                <span>Generate Password</span>
                            </Button>
                        </div>

                    </template>

                </div>

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
    import Button from '@Partials/Button.vue';

    export default {
        name: 'UpdateUserModal',
        inject: ['formState', 'notificationState'],
        components: { Modal, Input, Select, Switch, Button },
        data() {
            return {
                isUpdating: false,
                resetPassword: false,
                user: null,
                form: {
                    name: '',
                    email: '',
                    type: '',
                    password: ''
                },
                userTypes: [
                    { label: 'Super Admin', value: 'super_admin' },
                    { label: 'Regular', value: 'regular' }
                ]
            };
        },
        methods: {
            showModal(user) {
                this.reset();
                this.user = user;
                this.form.name = user.name;
                this.form.email = user.email;
                this.form.type = user.type;
                this.$refs.modal.showModal();
            },
            reset() {
                this.form.name = '';
                this.form.email = '';
                this.form.type = '';
                this.form.password = '';
                this.resetPassword = false;
                this.user = null;
            },
            generatePassword() {
                const chars = 'abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789!@#$%^&*()';
                let password = '';
                for (let i = 0; i < 12; i++) {
                    password += chars.charAt(Math.floor(Math.random() * chars.length));
                }
                this.form.password = password;
            },
            async updateUser(hideModal) {
                this.formState.hideFormErrors();

                if (this.form.name.trim() === '') this.formState.setFormError('name', 'Name is required');
                if (this.form.email.trim() === '') this.formState.setFormError('email', 'Email is required');
                if (!this.form.type) this.formState.setFormError('type', 'User type is required');

                if (this.resetPassword && this.form.password.trim() === '') {
                    this.formState.setFormError('password', 'Password is required');
                }

                if (this.formState.hasErrors) return;

                this.isUpdating = true;

                try {
                    const url = this.user._links.update_user;
                    const payload = {
                        name: this.form.name,
                        email: this.form.email,
                        type: this.form.type
                    };

                    if (this.resetPassword) {
                        payload.password = this.form.password;
                    }

                    await axios.put(url, payload);

                    this.notificationState.showSuccessNotification('User updated');
                    this.$emit('updated');
                    hideModal();

                } catch (error) {
                    const message = error?.response?.data?.message || error?.message || 'Something went wrong updating the user';
                    this.notificationState.showWarningNotification(message);
                    this.formState.setServerFormErrors(error);

                    console.error('Failed to update user:', error);
                } finally {
                    this.isUpdating = false;
                }
            }
        }
    };

</script>
