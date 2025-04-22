<template>
    <Modal
        size="sm"
        ref="modal"
        :showFooter="true"
        :dismissable="true"
        :scrollOnContent="false"
        :showApproveButton="true"
        header="Create New Call"
        approveText="Create Call"
        :approveAction="createCall"
        :approveLoading="isCreating"
        subheader="Add a new call to the platform">
        <template #trigger="props">
            <slot :showModal="props.showModal"></slot>
        </template>
        <template #content>
            <div class="space-y-4">
                <Input
                    type="mobile"
                    v-model="form.from"
                    :showAsterisk="true"
                    label="From Number"
                    placeholder="+1234567890"
                    :errorText="formState.getFormError('from')" />
                <Input
                    type="mobile"
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
                    label="Status"
                    placeholder="Select status"
                    :errorText="formState.getFormError('status')" />
                <Select
                    v-model="form.organization_id"
                    :options="organizationOptions"
                    :showAsterisk="true"
                    label="Organization"
                    placeholder="Select organization"
                    :errorText="formState.getFormError('organization_id')" />
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
    components: { Modal, Input, Select },
    inject: ['formState', 'notificationState'],
    data() {
        return {
            isCreating: false,
            form: {
                from: '',
                to: '',
                direction: '',
                status: '',
                organization_id: null,
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
            organizationOptions: [],
            departmentOptions: [],
            agentOptions: []
        };
    },
    methods: {
        showModal() {
            this.reset();
            this.fetchOrganizations();
            this.fetchDepartments();
            this.fetchAgents();
            this.$refs.modal.showModal();
        },
        reset() {
            this.form.from = '';
            this.form.to = '';
            this.form.direction = '';
            this.form.status = '';
            this.form.organization_id = null;
            this.form.department_id = null;
            this.form.agent_id = null;
        },
        async fetchOrganizations() {
            try {
                const response = await axios.get('/api/organizations');
                this.organizationOptions = (response.data?.data ?? []).map(org => ({
                    label: org.name,
                    value: org.id
                }));
            } catch (error) {
                console.error('Failed to load organizations:', error);
            }
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
        async createCall(hideModal) {
            this.formState.hideFormErrors();

            if (this.form.from.trim() === '') this.formState.setFormError('from', 'From number is required');
            if (this.form.to.trim() === '') this.formState.setFormError('to', 'To number is required');
            if (!this.form.direction) this.formState.setFormError('direction', 'Direction is required');
            if (!this.form.organization_id) this.formState.setFormError('organization_id', 'Organization is required');

            if (this.formState.hasErrors) return;

            this.isCreating = true;

            try {
                const payload = { ...this.form };
                await axios.post('/api/calls', payload, {
                    params: { organization_id: this.form.organization_id }
                });

                this.notificationState.showSuccessNotification('Call created');
                this.$emit('created');
                hideModal();
            } catch (error) {
                const message = error?.response?.data?.message || error?.message || 'Something went wrong trying to create the call';
                this.notificationState.showWarningNotification(message);
                this.formState.setServerFormErrors(error);
                console.error('Failed to create call:', error);
            } finally {
                this.isCreating = false;
            }
        }
    }
};
</script>
