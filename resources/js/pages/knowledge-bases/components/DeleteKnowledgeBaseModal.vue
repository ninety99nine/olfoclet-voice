<template>
    <Modal
        size="sm"
        ref="modal"
        approveType="danger"
        header="Confirm Delete"
        :isLoading="isDeleting"
        :approveAction="onDelete"
        approveText="Delete Knowledge Base">
        <template #content>
            <p v-if="knowledgeBase" class="text-sm text-gray-700 dark:text-neutral-400">
                Are you sure you want to permanently delete
                <span class="font-bold text-black">{{ knowledgeBase.name }}</span>?
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
            knowledgeBase: null,
            isDeleting: false,
        };
    },
    methods: {
        showModal(knowledgeBase) {
            this.knowledgeBase = knowledgeBase;
            this.$refs.modal.showModal();
        },
        async onDelete(hideModal) {
            if (!this.knowledgeBase) return;

            this.isDeleting = true;

            try {
                const url = this.knowledgeBase._links.delete;
                const response = await axios.delete(url);

                if (response?.data?.message) {
                    this.notificationState.showSuccessNotification(response.data.message);
                }

                this.$emit('deleted', this.knowledgeBase.id);
                hideModal();
            } catch (error) {
                const message = error?.response?.data?.message || error?.message || 'Something went wrong while deleting the knowledge base';
                this.notificationState.showWarningNotification(message);
            } finally {
                this.isDeleting = false;
            }
        },
    },
};
</script>
