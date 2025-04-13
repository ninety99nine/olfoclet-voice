<template>

    <component :is="shine ? 'ShineEffect' : 'div'" :class="[width, height, rounded, $attrs.class, 'overflow-hidden']">

        <div :class="['w-full h-full', color]"></div>

    </component>

</template>

<script>

    import ShineEffect from '@Partials/ShineEffect.vue';

    export default {
        components: { ShineEffect },
        props: {
            width: {
                type: String,
                default: 'w-full'
            },
            height: {
                type: String,
                default: 'h-2'
            },
            color: {
                type: String,
                default: 'bg-gray-200'
            },
            rounded: {
                type: String,
                default: 'rounded-full'
            },
            shine: {
                type: Boolean,
                default: true
            }
        }
    };

</script>
