<template>
    <Modal
        size="sm"
        ref="modal"
        :showFooter="true"
        :dismissable="true"
        :scrollOnContent="false"
        :showApproveButton="true"
        header="Select Copilot"
        approveText="Start Conversation"
        :approveAction="confirmSelection"
        :approveLoading="isLoading"
        subheader="Choose a Copilot to start a new conversation">

        <template #trigger="props">
            <slot :showModal="props.showModal"></slot>
        </template>

        <template #content>
            <div class="space-y-4">
                <Select
                    label="Copilot"
                    :search="true"
                    v-model="form.copilotId"
                    :showAsterisk="true"
                    :options="copilotOptions"
                    placeholder="Select a Copilot"
                    :errorText="formState.getFormError('copilotId')"
                    :disabled="isLoading || copilotOptions.length === 0" />
            </div>
        </template>
    </Modal>
</template>

<script>
    import axios from 'axios';
    import Modal from '@Partials/Modal.vue';
    import Select from '@Partials/Select.vue';

    export default {
        components: { Modal, Select },
        inject: ['formState', 'notificationState'],
        data() {
            return {
                isLoading: false,
                form: {
                    copilotId: null
                },
                copilotOptions: []
            };
        },
        methods: {
            showModal() {
                this.reset();
                this.fetchCopilots();
                this.$refs.modal.showModal();
            },
            reset() {
                this.form.copilotId = null;
                this.copilotOptions = [];
                this.isLoading = false;
                this.formState.hideFormErrors();
            },
            async fetchCopilots() {
                try {
                    this.isLoading = true;
                    const response = await axios.get('/api/copilots', {
                        params: { '_relationships': 'organization' }
                    });
                    const copilots = response.data.data || response.data.copilots || [];
                    this.copilotOptions = copilots.map(copilot => ({
                        label: copilot.name,
                        value: copilot.id
                    }));
                    if (this.copilotOptions.length === 0) {
                        this.formState.setFormError('copilotId', 'No Copilots available. Please create a Copilot first.');
                        this.notificationState.showWarningNotification('No Copilots available. Please create a Copilot first.');
                    }
                } catch (error) {
                    const message = error?.response?.data?.message || error?.message || 'Failed to load Copilots';
                    this.notificationState.showWarningNotification(message);
                    this.formState.setFormError('copilotId', 'Failed to load Copilots. Please try again.');
                    console.error('Failed to fetch Copilots:', error);
                } finally {
                    this.isLoading = false;
                }
            },
            async confirmSelection(hideModal) {
                this.formState.hideFormErrors();

                if (!this.form.copilotId) {
                    this.formState.setFormError('copilotId', 'Please select a Copilot');
                    return;
                }

                // Validate that the selected Copilot ID exists in the options
                const selectedCopilot = this.copilotOptions.find(option => option.value === this.form.copilotId);
                if (!selectedCopilot) {
                    this.formState.setFormError('copilotId', 'Invalid Copilot selected');
                    return;
                }

                try {
                    // Navigate to the create-conversation-thread route with the selected Copilot ID
                    this.$router.push({
                        name: 'create-conversation-thread',
                        query: { copilotId: this.form.copilotId }
                    });
                    hideModal();
                } catch (error) {
                    const message = error?.message || 'Failed to start a new conversation';
                    this.notificationState.showWarningNotification(message);
                    console.error('Failed to navigate to new conversation:', error);
                }
            }
        }
    };
</script>
