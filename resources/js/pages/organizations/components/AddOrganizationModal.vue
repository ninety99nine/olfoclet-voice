<template>

    <Modal
        size="sm"
        ref="modal"
        :showFooter="true"
        :dismissable="true"
        :scrollOnContent="false"
        :showApproveButton="true"
        :approveLoading="isCreating"
        header="Create New Organization"
        approveText="Create Organization"
        :approveAction="createOrganization"
        subheader="Add a new call center for an organisation">

        <template #trigger="props">
            <slot :showModal="props.showModal"></slot>
        </template>

        <template #content>

            <div class="space-y-4">

                <Input
                    v-model="form.name"
                    :showAsterisk="true"
                    label="Organization Name"
                    placeholder="Acme Telecom"
                    :errorText="formState.getFormError('name')" />

                <SelectCountry
                    label="Country"
                    :showAsterisk="true"
                    v-model="form.country"
                    :errorText="formState.getFormError('country')" />

                <Input
                    type="number"
                    label="Seats"
                    placeholder="10"
                    :showAsterisk="true"
                    v-model="form.seats"
                    :errorText="formState.getFormError('seats')" />

                <div class="border-t border-gray-200 border-dashed space-y-4 pt-4">

                    <Switch
                        size="sm"
                        v-model="createAdmin"
                        suffixText="Create Admin"/>

                    <template v-if="createAdmin">

                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mt-4">

                            <Input
                                label="Admin Name"
                                :showAsterisk="true"
                                placeholder="Full Name"
                                v-model="form.admin_name"
                                :errorText="formState.getFormError('admin_name')" />

                            <Input
                                type="email"
                                label="Admin Email"
                                :showAsterisk="true"
                                v-model="form.admin_email"
                                placeholder="admin@domain.com"
                                :errorText="formState.getFormError('admin_email')" />

                        </div>

                        <Input
                            type="mobile"
                            :showAsterisk="true"
                            label="Phone Number"
                            v-model="form.admin_mobile"
                            :errorText="formState.getFormError('admin_mobile')" />

                    </template>

                </div>

            </div>

        </template>

    </Modal>

</template>

<script>

    import axios from 'axios';
    import Modal from '@Partials/Modal.vue';
    import Input from '@Partials/Input.vue';
    import Switch from '@Partials/Switch.vue';
    import Select from '@Partials/Select.vue';
    import SelectCountry from '@Partials/SelectCountry.vue';

    export default {
        components: { Modal, Input, Switch, Select, SelectCountry },
        inject: ['formState', 'notificationState'],
        data() {
            return {
                isCreating: false,
                createAdmin: false,
                form: {
                    name: '',
                    seats: '1',
                    country: '',
                    admin_name: '',
                    admin_email: '',
                    admin_mobile: ''
                }
            }
        },
        methods: {
            showModal() {
                this.reset();
                this.$refs.modal.showModal();
            },
            reset() {
                this.form.name = '';
                this.form.seats = '1';
                this.form.country = '';
                this.form.admin_name = '';
                this.form.admin_email = '';
                this.form.admin_mobile = '';
            },
            async createOrganization(hideModal) {

                this.formState.hideFormErrors();

                if (this.form.name.trim() === '') this.formState.setFormError('name', 'Organization name is required');
                if (this.form.country === '') this.formState.setFormError('country', 'Select a country');
                if (this.form.seats === '') this.formState.setFormError('seats', 'Number of seats is required');

                if(this.createAdmin) {
                    if (this.form.admin_name.trim() === '') this.formState.setFormError('admin_name', 'Admin name is required');
                    if (this.form.admin_email.trim() === '') this.formState.setFormError('admin_email', 'Admin email is required');
                    if (this.form.admin_mobile.trim() === '') this.formState.setFormError('admin_mobile', 'Phone number is required');
                }

                if (this.formState.hasErrors) return;

                this.isCreating = true;

                try {

                    await axios.post('/api/organizations', this.form);
                    this.notificationState.showSuccessNotification('Organisation created');

                    this.$emit('created');
                    hideModal();

                } catch (error) {

                    const message = error?.response?.data?.message || error?.message || 'Something went wrong trying to create the organisation';
                    this.notificationState.showWarningNotification(message);
                    this.formState.setServerFormErrors(error);

                    console.error('Failed to create organization:', error);

                } finally {
                    this.isCreating = false;
                }
            }
        }
    }

</script>

