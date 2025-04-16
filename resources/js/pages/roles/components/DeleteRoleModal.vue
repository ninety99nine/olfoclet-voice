<!-- DeleteRoleModal.vue -->
<template>

    <Modal
        size="sm"
        ref="modal"
        approveType="danger"
        header="Confirm Delete"
        :isLoading="isDeleting"
        :approveAction="onDelete"
        approveText="Delete Role">

        <template #content>
            <p v-if="role" class="text-sm text-gray-700 dark:text-neutral-400">
                Are you sure you want to permanently delete
                <span class="font-bold text-black">{{ role.name }}</span>?
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
                role: null,
                isDeleting: false,
            };
        },
        methods: {
            showModal(role) {
                this.role = role;
                this.$refs.modal.showModal();
            },
            async onDelete(hideModal) {
                if (!this.role) return;

                this.isDeleting = true;

                try {

                    const url = this.role._links.delete_role;
                    const response = await axios.delete(url);

                    if (response?.data?.message) {
                        this.notificationState.showSuccessNotification(response.data.message);
                    }

                    this.$emit('deleted', this.role.id);
                    hideModal();

                } catch (error) {

                    const message = error?.response?.data?.message || error?.message || 'Something went wrong while deleting the role';
                    this.notificationState.showWarningNotification(message);

                } finally {

                    this.isDeleting = false;

                }
            },
        },
    };

</script>
