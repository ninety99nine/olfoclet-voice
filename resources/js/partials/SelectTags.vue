<template>
    <div>
        <div class="space-y-2">
            <div>
                <label
                    :for="uniqueId"
                    v-if="$slots.label || label || secondaryLabel || showAsterisk"
                    :class="{ 'text-sm leading-6 font-medium text-gray-900 space-x-1' : !$slots.label }">
                    <slot v-if="$slots.label" name="label"></slot>
                    <template v-else>
                        <span v-capitalize>{{ label }}</span>
                        <span v-if="secondaryLabel" class="font-normal text-gray-400 ml-1">{{ secondaryLabel }}</span>
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

            <div
                :class="[
                    'w-full flex',
                    alignItems,
                    { 'opacity-50': disabled }
                ]">
                <!-- Prefix Icon Slot -->
                <slot v-if="$slots.prefix" name="prefix"></slot>

                <div class="w-full relative" ref="dropdown">
                    <!-- Select Input -->
                    <div
                        @click="toggleDropdown"
                        :class="[
                            'w-full select-none bg-white border border-gray-300 rounded-md p-2 flex flex-wrap gap-2 items-center overflow-hidden',
                            disabled ? 'cursor-not-allowed' : 'cursor-pointer'
                        ]">
                        <!-- Draggable Support -->
                        <draggable
                            v-if="isDraggable"
                            v-model="selectedOptions"
                            handle=".draggable-handle"
                            ghost-class="bg-yellow-50"
                            class="flex flex-wrap gap-2">
                            <div v-for="(selectedOption, index) in selectedOptions" :key="index" class="draggable-handle">
                                <slot name="selectedOption" :selectedOption="selectedOption" :removeOption="() => removeOption(selectedOption)">
                                    <div class="flex items-center space-x-1 bg-gray-100 rounded-full px-2 py-1">
                                        <span class="text-xs font-medium text-gray-900">{{ selectedOption.label }}</span>
                                        <button @click.prevent.stop="() => removeOption(selectedOption)" class="focus:outline-none">
                                            <svg class="w-3 h-3 text-gray-500 hover:text-gray-700" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                                            </svg>
                                        </button>
                                    </div>
                                </slot>
                            </div>
                        </draggable>

                        <!-- If not draggable -->
                        <template v-else>
                            <template v-for="(selectedOption, index) in selectedOptions" :key="index">
                                <slot name="selectedOption" :selectedOption="selectedOption" :removeOption="() => removeOption(selectedOption)">
                                    <div class="flex items-center space-x-1 bg-gray-100 rounded-full px-2 py-1">
                                        <span class="text-xs font-medium text-gray-900">{{ selectedOption.label }}</span>
                                        <button @click.prevent.stop="() => removeOption(selectedOption)" class="focus:outline-none">
                                            <svg class="w-3 h-3 text-gray-500 hover:text-gray-700" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                                            </svg>
                                        </button>
                                    </div>
                                </slot>
                            </template>
                        </template>

                        <input
                            type="text"
                            ref="search"
                            v-if="search"
                            @keydown.stop
                            :id="uniqueId"
                            :disabled="disabled"
                            v-model="searchQuery"
                            :placeholder="placeholder"
                            @click.stop="openDropdown"
                            @keydown.enter.prevent="handleEnterKey"
                            :class="[
                                'flex-1 outline-none text-sm text-gray-900 placeholder:text-gray-400 border-0 focus:ring-0 py-1',
                                hasSelectedOptions ? 'px-2' : 'px-0'
                            ]"
                        />
                    </div>

                    <!-- Dropdown Options -->
                    <div v-if="isOpen" class="w-full absolute z-10 mt-1 select-none bg-white border border-gray-300 rounded-md shadow-lg overflow-hidden">
                        <!-- Scrollable Options List -->
                        <ul class="max-h-60 overflow-auto">
                            <li
                                :key="index"
                                @click="() => toggleOption(option)"
                                v-for="(option, index) in filteredOptions"
                                :class="[
                                    'px-4 py-2 text-sm flex justify-between items-center hover:bg-gray-100',
                                    option.disabled ? 'cursor-not-allowed text-gray-400' : 'cursor-pointer text-gray-700',
                                ]">
                                <!-- Custom Slot Support -->
                                <slot name="option" :option="option" :isSelected="isSelected(option)">
                                    <!-- Default Option Layout -->
                                    <span class="truncate">{{ option.label }}</span>
                                </slot>
                                <!-- Default Selected Icon -->
                                <span v-if="isSelected(option)">
                                    <svg class="w-4 h-4 text-blue-500" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                                    </svg>
                                </span>
                            </li>

                            <li
                                @click="addCustomOption"
                                v-if="allowCustom && hasSearchQuery && !isOptionInList(searchQuery)"
                                class="w-full flex items-center justify-between px-4 py-2 cursor-pointer hover:bg-gray-50 text-gray-700">
                                <div class="w-full flex items-center gap-3">
                                    <div class="flex items-center space-x-2 bg-gray-100 rounded-full px-3 py-1 border shadow-sm">
                                        <span class="text-xs font-medium text-gray-900">{{ searchQuery }}</span>
                                    </div>
                                    <span class="text-xs text-gray-500">{{ addNewText }}</span>
                                </div>
                                <div class="size-6 flex flex-col justify-center items-center bg-white border border-gray-200 text-xs uppercase text-gray-400 shadow-2xs rounded-sm dark:bg-neutral-800 dark:border-neutral-700">
                                    <svg class="shrink-0 size-3.5" xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                        <polyline points="9 10 4 15 9 20"></polyline>
                                        <path d="M20 4v7a4 4 0 0 1-4 4H4"></path>
                                    </svg>
                                </div>
                            </li>
                        </ul>

                        <!-- No Results Found -->
                        <div v-if="filteredOptions.length === 0 && !(allowCustom && hasSearchQuery)" class="text-center px-4 py-2 text-gray-500 text-sm">
                            {{ noResultsText }}
                        </div>
                    </div>
                </div>

                <!-- Suffix Icon Slot -->
                <slot v-if="$slots.suffix" name="suffix"></slot>
            </div>
        </div>

        <span v-if="errorText" class="scroll-to-error font-medium text-red-500 text-xs mt-1 ml-1">
            {{ errorText }}
        </span>

    </div>
