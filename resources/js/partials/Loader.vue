<template>
    <div :class="[wrapperClass, 'flex items-center']">
        <div :class="loaderClass"></div>
        <slot></slot>
    </div>

</template>

<script>
    export default {
        /**
         * Component Reference: https://flowbite.com/docs/components/spinner/
         */
        props: {
            size: {
                type: String,
                default: 'md',
                options: ['sm', 'md', 'custom']
            },
            customSize: {
                type: String,
                default: ''
            },
            type: {
                type: String,
                default: 'dark',
                options: ['dark', 'light', 'warning', 'danger']
            },
            color: {
                type: [String, null]
            }
        },
        computed: {
            wrapperClass() {
                if(this.type == 'dark') {
                    return 'text-black';
                }else if(this.type == 'light') {
                    return 'text-white';
                }else if(this.type == 'danger') {
                    return 'text-red-600';
                }else if(this.type == 'warning') {
                    return 'text-yellow-400';
                }
            },
            loaderClass() {
                let classes = ['inline-block animate-spin rounded-full border-solid border-current border-r-transparent align-[-0.125em] motion-reduce:animate-[spin_1.5s_linear_infinite]'];

                if(this.size == 'sm') {
                    classes.push('w-3 h-3 border');
                }else if(this.size == 'md') {
                    classes.push('w-4 h-4 border-2');
                }else{
                    classes.push(this.customSize);
                }

                if(this.color) {
                    classes.push(this.color);
                }else{

                    if(this.type == 'dark') {
                        classes.push('border-black');
                    }else if(this.type == 'light') {
                        classes.push('border-white');
                    }else if(this.type == 'danger') {
                        classes.push('border-red-600');
                    }else if(this.type == 'warning') {
                        classes.push('border-yellow-400');
                    }else if(this.type == 'warning') {
                        classes.push('border-yellow-400');
                    }

                }

                return classes;
            }
        }
    };
</script>
