<!-- DeleteRolesModal.vue -->
<template>

    <Modal
        size="sm"
        ref="modal"
        approveType="danger"
        approveIcon="delete"
        :isLoading="isDeleting"
        :approveAction="onDelete"
        :approveText="approveText"
        header="Delete Roles">

        <template #content>

            <div class="flex space-x-2 items-center p-4 text-xs bg-red-50 text-red-500 border border-red-200 border-dashed rounded-lg mb-8">

                <CircleAlert size="20"></CircleAlert>

                <span>
                    Are you sure you want to delete
                    {{ total > 1 ? `these ${total} roles` : 'this 1 role' }}?
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
                roleIds: [],
            };
        },
        computed: {
            total() {
                return this.roleIds.length;
            },
            approveText() {
                return this.total === 1 ? 'Delete Role' : 'Delete Roles';
            },
        },
        methods: {
            showModal(ids = []) {
                this.roleIds = ids;
                this.$refs.modal.showModal();
            },
            async onDelete(hideModal) {

                if (this.isDeleting || this.roleIds.length === 0) return;

                this.isDeleting = true;

                try {
                    const url = '/api/roles';
                    const config = {
                        data: {
                            role_ids: this.roleIds,
                        },
                    };

                    const response = await axios.delete(url, config);

                    const message = response?.data?.message;
                    if (message) this.notificationState.showSuccessNotification(message);

                    this.$emit('deleted', this.roleIds);
                    hideModal();

                } catch (error) {
                    const message = error?.response?.data?.message || error?.message || 'Something went wrong while deleting roles';
                    this.notificationState.showWarningNotification(message);
                } finally {
                    this.isDeleting = false;
                }
            }
        },
    };

</script>
