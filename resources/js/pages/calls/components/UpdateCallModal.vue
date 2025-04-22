<template>
    <Modal
        size="sm"
        ref="modal"
        :showFooter="true"
        :dismissable="true"
        :scrollOnContent="false"
        :showApproveButton="true"
        :approveLoading="isUpdating"
        header="Update Call"
        approveText="Update Call"
        :approveAction="updateCall"
        subheader="Modify the call's details">
        <template #content>
            <div class="space-y-4">
                <Input
                    v-model="form.from"
                    :showAsterisk="true"
                    label="From Number"
                    placeholder="+1234567890"
                    :errorText="formState.getFormError('from')" />
                <Input
                    v-model="form.to"
                    :showAsterisk="true"
                    label="To Number"
                    placeholder="+1234567890"
                    :errorText="formState.getFormError('to')" />
                <Select
                    v-model="form.direction"
                    :options="directionOptions"
                    :showAsterisk="true"
                    label="Direction"
                    placeholder="Select direction"
                    :errorText="formState.getFormError('direction')" />
                <Select
                    v-model="form.status"
                    :options="statusOptions"
                    :showAsterisk="true"
                    label="Status"
                    placeholder="Select status"
                    :errorText="formState.getFormError('status')" />
                <Select
                    v-model="form.department_id"
                    :options="departmentOptions"
                    label="Department"
                    placeholder="Select department"
                    :errorText="formState.getFormError('department_id')" />
                <Select
                    v-model="form.agent_id"
                    :options="agentOptions"
                    label="Agent"
                    placeholder="Select agent"
                    :errorText="formState.getFormError('agent_id')" />
            </div>
        </template>
    </Modal>
</template>

<script>
import axios from 'axios';
import Modal from '@Partials/Modal.vue';
import Input from '@Partials/Input.vue';
import Select from '@Partials/Select.vue';

export default {
    name: 'UpdateCallModal',
    inject: ['formState', 'notificationState'],
    components: { Modal, Input, Select },
    data() {
        return {
            isUpdating: false,
            call: null,
            form: {
                from: '',
                to: '',
                direction: '',
                status: '',
                department_id: null,
                agent_id: null
            },
            directionOptions: [
                { label: 'Inbound', value: 'inbound' },
                { label: 'Outbound', value: 'outbound' }
            ],
            statusOptions: [
                { label: 'Initiated', value: 'initiated' },
                { label: 'In Progress', value: 'in-progress' },
                { label: 'Completed', value: 'completed' },
                { label: 'Failed', value: 'failed' }
            ],
            departmentOptions: [],
            agentOptions: []
        };
    },
    methods: {
        showModal(call) {
            this.reset();
            this.call = call;
            this.form.from = call.from;
            this.form.to = call.to;
            this.form.direction = call.direction;
            this.form.status = call.status;
            this.form.department_id = call.department_id;
            this.form.agent_id = call.agent_id;
            this.fetchDepartments();
            this.fetchAgents();
            this.$refs.modal.showModal();
        },
        reset() {
            this.form.from = '';
            this.form.to = '';
            this.form.direction = '';
            this.form.status = '';
            this.form.department_id = null;
            this.form.agent_id = null;
            this.call = null;
        },
        async fetchDepartments() {
            try {
                const response = await axios.get('/api/departments');
                this.departmentOptions = (response.data?.data ?? []).map(dep => ({
                    label: dep.name,
                    value: dep.id
                }));
            } catch (error) {
                console.error('Failed to load departments:', error);
            }
        },
        async fetchAgents() {
            try {
                const response = await axios.get('/api/users');
                this.agentOptions = (response.data?.data ?? []).map(user => ({
                    label: user.name,
                    value: user.id
                }));
            } catch (error) {
                console.error('Failed to load agents:', error);
            }
        },
        async updateCall(hideModal) {
            this.formState.hideFormErrors();

            if (this.form.from.trim() === '') this.formState.setFormError('from', 'From number is required');
            if (this.form.to.trim() === '') this.formState.setFormError('to', 'To number is required');
            if (!this.form.direction) this.formState.setFormError('direction', 'Direction is required');
            if (!this.form.status) this.formState.setFormError('status', 'Status is required');

            if (this.formState.hasErrors) return;

            this.isUpdating = true;

            try {
                const url = this.call._links.update;
                const payload = { ...this.form };

                await axios.put(url, payload);

                this.notificationState.showSuccessNotification('Call updated');
                this.$emit('updated');
                hideModal();
            } catch (error) {
                const message = error?.response?.data?.message || error?.message || 'Something went wrong updating the call';
                this.notificationState.showWarningNotification(message);
                this.formState.setServerFormErrors(error);
                console.error('Failed to update call:', error);
            } finally {
                this.isUpdating = false;
            }
        }
    }
};
</script>