</template>

<script>
    import capitalize from '@Directives/capitalize.js';
    import { VueDraggableNext } from 'vue-draggable-next';
    import { generateUniqueId } from '@Utils/generalUtils.js';

    export default {
        directives: { capitalize },
        components: { draggable: VueDraggableNext },
        props: {
            modelValue: {
                type: Array,
                default: () => []
            },
            label: {
                type: [String, null],
                default: null
            },
            secondaryLabel: {
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
            alignItems: {
                type: [String, null],
                default: 'items-center'
            },
            errorText: {
                type: [String, null],
                default: null
            },
            options: {
                type: Array,
                default: () => []
            },
            search: {
                type: Boolean,
                default: true
            },
            noResultsText: {
                type: [String, null],
                default: 'No tags available'
            },
            searchableFields: {
                type: [Array, null],
                default: () => ['label', 'value']
            },
            isDraggable: {
                type: Boolean,
                default: true
            },
            allowCustom: {
                type: Boolean,
                default: true
            },
            addNewText: {
                type: [String, null],
                default: '+ Create'
            },
            onAddCustomOption: {
                type: Function,
                default: null
            },
        },
        emits: ['update:modelValue', 'update:options', 'change'],
        data() {
            return {
                isOpen: false,
                searchQuery: null,
                uniqueId: generateUniqueId('input')
            };
        },
        computed: {
            localModelValue: {
                get() {
                    return this.modelValue;
                },
                set(value) {
                    this.$emit("update:modelValue", value);
                    this.$emit("change", value);
                }
            },
            localOptions: {
                get() {
                    return this.options;
                },
                set(value) {
                    this.$emit("update:options", value);
                }
            },
            selectedOptions: {
                get() {
                    // Map modelValue (array of values) to option objects
                    return this.modelValue.map(value => {
                        const option = this.localOptions.find(option => option.value === value);
                        if (option) return option;
                        // If the option doesn't exist in localOptions, create it (for custom options)
                        return { label: value, value: value };
                    }).filter(option => option);
                },
                set(value) {
                    this.localModelValue = value.map(option => option.value);
                }
            },
            hasSelectedOptions() {
                return this.selectedOptions.length > 0;
            },
            hasSearchQuery() {
                return this.searchQuery && this.searchQuery.trim().length > 0;
            },
            filteredOptions() {
                if (!this.searchQuery) return this.localOptions;
                return this.localOptions.filter(option =>
                    this.searchableFields.some(field =>
                        option[field]?.toLowerCase().includes(this.searchQuery.toLowerCase())
                    )
                );
            }
        },
        methods: {
            openDropdown() {
                if (this.disabled) return;
                if (!this.isOpen) {
                    this.isOpen = true;
                }
                this.focusOnInput();
            },
            toggleDropdown() {
                if (this.disabled) return;
                this.isOpen = !this.isOpen;
                this.focusOnInput();
            },
            focusOnInput() {
                this.$nextTick(() => {
                    if (this.$refs.search && document.activeElement !== this.$refs.search) {
                        this.$refs.search.focus();
                    }
                });
            },
            toggleOption(option) {
                if (this.disabled) return;
                this.focusOnInput();
                if (option.disabled) return;
                const isSelected = this.isSelected(option);

                if (isSelected) {
                    this.removeOption(option);
                } else {
                    this.localModelValue = [...this.localModelValue, option.value];
                }

                this.searchQuery = "";
            },
            removeOption(option) {
                this.focusOnInput();
                this.localModelValue = this.localModelValue.filter(value => value !== option.value);
            },
            isSelected(option) {
                return this.localModelValue.includes(option.value);
            },
            isOptionInList(query) {
                return this.localOptions.some(option =>
                    this.searchableFields.some(field =>
                        option[field]?.toLowerCase() === query.toLowerCase()
                    )
                );
            },
            handleClickOutside(event) {
                if (!this.$refs.dropdown.contains(event.target)) {
                    this.isOpen = false;
                }
            },
            handleEnterKey() {
                if (this.allowCustom && this.hasSearchQuery && !this.isOptionInList(this.searchQuery)) {
                    this.addCustomOption();
                }
            },
            addCustomOption() {
                const newOptionValue = this.searchQuery.toLowerCase();
                const newOption = { label: this.searchQuery, value: newOptionValue };

                if (this.onAddCustomOption) {
                    this.onAddCustomOption(this.localOptions, this.localModelValue, newOptionValue);
                } else {
                    // Add the new option to localOptions
                    this.localOptions = [...this.localOptions, newOption];
                    // Add the new option's value to localModelValue
                    this.localModelValue = [...this.localModelValue, newOptionValue];
                }

                this.searchQuery = "";
            },
        },
        mounted() {
            document.addEventListener("click", this.handleClickOutside);
        },
        beforeUnmount() {
            document.removeEventListener("click", this.handleClickOutside);
        },
    }
</script>
