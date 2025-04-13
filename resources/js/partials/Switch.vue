<template>

    <div
        :class="[
            'w-fit flex items-center',
            this.size == 'xs' ? 'gap-x-1' : 'gap-x-2'
        ]">
        <slot name="prefix">
            <label
                :for="uniqueId"
                v-if="prefixText"
                :class="[
                    'text-sm leading-6 font-medium',
                    disabled ? 'text-gray-900/50 cursor-not-allowed' : 'text-gray-900 cursor-pointer'
                ]">
                {{ prefixText }}
            </label>
        </slot>
        <label
            :for="uniqueId"
            :class="[
                'relative inline-block',
                { 'w-9 h-5' : this.size == 'xs' },
                { 'w-11 h-6' : this.size == 'sm' },
                { 'w-13 h-7' : this.size == 'md' },
                { 'w-15 h-8' : this.size == 'lg' },
                disabled ? 'cursor-not-allowed' : 'cursor-pointer'
            ]">
            <input type="checkbox" v-model="localModelValue" :id="uniqueId" :disabled="disabled" class="peer sr-only">
            <span :class="[
                'absolute inset-0 bg-gray-200 rounded-full transition-colors duration-200 ease-in-out dark:bg-neutral-700 dark:peer-checked:bg-blue-500 peer-disabled:opacity-50 peer-disabled:pointer-events-none',
                { 'peer-checked:bg-blue-500' : type == 'primary' },
                { 'peer-checked:bg-green-500' : type == 'success' },
                { 'peer-checked:bg-yellow-500' : type == 'warning' },
                { 'peer-checked:bg-red-500' : type == 'danger' },
            ]"></span>
            <span :class="[
                'absolute top-1/2 start-0.5 -translate-y-1/2 bg-white rounded-full shadow-xs transition-transform duration-200 ease-in-out peer-checked:translate-x-full dark:bg-neutral-400 dark:peer-checked:bg-white',
                { 'size-4' : this.size == 'xs' },
                { 'size-5' : this.size == 'sm' },
                { 'size-6' : this.size == 'md' },
                { 'size-7' : this.size == 'lg' },
                ]">
            </span>
        </label>
        <slot name="suffix" :uniqueId="uniqueId">
            <label
                :for="uniqueId"
                v-if="suffixText"
                :class="[
                    'text-sm leading-6 font-medium',
                    disabled ? 'text-gray-900/50 cursor-not-allowed' : 'text-gray-900 cursor-pointer'
                ]">
                {{ suffixText }}
            </label>
        </slot>

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

    </div>

</template>

  <script>

    import { generateUniqueId } from '@Utils/generalUtils.js';
    import Popover from '@Partials/Popover.vue';
    import Tooltip from '@Partials/Tooltip.vue';

    export default {
        components: { Popover, Tooltip },
        props: {
            modelValue: {
                type: Boolean,
                default: false,
            },
            prefixText: {
                type: [String, null],
                default: null
            },
            suffixText: {
                type: [String, null],
                default: null
            },
            disabled: {
                type: Boolean,
                default: false
            },
            size: {
                type: String,
                default: "md",
                validator: (value) => ["xs", "sm", "md", "lg"].includes(value),
            },
            type: {
                type: String,
                default: "primary",
                validator: (value) => ["primary", "success", "warning", "danger"].includes(value),
            },
            popoverContent: {
                type: [String, null],
                default: null
            },
            tooltipContent: {
                type: [String, null],
                default: null
            },
        },
        emits: ["update:modelValue", "change"],
        data() {
            return {
                uniqueId: generateUniqueId("switch"),
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
            }
        },
    };

  </script>
