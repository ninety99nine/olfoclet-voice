<template>

    <div>

        <div :class="[{ 'w-full flex space-x-2 items-end' : copy }]">

            <div :class="[{ 'w-full' : copy }, 'space-y-2']">

                <div
                    v-if="$slots.label || label || secondaryLabel || showAsterisk || $slots.description || description || externalLinkName">

                    <label
                        :for="uniqueId"
                        v-if="$slots.label || label || secondaryLabel || showAsterisk"
                        :class="{ 'flex items-center text-sm leading-6 font-medium text-gray-900 space-x-1' : !$slots.label }">

                        <slot v-if="$slots.label" name="label"></slot>

                        <template v-else>

                            <span v-capitalize>{{ label }}</span>

                            <span v-if="secondaryLabel" class="font-normal text-gray-400 ml-1">{{ secondaryLabel }}</span>

                            <Popover
                                trigger="hover"
                                :content="popoverContent"
                                v-if="popoverContent || $slots.popover">
                                <slot name="popoverContent"></slot>
                            </Popover>

                            <Tooltip
                                trigger="hover"
                                :content="tooltipContent"
                                v-if="tooltipContent || $slots.tooltip">
                                <slot name="tooltipContent"></slot>
                            </Tooltip>

                            <span v-if="showAsterisk" class="text-red-500">*</span>

                        </template>

                    </label>

                    <slot v-if="$slots.description" name="description"></slot>

                    <div v-else-if="description || externalLinkName" class="leading-4">

                        <span v-if="description" class="text-xs text-gray-400 mr-1">{{ description }}</span>

                        <a
                            target="_blank"
                            :href="externalLinkUrl"
                            v-if="externalLinkName"
                            v-bind="type === 'file' ? fileEventListeners : {}"
                            class="inline-block text-xs text-blue-700 hover:underline hover:text-blue-90">
                            <span>{{ externalLinkName }}</span>
                            <svg class="w-3 h-3 inline-block ml-0.5 -mt-1" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M13.5 6H5.25A2.25 2.25 0 0 0 3 8.25v10.5A2.25 2.25 0 0 0 5.25 21h10.5A2.25 2.25 0 0 0 18 18.75V10.5m-10.5 6L21 3m0 0h-5.25M21 3v5.25"></path>
                            </svg>
                        </a>

                    </div>

                </div>

                <Skeleton
                    :shine="true"
                    v-if="skeleton"
                    :width="['radio', 'checkbox'].includes(type) ? 'h-4' : 'w-full'"
                    :rounded="['radio', 'checkbox'].includes(type) ? 'rounded-md' : 'rounded-lg'"
                    :height="type == 'file' ? 'h-20' : (['radio', 'checkbox'].includes(type) ? 'h-4' : 'h-8')">
                </Skeleton>

                <div
                    v-else
                    @drop="handleDrop"
                    @click="handleClick"
                    @dragover.prevent
                    :class="[
                        'flex',
                        wrapperAlignItems,
                        {
                            'bg-slate-50 py-2.5 px-2.5 rounded-md border border-gray-300 outline-1 -outline-offset-1 outline-gray-300 has-[input:focus-within]:outline-2 has-[input:focus-within]:-outline-offset-2 has-[input:focus-within]:outline-blue-800':
                            [
                                'text', 'password', 'email', 'number', 'tel', 'url', 'search', 'date',
                                'datetime-local', 'month', 'week', 'time', 'money', 'percentage', 'textarea'
                            ].includes(type)
                        },
                        {
                            'bg-white px-1 rounded-md border border-gray-300':
                            type == 'mobile'
                        },
                        {
                            'bg-white border border-gray-300 rounded-lg':
                            type == 'color'
                        },
                        {
                            [height]: type == 'file',
                            'cursor-pointer': type == 'file' && !(disabled || !filesLeftToUpload),
                            'select-none border-2 border-dashed border-gray-300 rounded-lg flex flex-col items-center justify-center text-gray-500 text-sm hover:bg-blue-50 transition':
                            type == 'file'
                        },
                        {
                            'opacity-50 cursor-not-allowed': disabled || hasFilesLeftToUpload === false
                        },
                        {
                            'bg-gray-100': (disabled || hasFilesLeftToUpload === false) && !['checkbox', 'radio'].includes(type)
                        }
                    ]">

                    <!-- Prefix Icon Slot -->
                    <slot v-if="$slots.prefix" name="prefix"></slot>
                    <Mail v-else-if="type == 'email'" :size="20" class="mr-2 -mt-0.5 -mb-0.5 text-slate-400"></Mail>
                    <Lock v-else-if="type == 'password'" :size="20" class="mr-2 -mt-0.5 -mb-0.5 text-slate-400"></Lock>
                    <Search v-else-if="type == 'search'" :size="20" class="mr-2 -mt-0.5 -mb-0.5 text-slate-400"></Search>
                    <div v-else-if="type == 'money' && currency" class="text-sm leading-4 mr-2">
                        {{ currencySymbol }}
                    </div>

                    <textarea
                        ref="input"
                        :rows="rows"
                        @blur="onBlur"
                        @input="onInput"
                        @focus="onFocus"
                        @change="onChange"
                        :value="modelValue"
                        :disabled="disabled"
                        :minLength="minLength"
                        :maxLength="maxLength"
                        v-if="type == 'textarea'"
                        :placeholder="inputPlaceholder"
                        :class="[
                            'w-full h-full text-sm leading-6 font-medium text-gray-700 placeholder:text-gray-400 placeholder:font-normal invalid:text-red-400 focus-within:outline-none bg-transparent appearance-none [&::-webkit-search-cancel-button]:cursor-pointer',
                            {
                                'cursor-not-allowed': disabled
                            }
                        ]">
                    </textarea>

                    <input
                        class="hidden"
                        :id="uniqueId"
                        :type="inputType"
                        @change="onChange"
                        :disabled="disabled"
                        :multiple="multiple"
                        v-else-if="type == 'file'"
                        :accept="mimeTypesAsString"
                    />

                    <IntlTelInput
                        ref="intlTelInput"
                        :value="modelValue"
                        :disabled="disabled"
                        :options="mobileOptions"
                        v-else-if="type == 'mobile'"
                        @changeNumber="changeNumber"
                        class="w-full h-full py-2 px-2.5 text-sm leading-6 font-medium text-gray-700 placeholder:text-gray-400 placeholder:font-normal invalid:text-red-400 focus-within:outline-none bg-transparent appearance-none [&::-webkit-search-cancel-button]:cursor-pointer"
                    />

                    <input
                        v-else
                        :max="max"
                        ref="input"
                        :name="name"
                        :id="uniqueId"
                        @blur="onBlur"
                        :min="inputMin"
                        @input="onInput"
                        @focus="onFocus"
                        :step="inputStep"
                        :type="inputType"
                        @change="onChange"
                        :value="modelValue"
                        :disabled="disabled"
                        :minLength="minLength"
                        :maxLength="maxLength"
                        :checked="inputChecked"
                        :placeholder="inputPlaceholder"
                        :class="[
                            {
                                'w-full h-full text-sm leading-6 font-medium text-gray-700 placeholder:text-gray-400 placeholder:font-normal invalid:text-red-400 focus-within:outline-none bg-transparent appearance-none [&::-webkit-search-cancel-button]:cursor-pointer':
                                [
                                    'text', 'password', 'email', 'number', 'tel', 'url', 'search', 'date',
                                    'datetime-local', 'month', 'week', 'time', 'money',
                                    'percentage'
                                ].includes(type)
                            },
                            {
                                'p-0':
                                [
                                    'text', 'password', 'email', 'number', 'tel', 'url', 'search', 'date',
                                    'datetime-local', 'month', 'week', 'time', 'money', 'percentage'
                                ].includes(type)
                            },
                            {
                                'w-full h-10 border-0 focus:ring-0 bg-transparent cursor-pointer appearance-none [&::-webkit-color-swatch-wrapper]:p-0 [&::-webkit-color-swatch]:rounded-md [&::-webkit-color-swatch]:border-none':
                                type == 'color'
                            },
                            {
                                'shrink-0 mt-0.5 p-1.5 border-gray-300 rounded-[4px] text-blue-600 focus:ring-0 focus:ring-offset-0 checked:border-blue-500':
                                type == 'checkbox'
                            },
                            {
                                'shrink-0 mt-0.5 p-1.5 border-gray-300 rounded-full text-blue-600 focus:ring-0 focus:ring-offset-0 checked:border-blue-500':
                                type == 'radio'
                            },
                            {
                                'cursor-not-allowed': disabled || hasFilesLeftToUpload === false
                            }
                        ]"
                    />

                    <template v-if="['radio', 'checkbox'].includes(type)">
                        <label :for="uniqueId" :class="['select-none text-sm ms-2 flex flex-col', { 'cursor-not-allowed' : disabled }]">

                            <slot name="inputLabel">
                                <span
                                    v-if="inputLabel"
                                    :class="['font-medium', modelValue ? 'text-gray-900' : 'text-gray-500']">
                                    {{ inputLabel }}
                                </span>
                            </slot>

                            <slot name="inputDescription">
                                <span v-if="inputDescription" class="text-xs text-gray-500">{{ inputDescription }}</span>
                            </slot>

                        </label>
                    </template>

                    <template v-if="type == 'file'">
                        <svg :class="['w-6 h-6 mb-2', { 'text-gray-400' : !filesLeftToUpload }]" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M12 9.75v6.75m0 0-3-3m3 3 3-3m-8.25 6a4.5 4.5 0 0 1-1.41-8.775 5.25 5.25 0 0 1 10.233-2.33 3 3 0 0 1 3.758 3.848A3.752 3.752 0 0 1 18 19.5H6.75Z" />
                        </svg>
                        <p v-if="disabled" class="text-gray-400 cursor-not-allowed">File upload is disabled</p>
                        <p v-else-if="!filesLeftToUpload" class="text-gray-400 cursor-not-allowed">Upload limit reached</p>
                        <p v-else-if="!currentFileCount">Click or Drag & Drop Images</p>
                        <p v-else>Upload More Images</p>
                    </template>

                    <!-- Suffix Icon Slot -->
                    <slot v-if="$slots.suffix" name="suffix"></slot>
                    <div v-else-if="type == 'password'" :class="[showPassword ? '' : '']">

                        <!-- Open Eye Icon -->
                        <Eye v-if="showPassword" @click="showPassword = false" size="16" class="cursor-pointer text-slate-400"></Eye>

                        <!-- Closed Eye Icon -->
                        <EyeOff v-else @click="showPassword = true" size="16" class="cursor-pointer text-slate-400"></EyeOff>

                    </div>
                    <svg v-else-if="type == 'percentage'" class="h-3 w-3 ml-1" fill="#374151" version="1.1" id="Capa_1" xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" viewBox="0 0 263.285 263.285" xml:space="preserve">
                        <g>
                            <path d="M193.882,8.561c-7.383-3.756-16.414-0.813-20.169,6.573L62.153,234.556c-3.755,7.385-0.812,16.414,6.573,20.169
                                c2.178,1.107,4.499,1.632,6.786,1.632c5.466,0,10.735-2.998,13.383-8.205L200.455,28.73
                                C204.21,21.345,201.267,12.316,193.882,8.561z"/>
                            <path d="M113.778,80.818c0-31.369-25.521-56.89-56.89-56.89C25.521,23.928,0,49.449,0,80.818c0,31.368,25.521,56.889,56.889,56.889
                                C88.258,137.707,113.778,112.186,113.778,80.818z M56.889,107.707C42.063,107.707,30,95.644,30,80.818
                                c0-14.827,12.063-26.89,26.889-26.89c14.827,0,26.89,12.062,26.89,26.89C83.778,95.644,71.716,107.707,56.889,107.707z"/>
                            <path d="M206.396,125.58c-31.369,0-56.89,25.521-56.89,56.889c0,31.368,25.52,56.889,56.89,56.889
                                c31.368,0,56.889-25.52,56.889-56.889C263.285,151.1,237.765,125.58,206.396,125.58z M206.396,209.357
                                c-14.827,0-26.89-12.063-26.89-26.889c0-14.826,12.063-26.889,26.89-26.889c14.826,0,26.889,12.063,26.889,26.889
                                C233.285,197.294,221.223,209.357,206.396,209.357z"/>
                        </g>
                    </svg>

                </div>

                <template v-if="type == 'file'">

                    <!-- Image Previews -->
                    <div v-if="currentFileCount"
                        :class="[
                            'select-none grid gap-2',
                            { 'grid-cols-1' : imagePreviewGridCols == 1 },
                            { 'grid-cols-1 md:grid-cols-2' : imagePreviewGridCols == 2 },
                            { 'grid-cols-1 md:grid-cols-2 lg:grid-cols-3' : imagePreviewGridCols == 3 },
                        ]">

                        <div v-for="(file, fileIndex) in modelValue" :key="fileIndex" class="relative group">

                            <template v-if="!file.uploading">

                                <!-- Success Tick -->
                                <div v-if="file.uploaded === true" class="absolute z-10 top-1 right-1 bg-green-500 text-white rounded-full w-5 h-5 flex items-center justify-center text-xs">
                                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="m4.5 12.75 6 6 9-13.5" />
                                    </svg>
                                </div>

                                <!-- Failure Exclamation -->
                                <div v-if="file.uploaded === false" class="w-5 h-5 sm:hidden lg:block group-hover:hidden absolute z-10 top-1 right-1 bg-red-500 text-white rounded-full">
                                    <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="currentColor">
                                        <path fill-rule="evenodd" d="M2.25 12c0-5.385 4.365-9.75 9.75-9.75s9.75 4.365 9.75 9.75-4.365 9.75-9.75 9.75S2.25 17.385 2.25 12ZM12 8.25a.75.75 0 0 1 .75.75v3.75a.75.75 0 0 1-1.5 0V9a.75.75 0 0 1 .75-.75Zm0 8.25a.75.75 0 1 0 0-1.5.75.75 0 0 0 0 1.5Z" clip-rule="evenodd" />
                                    </svg>
                                </div>

                                <!-- Retry Button -->
                                <div
                                    v-if="file.uploaded === false"
                                    @click.stop="() => onUpload(modelValue, fileIndex)"
                                    class="flex items-center justify-center w-8 h-8 rounded-full cursor-pointer bg-yellow-500 text-white hover:bg-yellow-600 active:scale-95 absolute z-10 top-1/2 left-1/2 transform -translate-x-1/2 -translate-y-1/2">
                                    <svg class="w-5 h-5" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="currentColor">
                                        <path fill-rule="evenodd" d="M4.755 10.059a7.5 7.5 0 0 1 12.548-3.364l1.903 1.903h-3.183a.75.75 0 1 0 0 1.5h4.992a.75.75 0 0 0 .75-.75V4.356a.75.75 0 0 0-1.5 0v3.18l-1.9-1.9A9 9 0 0 0 3.306 9.67a.75.75 0 1 0 1.45.388Zm15.408 3.352a.75.75 0 0 0-.919.53 7.5 7.5 0 0 1-12.548 3.364l-1.902-1.903h3.183a.75.75 0 0 0 0-1.5H2.984a.75.75 0 0 0-.75.75v4.992a.75.75 0 0 0 1.5 0v-3.18l1.9 1.9a9 9 0 0 0 15.059-4.035.75.75 0 0 0-.53-.918Z" clip-rule="evenodd" />
                                    </svg>
                                </div>

                                <!-- Remove Image Button -->
                                <div
                                    v-if="(!file.uploaded && !file.uploading)"
                                    @click.stop="(event) => removeFile(event, fileIndex)"
                                    class="w-5 h-5 lg:opacity-0 group-hover:opacity-100 active:scale-90 transition cursor-pointer flex items-center justify-center absolute z-10 top-1 right-1 bg-red-500 text-white rounded-full">
                                    <div v-if="isTemporaryFile(fileIndex)" class="-mt-0.5">x</div>
                                    <svg v-else class="w-3 h-3" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="m14.74 9-.346 9m-4.788 0L9.26 9m9.968-3.21c.342.052.682.107 1.022.166m-1.022-.165L18.16 19.673a2.25 2.25 0 0 1-2.244 2.077H8.084a2.25 2.25 0 0 1-2.244-2.077L4.772 5.79m14.456 0a48.108 48.108 0 0 0-3.478-.397m-12 .562c.34-.059.68-.114 1.022-.165m0 0a48.11 48.11 0 0 1 3.478-.397m7.5 0v-.916c0-1.18-.91-2.164-2.09-2.201a51.964 51.964 0 0 0-3.32 0c-1.18.037-2.09 1.022-2.09 2.201v.916m7.5 0a48.667 48.667 0 0 0-7.5 0" />
                                    </svg>
                                </div>

                                <!-- Remove Image Button -->
                                <div
                                    v-if="(file.hasOwnProperty('qrCode') && !file.qrCode.valid)"
                                    @click.stop="(event) => removeFile(event, fileIndex)"
                                    class="w-full flex items-center justify-center absolute z-10 bottom-2">
                                    <div class="bg-yellow-500 text-xs text-white rounded-full px-2">Invalid QR code</div>
                                </div>

                                <!-- Failed Indicator -->
                                <div v-if="file.uploaded === false" class="absolute inset-0 bg-white bg-opacity-80 border border-red-500 rounded-lg flex items-center justify-center"></div>

                            </template>

                            <!-- Uploading Indicator -->
                            <div v-if="file.uploading" class="absolute inset-0 bg-gray-900 bg-opacity-50 rounded-lg flex items-center justify-center">
                                <span class="text-white text-xs font-bold">Uploading...</span>
                            </div>

                            <!-- Image -->
                            <img
                                :src="file.filePath"
                                class="w-full h-24 p-4 object-contain rounded-lg border border-gray-300 dark:border-gray-700"
                            />

                        </div>

                    </div>

                    <div v-if="maxFiles >= 2 && hasFailedUploads" :class="['flex justify-end mt-2']">

                        <Button
                            size="xs"
                            type="warning"
                            icon="refresh"
                            :disabled="isUploading"
                            :action="() => onUpload(modelValue)">
                            <span>Retry Uplaods</span>
                        </Button>

                    </div>

                </template>

            </div>

            <Copy v-if="copy" :text="modelValue" :showText="false" class="w-10 h-10 rounded-md border border-gray-300 flex items-center justify-center" />

        </div>

        <span v-if="errorText || inputError" class="scroll-to-error font-medium text-red-500 text-xs mt-1 ml-1">
            {{ errorText ?? inputError }}
        </span>

    </div>

