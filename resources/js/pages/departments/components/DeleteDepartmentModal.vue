<!-- DeleteUserModal.vue -->
<template>

    <Modal
        size="sm"
        ref="modal"
        approveType="danger"
        header="Confirm Delete"
        :isLoading="isDeleting"
        :approveAction="onDelete"
        approveText="Delete User">

        <template #content>
            <p v-if="user" class="text-sm text-gray-700 dark:text-neutral-400">
                Are you sure you want to permanently delete
                <span class="font-bold text-black">{{ user.name }}</span>?
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
                user: null,
                isDeleting: false,
            };
        },
        methods: {
            showModal(user) {
                this.user = user;
                this.$refs.modal.showModal();
            },
            async onDelete(hideModal) {
                if (!this.user) return;

                this.isDeleting = true;

                try {

                    const url = this.user._links.delete_user;
                    const response = await axios.delete(url);

                    if (response?.data?.message) {
                        this.notificationState.showSuccessNotification(response.data.message);
                    }

                    this.$emit('deleted', this.user.id);
                    hideModal();

                } catch (error) {

                    const message = error?.response?.data?.message || error?.message || 'Something went wrong while deleting the user';
                    this.notificationState.showWarningNotification(message);

                } finally {

                    this.isDeleting = false;

                }
            },
        },
    };

</script>
