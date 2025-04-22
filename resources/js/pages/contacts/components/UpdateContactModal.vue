<template>
    <Modal
        size="md"
        ref="modal"
        :showFooter="true"
        :dismissable="true"
        :scrollOnContent="false"
        :showApproveButton="true"
        :approveLoading="isUpdating"
        header="Update Contact"
        approveText="Update Contact"
        :approveAction="updateContact"
        subheader="Modify the contact's details">
        <template #trigger="props">
            <slot :showModal="props.showModal"></slot>
        </template>
        <template #content>
            <div class="space-y-4">
                <Select
                    :showAsterisk="true"
                    label="Organization"
                    v-model="form.organization_id"
                    :options="organizationOptions"
                    @change="onOrganizationChange"
                    placeholder="Select organization"
                    :errorText="formState.getFormError('organization_id')"
                    :disabled="true" />

                <!-- Phone Numbers -->
                <div class="space-y-2">
                    <label class="block text-sm font-medium text-gray-700">
                        Phone Number <span class="text-red-500">*</span>
                    </label>
                    <div v-for="(phone, index) in form.phone_numbers" :key="index" class="flex items-center space-x-2">
                        <Input
                            type="tel"
                            class="flex-1"
                            placeholder="+1234567890"
                            v-model="form.phone_numbers[index].value"
                            :errorText="formState.getFormError(`phone_numbers.${index}.value`)" />
                        <label class="flex items-center space-x-1">
                            <input
                                type="radio"
                                :value="index"
                                v-model="primaryPhoneIndex"
                                @change="setPrimaryPhone(index)"
                                :disabled="form.phone_numbers[index].value === ''" />
                            <span class="text-sm text-gray-700">Primary</span>
                        </label>
                        <Button
                            size="xs"
                            :leftIcon="Trash2"
                            type="outlineDanger"
                            v-if="form.phone_numbers.length > 1"
                            :action="() => removePhoneNumber(index)">
                        </Button>
                    </div>
                    <Button
                        size="xs"
                        type="light"
                        :leftIcon="Plus"
                        :action="addPhoneNumber">
                        Add another phone number
                    </Button>
                    <p v-if="formState.getFormError('phone_numbers')" class="text-sm text-red-600">
                        {{ formState.getFormError('phone_numbers') }}
                    </p>
                </div>

                <!-- Emails -->
                <div class="space-y-2">
                    <label class="block text-sm font-medium text-gray-700">
                        Email Address
                    </label>
                    <div v-for="(email, index) in form.emails" :key="index" class="flex items-center space-x-2">
                        <Input
                            v-model="form.emails[index].value"
                            type="email"
                            placeholder="example@domain.com"
                            class="flex-1"
                            :errorText="formState.getFormError(`emails.${index}.value`)" />
                        <label class="flex items-center space-x-1">
                            <input
                                type="radio"
                                :value="index"
                                v-model="primaryEmailIndex"
                                @change="setPrimaryEmail(index)"
                                :disabled="form.emails[index].value === ''" />
                            <span class="text-sm text-gray-700">Primary</span>
                        </label>
                        <Button
                            size="xs"
                            :leftIcon="Trash2"
                            type="outlineDanger"
                            v-if="form.emails.length > 1"
                            :action="() => removeEmail(index)">
                        </Button>
                    </div>
                    <Button
                        size="xs"
                        type="light"
                        :leftIcon="Plus"
                        :action="addEmail">
                        Add another email address
                    </Button>
                    <p v-if="formState.getFormError('emails')" class="text-sm text-red-600">
                        {{ formState.getFormError('emails') }}
                    </p>
                </div>

                <!-- Custom Attributes -->
                <div v-for="attr in customAttributes" :key="attr.id" class="space-y-2">
                    <Input
                        :showAsterisk="attr.name === 'name'"
                        :placeholder="'Enter ' + attr.name"
                        v-model="form.custom_attributes[attr.name]"
                        :type="attr.type === 'textarea' ? 'textarea' : 'text'"
                        :label="attr.name.charAt(0).toUpperCase() + attr.name.slice(1)"
                        :errorText="formState.getFormError(`custom_attributes.${attr.name}`)" />
                </div>

                <!-- Tags -->
                <SelectTags
                    allowCustom
                    isDraggable
                    label="Tags"
                    v-model="form.tags"
                    :options="tagOptions"
                    placeholder="Select or create tags"
                    :searchableFields="['label', 'value']"
                    :errorText="formState.getFormError('tags')" />
            </div>
        </template>
    </Modal>
