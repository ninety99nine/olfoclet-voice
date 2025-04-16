<template>
    <!-- Navigation Toggle -->
    <div class="lg:hidden py-16 text-center">
      <button
        type="button"
        class="py-2 px-3 inline-flex justify-center items-center gap-x-2 bg-gray-800 border border-gray-800 text-white text-sm font-medium rounded-lg shadow-2xs hover:bg-gray-950 focus:outline-hidden focus:bg-gray-900 dark:bg-white dark:text-neutral-800 dark:hover:bg-neutral-200 dark:focus:bg-neutral-200"
        data-hs-overlay="#hs-sidebar-basic-usage">
        Open
      </button>
    </div>
    <!-- End Navigation Toggle -->

    <!-- Sidebar -->
    <div id="hs-sidebar-basic-usage"
         class="hs-overlay [--auto-close:lg] lg:block lg:translate-x-0 lg:end-auto lg:bottom-0 w-64 hs-overlay-open:translate-x-0 -translate-x-full transition-all duration-300 transform h-full hidden fixed top-0 start-0 bottom-0 z-60 bg-slate-900 border-e border-gray-200 dark:bg-neutral-800 dark:border-neutral-700"
         role="dialog"
         tabindex="-1"
         aria-label="Sidebar">

      <div class="relative flex flex-col h-full max-h-full">
        <!-- Header -->
        <header class="p-4 flex justify-between items-center gap-x-2 border-b border-slate-800 mb-4">
          <h1 class="flex-none font-semibold text-xl text-white dark:text-white">Orange Botswana</h1>
          <div class="lg:hidden -me-2">
            <!-- Close Button -->
            <button type="button"
                    class="flex justify-center items-center size-6 bg-white border border-gray-200 text-sm text-gray-600 hover:bg-gray-100 rounded-full focus:outline-hidden focus:bg-gray-100 dark:bg-neutral-800 dark:border-neutral-700 dark:text-neutral-400 dark:hover:bg-neutral-700 dark:focus:bg-neutral-700"
                    data-hs-overlay="#hs-sidebar-basic-usage">
              <svg class="size-4" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"
                   stroke="currentColor" stroke-width="2"><path d="M18 6 6 18"/><path d="m6 6 12 12"/></svg>
            </button>
          </div>
        </header>
        <!-- End Header -->

        <!-- Body -->
        <nav class="h-full overflow-y-auto [&::-webkit-scrollbar]:w-2 [&::-webkit-scrollbar-thumb]:rounded-full [&::-webkit-scrollbar-track]:bg-gray-100 [&::-webkit-scrollbar-thumb]:bg-gray-300 dark:[&::-webkit-scrollbar-track]:bg-neutral-700 dark:[&::-webkit-scrollbar-thumb]:bg-neutral-500">
          <ul>
            <li v-for="(menu, index) in menus" :key="index">
              <router-link
                :to="{ name: menu.route }"
                :class="[
                  'flex items-center gap-x-3.5 py-2 px-2.5 focus:outline-hidden',
                  $route.name === menu.route
                    ? 'bg-slate-700 dark:bg-neutral-700 text-white dark:text-white'
                    : 'text-white hover:bg-slate-700 dark:text-neutral-200 dark:hover:bg-neutral-700'
                ]">

                <component v-if="menu.icon" :is="menu.icon" :size="20" />

                <span>{{ menu.name }}</span>

              </router-link>
            </li>
          </ul>
        </nav>
        <!-- End Body -->
      </div>
    </div>
    <!-- End Sidebar -->
  </template>

  <script>

    import { Lock, PhoneCall, HouseIcon, BuildingIcon, UsersRoundIcon } from 'lucide-vue-next';

    export default {
        data() {
            return {
                menus: [
                    { name: 'Home', route: 'show-home', icon: HouseIcon },
                    { name: 'Calls', route: 'show-calls', icon: PhoneCall },
                    { name: 'Users', route: 'show-users', icon: UsersRoundIcon },
                    { name: 'Roles', route: 'show-roles', icon: Lock },
                    { name: 'Organizations', route: 'show-organizations', icon: BuildingIcon },
                ],
            };
        }
    };
  </script>
