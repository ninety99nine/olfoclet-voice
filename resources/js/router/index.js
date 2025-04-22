import { useAuthStore } from "@Stores/auth-store.js";
import { createRouter, createWebHistory } from 'vue-router';

const routes = [
    {
        component: () => import('@Layouts/auth/Auth.vue'),
        children: [
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
                path: '/:alias/login',
                name: 'organization-login',
                component: () => import('@Pages/auth/Login.vue'),
                props: true
            },
            {
                path: '/:catchAll(.*)',
                name: 'notFound',
                component: () => import('@Pages/error/404.vue'),
            }
        ]
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
                path: 'calls',
                name: 'show-calls',
                component: () => import('@Pages/calls/show/Show.vue'),
            },
            {
                path: 'users',
                name: 'show-users',
                component: () => import('@Pages/users/show/Show.vue'),
            },
            {
                path: 'contacts',
                name: 'show-contacts',
                component: () => import('@Pages/contacts/show/Show.vue'),
            },
            {
                path: 'organizations',
                name: 'show-organizations',
                component: () => import('@Pages/organizations/show/Show.vue'),
            },
            {
                path: 'roles',
                name: 'show-roles',
                component: () => import('@Pages/roles/show/Show.vue'),
            },
            {
                path: 'departments',
                name: 'show-departments',
                component: () => import('@Pages/departments/show/Show.vue'),
            },
            {
                path: 'account',
                name: 'show-account',
                component: () => import('@Pages/account/show/Show.vue'),
            },
            /*
            {
                path: 'call-flows',
                children: [
                    {
                        path: '',
                        name: 'show-call-flows',
                        component: () => import('@Pages/call-flows/show/Show.vue'),
                    },
                    {
                        path: 'create',
                        name: 'create-call-flow',
                        component: () => import('@Pages/call-flows/edit/components/Builder.vue'),
                    },
                    {
                        path: ':call_flow_id',
                        name: 'edit-call-flow',
                        component: () => import('@Pages/call-flows/edit/components/Builder.vue'),
                    }
                ]
            },
            */
            {
                path: 'media-files',
                name: 'show-media-files',
                component: () => import('@Pages/media-files/show/Show.vue'),
            },
        ]
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
