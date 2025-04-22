<template>
    <Modal
        ref="modal"
        :showFooter="true"
        :dismissable="true"
        :scrollOnContent="false"
        :showApproveButton="true"
        header="Upload Media File"
        approveText="Upload"
        :approveAction="uploadFile"
    >
        <template #content>
            <div class="space-y-4 p-4">
                <Select
                    v-model="form.organization_id"
                    :options="organizationOptions"
                    :showAsterisk="true"
                    label="Organization"
                    placeholder="Select organization"
                    :error="errors.organization_id"
                />
                <Input
                    v-model="form.name"
                    label="File Name"
                    placeholder="Enter a name for the file"
                    :error="errors.name"
                />
                <div>
                    <label class="block text-sm font-medium text-gray-700">File</label>
                    <input
                        type="file"
                        @change="onFileChange"
                        class="mt-1 block w-full text-sm text-gray-500 file:mr-4 file:py-2 file:px-4 file:rounded file:border-0 file:text-sm file:font-semibold file:bg-blue-50 file:text-blue-700 hover:file:bg-blue-100"
                    />
                    <p v-if="errors.file" class="text-red-600 text-sm mt-1">{{ errors.file }}</p>
                </div>
            </div>
        </template>
    </Modal>
</template>

<script>
import Modal from '@Partials/Modal.vue';
import Input from '@Partials/Input.vue';
import Select from '@Partials/Select.vue';
import axios from 'axios';

export default {
    components: {
        Modal,
        Input,
        Select,
    },
    data() {
        return {
            form: {
                organization_id: null,
                name: '',
                file: null,
            },
            errors: {},
            organizationOptions: [],
        };
    },
    mounted() {
        this.fetchOrganizations();
    },
    methods: {
        async fetchOrganizations() {
            try {
                const response = await axios.get('/api/organizations');
                this.organizationOptions = (response.data?.data ?? []).map(org => ({
                    label: org.name,
                    value: org.id,
                }));
            } catch (error) {
                console.error('Failed to load organizations:', error);
                this.$notificationState.showWarningNotification('Failed to load organizations.');
            }
        },
        showModal() {
            this.form = { organization_id: null, name: '', file: null };
            this.errors = {};
            this.$refs.modal.showModal();
        },
        onFileChange(event) {
            this.form.file = event.target.files[0];
            if (!this.form.name && this.form.file) {
                this.form.name = this.form.file.name.split('.').slice(0, -1).join('.');
            }
        },
        async uploadFile(hideModal) {
            this.errors = {};

            if (!this.form.organization_id) {
                this.errors.organization_id = 'Organization is required.';
            }

            if (!this.form.file) {
                this.errors.file = 'Please select a file to upload.';
            }

            if (Object.keys(this.errors).length > 0) return;

            const formData = new FormData();
            formData.append('name', this.form.name);
            formData.append('file', this.form.file);
            formData.append('organization_id', this.form.organization_id);

            try {
                await axios.post('/api/media-files', formData, {
                    headers: {
                        'Content-Type': 'multipart/form-data',
                    },
                });
                this.$notificationState.showSuccessNotification('Media file uploaded successfully!');
                this.$emit('file-uploaded');
                hideModal();
            } catch (error) {
                console.error('Failed to upload media file:', error);
                if (error.response?.data?.errors) {
                    this.errors = error.response.data.errors;
                } else {
                    this.$notificationState.showWarningNotification('Failed to upload media file.');
                }
            }
        },
    },
};
</script>
