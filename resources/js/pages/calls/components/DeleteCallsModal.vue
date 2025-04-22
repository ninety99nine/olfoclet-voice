<template>
    <Modal
        size="sm"
        ref="modal"
        approveType="danger"
        approveIcon="delete"
        :isLoading="isDeleting"
        :approveAction="onDelete"
        :approveText="approveText"
        header="Delete Calls">
        <template #content>
            <div class="flex space-x-2 items-center p-4 text-xs bg-red-50 text-red-500 border border-red-200 border-dashed rounded-lg mb-8">
                <CircleAlert size="20"></CircleAlert>
                <span>
                    Are you sure you want to delete
                    {{ total > 1 ? `these ${total} calls` : 'this 1 call' }}?
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
            callIds: [],
        };
    },
    computed: {
        total() {
            return this.callIds.length;
        },
        approveText() {
            return this.total === 1 ? 'Delete Call' : 'Delete Calls';
        },
    },
    methods: {
        showModal(ids = []) {
            this.callIds = ids;
            this.$refs.modal.showModal();
        },
        async onDelete(hideModal) {
            if (this.isDeleting || this.callIds.length === 0) return;

            this.isDeleting = true;

            try {
                const url = '/api/calls';
                const config = {
                    data: {
                        call_ids: this.callIds,
                    },
                };

                const response = await axios.delete(url, config);

                const message = response?.data?.message;
                if (message) this.notificationState.showSuccessNotification(message);

                this.$emit('deleted', this.callIds);
                hideModal();
            } catch (error) {
                const message = error?.response?.data?.message || error?.message || 'Something went wrong while deleting calls';
                this.notificationState.showWarningNotification(message);
            } finally {
                this.isDeleting = false;
            }
        }
    },
};
</script>
