<template>
    <Modal
        size="sm"
        ref="modal"
        :showFooter="true"
        :dismissable="true"
        :header="modalHeader"
        :subheader="subheader"
        :scrollOnContent="false"
        :showApproveButton="true"
        :approveText="approveText"
        :approveLoading="isImporting"
        :approveAction="approveAction"
        :showDelineButton="showDelineButton">
        <template #trigger="props">
            <slot :showModal="props.showModal"></slot>
        </template>
        <template #content>
            <div v-if="!showImportStatus" class="space-y-4">
                <Input
                    type="file"
                    :maxFiles="1"
                    label="CSV File"
                    :showAsterisk="true"
                    v-model="form.csv_file"
                    :mimeTypes="['text/csv']"
                    :imagePreviewGridCols="1"
                    @change="handleFileChange"
                    :errorText="formState.getFormError('csv_file')" />
                <Select
                    v-model="form.organization_id"
                    :options="organizationOptions"
                    :showAsterisk="true"
                    label="Organization"
                    placeholder="Select organization"
                    :errorText="formState.getFormError('organization_id')"
                    @change="onOrganizationChange" />
                <SelectTags
                    v-model="form.tags"
                    :options="tagOptions"
                    label="Tags"
                    placeholder="Select or create tags"
                    :errorText="formState.getFormError('tags')"
                    allowCustom
                    isDraggable
                    :searchableFields="['label', 'value']" />
                <p class="text-sm text-gray-500">
                    {{ instructionText }}
                </p>
                <a
                    href="/templates/contact_import_template.csv"
                    download
                    class="text-sm text-blue-600 hover:underline">
                    Download CSV template
                </a>
            </div>
            <div v-else class="space-y-4">
                <div class="text-center">
                    <CircleCheck v-if="importResult.full_import || importResult.partial_import" size="60" :class="[importResult.full_import ? 'text-green-500' : 'text-gray-300', 'mx-auto mb-4']"></CircleCheck>
                    <CircleAlert v-else size="60" class="text-red-500 mx-auto mb-4"></CircleAlert>
                    <h3 class="text-xl font-medium text-gray-900 mb-2">{{ importStatusTitle }}</h3>
                    <p class="text-sm text-gray-500">{{ importResult.message }}</p>
                    <p v-if="importResult.total" class="text-sm text-gray-500">
                        <span class="font-bold">{{ importResult.imported }}/{{ importResult.total }}</span> imported, <span class="font-bold">{{ importResult.percentage_imported }}</span> completed.
                    </p>
                </div>
                <div v-if="importResult.errors.length" class="mt-4">
                    <div class="mt-2 overflow-x-auto">
                        <table class="divide-y divide-gray-200">
                            <thead class="bg-gray-50">
                                <tr>
                                    <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Row</th>
                                    <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Record</th>
                                    <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Errors</th>
                                </tr>
                            </thead>
                            <tbody class="bg-white divide-y divide-gray-200">
                                <tr v-for="error in importResult.errors" :key="error.row">
                                    <td class="px-4 py-2 whitespace-nowrap text-xs text-gray-900">{{ error.row }}</td>
                                    <td class="px-4 py-2 text-xs text-gray-900">
                                        <div class="whitespace-pre-wrap min-w-60">{{ formatRecord(error.record) }}</div>
                                    </td>
                                    <td class="px-4 py-2 text-xs text-red-600">
                                        <ul :class="['whitespace-nowrap', { 'list-disc list-inside' :error.errors.length >= 2 }]">
                                            <li v-for="(err, idx) in error.errors" :key="idx">{{ err }}</li>
                                        </ul>
                                    </td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </template>
    </Modal>
</template>

<script>
import axios from 'axios';
import Modal from '@Partials/Modal.vue';
import Input from '@Partials/Input.vue';
import Select from '@Partials/Select.vue';
import { CircleAlert } from 'lucide-vue-next';
import { CircleCheck } from 'lucide-vue-next';
import SelectTags from '@Partials/SelectTags.vue';

