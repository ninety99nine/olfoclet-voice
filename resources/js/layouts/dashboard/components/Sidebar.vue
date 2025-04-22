<template>
    <!-- Navigation Toggle -->
    <div class="lg:hidden py-16 text-center">
      <button
        type="button"
        class="py-2 px-3 inline-flex justify-center items-center gap-x-2 bg-gray-800 border border-gray-800 text-sm font-medium rounded-lg shadow-2xs hover:bg-gray-950 focus:outline-hidden focus:bg-gray-900"
        data-hs-overlay="#hs-sidebar-basic-usage">
        Open
      </button>
    </div>
    <!-- End Navigation Toggle -->

    <!-- Sidebar -->
    <div id="hs-sidebar-basic-usage"
         class="hs-overlay [--auto-close:lg] lg:block lg:translate-x-0 lg:end-auto lg:bottom-0 w-64 hs-overlay-open:translate-x-0 -translate-x-full transition-all duration-300 transform h-full hidden fixed top-0 start-0 bottom-0 z-40 bg-indigo-50 border-e border-gray-200"
         role="dialog"
         tabindex="-1"
         aria-label="Sidebar">

      <div class="relative flex flex-col h-full max-h-full">
        <!-- Header -->
        <header class="p-4 pb-8 flex justify-between items-center gap-x-2">
          <h1 class="flex-none font-semibold text-xl px-2.5">Orange Botswana</h1>
          <div class="lg:hidden -me-2">
            <!-- Close Button -->
            <button type="button"
                    class="flex justify-center items-center size-6 bg-white border border-gray-200 text-sm text-gray-600 hover:bg-gray-100 rounded-full focus:outline-hidden focus:bg-gray-100"
                    data-hs-overlay="#hs-sidebar-basic-usage">
              <svg class="size-4" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"
                   stroke="currentColor" stroke-width="2"><path d="M18 6 6 18"/><path d="m6 6 12 12"/></svg>
            </button>
          </div>
        </header>
        <!-- End Header -->

        <!-- Body -->
        <nav class="h-full overflow-y-auto px-4 [&::-webkit-scrollbar]:w-2 [&::-webkit-scrollbar-thumb]:rounded-full [&::-webkit-scrollbar-track]:bg-gray-100 [&::-webkit-scrollbar-thumb]:bg-gray-300">
          <ul class="space-y-1">
            <li v-for="(menu, index) in menus" :key="index">
              <router-link
                :to="{ name: menu.route }"
                :class="[
                  'flex items-center gap-x-2.5 py-2 px-2.5 text-sm rounded-lg focus:outline-hidden',
                  $route.name === menu.route
                    ? 'bg-indigo-500 shadow-sm text-white border border-indigo-400'
                    : 'hover:bg-indigo-50 hover:shadow-sm hover:text-indigo-500 border border-transparent hover:border-indigo-300 transition-all duration-300'
                ]">

                <component v-if="menu.icon" :is="menu.icon" :size="16" />

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

    import { Lock, Contact, PhoneCall, CirclePlay, HouseIcon, BuildingIcon, UserRoundIcon, UsersRoundIcon } from 'lucide-vue-next';

    export default {
        data() {
            return {
                menus: [
                    { name: 'Home', route: 'show-home', icon: HouseIcon },
                    { name: 'Calls', route: 'show-calls', icon: PhoneCall },
                    { name: 'Users', route: 'show-users', icon: UserRoundIcon },
                    { name: 'Roles', route: 'show-roles', icon: Lock },
                    { name: 'Contacts', route: 'show-contacts', icon: Contact },
                    { name: 'Departments', route: 'show-departments', icon: UsersRoundIcon },
                    { name: 'Media Files', route: 'show-media-files', icon: CirclePlay },
                    { name: 'Organizations', route: 'show-organizations', icon: BuildingIcon },
                ],
            };
        }
    };
  </script>
