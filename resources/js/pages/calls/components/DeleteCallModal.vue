<template>
    <Modal
        size="sm"
        ref="modal"
        approveType="danger"
        header="Confirm Delete"
        :isLoading="isDeleting"
        :approveAction="onDelete"
        approveText="Delete Call">
        <template #content>
            <p v-if="call" class="text-sm text-gray-700 dark:text-neutral-400">
                Are you sure you want to permanently delete the call from
                <span class="font-bold text-black">{{ call.from }}</span> to
                <span class="font-bold text-black">{{ call.to }}</span>?
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
            call: null,
            isDeleting: false,
        };
    },
    methods: {
        showModal(call) {
            this.call = call;
            this.$refs.modal.showModal();
        },
        async onDelete(hideModal) {
            if (!this.call) return;

            this.isDeleting = true;

            try {
                const url = this.call._links.delete;
                const response = await axios.delete(url);

                if (response?.data?.message) {
                    this.notificationState.showSuccessNotification(response.data.message);
                }

                this.$emit('deleted', this.call.id);
                hideModal();
            } catch (error) {
                const message = error?.response?.data?.message || error?.message || 'Something went wrong while deleting the call';
                this.notificationState.showWarningNotification(message);
            } finally {
                this.isDeleting = false;
            }
        },
    },
};
</script>
