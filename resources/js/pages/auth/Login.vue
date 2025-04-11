<template>
    <div class="min-h-screen flex items-center justify-center bg-gray-50 px-4">
      <div class="w-full max-w-md bg-white rounded-lg shadow-md p-6">
        <h1 class="text-xl font-semibold mb-4">Sign in to your account</h1>

        <form @submit.prevent="submit">
          <div class="mb-4">
            <label class="block mb-1 text-sm font-medium">Email</label>
            <input v-model="form.email" type="email" class="w-full p-2 border rounded" />
          </div>

          <div class="mb-4">
            <label class="block mb-1 text-sm font-medium">Password</label>
            <input v-model="form.password" type="password" class="w-full p-2 border rounded" />
          </div>

          <div class="flex justify-between items-center mb-6">
            <label class="text-sm">
              <input type="checkbox" v-model="form.remember" class="mr-1"> Remember me
            </label>
            <router-link to="/forgot-password" class="text-sm text-blue-600 hover:underline">Forgot password?</router-link>
          </div>

          <button :disabled="loading" type="submit" class="w-full bg-blue-600 text-white p-2 rounded hover:bg-blue-700">
            {{ loading ? 'Signing in...' : 'Login' }}
          </button>
        </form>

        <p v-if="error" class="text-red-500 text-sm mt-4">{{ error }}</p>
      </div>
    </div>
  </template>

  <script setup>
    import { ref } from 'vue';
    import axios from 'axios';
    import { useRouter } from 'vue-router';

    const router = useRouter();
    const loading = ref(false);
    const error = ref('');

    const form = ref({
        email: '',
        password: '',
        remember: false
    });

    const submit = async () => {
        loading.value = true;
        error.value = '';

        try {
        const response = await axios.post('/api/login', form.value);
        // Handle success
        router.push('/dashboard');
        } catch (err) {
        error.value = err.response?.data?.message || 'Login failed.';
        } finally {
        loading.value = false;
        }
    };
  </script>
