<template>
    <div :class="badgeClasses" @click.stop.prevent="action">
        <StatusDot v-if="showDot" :type="dotType"></StatusDot>
        <slot></slot>
        <svg
            v-if="closableAction"
            @click.stop="closableAction"
            class="w-5 h-5 ml-2 -mr-1 cursor-pointer hover:opacity-70 active:scale-90" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
            <path stroke-linecap="round" stroke-linejoin="round" d="m9.75 9.75 4.5 4.5m0-4.5-4.5 4.5M21 12a9 9 0 1 1-18 0 9 9 0 0 1 18 0Z" />
        </svg>
    </div>
</template>

<script>
    import StatusDot from '@Partials/StatusDot.vue';
    export default {
        props: {
            showDot: {
                type: Boolean,
                default: false
            },
            type: {
                type: String,
                default: 'primary',
                validator: value => [
                    'light', 'primary', 'success', 'warning', 'danger', 'dark', 'outline'
                ].includes(value)
            },
            size: {
                type: String,
                default: 'md',
                validator: value => ['xs', 'sm', 'md', 'lg'].includes(value)
            },
            action: {
                type: Function,
                default: null
            },
            closableAction: {
                type: Function,
                default: null
            },
        },
        computed: {
            badgeClasses() {
                const typeClassMap = {
                    light: 'inline-flex items-center rounded-full border px-2.5 py-0.5 text-xs font-semibold transition-colors focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-offset-2 text-gray-700 border-gray-300',
                    primary: 'inline-flex items-center rounded-full border px-2.5 py-0.5 text-xs font-semibold transition-colors focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-offset-2 bg-blue-600 text-white border-transparent hover:bg-blue-700',
                    success: 'inline-flex items-center rounded-full border px-2.5 py-0.5 text-xs font-semibold transition-colors focus:outline-none focus:ring-2 focus:ring-green-500 focus:ring-offset-2 bg-green-100 text-green-800 border-transparent hover:bg-green-200/80',
                    warning: 'inline-flex items-center rounded-full border px-2.5 py-0.5 text-xs font-semibold transition-colors focus:outline-none focus:ring-2 focus:ring-yellow-500 focus:ring-offset-2 bg-yellow-100 text-yellow-800 border-transparent hover:bg-yellow-200/80',
                    danger: 'inline-flex items-center rounded-full border px-2.5 py-0.5 text-xs font-semibold transition-colors focus:outline-none focus:ring-2 focus:ring-red-500 focus:ring-offset-2 bg-red-600 text-white border-transparent hover:bg-red-700',
                    dark: 'inline-flex items-center rounded-full border px-2.5 py-0.5 text-xs font-semibold transition-colors focus:outline-none focus:ring-2 focus:ring-gray-700 focus:ring-offset-2 bg-gray-900 text-white border-gray-700',
                    outline: 'inline-flex items-center rounded-full border px-2.5 py-0.5 text-xs font-semibold transition-colors focus:outline-none focus:ring-2 focus:ring-gray-300 focus:ring-offset-2 border border-gray-400 bg-transparent text-gray-700'
                };

                const sizeClassMap = {
                    xs: 'px-2 py-0.5 text-xs',
                    sm: 'px-2.5 py-1 text-sm',
                    md: 'px-3 py-1.5 text-base',
                    lg: 'px-4 py-2 text-lg'
                };

                const clickClasses = 'cursor-pointer hover:scale-95 active:scale-90';

                const baseClasses = `${typeClassMap[this.type]} ${sizeClassMap[this.size]}`;
                let classes = `${baseClasses}`;

                if (this.action) {
                    classes += ` ${clickClasses}`;
                }

                return classes;
            },
            dotType() {
                return this.type;
            }
        },
        components: { StatusDot }
    };
</script>
