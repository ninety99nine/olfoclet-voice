<template>

    <header class="sticky top-0 inset-x-0 flex flex-wrap md:justify-start md:flex-nowrap z-30 w-full bg-gray-50 shadow-sm border-b border-gray-200 text-sm py-2.5 lg:ps-65 dark:bg-neutral-800 dark:border-neutral-700">

        <nav class="px-4 sm:px-6 flex basis-full items-center w-full mx-auto">

            <div class="me-5 lg:me-0 lg:hidden">

                <!-- Logo -->
                <Logo></Logo>

                <div class="lg:hidden ms-1">

                </div>

            </div>

            <div class="w-full flex items-center justify-end">

                <div class="flex flex-row items-center justify-end gap-2">

                    <button type="button" class="size-9.5 relative inline-flex justify-center items-center gap-x-2 text-sm font-semibold rounded-full border border-transparent text-gray-800 hover:bg-gray-100 focus:outline-hidden focus:bg-gray-100 disabled:opacity-50 disabled:pointer-events-none dark:text-white dark:hover:bg-neutral-700 dark:focus:bg-neutral-700">
                        <Bell size="16"></Bell>
                        <span class="sr-only">Notifications</span>
                    </button>

                    <!-- Dropdown -->
                    <div class="hs-dropdown [--placement:bottom-right] relative inline-flex">

                        <button id="hs-dropdown-account" type="button" class="size-9.5 inline-flex justify-center items-center gap-x-2 text-sm font-semibold rounded-full border border-transparent text-gray-800 focus:outline-hidden disabled:opacity-50 disabled:pointer-events-none dark:text-white" aria-haspopup="menu" aria-expanded="false" aria-label="Dropdown">
                            <img class="shrink-0 size-9.5 rounded-full" :src="'/images/avatar.jpg'" alt="Avatar">
                        </button>

                        <div class="hs-dropdown-menu transition-[opacity,margin] duration hs-dropdown-open:opacity-100 opacity-0 hidden min-w-72 bg-white border border-gray-200 shadow-md rounded-lg mt-2 dark:bg-neutral-800 dark:border dark:border-neutral-700 dark:divide-neutral-700 after:h-4 after:absolute after:-bottom-4 after:start-0 after:w-full before:h-4 before:absolute before:-top-4 before:start-0 before:w-full" role="menu" aria-orientation="vertical" aria-labelledby="hs-dropdown-account">

                            <div class="py-3 px-5 bg-gray-100 rounded-t-lg dark:bg-neutral-700">
                                <p class="text-sm text-gray-500 dark:text-neutral-500">{{ authUser.name }}</p>
                                <p class="text-sm font-medium text-gray-800 dark:text-neutral-200">{{ authUser.email }}</p>
                            </div>

                            <div class="p-1.5 space-y-0.5">

                                <a class="flex items-center gap-x-3.5 py-2 px-3 rounded-lg text-sm text-gray-800 hover:bg-gray-100 cursor-pointer focus:outline-hidden focus:bg-gray-100 dark:text-neutral-400 dark:hover:bg-neutral-700 dark:hover:text-neutral-300 dark:focus:bg-neutral-700 dark:focus:text-neutral-300" href="#">
                                    <User :size="16"></User>
                                    Account
                                </a>

                                <span @click="attemptLogout" class="flex items-center gap-x-3.5 py-2 px-3 rounded-lg text-sm text-gray-800 hover:bg-gray-100 cursor-pointer focus:outline-hidden focus:bg-gray-100 dark:text-neutral-400 dark:hover:bg-neutral-700 dark:hover:text-neutral-300 dark:focus:bg-neutral-700 dark:focus:text-neutral-300" href="#">
                                    <LogOut :size="16"></LogOut>
                                    Sign Out
                                </span>

                            </div>

                        </div>

                    </div>
                    <!-- End Dropdown -->

                </div>

            </div>

        </nav>

    </header>

</template>

<script>

    import Logo from '@Partials/Logo.vue';
    import { User, LogOut } from 'lucide-vue-next';
    import { Bell, Mail } from 'lucide-vue-next';


    export default {
        inject: ['authState', 'notificationState'],
        components: { Bell, Mail, Logo, User, LogOut },
        data() {
            return {
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
            async attemptLogout() {
                this.isLoggingOut = true;
                try {
                    await this.authState.logout();
                    this.$router.replace({ name: 'login' });
                } catch (error) {
                    this.isLoggingOut = false;
                    this.notificationState.showWarningNotification(
                        error?.response?.data?.message || error.message || 'Something went wrong trying to logout'
                    );
                }
            }
        }
    }

</script>
