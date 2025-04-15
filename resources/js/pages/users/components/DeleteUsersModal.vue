<template>

    <Modal
        size="sm"
        ref="modal"
        approveType="danger"
        approveIcon="delete"
        :isLoading="isDeleting"
        :approveAction="onDelete"
        :approveText="approveText"
        header="Delete Users">

        <template #content>

            <div class="flex space-x-2 items-center p-4 text-xs bg-red-50 text-red-500 border border-red-200 border-dashed rounded-lg mb-8">

                <CircleAlert size="20"></CircleAlert>

                <span>
                    Are you sure you want to delete
                    {{ total > 1 ? `these ${total} users` : 'this 1 user' }}?
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
                userIds: [],
            };
        },
        computed: {
            total() {
                return this.userIds.length;
            },
            approveText() {
                return this.total === 1 ? 'Delete User' : 'Delete Users';
            },
        },
        methods: {
            showModal(ids = []) {
                this.userIds = ids;
                this.$refs.modal.showModal();
            },
            async onDelete(hideModal) {

                if (this.isDeleting || this.userIds.length === 0) return;

                this.isDeleting = true;

                try {
                    const url = '/api/users';
                    const config = {
                        data: {
                            user_ids: this.userIds,
                        },
                    };

                    const response = await axios.delete(url, config);

                    const message = response?.data?.message;
                    if (message) this.notificationState.showSuccessNotification(message);

                    this.$emit('deleted', this.userIds);
                    hideModal();

                } catch (error) {
                    const message = error?.response?.data?.message || error?.message || 'Something went wrong while deleting users';
                    this.notificationState.showWarningNotification(message);
                } finally {
                    this.isDeleting = false;
                }
            }
        },
    };

</script>
