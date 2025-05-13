import { useAuthStore } from "@Stores/auth-store.js";
import { createRouter, createWebHistory } from 'vue-router';

const routes = [
    {
        path: '/',
        component: () => import('@Layouts/website/Website.vue'),
        children: [
            {
                path: '/',
                name: 'landing',
                component: () => import('@Pages/website/landing-page/LandingPage.vue'),
            },
            {
                path: '/privacy-policy',
                name: 'privacy-policy',
                component: () => import('@Pages/website/privacy-policy/PrivacyPolicy.vue'),
            },
            {
                path: '/terms-of-service',
                name: 'terms-of-service',
                component: () => import('@Pages/website/terms-of-service/TermsOfService.vue'),
            },
            {
                path: '/data-deletion-instructions',
                name: 'data-deletion-instructions',
                component: () => import('@Pages/website/data-deletion-instructions/DataDeletionInstructions.vue'),
            }
        ]
    },
    {
        path: '/auth',
        component: () => import('@Layouts/auth/Auth.vue'),
        children: [
            {
                path: 'login',
                name: 'login',
                component: () => import('@Pages/auth/Login.vue'),
            },
            {
                path: ':alias/login',
                name: 'organization-login',
                component: () => import('@Pages/auth/Login.vue'),
                props: true
            },
            {
                path: 'setup-account',
                name: 'setup-account',
                component: () => import('@Pages/auth/SetupAccount.vue'),
            },
            {
                path: 'forgot-password',
                name: 'forgot-password',
                component: () => import('@Pages/auth/ForgotPassword.vue'),
            },
            {
                path: 'reset-password',
                name: 'reset-password',
                component: () => import('@Pages/auth/ResetPassword.vue'),
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
                path: 'flow',
                name: 'show-flow',
                component: () => import('@Pages/flow/show/Show.vue'),
            },
            {
                path: 'calls',
                name: 'show-calls',
                component: () => import('@Pages/calls/show/Show.vue'),
            },
            {
                path: 'scripts',
                name: 'show-scripts',
                component: () => import('@Pages/scripts/show/Show.vue'),
            },
            {
                path: 'users',
                name: 'show-users',
                component: () => import('@Pages/users/show/Show.vue'),
            },
            {
                path: 'channels',
                name: 'show-channels',
                component: () => import('@Pages/channels/show/Show.vue'),
            },
            {
                path: 'contacts',
                name: 'show-contacts',
                component: () => import('@Pages/contacts/show/Show.vue'),
            },
            {
                path: 'numbers',
                name: 'show-numbers',
                component: () => import('@Pages/numbers/Show.vue'),
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
                children: [
                    {
                        path: '',
                        name: 'show-account',
                        component: () => import('@Pages/account/show/Show.vue'),
                    },
                    {
                        path: 'update',
                        name: 'update-account',
                        component: () => import('@Pages/account/update/Update.vue'),
                    }
                ]
            },
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
                        component: () => import('@Pages/call-flows/edit/Builder.vue'),
                    },
                    {
                        path: ':call_flow_id',
                        name: 'edit-call-flow',
                        component: () => import('@Pages/call-flows/edit/Builder.vue'),
                    }
                ]
            },
            {
                path: 'script-flows',
                children: [
                    {
                        path: '',
                        name: 'show-script-flows',
                        component: () => import('@Pages/script-flows/show/Show.vue'),
                    },
                    {
                        path: 'create',
                        name: 'create-script-flow',
                        component: () => import('@Pages/script-flows/edit/Builder.vue'),
                    },
                    {
                        path: ':script_flow_id',
                        name: 'edit-script-flow',
                        component: () => import('@Pages/script-flows/edit/Builder.vue'),
                    }
                ]
            },
            {
                path: 'media-files',
                name: 'show-media-files',
                component: () => import('@Pages/media-files/show/Show.vue'),
            },
            {
                path: 'knowledge-bases',
                children: [
                    {
                        path: '',
                        name: 'show-knowledge-bases',
                        component: () => import('@Pages/knowledge-bases/show/Show.vue'),
                    },
                    {
                        path: ':knowledgeBaseId',
                        name: 'manage-knowledge-base',
                        component: () => import('@Pages/knowledge-bases/manage/Manage.vue'),
                        props: true
                    }
                ]
            },
            {
                path: 'copilots',
                children: [
                    {
                        path: '',
                        name: 'show-copilots',
                        component: () => import('@Pages/copilots/show/Show.vue'),
                    },
                    {
                        path: ':copilotId/conversation-threads',
                        name: 'show-copilot-conversation-threads',
                        component: () => import('@Pages/conversation-threads/Show/Show.vue'),
                        props: true
                    },
                    {
                        path: ':copilotId/conversation-threads/create',
                        name: 'create-copilot-conversation-thread',
                        component: () => import('@Pages/conversation-threads/chat/Chat.vue')
                    },
                    {
                        path: ':copilotId/conversation-threads/:conversationThreadId',
                        name: 'chat-copilot-conversation-thread',
                        component: () => import('@Pages/conversation-threads/chat/Chat.vue'),
                        props: true
                    }
                ]
            },
            {
                path: 'conversation-threads',
                children: [
                    {
                        path: '',
                        name: 'show-conversation-threads',
                        component: () => import('@Pages/conversation-threads/Show/Show.vue'),
                    }
                ]
            },
            {
                path: 'integrations',
                name: 'show-integrations',
                component: () => import('@Pages/integrations/show/Show.vue'),
            },
            {
                path: 'nexflo',
                name: 'show-nexflo',
                component: () => import('@Pages/nexflo/show/Show.vue'),
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
    scrollBehavior(to, from, savedPosition) {
        // Always scroll to the top on navigation
        return { top: 0 };
    },
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
