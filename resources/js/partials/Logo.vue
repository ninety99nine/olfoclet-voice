<template>
    <img :class="height" :src="`/images/logo.png`" alt="TelcoFlo Logo">
</template>

<script>
export default {
    props: {
        height: {
            type: String,
            default: 'h-12'
        }
    }
};
</script>
