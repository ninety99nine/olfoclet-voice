<template>
    <div class="min-h-screen grid grid-cols-1 lg:grid-cols-2 transition-opacity opacity-100 duration-750 lg:grow starting:opacity-0">
        <div class="flex items-center justify-center p-6 bg-slate-100">
            <div class="w-full max-w-md">
                <div class="flex justify-center mb-6">
                    <Logo height="h-10"></Logo>
                </div>

                <div class="bg-white rounded-lg shadow-sm border border-slate-200 p-6 mb-6">
                    <h1 class="text-2xl font-semibold leading-none tracking-tight mb-4">
                        {{ tokenError ? (tokenError.includes('expired') ? 'Setup Link Expired' : 'Setup Link Invalid') : 'Set Up Your Account' }}
                    </h1>
                    <h1 class="text-sm text-gray-500 mb-4">
                        {{ tokenError ? (tokenError.includes('expired') ? 'The link to set up your account has expired. Contact your administrator to assist.' : 'The link to set up your account is invalid. Contact your administrator to assist.') : 'Please set your password to activate your account.' }}
                    </h1>

                    <template v-if="tokenError">

                        <Alert :title="tokenError" type="danger" :dismissable="false" class="mb-8"></Alert>

                        <Button type="primary" size="lg" :action="goHome" class="w-full">
                            <span>Go Home</span>
                        </Button>

                    </template>

                    <form v-else @submit.prevent="submit" class="space-y-4">
                        <Input
                            type="email"
                            label="Email"
                            v-model="form.email"
                            :errorText="formState.getFormError('email')"
                            disabled />

                        <Input
                            type="password"
                            label="Password"
                            v-model="form.password"
                            :errorText="formState.getFormError('password')" />

                        <Input
                            type="password"
                            label="Confirm Password"
                            v-model="form.confirmPassword"
                            :errorText="formState.getFormError('confirmPassword')" />

                        <div v-if="formState.generalErrorText" class="text-red-500 text-sm mb-3">
                            {{ formState.generalErrorText }}
                        </div>

                        <Button
                            size="md"
                            type="primary"
                            class="w-full"
                            :action="submit"
                            :leftIcon="LogIn"
                            :disabled="loading">
                            <span class="ml-1">{{ loading ? 'Setting Password...' : 'Set Password' }}</span>
                        </Button>
                    </form>
                </div>

                <p class="text-sm text-center text-gray-500">© {{ currentYear }} Telcoflo. All rights reserved.</p>
            </div>
        </div>

        <!-- Right: Full Height Image (hidden on small screens) -->
        <div class="hidden lg:block">
            <img :src="'/images/smiling-customer-service-agent.jpg'"
                 class="w-full h-full object-cover"
                 alt="Customer service agent" />
        </div>
    </div>
</template>

<script>
import Logo from '@Partials/Logo.vue';
import { LogIn } from 'lucide-vue-next';
import Alert from '@Partials/Alert.vue';
import Input from '@Partials/Input.vue';
import Button from '@Partials/Button.vue';
import axios from 'axios';

export default {
    name: 'SetupAccount',
    components: { Logo, Alert, Input, Button },
    inject: ['authState', 'formState', 'notificationState'],
    data() {
        return {
            LogIn,
            form: {
                email: '',
                token: '',
                password: '',
                confirmPassword: ''
            },
            loading: false,
            tokenError: null,
            currentYear: new Date().getFullYear()
        };
    },
    methods: {
        goHome() {
            this.$router.push({ name: 'login' });
        },
        async validateToken() {
            try {
                await axios.post('/api/auth/validate-token', {
                    email: this.form.email,
                    token: this.form.token
                });
                this.tokenError = null; // Token is valid
            } catch (error) {
                const message = error?.response?.data?.message || error?.message || 'Invalid or expired token.';
                this.tokenError = message;
                this.notificationState.showWarningNotification(message);
            }
        },
        async submit() {
            if (this.loading) return;
            this.formState.hideFormErrors();

            if (this.form.email.trim() === '') {
                this.formState.setFormError('email', 'Email is required');
            }
            if (this.form.password.trim() === '') {
                this.formState.setFormError('password', 'Password is required');
            }
            if (this.form.confirmPassword.trim() === '') {
                this.formState.setFormError('confirmPassword', 'Confirm Password is required');
            }
            if (this.form.password !== this.form.confirmPassword) {
                this.formState.setFormError('confirmPassword', 'Passwords do not match');
            }

            if (this.formState.hasErrors) return;

            this.loading = true;

            try {
                const response = await axios.post('/api/auth/setup-password', this.form);

                const token = response.data?.token;

                if (token) {
                    this.authState.token = token;
                    this.authState.setTokenOnLocalStorage(token);
                    this.authState.setTokenOnRequest(token);
                    await this.authState.fetchUser();
                    this.$router.push({ name: 'show-home' });
                    this.notificationState.showSuccessNotification('Password set successfully. You are now logged in.');
                } else {
                    throw new Error('Token not received from server');
                }
            } catch (error) {
                const message = error?.response?.data?.message || error?.message || 'Something went wrong while setting your password';
                this.formState.setServerFormErrors(error);
                this.notificationState.showWarningNotification(message);
                console.error('Failed to set password:', error);
            } finally {
                this.loading = false;
            }
        }
    },
    async mounted() {
        this.form.email = this.$route.query.email || '';
        this.form.token = this.$route.query.token || '';

        // Validate token on page load
        if (this.form.email && this.form.token) {
            await this.validateToken();
        } else {
            this.tokenError = 'Invalid or missing email/token.';
            this.notificationState.showWarningNotification('Invalid or missing email/token.');
        }
    }
};
</script>
