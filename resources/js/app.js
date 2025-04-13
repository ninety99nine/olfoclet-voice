import 'preline';
import './bootstrap';
import router from './router';
import { createApp } from 'vue';
import { createPinia } from 'pinia';
import { useAuthStore } from "@Stores/auth-store.js";
import { useFormStore } from "@Stores/form-store.js";
import { useNotificationStore } from "@Stores/notification-store.js";
import { useChangeHistoryStore } from "@Stores/change-history-store.js";

const app = createApp({});
const pinia = createPinia();

app.use(pinia);
app.use(router);
app.mount('#app');

// Make Pinia States globally available
app.provide("authState", useAuthStore());
app.provide("formState", useFormStore());
app.provide("notificationState", useNotificationStore());
app.provide("changeHistoryState", useChangeHistoryStore());
