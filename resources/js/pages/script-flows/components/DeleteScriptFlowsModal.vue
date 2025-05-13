<template>
    <Modal
        size="sm"
        ref="modal"
        :showFooter="true"
        :dismissable="true"
        :scrollOnContent="false"
        :showApproveButton="true"
        header="Delete Call Flow"
        approveText="Delete"
        :approveLoading="isDeleting"
        :approveAction="deleteCallFlow"
        subheader="Are you sure you want to delete this call flow?"
    >
        <template #content>
            <div class="p-4">
                <p class="text-sm text-gray-700 dark:text-neutral-400">
                    This action cannot be undone. The call flow will be permanently deleted.
                </p>
            </div>
        </template>
    </Modal>
</template>

<script>
import axios from 'axios';
import Modal from '@Partials/Modal.vue';

export default {
    components: {
        Modal,
    },
    data() {
        return {
            isDeleting: false,
            callFlow: null,
        };
    },
    methods: {
        showModal(callFlow) {
            this.callFlow = callFlow;
            this.$refs.modal.showModal();
        },
        async deleteCallFlow(hideModal) {
            this.isDeleting = true;
            try {
                await axios.delete(`/api/call-flows/${this.callFlow.id}`);
                this.$notificationState.showSuccessNotification('Call flow deleted successfully!');
                this.$emit('deleted', this.callFlow.id);
                hideModal();
            } catch (error) {
                console.error('Failed to delete call flow:', error);
                this.$notificationState.showWarningNotification('Failed to delete call flow.');
            } finally {
                this.isDeleting = false;
            }
        },
    },
};
</script>
