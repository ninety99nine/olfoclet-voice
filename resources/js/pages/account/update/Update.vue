<template>

    <div class="min-h-screen bg-gray-50 p-6">

        <div class="max-w-2xl mx-auto">

            <div class="bg-white rounded-lg shadow-sm border border-slate-200 p-6 mb-6">

                <div class="flex items-center justify-between mb-6">
                    <h1 class="text-2xl font-semibold leading-none tracking-tight">Update Account</h1>
                    <Button type="light" size="md" :leftIcon="MoveLeft" :action="goBack">
                        <span class="ml-2">Back to Account</span>
                    </Button>
                </div>

                <form @submit.prevent="submit">

                    <Input
                        type="text"
                        label="Name"
                        class="mb-4"
                        v-model="form.name"
                        :errorText="formState.getFormError('name')" />

                    <Input
                        type="email"
                        class="mb-8"
                        label="Email"
                        v-model="form.email"
                        :errorText="formState.getFormError('email')"
                        disabled />

                    <div class="space-y-4 mb-8">

                        <Switch size="sm" v-model="changePassword" suffixText="Change Password" />

                        <template v-if="changePassword">

                            <Input
                                type="password"
                                label="New Password"
                                v-model="form.password"
                                :errorText="formState.getFormError('password')" />

                            <Input
                                type="password"
                                label="Confirm New Password"
                                v-model="form.confirmPassword"
                                :errorText="formState.getFormError('confirmPassword')" />

                        </template>

                    </div>

                    <div v-if="formState.generalErrorText" class="text-red-500 text-sm mb-3">
                        {{ formState.generalErrorText }}
                    </div>

                    <Button
                        size="md"
                        type="primary"
                        class="w-full"
                        :action="submit"
                        :disabled="loading">
                        <span class="ml-1">{{ loading ? 'Updating...' : 'Update Account' }}</span>
                    </Button>
                </form>
            </div>
        </div>
    </div>
</template>

<script>
import axios from 'axios';
import Logo from '@Partials/Logo.vue';
import Input from '@Partials/Input.vue';
import Switch from '@Partials/Switch.vue'; // Assuming you have a Switch component
import Button from '@Partials/Button.vue';
import { MoveLeft } from 'lucide-vue-next';
import { useAuthStore } from '@Stores/auth-store.js';

export default {
    name: 'UpdateAccount',
    components: { Input, Switch, Button, Logo },
    inject: ['formState', 'notificationState'],
    data() {
        return {
            form: {
                name: '',
                email: '',
                password: '',
                confirmPassword: ''
            },
            changePassword: false, // Toggle state for password change
            MoveLeft,
            loading: false
        };
    },
    methods: {
        goBack() {
            this.$router.push({ name: 'show-account' });
        },
        async submit() {
            if (this.loading) return;
            this.formState.hideFormErrors();

            if (this.form.name.trim() === '') {
                this.formState.setFormError('name', 'Name is required');
            }

            if (this.changePassword) {

                if (this.form.password.trim() === '') {
                    this.formState.setFormError('password', 'New Password is required');
                }else if (this.form.confirmPassword.trim() === '') {
                    this.formState.setFormError('confirmPassword', 'Confirm New Password is required');
                }else if (this.form.password !== this.form.confirmPassword) {
                    this.formState.setFormError('confirmPassword', 'Passwords do not match');
                }

            }

            if (this.formState.hasErrors) return;

            this.loading = true;

            try {
                const payload = {
                    name: this.form.name,
                    password: this.changePassword ? this.form.password || undefined : undefined // Only send password if changePassword is true
                };

                await axios.post('/api/auth/update', payload);
                const authStore = useAuthStore();
                await authStore.fetchUser(); // Refresh user data in store
                this.notificationState.showSuccessNotification('Account updated successfully.');
                this.$router.push({ name: 'show-account' });
            } catch (error) {
                const message = error?.response?.data?.message || error?.message || 'Something went wrong while updating your account';
                this.formState.setServerFormErrors(error);
                this.notificationState.showWarningNotification(message);
                console.error('Failed to update account:', error);
            } finally {
                this.loading = false;
            }
        }
    },
    mounted() {
        const authStore = useAuthStore();
        if (authStore.user) {
            this.form.name = authStore.user.name;
            this.form.email = authStore.user.email;
        }
    }
};
</script>
