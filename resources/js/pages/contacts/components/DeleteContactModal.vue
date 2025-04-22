<template>
    <Modal
        size="sm"
        ref="modal"
        approveType="danger"
        header="Confirm Delete"
        :isLoading="isDeleting"
        :approveAction="onDelete"
        approveText="Delete Contact">
        <template #content>
            <p v-if="contact" class="text-sm text-gray-700 dark:text-neutral-400">
                Are you sure you want to permanently delete
                <span class="font-bold text-black">{{ contact.first_name }} {{ contact.last_name }}</span>?
            </p>
        </template>
    </Modal>
</template>

<script>
import Modal from '@Partials/Modal.vue';
import axios from 'axios';

export default {
    components: { Modal },
    inject: ['notificationState'],
    data() {
        return {
            contact: null,
            isDeleting: false,
        };
    },
    methods: {
        showModal(contact) {
            this.contact = contact;
            this.$refs.modal.showModal();
        },
        async onDelete(hideModal) {
            if (!this.contact) return;

            this.isDeleting = true;

            try {
                const url = this.contact._links.delete_contact || `/api/contacts/${this.contact.id}`;
                const response = await axios.delete(url);

                if (response?.data?.message) {
                    this.notificationState.showSuccessNotification(response.data.message);
                }

                this.$emit('deleted', this.contact.id);
                hideModal();
            } catch (error) {
                const message = error?.response?.data?.message || error?.message || 'Something went wrong while deleting the contact';
                this.notificationState.showWarningNotification(message);
            } finally {
                this.isDeleting = false;
            }
        },
    },
};
</script>
