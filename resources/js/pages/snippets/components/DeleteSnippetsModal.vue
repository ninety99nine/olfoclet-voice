<template>
    <Modal
        size="sm"
        ref="modal"
        approveType="danger"
        approveIcon="delete"
        :isLoading="isDeleting"
        :approveAction="onDelete"
        :approveText="approveText"
        header="Delete Articles">
        <template #content>
            <div class="flex space-x-2 items-center p-4 text-xs bg-red-50 text-red-500 border border-red-200 border-dashed rounded-lg mb-8">
                <CircleAlert size="20"></CircleAlert>
                <span>
                    Are you sure you want to delete
                    {{ total > 1 ? `these ${total} articles` : 'this 1 article' }}?
                </span>
            </div>
        </template>
    </Modal>
</template>

<script>
import axios from 'axios';
import Modal from '@Partials/Modal.vue';
import { CircleAlert } from 'lucide-vue-next';

export default {
    inject: ['notificationState'],
    components: { Modal, CircleAlert },
    data() {
        return {
            isDeleting: false,
            articleIds: [],
        };
    },
    computed: {
        total() {
            return this.articleIds.length;
        },
        approveText() {
            return this.total === 1 ? 'Delete Article' : 'Delete Articles';
        },
    },
    methods: {
        showModal(ids = []) {
            this.articleIds = ids;
            this.$refs.modal.showModal();
        },
        async onDelete(hideModal) {
            if (this.isDeleting || this.articleIds.length === 0) return;

            this.isDeleting = true;

            try {
                const url = '/api/articles';
                const config = {
                    data: {
                        article_ids: this.articleIds,
                    },
                };

                const response = await axios.delete(url, config);

                const message = response?.data?.message;
                if (message) this.notificationState.showSuccessNotification(message);

                this.$emit('deleted', this.articleIds);
                hideModal();
            } catch (error) {
                const message = error?.response?.data?.message || error?.message || 'Something went wrong while deleting articles';
                this.notificationState.showWarningNotification(message);
            } finally {
                this.isDeleting = false;
            }
        }
    },
};
</script>
