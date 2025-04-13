import { defineStore } from 'pinia';
import { generateUniqueId } from '@Utils/generalUtils.js';

export const useNotificationStore = defineStore('notification', {
    state: () => ({
        notifications: []
    }),
    actions: {
        showSuccessNotification(message, duration = 3000) {
            this.addNotification(message, 'success', duration);
        },
        showWarningNotification(message, duration = 3000) {
            this.addNotification(message, 'warning', duration);
        },
        addNotification(message, type, duration = 3000) {
            const id = generateUniqueId('notification');
            this.notifications.push({ id, message, type, duration });
            setTimeout(() => this.removeNotification(id), duration * 2);
        },
        removeNotification(id) {
            this.notifications = this.notifications.filter(n => n.id !== id);
        }
    },
});
