<template>

    <component :is="skeleton ? 'ShineEffect' : 'div'" :class="wrapperClass">

        <button
            @click.prevent.stop="onClick"
            :class="[buttonClasses, $attrs.class]">

            <!-- Spinning Loader -->
            <Loader v-if="loading" type="light"></Loader>

            <div v-else class="flex justify-center items-center space-x-1 whitespace-nowrap">

                <!-- Left Icon -->
                <component v-if="leftIcon" :is="leftIcon" :size="leftIconSize" />

                <!-- Content -->
                <span v-if="showCopiedText">Copied!</span>
                <slot v-else></slot>

                <!-- Right Icon -->
                <component v-if="rightIcon" :is="rightIcon" :size="rightIconSize" />

            </div>

        </button>

    </component>

</template>

<script>

    import Loader from "@Partials/Loader.vue";
    import ShineEffect from '@Partials/ShineEffect.vue';

    export default {
        components: { ShineEffect, Loader },
        props: {
            action: {
                type: [Function, null],
                default: null
            },
            type: {
                type: String,
                default: 'primary',
                validator: (value) => [
                    'light', 'primary', 'success', 'warning', 'danger', 'ghost', 'dark',
                    'outline', 'outlineDanger', 'bare', 'bareDanger'
                ].includes(value)
            },
            disabled: {
                type: Boolean,
                default: false
            },
            skeleton: {
                type: Boolean,
                default: false
            },
            loading: {
                type: Boolean,
                default: false
            },
            size: {
                type: String,
                default: 'md',
                validator: (value) => ['xs', 'sm', 'md', 'lg'].includes(value)
            },
            leftIcon: {
                type: [Object, Function, null],
                default: null
            },
            leftIconSize: {
                type: String,
                default: '16',
            },
            rightIcon: {
                type: [Object, Function, null],
                default: null
            },
            rightIconSize: {
                type: String,
                default: '16',
            },
            spacing: {
                type: String,
                default: 'space-x-2',
            },
            rounded: {
                type: String,
                default: 'rounded-lg',
            },
            wrapperClass: {
                type: String,
                default: null,
            },
            copyContent: {
                type: [String, null],
                default: null
            }
        },
        data() {
            return {
                copyTimeout: null,
                showCopiedText: false
            }
        },
        computed: {
            buttonClasses() {

                var classes = [];
                const baseClasses = [this.spacing, this.rounded, 'group flex justify-center items-center font-semibold transition-all duration-250 active:scale-95'];

                const paddingClasses = {
                    xs: "py-1.5 px-3",
                    sm: "py-1.5 px-4",
                    md: "py-2 px-5",
                    lg: "py-2.5 px-6"
                };

                const fontSizeClasses = {
                    xs: "text-xs",
                    sm: "text-sm",
                    md: "text-base",
                    lg: "text-lg"
                };

                const typeClasses = {
                    light: "border border-gray-300 bg-gray-100 hover:bg-gray-300",
                    primary: "bg-indigo-600 hover:bg-indigo-800 text-white",
                    success: "bg-green-600 hover:bg-green-800 text-white",
                    warning: "bg-yellow-600 hover:bg-yellow-800 text-white",
                    danger: "bg-red-600 hover:bg-red-800 text-white",
                    dark: "border border-gray-800 bg-gray-900 hover:bg-black text-white",
                    outline: "border border-gray-600 bg-transparent hover:bg-gray-200 text-gray-600",
                    outlineDanger: "border border-red-600 bg-transparent hover:bg-red-200 text-red-600",
                    disabled: "bg-gray-100 text-gray-300",
                    skeleton: "bg-gray-100 text-gray-300",
                    ghost: "bg-transparent text-blue-600 hover:text-blue-800 hover:underline shadow-none",
                    bare: "hover:opacity-50",
                    bareDanger: "hover:opacity-50 text-red-600"
                };

                classes.push(baseClasses);
                classes.push(fontSizeClasses[this.size]);

                if (!['ghost', 'bare', 'bareDanger'].includes(this.type)) {
                    classes.push(paddingClasses[this.size]);
                }

                if (this.disabled) {
                    classes.push(typeClasses['disabled'], 'cursor-not-allowed');
                } else if (this.skeleton) {
                    classes.push(typeClasses['skeleton'], 'cursor-not-allowed');
                } else {
                    classes.push(typeClasses[this.type], 'cursor-pointer');
                }

                return classes;
            }
        },
        methods: {
            onClick(event) {
                if(this.disabled) return;
                if(this.copyContent) this.copyToClipboard();
                if(this.action) this.action(event);
            },
            copyToClipboard() {

                this.showCopiedText = true;
                if(this.copyTimeout == null) clearInterval(this.copyTimeout);

                this.copyTimeout = setTimeout(() => {
                    this.showCopiedText = false;
                    clearInterval(this.copyTimeout);
                }, 1000);

            }
        }
    };

</script>
