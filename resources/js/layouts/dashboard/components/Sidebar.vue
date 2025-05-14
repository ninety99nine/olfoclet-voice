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
         class="hs-overlay [--auto-close:lg] lg:block lg:translate-x-0 lg:end-auto lg:bottom-0 w-60 hs-overlay-open:translate-x-0 -translate-x-full transition-all duration-300 transform h-full hidden fixed top-0 start-0 bottom-0 z-40 bg-indigo-50 border-e border-gray-200"
         role="dialog"
         tabindex="-1"
         aria-label="Sidebar">

      <div class="relative flex flex-col h-full max-h-full">
        <!-- Header -->
        <header class="p-4 pb-8 flex justify-between items-center gap-x-2">
            <h1 class="flex-none font-semibold text-xl px-2.5">Perfect Order</h1>
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
              <!-- Main Menu Item (Expandable if it has subMenus) -->
              <div>
                <!-- Menu with Subitems -->
                <button
                  v-if="menu.subMenus"
                  @click="toggleMenu(index)"
                  :class="[
                    'w-full flex items-center justify-between gap-x-2.5 py-2 px-2.5 text-sm rounded-lg focus:outline-hidden cursor-pointer',
                    (isActive(menu) || isSubMenuActive(menu))
                      ? (menu.subMenus && expandedMenus[index] ? 'hover:bg-indigo-50 hover:shadow-sm hover:text-indigo-500 border border-transparent hover:border-indigo-300 transition-all duration-300' : 'bg-indigo-500 shadow-sm text-white border border-indigo-400')
                      : 'hover:bg-indigo-50 hover:shadow-sm hover:text-indigo-500 border border-transparent hover:border-indigo-300 transition-all duration-300'
                  ]"
                >
                  <div class="flex items-center gap-x-2.5">
                    <component v-if="menu.icon" :is="menu.icon" :size="16" :class="{ 'text-red-600' : (menu.name == 'Virtual Agents' && !isActive(menu) && !isSubMenuActive(menu)) }" />
                    <span :class="{ 'bg-gradient-to-r from-red-600 to-indigo-600 bg-clip-text text-transparent' : menu.name == 'Virtual Agents' && !isActive(menu) && !isSubMenuActive(menu) }">
                      {{ menu.name }}
                    </span>
                  </div>
                  <component :is="expandedMenus[index] ? ChevronUp : ChevronDown" :size="16" />
                </button>
                <!-- Menu without Subitems -->
                <router-link
                  v-else
                  :to="{ name: menu.route }"
                  @click="closeAllMultiMenus"
                  :class="[
                    'flex items-center gap-x-2.5 py-2 px-2.5 text-sm rounded-lg focus:outline-hidden',
                    isActive(menu)
                      ? 'bg-indigo-500 shadow-sm text-white border border-indigo-400'
                      : 'hover:bg-indigo-50 hover:shadow-sm hover:text-indigo-500 border border-transparent hover:border-indigo-300 transition-all duration-300'
                  ]"
                >
                  <component v-if="menu.icon" :is="menu.icon" :size="16" :class="{ 'text-red-600' : (menu.name == 'Virtual Agents' && !isActive(menu)) }" />
                  <span :class="{ 'bg-gradient-to-r from-red-600 to-indigo-600 bg-clip-text text-transparent' : menu.name == 'Virtual Agents' && !isActive(menu) }">
                    {{ menu.name }}
                  </span>
                </router-link>

                <!-- Nested Menu Items with Animation -->
                <vue-slide-up-down :active="menu.subMenus && expandedMenus[index]">
                  <ul v-if="menu.subMenus && expandedMenus[index]" class="pl-6 space-y-1 mt-1">
                    <li v-for="(subMenu, subIndex) in menu.subMenus" :key="subIndex">
                      <router-link
                        :to="{ name: subMenu.route }"
                        :class="[
                          'flex items-center gap-x-2.5 py-2 px-2.5 text-sm rounded-lg focus:outline-hidden',
                          isActive(subMenu)
                            ? 'bg-indigo-500 shadow-sm text-white border border-indigo-400'
                            : 'hover:bg-indigo-50 hover:shadow-sm hover:text-indigo-500 border border-transparent hover:border-indigo-300 transition-all duration-300'
                        ]"
                      >
                        <component v-if="subMenu.icon" :is="subMenu.icon" :size="16" />
                        <span>{{ subMenu.name }}</span>
                      </router-link>
                    </li>
                  </ul>
                </vue-slide-up-down>
              </div>
            </li>
          </ul>
        </nav>
        <!-- End Body -->
        <div class="p-8">
            <Logo height="h-6"></Logo>
        </div>
      </div>
    </div>
    <!-- End Sidebar -->
