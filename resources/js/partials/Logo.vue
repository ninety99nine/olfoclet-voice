<template>
    <img :class="height" :src="`/images/logo-swirl-text.png`" alt="TelcoFlo Logo">
</template>

<script>
export default {
    props: {
        height: {
            type: String,
            default: 'h-16'
        }
    }
};
</script>