</template>

<script>
import axios from 'axios';
import Modal from '@Partials/Modal.vue';
import Input from '@Partials/Input.vue';
import Button from '@Partials/Button.vue';
import Select from '@Partials/Select.vue';
import SelectTags from '@Partials/SelectTags.vue';
import { Plus, Trash2 } from 'lucide-vue-next';

export default {
    name: 'UpdateContactModal',
    inject: ['formState', 'notificationState'],
    components: { Modal, Input, Button, Select, SelectTags, Plus, Trash2 },
    data() {
        return {
            isUpdating: false,
            contact: null,
            form: {
                organization_id: null,
                custom_attributes: {},
                phone_numbers: [{ value: '', is_primary: true }],
                emails: [{ value: '', is_primary: true }],
                tags: [],
            },
            organizationOptions: [],
            customAttributes: [],
            tagOptions: [],
            primaryPhoneIndex: 0,
            primaryEmailIndex: 0,
        };
    },
    methods: {
        async showModal(contact) {
            this.reset();
            this.contact = contact;
            this.form.organization_id = contact.organization_id;

            // Populate custom attributes
            this.form.custom_attributes = {};
            await this.fetchCustomAttributes();
            if (contact.custom_attributes && Array.isArray(contact.custom_attributes)) {
                contact.custom_attributes.forEach(attr => {
                    if (this.form.custom_attributes.hasOwnProperty(attr.name)) {
                        this.form.custom_attributes[attr.name] = attr.value;
                    }
                });
            }

            // Populate phone numbers and emails from identifiers
            const identifiers = contact.identifiers || [];
            this.form.phone_numbers = identifiers
                .filter(id => id.type === 'phone')
                .map((id, index) => ({
                    value: id.value,
                    is_primary: id.is_primary,
                }));
            if (this.form.phone_numbers.length === 0) {
                this.form.phone_numbers = [{ value: '', is_primary: true }];
            }
            this.primaryPhoneIndex = this.form.phone_numbers.findIndex(phone => phone.is_primary) || 0;

            this.form.emails = identifiers
                .filter(id => id.type === 'email')
                .map((id, index) => ({
                    value: id.value,
                    is_primary: id.is_primary,
                }));
            if (this.form.emails.length === 0) {
                this.form.emails = [{ value: '', is_primary: true }];
            }
            this.primaryEmailIndex = this.form.emails.findIndex(email => email.is_primary) || 0;

            // Populate tags
            this.form.tags = contact.tags && Array.isArray(contact.tags) ? contact.tags.map(tag => typeof tag === 'string' ? tag : tag.name) : [];
            await this.fetchTags();

            this.fetchOrganizations();
            this.$refs.modal.showModal();
        },
        reset() {
            this.form.organization_id = null;
            this.form.custom_attributes = {};
            this.form.phone_numbers = [{ value: '', is_primary: true }];
            this.form.emails = [{ value: '', is_primary: true }];
            this.form.tags = [];
            this.customAttributes = [];
            this.tagOptions = [];
            this.primaryPhoneIndex = 0;
            this.primaryEmailIndex = 0;
            this.contact = null;
            this.formState.hideFormErrors();
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
        async fetchCustomAttributes() {
            if (!this.form.organization_id) {
                this.customAttributes = [];
                this.form.custom_attributes = {};
                return;
            }
            try {
                const response = await axios.get('/api/custom-attributes', {
                    params: { organization_id: this.form.organization_id },
                });
                this.customAttributes = response.data?.data ?? [];
                this.form.custom_attributes = this.customAttributes.reduce((acc, attr) => {
                    acc[attr.name] = '';
                    return acc;
                }, {});
            } catch (error) {
                console.error('Failed to load custom attributes:', error);
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
                // Transform the API response to match the SelectTags expected format: { label, value }
                this.tagOptions = (response.data?.data ?? []).map(tag => ({
                    label: tag.name,
                    value: tag.name,
                }));
            } catch (error) {
                console.error('Failed to load tags:', error);
            }
        },
        async onOrganizationChange() {
            await Promise.all([this.fetchCustomAttributes(), this.fetchTags()]);
        },
        addPhoneNumber() {
            this.form.phone_numbers.push({ value: '', is_primary: false });
        },
        removePhoneNumber(index) {
            if (this.form.phone_numbers[index].is_primary) {
                this.form.phone_numbers[0].is_primary = true;
                this.primaryPhoneIndex = 0;
            }
            this.form.phone_numbers.splice(index, 1);
            if (this.primaryPhoneIndex >= this.form.phone_numbers.length) {
                this.primaryPhoneIndex = this.form.phone_numbers.length - 1;
            }
        },
        setPrimaryPhone(index) {
            this.form.phone_numbers.forEach((phone, i) => {
                phone.is_primary = i === index;
            });
            this.primaryPhoneIndex = index;
        },
        addEmail() {
            this.form.emails.push({ value: '', is_primary: false });
        },
        removeEmail(index) {
            if (this.form.emails[index].is_primary) {
                this.form.emails[0].is_primary = true;
                this.primaryEmailIndex = 0;
            }
            this.form.emails.splice(index, 1);
            if (this.primaryEmailIndex >= this.form.emails.length) {
                this.primaryEmailIndex = this.form.emails.length - 1;
            }
        },
        setPrimaryEmail(index) {
            this.form.emails.forEach((email, i) => {
                email.is_primary = i === index;
            });
            this.primaryEmailIndex = index;
        },
        async updateContact(hideModal) {
            this.formState.hideFormErrors();

            // Validate organization
            if (!this.form.organization_id) {
                this.formState.setFormError('organization_id', 'Organization is required');
            }

            // Validate custom attributes (name is required)
            if (!this.form.custom_attributes.name || this.form.custom_attributes.name.trim() === '') {
                this.formState.setFormError('custom_attributes.name', 'Name is required');
            }

            // Validate phone numbers (at least one required)
            const validPhoneNumbers = this.form.phone_numbers.filter(phone => phone.value.trim() !== '');
            if (validPhoneNumbers.length === 0) {
                this.formState.setFormError('phone_numbers', 'At least one phone number is required');
            } else {
                validPhoneNumbers.forEach((phone, index) => {
                    if (!phone.value.match(/^\+[1-9]\d{1,14}$/)) {
                        this.formState.setFormError(`phone_numbers.${index}.value`, 'Phone number must be in E.164 format (e.g., +1234567890)');
                    }
                });
            }

            // Validate emails (optional, but must be valid if provided)
            const validEmails = this.form.emails.filter(email => email.value.trim() !== '');
            validEmails.forEach((email, index) => {
                if (!email.value.match(/^[a-zA-Z0-9._%+-]+@[a-zA-Z0-9.-]+\.[a-zA-Z]{2,}$/)) {
                    this.formState.setFormError(`emails.${index}.value`, 'Email address must be valid (e.g., example@domain.com)');
                }
            });

            if (this.formState.hasErrors) return;

            this.isUpdating = true;

            try {
                const payload = {
                    identifiers: [],
                    tags: this.form.tags,
                    custom_attributes: [],
                    organization_id: this.form.organization_id,
                };

                // Add phone numbers
                validPhoneNumbers.forEach(phone => {
                    payload.identifiers.push({
                        type: 'phone',
                        value: phone.value,
                        is_primary: phone.is_primary,
                    });
                });

                // Add emails
                validEmails.forEach(email => {
                    payload.identifiers.push({
                        type: 'email',
                        value: email.value,
                        is_primary: email.is_primary,
                    });
                });

                // Add custom attributes
                for (const [name, value] of Object.entries(this.form.custom_attributes)) {
                    if (value && value.trim() !== '') {
                        const customAttr = this.customAttributes.find(ca => ca.name === name);
                        if (customAttr) {
                            payload.custom_attributes.push({
                                custom_attribute_id: customAttr.id,
                                name: customAttr.name,
                                type: customAttr.type,
                                value: value,
                            });
                        }
                    }
                }

                const url = `/api/contacts/${this.contact.id}`;
                await axios.put(url, payload);

                this.notificationState.showSuccessNotification('Contact updated');
                this.$emit('updated');
                hideModal();
            } catch (error) {
                const message = error?.response?.data?.message || error?.message || 'Something went wrong updating the contact';
                this.notificationState.showWarningNotification(message);
                this.formState.setServerFormErrors(error);
                console.error('Failed to update contact:', error);
            } finally {
                this.isUpdating = false;
            }
        },
    },
};
</script>