</template>

<script>
import Logo from '@Partials/Logo.vue';
import VueSlideUpDown from 'vue-slide-up-down';
import { Bot, Lock, Atom, Earth, Route, Slack, CircleUser, Contact, FileText, Settings, MessageSquareMore, Shapes, SquareDashedBottom, LibraryBig, ScrollText, PhoneCall, CirclePlay, Workflow, HouseIcon, Puzzle, BuildingIcon, UserRoundIcon, UsersRoundIcon, Shell, ChevronUp, ChevronDown } from 'lucide-vue-next';

export default {
    components: { Logo, VueSlideUpDown },
    data() {
        return {
            ChevronUp,
            ChevronDown,
            expandedMenus: {}, // Track expanded state for each menu
            menus: [
                { name: 'Home', route: 'show-home', icon: HouseIcon },
                { name: 'Flow', route: 'show-flow', icon: Shell },
                { name: 'Calls', route: 'show-calls', icon: PhoneCall },
                { name: 'Contacts', route: 'show-contacts', icon: Contact },
                { name: 'Conversations', route: 'show-conversation-threads', relatedRoutes: ['show-copilot-conversation-threads'], icon: MessageSquareMore },

                { name: 'Media Files', route: 'show-media-files', icon: CirclePlay },
                { name: 'Knowledge', route: 'show-knowledge-bases', icon: LibraryBig },
                {
                    name: 'Automation',
                    route: 'show-copilots',
                    relatedRoutes: [],
                    icon: Shapes,
                    subMenus: [
                        { name: 'Virtual Agents', route: 'show-nexflo', icon: Atom },
                        { name: 'Workflows', route: 'show-call-flows', relatedRoutes: ['create-call-flow', 'edit-call-flow'], icon: Workflow },
                        { name: 'Copilots', route: 'show-copilots', icon: Bot },
                        { name: 'Numbers', route: 'show-numbers', icon: Route },
                        { name: 'Script Flows', route: 'create-script-flow', relatedRoutes: ['create-script-flow', 'edit-script-flow'], icon: ScrollText },
                    ]
                },
                {
                    name: 'Settings',
                    route: 'show-users',
                    relatedRoutes: [],
                    icon: Settings,
                    subMenus: [
                        { name: 'Users', route: 'show-users', icon: UserRoundIcon },
                        { name: 'Roles', route: 'show-roles', icon: Lock },
                        { name: 'Departments', route: 'show-departments', icon: UsersRoundIcon },
                        { name: 'Organizations', route: 'show-organizations', icon: BuildingIcon },
                        { name: 'Integrations', route: 'show-integrations', icon: Puzzle },
                        { name: 'Channels', route: 'show-channels', icon: Slack },
                        { name: 'Account', route: 'show-account', relatedRoutes: ['show-account', 'update-account'], icon: CircleUser },
                    ]
                },
            ],
        };
    },
    computed: {
        isSubMenuActive() {
            return (menu) => {
                if (!menu.subMenus) return false;
                return menu.subMenus.some(subMenu => this.isActive(subMenu));
            };
        },
    },
    methods: {
        isActive(menu) {
            // Check if the current route matches the menu's primary route
            if (this.$route.name === menu.route) {
                return true;
            }
            // Check if the current route is in the menu's relatedRoutes
            if (menu.relatedRoutes && menu.relatedRoutes.includes(this.$route.name)) {
                return true;
            }
            return false;
        },
        toggleMenu(index) {
            // Toggle the expanded state for the menu at the given index
            this.expandedMenus = {
                ...this.expandedMenus,
                [index]: !this.expandedMenus[index]
            };
            // If expanding and no submenu is active, navigate to the first submenu
            const menu = this.menus[index];
            if (this.expandedMenus[index] && !this.isSubMenuActive(menu) && menu.subMenus && menu.subMenus.length > 0) {
                this.$router.push({ name: menu.subMenus[0].route });
            }
        },
        closeAllMultiMenus() {
            // Reset expanded state for all menus with subMenus
            const newExpandedMenus = {};
            this.menus.forEach((_, index) => {
                newExpandedMenus[index] = false;
            });
            this.expandedMenus = newExpandedMenus;
        },
    },
    created() {
        // Initialize expanded state for each menu and expand if active
        this.menus.forEach((menu, index) => {
            // Check if the menu or any of its submenus are active
            const isMenuActive = this.isActive(menu) || (menu.subMenus && this.isSubMenuActive(menu));
            this.expandedMenus[index] = isMenuActive;
        });
    },
};
</script>
