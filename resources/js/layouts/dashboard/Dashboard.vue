<template>

    <div class="transition-opacity opacity-100 duration-750 lg:grow starting:opacity-0">

        <Notifications></Notifications>

        <Header></Header>

        <Breadcrumb></Breadcrumb>

        <Sidebar></Sidebar>

        <Content></Content>

    </div>

</template>

<script>

    import Header from '@Layouts/dashboard/components/Header.vue';
    import Content from '@Layouts/dashboard/components/Content.vue';
    import Sidebar from '@Layouts/dashboard/components/Sidebar.vue';
    import Breadcrumb from '@Layouts/dashboard/components/Breadcrumb.vue';
    import Notifications from '@Layouts/dashboard/components/Notifications.vue';

    export default {
        inject: ['formState', 'changeHistoryState'],
        components: {
            Header, Content, Sidebar, Breadcrumb, Notifications
        },
        data() {
            return {
                currentYear: new Date().getFullYear(),
                profileNavMenus: [
                    {
                        name: 'Manage Stores',
                        routeName: 'show-stores',
                    },
                    {
                        name: 'Sign Out',
                        routeName: null,
                    }
                ],
                isLoggingOut: false
            }
        },
        mounted() {
            if (typeof window.HSStaticMethods !== 'undefined') {
                window.HSStaticMethods.autoInit();
            }
        }
    };

</script>
