<template>

    <div class="min-h-screen grid grid-cols-1 lg:grid-cols-2  transition-opacity opacity-100 duration-750 lg:grow starting:opacity-0">

        <div class="flex items-center justify-center p-6 bg-gray-50">

            <div class="w-full max-w-md bg-white rounded-lg shadow-md p-6">

                <!-- Logo -->
                <div class="flex justify-center mb-6">
                    <Logo />
                </div>

                <h1 class="text-gray-500 text-center mb-4">Sign in to your account</h1>

                <form @submit.prevent="submit" class="space-y-4">

                    <Input
                        type="email"
                        label="Email"
                        v-model="form.email"
                        :errorText="formState.getFormError('email')" />

                    <Input
                        type="password"
                        label="Password"
                        v-model="form.password"
                        :errorText="formState.getFormError('password')" />

                    <div v-if="formState.generalErrorText" class="text-red-500 text-sm mb-3">
                        {{ formState.generalErrorText }}
                    </div>

                    <div class="flex justify-end mb-6">
                        <router-link to="/forgot-password" class="text-sm text-blue-600 hover:underline">Forgot password?</router-link>
                    </div>

                    <Button
                        size="md"
                        type="primary"
                        class="w-full"
                        :action="submit">
                        <span>{{ loading ? 'Signing in...' : 'Login' }}</span>
                    </Button>

                </form>

            </div>

        </div>

        <!-- Right: Full Height Image (hidden on small screens) -->
        <div class="hidden lg:block">

            <img :src="'/images/smiling-customer-service-agent.jpg'"
                class="w-full h-full object-cover"
                alt="Customer service agent"
            />

        </div>

    </div>

  </template>

  <script>

    import Logo from '@Partials/Logo.vue';
    import Input from '@Partials/Input.vue';
    import Button from '@Partials/Button.vue';

    export default {
        name: 'Login',
        components: { Logo, Input, Button },
        inject: ['authState', 'formState'],
        data() {
            return {
                loading: false,
                form: {
                    email: '',
                    password: ''
                },
            };
        },
        methods: {
            async submit() {
                if(this.loading) return;
                this.formState.hideFormErrors();

                if(this.form.email.trim() == '') {
                    this.formState.setFormError('email', 'Enter your email');
                }else if(this.form.password.trim() == '') {
                    this.formState.setFormError('password', 'Enter your password');
                }

                if(this.formState.hasErrors) {
                    return;
                }

                this.loading = true;

                try {
                    await this.authState.login(this.form);
                    this.$router.push({ name: 'show-home' });
                } catch (err) {
                    this.formState.setServerFormErrors(err);
                } finally {
                    this.loading = false;
                }
            }
        }
    };

</script>
