import { createRouter, createWebHistory } from 'vue-router';

const routes = [
    {
        path: '/',
        redirect: '/login',
    },
    {
        path: '/login',
        name: 'login',
        meta: { requiresAuth: false },
        component: () => import('@Pages/auth/Login.vue')
    }
];

const router = createRouter({
  history: createWebHistory(),
  routes,
});

export default router;
