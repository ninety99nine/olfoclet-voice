import { useAuthStore } from "@Stores/auth-store.js";
import { createRouter, createWebHistory } from 'vue-router';

const routes = [
    {
        path: '/',
        redirect: '/login',
    },
    {
        path: '/login',
        name: 'login',
        component: () => import('@Pages/auth/Login.vue'),
    },
    {
        path: '/dashboard',
        component: () => import('@Layouts/dashboard/Dashboard.vue'),
        meta: { requiresAuth: true },
        children: [
            {
                path: '',
                name: 'show-home',
                component: () => import('@Pages/home/show/Show.vue'),
            },
            {
                path: 'agents',
                name: 'show-agents',
                component: () => import('@Pages/agents/show/Show.vue'),
            },
            {
                path: 'organisations',
                name: 'show-organisations',
                component: () => import('@Pages/organisations/show/Show.vue'),
            },
            {
                path: 'account',
                name: 'show-account',
                component: () => import('@Pages/account/show/Show.vue'),
            },
        ]
    },
    {
        path: '/:catchAll(.*)',
        name: 'notFound',
        component: () => import('@Pages/error/404.vue'),
    }
];

const router = createRouter({
    history: createWebHistory(),
    routes,
});

router.beforeEach(async (to, from, next) => {

    const authState = useAuthStore();

    if (!authState.user) {

        const storedToken = authState.getTokenFromLocalStorage();

        if (storedToken) {

            authState.token = storedToken;
            authState.setTokenOnRequest(storedToken);

            try {
                await authState.fetchUser();
            } catch (e) {
                authState.logout();
            }

        }

    }

    // Protected route
    if (to.meta?.requiresAuth === true && !authState.isAuthenticated) {
        return next({
            name: 'login',
            query: { redirect: to.fullPath },
            replace: true
        });
    }

    // Public route
    if (authState.isAuthenticated && to.name === 'login') {
        return next({ name: 'show-home' });
    }

    return next();
});

export default router;
