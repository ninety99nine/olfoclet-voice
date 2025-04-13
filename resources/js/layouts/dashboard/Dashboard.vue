<template>

    <Header></Header>

    <Notifications></Notifications>

    <Breadcrumb></Breadcrumb>

    <Sidebar></Sidebar>

    <Content></Content>

</template>

<script>

    import Header from '@Layouts/dashboard/components/Header.vue';
    import Content from '@Layouts/dashboard/components/Content.vue';
    import Sidebar from '@Layouts/dashboard/components/Sidebar.vue';
    import Breadcrumb from '@Layouts/dashboard/components/Breadcrumb.vue';
    import Notifications from '@Layouts/dashboard/components/Notifications.vue';

    export default {
        inject: ['authState', 'formState', 'changeHistoryState'],
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
        computed: {
            authUser() {
                return this.authState.user;
            },
            isLoadingAuthUser() {
                return this.authState.isLoadingUser;
            }
        },
        methods: {
            isActiveNavMenu(navMenu) {
                const routeNames = [navMenu.routeName];
                if(navMenu.associatedRouteNames) routeNames.push(...navMenu.associatedRouteNames);
                return routeNames.includes(this.$route.name);
            },
            attemptLogout() {

                this.isLoggingOut = true;

                logout().then(response => {

                    if(response.status == 200) {

                        this.isLoggingOut = false;

                        // Redirect to login
                        this.$router.replace({ name: 'login' });

                    }

                }).catch(errorException => {

                    //  Stop loader
                    this.isLoggingOut = false;

                    this.showErrors(errorException, 'attemptLogout');

                });
            }
        },
        mounted() {
            if (typeof window.HSStaticMethods !== 'undefined') {
                window.HSStaticMethods.autoInit();
            }
        }
    };

</script>
