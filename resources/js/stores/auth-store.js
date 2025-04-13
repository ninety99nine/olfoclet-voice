import axios from 'axios';
import { defineStore } from 'pinia';

export const useAuthStore = defineStore('auth', {
    state: () => ({
        user: null,
        isLoadingUser: null,
        token: localStorage.getItem('auth_token') || null
    }),
    getters: {
        isAuthenticated: (state) => !!state.token,
    },
    actions: {
        async login(payload) {
            const response = await axios.post('/api/login', payload);

            this.token = response.data.token;
            this.setTokenOnRequest(this.token);
            this.setTokenOnLocalStorage(this.token);
        },
        logout() {
            this.user = null;
            this.token = null;
            localStorage.removeItem('auth_token');
            delete axios.defaults.headers.common['Authorization'];
        },
        async fetchUser() {
            this.isLoadingUser = true;
            const response = await axios.get('/api/user');
            this.user = response.data;
            this.isLoadingUser = false;
        },
        getTokenFromLocalStorage() {
            return localStorage.getItem('auth_token');
        },
        setTokenOnLocalStorage(token) {
            localStorage.setItem('auth_token', token);
        },
        setTokenOnRequest(token) {
            axios.defaults.headers.common['Authorization'] = `Bearer ${token}`;
        },
    },
});