export default {
    components: { Modal, Input, Select, SelectTags, CircleAlert, CircleCheck },
    inject: ['formState', 'notificationState'],
    data() {
        return {
            isImporting: false,
            showImportStatus: false,
            importResult: null,
            form: {
                organization_id: null,
                csv_file: [],
                tags: [],
            },
            organizationOptions: [],
            tagOptions: [],
            customAttributes: [],
        };
    },
    computed: {
        modalHeader() {
            return this.showImportStatus ? 'Import Status' : 'Import Contacts from CSV';
        },
        subheader() {
            if(this.showImportStatus && this.importResult.full_import) {
                return 'Contacts successfully imported';
            }else if(this.showImportStatus && this.importResult.no_import) {
                return 'Contacts were not imported';
            }else if(this.showImportStatus && this.importResult.partial_import) {
                return 'Some contacts were not imported';
            }else {
                return 'Upload a CSV file to import multiple contacts';
            }
        },
        approveText() {
            if(this.showImportStatus && this.importResult.full_import) {
                return 'Done';
            }else if(this.showImportStatus && (this.importResult.no_import || this.importResult.partial_import)) {
                return 'Try Again';
            }else {
                return 'Import Contacts';

            }
        },
        approveAction() {
            if(this.showImportStatus && this.importResult.full_import) {
                return this.closeModal;
            }else if(this.showImportStatus && (this.importResult.no_import || this.importResult.partial_import)) {
                return this.reset;
            }else {
                return this.importContacts;
            }
        },
        showDelineButton() {
            return !this.showImportStatus ||
                    this.showImportStatus && (this.importResult.no_import || this.importResult.partial_import);
        },
        importStatusTitle() {
            if (!this.importResult) return '';
            if (this.importResult.no_import) return 'Import Failed';
            if (this.importResult.partial_import) return 'Import Partially Successful';
            return 'Import Successful';
        },
        instructionText() {
            if (!this.form.organization_id || !this.customAttributes.length) {
                return "Select an organization to see the required CSV columns. The CSV file must include 'name' and 'phone' (required with non-empty values), and may include 'email' and organization-specific attributes. Multiple phone numbers or emails can be separated by colons (:).";
            }

            const attributeNames = this.customAttributes
                .filter(attr => attr.name !== 'name')
                .map(attr => {
                    let description = `'${attr.name}'`;
                    if (attr.type === 'url') {
                        description += ' (URL, e.g., https://example.com)';
                    } else if (attr.type === 'textarea') {
                        description += ' (multi-line text)';
                    } else if (attr.type === 'text') {
                        description += ' (single-line text)';
                    }
                    return description;
                });

            const attributesText = attributeNames.length ? `, ${attributeNames.join(', ')}` : '';

            return `The CSV file must include 'name' (required, single-line text) and 'phone' (required, e.g., +1234567890), and may include 'email'${attributesText}. Multiple phone numbers or emails can be separated by colons (:). Ensure 'website' is a valid URL and 'address' supports multi-line text.`;
        },
    },
    methods: {
        showModal() {
            this.reset();
            this.fetchOrganizations();
            this.$refs.modal.showModal();
        },
        reset() {
            this.showImportStatus = false;
            this.importResult = null;
            this.form.organization_id = null;
            this.form.csv_file = [];
            this.form.tags = [];
            this.tagOptions = [];
            this.customAttributes = [];
            this.formState.hideFormErrors();
        },
        closeModal() {
            this.$refs.modal.hideModal();
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
        async fetchTags() {
            if (!this.form.organization_id) {
                this.tagOptions = [];
                return;
            }
            try {
                const response = await axios.get('/api/tags', {
                    params: { organization_id: this.form.organization_id },
                });
                this.tagOptions = (response.data?.data ?? []).map(tag => ({
                    label: tag.name,
                    value: tag.name,
                }));
            } catch (error) {
                console.error('Failed to load tags:', error);
            }
        },
        async fetchCustomAttributes() {
            if (!this.form.organization_id) {
                this.customAttributes = [];
                return;
            }
            try {
                const response = await axios.get('/api/custom-attributes', {
                    params: { organization_id: this.form.organization_id },
                });
                this.customAttributes = response.data?.data ?? [];
            } catch (error) {
                console.error('Failed to load custom attributes:', error);
            }
        },
        async onOrganizationChange() {
            await Promise.all([this.fetchTags(), this.fetchCustomAttributes()]);
        },
        handleFileChange(event) {
            this.form.csv_file = event.target.files.length ? [{
                filePath: URL.createObjectURL(event.target.files[0]),
                uploading: false,
                uploaded: null,
                fileRef: event.target.files[0]
            }] : [];
        },
        formatRecord(record) {
            return Object.entries(record)
                .map(([key, value]) => `${key}: ${value || '(empty)'}`)
                .join(', ');
        },
        async importContacts(hideModal) {
            this.formState.hideFormErrors();

            if (!this.form.organization_id) {
                this.formState.setFormError('organization_id', 'Organization is required');
            }
            if (this.form.csv_file.length === 0) {
                this.formState.setFormError('csv_file', 'CSV file is required');
            }

            if (this.formState.hasErrors) return;

            this.isImporting = true;

            try {
                const formData = new FormData();
                formData.append('organization_id', this.form.organization_id);
                formData.append('csv_file', this.form.csv_file[0].fileRef);

                if (this.form.tags.length) {
                    this.form.tags.forEach(tag => formData.append('tags[]', tag));
                }

                const response = await axios.post('/api/contacts/import', formData, {
                    headers: { 'Content-Type': 'multipart/form-data' },
                    params: { organization_id: this.form.organization_id },
                });

                this.importResult = response.data;
                this.showImportStatus = true;

                if (this.importResult.imported > 0) {
                    this.$emit('imported');
                }
            } catch (error) {
                const message = error?.response?.data?.message || error?.message || 'Something went wrong while importing contacts';
                this.notificationState.showWarningNotification(message);
                this.formState.setServerFormErrors(error);
                console.error('Failed to import contacts:', error);
            } finally {
                this.isImporting = false;
            }
        },
    },
};
</script>
