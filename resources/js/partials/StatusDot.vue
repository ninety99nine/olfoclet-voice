<template>
    <span :class="spinnerClasses"></span>
</template>

<script>
export default {
    props: {
        type: {
            type: String,
            default: 'danger',
            validator: (value) => [
                'light', 'primary', 'success', 'warning', 'danger',
                'dark', 'info', 'outline', 'gradient', 'glass'
            ].includes(value)
        },
        animate: {
            type: Boolean,
            default: false
        }
    },
    computed: {
        spinnerClasses() {
            const baseClasses = ['w-2 h-2 me-1 rounded-full', {'animate-pulse': this.animate}];

            const typeClasses = {
                light: 'bg-gray-400',
                primary: 'bg-blue-500',
                success: 'bg-green-500',
                warning: 'bg-yellow-500',
                danger: 'bg-red-500',
                dark: 'bg-white',
                info: 'bg-cyan-500',
                outline: 'bg-gray-500',
                gradient: 'bg-gradient-to-r from-sky-200 to-indigo-200',
                glass: 'bg-gray-500 backdrop-blur-lg'
            };

            return [...baseClasses, typeClasses[this.type]];
        }
    }
};
</script>