</template>

<script>

    import "intl-tel-input/styles";
    import Copy from '@Partials/Copy.vue';
    import debounce from 'lodash.debounce';
    import cloneDeep from 'lodash.clonedeep';
    import Button from '@Partials/Button.vue';
    import Popover from '@Partials/Popover.vue';
    import Tooltip from '@Partials/Tooltip.vue';
    import Skeleton from '@Partials/Skeleton.vue';
    import currencies from '@Json/currencies.json';
    import capitalize from '@Directives/capitalize.js';
    import IntlTelInput from "intl-tel-input/vueWithUtils";
    import { generateUniqueId } from '@Utils/generalUtils.js';
    import { convertToValidMoney } from '@Utils/numberUtils.js';
    import { parsePhoneNumberFromString } from 'libphonenumber-js';
    import { Eye, Mail, Lock, Search, EyeOff } from 'lucide-vue-next';

    export default {
        inject: ['notificationState'],
        directives: { capitalize },
        components: { Eye, Copy, Mail, Lock, Search, EyeOff, Button, Popover, Tooltip, Skeleton, IntlTelInput },
        props: {
            modelValue: {
                type: [String, Boolean, Array, null]
            },
            type: {
                type: String,
                default: 'text',
                validator: (value) => [
                    'text', 'password', 'email', 'number', 'tel', 'url', 'search',
                    'date', 'datetime-local', 'month', 'week', 'time', 'color',
                    'file', 'checkbox', 'radio', 'mobile', 'money',
                    'percentage', 'textarea'
                ].includes(value)
            },
            label: {
                type: [String, null],
                default: null
            },
            secondaryLabel: {
                type: [String, null],
                default: null
            },
            popoverContent: {
                type: [String, null],
                default: null
            },
            tooltipContent: {
                type: [String, null],
                default: null
            },
            showAsterisk: {
                type: Boolean,
                default: false
            },
            description: {
                type: [String, null],
                default: null
            },
            externalLinkName: {
                type: [String, null],
                default: null
            },
            externalLinkUrl: {
                type: [String, null],
                default: null
            },
            placeholder: {
                type: [String, null],
                default: null
            },
            disabled: {
                type: Boolean,
                default: false
            },
            skeleton: {
                type: Boolean,
                default: false
            },
            alignItems: {
                type: [String, null]
            },
            errorText: {
                type: [String, null],
                default: null
            },

            //  Text
            minLength: {
                type: [String, null],
                default: null
            },
            maxLength: {
                type: [String, null],
                default: null
            },

            //  Textarea
            rows: {
                type: [String, null],
                default: '4'
            },

            //  Number
            step: {
                type: [String, null],
                default: null
            },
            min: {
                type: [String, null],
                default: null
            },
            max: {
                type: [String, null],
                default: null
            },
            currency: {
                type: [String, null],
                default: null
            },
            allowNegativeAmounts: {
                type: Boolean,
                default: false
            },

            //  Text
            minLength: {
                type: [String, null],
                default: null
            },
            maxLength: {
                type: [String, null],
                default: null
            },

            //  Checkbox & Radio
            inputLabel: {
                type: [String, null],
                default: null
            },
            inputDescription: {
                type: [String, null],
                default: null
            },

            // Radio
            name: {
                type: [String, null],
                default: null
            },
            radioValue: {
                type: [String, null],
                default: null
            },

            //  File
            height: {
                type: [String, null],
                default: 'h-20'
            },
            maxFiles: {
                type: [Number, null],
                default: 5,
            },
            mimeTypes: {
                type: [Array, null],
                default: () => ["image/*"],
            },
            maxSizeMB: {
                type: [Number, null],
                default: 10
            },
            imagePreviewGridCols: {
                type: [Number, null],
                validator: (value) => [1, 2, 3].includes(value),
                default: 3
            },
            validateQRCode: {
                type: Boolean,
                default: false
            },
            onUpload: {
                type: Function,
                default: () => {},
            },
            onDelete: {
                type: Function,
                default: () => {},
            },

            debounced: {
                type: Boolean,
                default: false
            },

            copy: {
                type: Boolean,
                default: false
            },

            emits: ['update:modelValue', 'focus', 'blur'],
        },
        data() {
            return {
                iti: null,
                mobileError: null,
                focusedValue: null,
                debouncedEmit: null,
                showPassword: false,
                uniqueId: generateUniqueId('input'),
                mobileOptions: {
                    initialCountry: 'auto',
                    separateDialCode: true,
                    //  geoIpLookup: this.geoIpLookup,
                    loadUtilsOnInit: () => import("intl-tel-input/utils")
                }
            };
        },
        computed: {
            wrapperAlignItems() {
                if(this.alignItems) return this.alignItems;
                if(['radio', 'checkbox'].includes(this.type)) return 'items-start';
                return 'items-center';
            },
            inputType() {
                if(this.type == 'mobile' || this.showPassword) return 'text';
                if(['money', 'percentage'].includes(this.type)) return 'number';
                return this.type;
            },
            inputMin() {
                if (this.min) return this.min;
                if (this.type === 'money' && !this.allowNegativeAmounts) return '0';
                return null;
            },
            inputStep() {
                if (this.step) return this.step;

                if (this.type === 'number') return '1';
                if (this.type === 'money') {
                    const decimalPlaces = currencies[this.currency]?.decimal_digits || 2;
                    return (1 / Math.pow(10, decimalPlaces)).toFixed(decimalPlaces);
                }
                if (this.type === 'percentage') return '.1';

                return null;
            },
            inputPlaceholder() {
                if (this.placeholder) return this.placeholder;

                if (this.type === 'money') {
                    const decimalPlaces = currencies[this.currency]?.decimal_digits || 2; // Default to 2 decimals
                    return `10.${'0'.repeat(decimalPlaces)}`; // Generates "10.00" or "10.000"
                }

                return null;
            },
            inputChecked() {
                if(this.type == 'checkbox') return this.modelValue;
                if(this.type == 'radio') return this.modelValue == this.radioValue;
                return null;
            },
            hasPrefix() {
                return this.$slots.prefix || ['email'].includes(this.type);
            },
            inputError() {
                if(this.modelValue && ['text', 'password', 'email', 'url', 'search'].includes(this.type)) {

                    if (!this.minLength) return null;

                    const minLength = parseInt(this.minLength, 10);

                    if (this.modelValue.length < minLength) {
                        return `Enter ${minLength} or more characters`;
                    }

                }else if(this.mobileError) {

                    return this.mobileError;

                }

                return null;
            },
            currencySymbol() {
                if (this.currency && currencies[this.currency]) {
                    return currencies[this.currency].symbol_native;
                }
                return ''; // Default fallback
            },
            currentFileCount() {
                return this.type == 'file' ? this.modelValue.length : 0;
            },
            filesLeftToUpload() {
                return this.type == 'file' ? this.maxFiles - this.currentFileCount : 0;
            },
            hasFilesLeftToUpload() {
                return this.type == 'file' ? this.filesLeftToUpload > 0 : null;
            },
            mimeTypesAsString() {
                return Array.isArray(this.mimeTypes) ? this.mimeTypes.join(',') : '';
            },
            multiple() {
                return this.maxFiles > 1;
            },
            hasFailedUploads() {
                return this.modelValue.filter(file => file.uploaded === false).length > 0;
            },
            isUploading() {
                return this.modelValue.filter(file => file.uploading === true).length > 0;
            },
        },
        methods: {
            onInput(event) {
                console.log("onInput event:", event.target.value);
                if (this.disabled) return;

                if (!['file', 'checkbox', 'radio'].includes(this.type)) {
                    this.updateModalValue(event.target.value);
                }
            },
            onFocus() {
                console.log("onFocus 1");
                if(this.disabled) return;
                console.log("onFocus 2");
                this.focusedValue = this.modelValue;
                this.$emit('focus', this.modelValue);
            },
            onBlur() {
                console.log("onBlur");
                if(this.disabled) return;
                if(this.modelValue != this.focusedValue) {
                    if(this.type == 'money') {
                        this.updateModalValue(convertToValidMoney(this.modelValue, this.currency, this.allowNegativeAmounts));
                    };
                    this.$emit('blur', this.modelValue);
                }
            },
            onChange(event) {
                console.log("onChange");
                if(this.disabled) return;
                if(this.type == 'file') {
                    this.handleFileUpload(event);
                }else if(this.type == 'radio') {
                    this.updateModalValue(this.radioValue);
                }else if(this.type == 'checkbox') {
                    this.updateModalValue(event.target.checked);
                }
            },
            changeNumber(newMobileNumber) {
                if(newMobileNumber) {
                    const phoneNumber = parsePhoneNumberFromString(newMobileNumber);

                    if (phoneNumber && phoneNumber.isValid()) {
                        const formatted = phoneNumber.formatInternational();
                        this.updateModalValue(formatted);
                        this.mobileError = null;
                    } else {
                        this.mobileError = 'Invalid mobile number. Please check the format.';
                    }
                }
            },
            handleClick(event) {
                if(this.disabled) return;
                if(this.type == 'file') {
                    if(!this.filesLeftToUpload) return;
                    this.triggerFileInput(event);
                }else{
                    this.focusInput();
                }
            },
            triggerFileInput() {
                this.$refs.input.click();
            },
            focusInput() {
                if(this.type == 'mobile') {
                    this.$refs.intlTelInput.input.focus();
                }else{
                    this.$refs.input.focus();
                }
            },
            handleDrop(event) {
                if(this.disabled || !this.filesLeftToUpload) return;
                if(this.type == 'file') {
                    event.preventDefault();
                    const files = event.dataTransfer.files;
                    this.processFiles(files);
                }
            },
            handleFileUpload(event) {
                console.log('handleFileUpload');
                const files = event.target.files;

                if (!files.length) return;

                this.processFiles(files);

                this.$nextTick(() => {
                    this.$refs.input.value = '';
                });
            },
            processFiles(files) {

                console.log('processFiles');
                if (!files.length) return;

                if (this.filesLeftToUpload <= 0) {
                    this.notificationState.showWarningNotification(`You can only upload up to ${this.maxFiles} files.`);
                    return;
                }

                const validFiles = Array.from(files)
                    .slice(0, this.filesLeftToUpload)
                    .filter((file) => {

                        const isAllowed = this.mimeTypes.some((allowedType) =>
                            allowedType === file.type || (allowedType.endsWith("/*") && file.type.startsWith(allowedType.split("/")[0]))
                        );

                        if (!isAllowed) {
                            this.notificationState.showWarningNotification(
                                `Invalid file type: ${file.type}. Allowed types: ${this.mimeTypes.join(", ")}`
                            );
                            return false;
                        }

                        if (file.size > this.maxSizeMB * 1024 * 1024) {
                            this.notificationState.showWarningNotification(
                                `File ${file.name} exceeds the ${this.maxSizeMB}MB limit.`
                            );
                            return false;
                        }

                        return true;

                    });

                let newFiles = [];

                const filePromises = validFiles.map((file) => {
                    return new Promise(async (resolve) => {
                        const reader = new FileReader();
                        reader.onload = async (e) => {

                            let newFile = {
                                filePath: URL.createObjectURL(file),
                                uploading: false,
                                deleting: false,
                                uploaded: null,
                                fileRef: file,
                            }

                            if (this.validateQRCode) {
                                newFile = await this.checkFileForValidQrCode(newFile);
                            }

                            newFiles.push(newFile);
                            resolve();
                        };
                        reader.readAsDataURL(file);
                    });
                });

                Promise.all(filePromises).then(() => {

                    console.log('emit files');

                    this.updateModalValue([...this.modelValue, ...newFiles]);

                    if (this.onUpload) {
                        this.onUpload(this.modelValue);
                    }
                });
            },
            isTemporaryFile(fileIndex) {
                return this.modelValue[fileIndex].filePath && this.modelValue[fileIndex].filePath.startsWith("blob:");
            },
            removeFile(event, fileIndex) {

                event.preventDefault();
                event.stopPropagation();

                if (this.isTemporaryFile(fileIndex)) {

                    let files = cloneDeep(this.modelValue);
                    files.splice(fileIndex, 1);

                    this.updateModalValue(files);

                }else if (this.onDelete) {
                    this.onDelete(this.modelValue, fileIndex);
                }
            },
            async checkFileForValidQrCode(newFile) {

                try {

                    // Dynamically import QrScanner
                    const QrScanner = (await import('qr-scanner')).default;

                    // Scan QR code
                    const scanResult = await QrScanner.scanImage(newFile.filePath, { returnDetailedScanResult: true });

                    // Extract QR data
                    const qrData = scanResult.data;

                    if (qrData) {

                        return {
                            ...newFile,
                            qrCode: {
                                valid: true,
                                data: qrData
                            }
                        };

                    } else {

                        throw new Error("Invalid QR code");

                    }

                } catch (error) {

                    return {
                        ...newFile,
                        qrCode: {
                            valid: false,
                            errorMessage: error
                        }
                    };

                }
            },
            geoIpLookup(callback) {
                const cachedCountry = sessionStorage.getItem('geoip_country');
                if(cachedCountry) {
                    callback(cachedCountry); // Use the cached country
                    return;
                }

                fetch('https://ipinfo.io?token=' + this.ipInfoToken)
                    .then(response => response.json())
                    .then((data) => {
                        const country = data.country || 'US';
                        sessionStorage.setItem('geoip_country', country); // Cache the result
                        callback(country);
                    })
                    .catch(() => callback('US')); // Fallback to 'US' if there's an error
            },
            updateModalValue(value) {
                if(this.debounced) {
                    this.debouncedEmit(value);
                }else{
                    this.$emit('update:modelValue', value);
                }
            }
        },
        created() {
            this.debouncedEmit = debounce((value) => {
                this.$emit('update:modelValue', value);
            }, 1000);
        },
    }

</script>
