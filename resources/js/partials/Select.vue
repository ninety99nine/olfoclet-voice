<template>

    <div>

        <div class="space-y-2">

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

            <div
                :class="[
                    'w-full flex',
                    alignItems,
                    {
                        'opacity-50': disabled
                    }
                ]">

                <!-- Prefix Icon Slot -->
                <slot v-if="$slots.prefix" name="prefix"></slot>

                <Skeleton
                    height="h-8"
                    :shine="true"
                    v-if="skeleton"
                    rounded="rounded-lg">
                </Skeleton>

                <div v-else class="w-full relative" ref="dropdown">

                    <!-- Select Input -->
                    <div
                        @click="toggleDropdown"
                        :class="[
                            'w-full select-none bg-white border border-gray-300 rounded-md',
                            disabled ? 'cursor-not-allowed' : 'cursor-pointer'
                        ]">
                        <div class="flex items-center justify-between py-2 px-3">

                            <!-- Custom Slot Support -->
                            <slot name="selectedOption" :selectedOption="selectedOption">

                                <!-- Default Option Layout -->
                                <span class="text-gray-700 text-sm truncate">{{ selectedOption?.label ?? placeholder }}</span>

                            </slot>

                            <svg v-if="selectedOption && clearable" @click.stop="clearSelectedOption" class="w-4 h-4 text-gray-700" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" d="m9.75 9.75 4.5 4.5m0-4.5-4.5 4.5M21 12a9 9 0 1 1-18 0 9 9 0 0 1 18 0Z" />
                            </svg>
                            <svg v-else-if="isOpen" class="w-4 h-4 text-gray-700" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" d="m4.5 15.75 7.5-7.5 7.5 7.5" />
                            </svg>
                            <svg v-else class="w-4 h-4 text-gray-700" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" d="m19.5 8.25-7.5 7.5-7.5-7.5" />
                            </svg>

                        </div>
                    </div>

                    <!-- Dropdown Options -->
                    <div v-if="isOpen" class="w-full absolute z-10 mt-1 select-none bg-white border border-gray-300 rounded-md shadow-lg overflow-hidden">

                        <!-- Search Input (Fixed) -->
                        <div v-if="search" class="px-3 py-2 border-b border-gray-200 bg-white">
                            <Input
                                ref="search"
                                type="search"
                                v-model="searchQuery"
                                placeholder="Search..."
                            />
                        </div>

                        <!-- Scrollable Options List -->
                        <ul class="max-h-60 overflow-auto">

                            <li
                                :key="index"
                                @click="() => selectOption(option)"
                                v-for="(option, index) in filteredOptions"
                                :class="[
                                    'px-4 py-2 text-sm flex justify-between items-center hover:bg-gray-100',
                                    option.disabled ? 'cursor-not-allowed text-gray-400' : 'cursor-pointer text-gray-700',
                                ]">

                                <!-- Custom Slot Support -->
                                <slot name="option" :option="option" :isSelected="option.value === localModelValue">

                                    <!-- Default Option Layout -->
                                    <span class="truncate">{{ option.label }}</span>

                                </slot>

                                <!-- Default Selected Icon -->
                                <span v-if="option.value === localModelValue">
                                    <svg class="w-4 h-4 text-blue-500" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                                    </svg>
                                </span>

                            </li>

                        </ul>

                        <!-- No Results Found -->
                        <div v-if="filteredOptions.length === 0" class="text-center px-4 py-2 text-gray-500 text-sm">
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

    import Input from '@Partials/Input.vue';
    import Popover from '@Partials/Popover.vue';
    import Tooltip from '@Partials/Tooltip.vue';
    import Skeleton from '@Partials/Skeleton.vue';
    import capitalize from '@Directives/capitalize.js';
    import { generateUniqueId } from '@Utils/generalUtils.js';

    export default {
        directives: { capitalize },
        components: { Input, Popover, Tooltip, Skeleton },
        props: {
            modelValue: {
                type: [String, null]
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
            clearable: {
                type: Boolean,
                default: false
            },
            noResultsText: {
                type: [String, null],
                default: 'No results found'
            },
            searchableFields: {
                type: [Array, null],
                default: () => [
                    'label', 'value'
                ]
            },

            emits: ['update:modelValue'],
        },
        data() {
            return {
                isOpen: false,
                searchQuery: null,
                uniqueId: generateUniqueId('input'),
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
            selectedOption() {
                return this.options.find(option => option.value === this.localModelValue) || null;
            },
            filteredOptions() {
                if (!this.searchQuery) return this.options;

                return this.options.filter(option =>
                    this.searchableFields.some(field =>
                        option[field]?.toLowerCase().includes(this.searchQuery.toLowerCase())
                    )
                );
            }
        },
        methods: {
            toggleDropdown() {
                if(this.disabled) return;
                this.isOpen = !this.isOpen;
                if(this.search) this.focusOnInput();
            },
            focusOnInput() {
                this.$nextTick(() => {
                    if (this.$refs.search && document.activeElement !== this.$refs.search) {
                        this.$refs.search.$refs.input.focus();
                    }
                });
            },
            selectOption(option) {
                if(this.disabled || option.disabled) return;

                this.isOpen = false;
                this.searchQuery = null;
                this.localModelValue = option.value;
            },
            clearSelectedOption() {
                this.localModelValue = null;
            },
            handleClickOutside(event) {
                if (this.$refs.dropdown && !this.$refs.dropdown.contains(event.target)) {
                    this.isOpen = false;
                    this.searchQuery = null;
                }
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
