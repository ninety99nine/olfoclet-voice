<template>
    <Modal
        size="sm"
        ref="modal"
        :showFooter="true"
        :dismissable="true"
        :scrollOnContent="false"
        :showApproveButton="true"
        header="Delete Call Flows"
        approveText="Delete"
        :approveLoading="isDeleting"
        :approveAction="deleteCallFlows"
        subheader="Are you sure you want to delete the selected call flows?"
    >
        <template #content>
            <div class="p-4">
                <p class="text-sm text-gray-700 dark:text-neutral-400">
                    This action cannot be undone. {{ callFlowIds.length }} call flow(s) will be permanently deleted.
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
            callFlowIds: [],
        };
    },
    methods: {
        showModal(callFlowIds) {
            this.callFlowIds = callFlowIds;
            this.$refs.modal.showModal();
        },
        async deleteCallFlows(hideModal) {
            this.isDeleting = true;
            try {
                await axios.delete('/api/call-flows', {
                    params: {
                        call_flow_ids: this.callFlowIds,
                    },
                });
                this.$notificationState.showSuccessNotification(`${this.callFlowIds.length} call flow(s) deleted successfully!`);
                this.$emit('deleted', this.callFlowIds);
                hideModal();
            } catch (error) {
                console.error('Failed to delete call flows:', error);
                this.$notificationState.showWarningNotification('Failed to delete call flows.');
            } finally {
                this.isDeleting = false;
            }
        },
    },
};
</script>
