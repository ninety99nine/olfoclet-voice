<template>
    <Modal
        size="sm"
        ref="modal"
        approveType="danger"
        approveIcon="delete"
        :isLoading="isDeleting"
        :approveAction="onDelete"
        :approveText="approveText"
        header="Delete Snippets">
        <template #content>
            <div class="flex space-x-2 items-center p-4 text-xs bg-red-50 text-red-500 border border-red-200 border-dashed rounded-lg mb-8">
                <CircleAlert size="20"></CircleAlert>
                <span>
                    Are you sure you want to delete
                    {{ total > 1 ? `these ${total} snippets` : 'this 1 snippet' }}?
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
            snippetIds: [],
        };
    },
    computed: {
        total() {
            return this.snippetIds.length;
        },
        approveText() {
            return this.total === 1 ? 'Delete Snippet' : 'Delete Snippets';
        },
    },
    methods: {
        showModal(ids = []) {
            this.snippetIds = ids;
            this.$refs.modal.showModal();
        },
        async onDelete(hideModal) {
            if (this.isDeleting || this.snippetIds.length === 0) return;

            this.isDeleting = true;

            try {
                const url = '/api/snippets';
                const config = {
                    data: {
                        snippet_ids: this.snippetIds,
                    },
                };

                const response = await axios.delete(url, config);

                const message = response?.data?.message;
                if (message) this.notificationState.showSuccessNotification(message);

                this.$emit('deleted', this.snippetIds);
                hideModal();
            } catch (error) {
                const message = error?.response?.data?.message || error?.message || 'Something went wrong while deleting snippets';
                this.notificationState.showWarningNotification(message);
            } finally {
                this.isDeleting = false;
            }
        }
    },
};
</script>
