<template>

    <Modal
        :onShow="onShow"
        :showFooter="false"
        :scrollOnContent="false"
        :header="mobileNumber ? 'Edit Mobile Number' : 'Add Mobile Number'">

        <template #content="triggerProps">

            <div>

                <!-- Mobile Number Input -->
                <Input
                    type="mobile"
                    label="Mobile Number"
                    :key="InputKey"
                    v-model="form.mobileNumber"
                    :errorText="formState.getFormError('mobileNumber')">

                </Input>

                <div class="flex space-x-2 mt-4">

                    <!-- Delete Button -->
                    <Button
                        size="sm"
                        type="danger"
                        v-if="mobileNumber"
                        :disabled="isSubmitting"
                        :action="() => _deleteMobileNumber(triggerProps.hideModal)">
                        <span>Delete</span>
                    </Button>

                    <!-- Save Changes / Add Mobile Number Button -->
                    <Button
                        size="sm"
                        type="success"
                        class="w-full"
                        :disabled="!mustSaveChanges"
                        :action="() => mobileNumber ? _updateMobileNumber(triggerProps.hideModal) : _createMobileNumber(triggerProps.hideModal)">
                        <span>{{ mobileNumber ? 'Save Changes' : 'Add Mobile Number' }}</span>
                    </Button>

                </div>

            </div>

        </template>

        <template #trigger="triggerProps">

            <!-- Edit Mobile Number / Add Mobile Number Button - Triggers Modal -->
            <div :class="bodyClass">

                <!-- Content -->
                <slot name="content"></slot>

                <template v-if="hasMobileNumbers">

                    <div class="flex flex-wrap gap-2">

                        <!-- Prefix content -->
                        <slot name="prefix"></slot>

                        <Pill
                            size="xs"
                            :key="index"
                            type="primary"
                            :showDot="false"
                            v-for="(nationalMobileNumber, index) in nationalMobileNumbers"
                            :action="() => showMobileNumber(triggerProps.showModal, modelValue[index], index)">

                            <div class="flex items-center">
                                <span class="mx-2">{{ nationalMobileNumber }}</span>
                                <svg class="w-3 h-3" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="m16.862 4.487 1.687-1.688a1.875 1.875 0 1 1 2.652 2.652L6.832 19.82a4.5 4.5 0 0 1-1.897 1.13l-2.685.8.8-2.685a4.5 4.5 0 0 1 1.13-1.897L16.863 4.487Zm0 0L19.5 7.125" />
                                </svg>
                            </div>

                        </Pill>

                        <Button
                            size="sm"
                            icon="add"
                            class="w-48"
                            rounded="rounded-full"
                            :action="() => showMobileNumber(triggerProps.showModal, null, null)">
                            <span class="text-xs">Add Mobile Number</span>
                        </Button>

                    </div>

                </template>

                <Button v-else type="light" size="xs" :action="() => showMobileNumber(triggerProps.showModal, null, null)" class="w-48">
                    <span class="whitespace-nowrap ml-2">Add Mobile Number</span>
                </Button>

            </div>

        </template>

    </Modal>

</template>


<script>

    import isEqual from 'lodash.isequal';
    import Pill from '@Partials/Pill.vue';
    import Input from '@Partials/Input.vue';
    import Modal from '@Partials/Modal.vue';
    import cloneDeep from 'lodash.clonedeep';
    import Button from '@Partials/Button.vue';
    import { parsePhoneNumberFromString } from 'libphonenumber-js';

    export default {
        inject: ['formState', 'notificationState'],
        components: { Pill, Input, Modal, Button  },
        props: {
            modelValue: {
                type: [String, Array]
            },
            isSubmitting: {
                type: Boolean,
                default: false
            },
            createMobileNumber: {
                type: Function,
                default: null
            },
            updateMobileNumber: {
                type: Function,
                default: null
            },
            deleteMobileNumber: {
                type: Function,
                default: null
            },
            bodyClass: {
                type: String,
                default: null
            },
        },
        data() {
            return {
                form: {
                    mobileNumber: ''
                },
                InputKey: 0,
                originalForm: null,
                mobileNumber: null,
                mobileNumberIndex: null,
            };
        },
        computed: {
            hasMobileNumbers() {
                return this.modelValue.length > 0;
            },
            nationalMobileNumbers() {
                return this.modelValue.map((mobileNumber) => {

                    const phoneNumber = parsePhoneNumberFromString(mobileNumber);
                    let nationalNumber =  phoneNumber.formatNational();
                    return nationalNumber.replace(/\s+/g, '');

                });
            },
            formHasChanged() {
                return !isEqual(this.form, this.originalForm);
            },
            mustSaveChanges() {
                return this.formHasChanged && !this.isSubmitting;
            },
        },
        methods: {
            onShow() {
                ++this.InputKey;
            },
            showMobileNumber(showModal, mobileNumber, mobileNumberIndex) {
                this.mobileNumberIndex = mobileNumberIndex;
                this.mobileNumber = mobileNumber;

                if(this.mobileNumber) {
                    this.setForm();
                }else{
                    this.resetForm();
                }

                showModal();

            },
            setForm() {
                this.form.mobileNumber = this.mobileNumber;
                this.originalForm = cloneDeep(this.form);
            },
            resetForm() {
                this.form.mobileNumber = '';
                this.originalForm = cloneDeep(this.form);
            },
            _createMobileNumber(hideModal) {

                // Check if the mobile number already exists
                const mobileNumberExists = this.modelValue.some(
                    (number) => number === this.form.mobileNumber
                );

                // If it exists, prevent adding and possibly show a warning
                if(mobileNumberExists) {
                    this.notificationState.showWarningNotification('Mobile number already exists');
                    hideModal();
                    return;
                }

                if(this.createMobileNumber) {
                    this.createMobileNumber(
                        this.form.mobileNumber, this.resetForm, hideModal
                    );
                }else{
                    this.modelValue.push(this.form.mobileNumber);
                    this.$emit('change', this.modelValue);
                    this.resetForm();
                    hideModal();
                }

            },
            _updateMobileNumber(hideModal) {

                // Check if the mobile number already exists
                const mobileNumberExists = this.modelValue.some(
                    (number) => number === this.form.mobileNumber
                );

                // If it exists, prevent adding and possibly show a warning
                if(mobileNumberExists) {
                    this.notificationState.showWarningNotification('Mobile number already exists');
                    hideModal();
                    return;
                }

                if(this.updateMobileNumber) {
                    this.updateMobileNumber(
                        this.form.mobileNumber, this.mobileNumberIndex, this.resetForm, hideModal
                    );
                }else{
                    this.modelValue.splice(this.mobileNumberIndex, 1, this.form.mobileNumber);
                    this.$emit('change', this.modelValue);
                    this.resetForm();
                    hideModal();
                }

            },
            _deleteMobileNumber(hideModal) {

                if(this.deleteMobileNumber) {
                    this.deleteMobileNumber(
                        this.mobileNumberIndex, this.resetForm, hideModal
                    );
                }else{
                    this.modelValue.splice(this.mobileNumberIndex, 1);
                    this.$emit('change', this.modelValue);
                    this.resetForm();
                    hideModal();
                }

            },
        }
    };

</script>
