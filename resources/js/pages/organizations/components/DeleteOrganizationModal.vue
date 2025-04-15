<!-- DeleteOrganizationModal.vue -->
<template>

    <Modal
        size="sm"
        ref="modal"
        approveType="danger"
        header="Confirm Delete"
        :isLoading="isDeleting"
        :approveAction="onDelete"
        approveText="Delete Organization">

        <template #content>
            <p v-if="organization" class="text-sm text-gray-700 dark:text-neutral-400">
                Are you sure you want to permanently delete
                <span class="font-bold text-black">{{ organization.name }}</span>?
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
                organization: null,
                isDeleting: false,
            };
        },
        methods: {
            showModal(organization) {
                this.organization = organization;
                this.$refs.modal.showModal();
            },
            async onDelete(hideModal) {
                if (!this.organization) return;

                this.isDeleting = true;

                try {

                    const url = this.organization._links.delete_organization;
                    const response = await axios.delete(url);

                    if (response?.data?.message) {
                        this.notificationState.showSuccessNotification(response.data.message);
                    }

                    this.$emit('deleted', this.organization.id);
                    hideModal();

                } catch (error) {

                    const message = error?.response?.data?.message || error?.message || 'Something went wrong while deleting the organization';
                    this.notificationState.showWarningNotification(message);

                } finally {

                    this.isDeleting = false;

                }
            },
        },
    };

</script>
