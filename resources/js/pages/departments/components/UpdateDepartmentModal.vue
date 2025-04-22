<template>
    <Modal
        size="sm"
        ref="modal"
        approveType="danger"
        header="Confirm Delete"
        :isLoading="isDeleting"
        :approveAction="onDelete"
        approveText="Delete Department">
        <template #content>
            <p v-if="department" class="text-sm text-gray-700 dark:text-neutral-400">
                Are you sure you want to permanently delete
                <span class="font-bold text-black">{{ department.name }}</span>?
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
            department: null,
            isDeleting: false,
        };
    },
    methods: {
        showModal(department) {
            this.department = department;
            this.$refs.modal.showModal();
        },
        async onDelete(hideModal) {
            if (!this.department) return;

            this.isDeleting = true;

            try {
                const url = this.department._links.delete;
                const response = await axios.delete(url);

                if (response?.data?.message) {
                    this.notificationState.showSuccessNotification(response.data.message);
                }

                this.$emit('deleted', this.department.id);
                hideModal();
            } catch (error) {
                const message = error?.response?.data?.message || error?.message || 'Something went wrong while deleting the department';
                this.notificationState.showWarningNotification(message);
            } finally {
                this.isDeleting = false;
            }
        },
    },
};
</script>
