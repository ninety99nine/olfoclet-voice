<template>

    <Notifications></Notifications>

    <router-view></router-view>

</template>

<script>

    import Notifications from '@Layouts/dashboard/components/Notifications.vue';

    export default {
        components: {
            Notifications
        }
    };

</script>
