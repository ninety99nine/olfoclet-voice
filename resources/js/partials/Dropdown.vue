<template>

    <div
        ref="dropdown"
        :data-hs-dropdown="trigger"
        class="hs-dropdown relative"
        :data-hs-dropdown-placement="position">

        <!-- Trigger Slot -->
        <slot name="trigger" :isOpen="isOpen" :toggleDropdown="toggleDropdown">

            <Button
                v-if="trigger"
                :size="triggerSize"
                :type="triggerType"
                :icon="triggerIcon"
                :action="toggleDropdown"
                :skeleton="triggerLoading">

                <div class="flex items-center space-x-1">

                    <span v-if="triggerText">{{ triggerText }}</span>

                    <svg v-if="isOpen" :class="['w-4 h-4 text-gray-700', { 'opacity-20' : triggerLoading }]" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" d="m4.5 15.75 7.5-7.5 7.5 7.5" />
                    </svg>

                    <svg v-else :class="['w-4 h-4 text-gray-700', { 'opacity-20' : triggerLoading }]" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" d="m19.5 8.25-7.5 7.5-7.5-7.5" />
                    </svg>

                </div>

            </Button>

        </slot>

        <!-- Dropdown Content -->
        <div
            role="menu"
            :id="uniqueId"
            :class="[
                width,
                { 'hidden opacity-0 invisible': !isOpen, 'opacity-100 visible': isOpen },
                'hs-dropdown-menu absolute z-50 bg-white border border-gray-100 mt-1 text-start rounded-lg overflow-hidden shadow-md dark:bg-neutral-800 dark:border-neutral-700 transition-opacity duration-200 ease-in-out'
            ]">

            <slot name="header"></slot>

            <slot name="content" :toggleDropdown="toggleDropdown" :handleItemClick="handleItemClick">

                <ul class="max-h-60 overflow-auto">

                    <li
                        :key="index"
                        v-for="(option, index) in options"
                        @click="() => handleItemClick(option)"
                        :class="[
                            'px-4 py-2 text-sm hover:bg-gray-100',
                            option.disabled ? 'cursor-not-allowed text-gray-400' : 'cursor-pointer text-gray-700',
                        ]">

                        <!-- Custom Slot Support -->
                        <slot name="option" :option="option">
                            <span class="truncate">{{ option.label }}</span>
                        </slot>

                    </li>

                </ul>

            </slot>

            <slot name="footer"></slot>

        </div>

    </div>

</template>

<script>

    import Button from '@Partials/Button.vue';
    import { generateUniqueId } from '@Utils/generalUtils.js';

    export default {
        components: { Button },
        props: {
            triggerLoading: {
                type: Boolean,
                default: false
            },
            triggerText: {
                type: [String, null],
                default: null
            },
            triggerSize: {
                type: String,
                default: 'sm'
            },
            triggerType: {
                type: String,
                default: 'light'
            },
            triggerIcon: {
                type: [String, null],
                default: null
            },
            options: {
                type: Array,
                default: () => []
            },
            trigger: {
                type: String,
                default: "click",
                validator: (value) => ["click", "hover"].includes(value),
            },
            position: {
                type: String,
                default: "bottom",
                validator: (value) => ["top", "bottom", "left", "right"].includes(value),
            },
            width: {
                type: String,
                default: "w-60"
            }
        },
        data() {
            return {
                uniqueId: generateUniqueId('dropdown'),
                isOpen: false
            }
        },
        mounted() {
            setTimeout(() => {
                if (window.HSDropdown) {
                    window.HSDropdown.autoInit();
                }
            }, 500);

            document.addEventListener("click", this.handleClickOutside);
        },
        beforeUnmount() {
            document.removeEventListener("click", this.handleClickOutside);
        },
        methods: {
            showDropdown() {
                if(this.triggerLoading) return;
                this.isOpen = true;
            },
            hideDropdown() {
                if(this.triggerLoading) return;
                this.isOpen = false;
            },
            toggleDropdown(event) {
                if(this.triggerLoading) return;
                event.stopPropagation(); // Prevents immediate closing when clicking inside
                this.isOpen = !this.isOpen;
            },
            handleItemClick(item) {
                if(this.triggerLoading) return;
                if (item.action && typeof item.action === "function") {
                    item.action();
                }
                this.isOpen = false; // Close dropdown after selection
            },
            handleClickOutside(event) {
                if (this.$refs.dropdown && !this.$refs.dropdown.contains(event.target)) {
                    this.isOpen = false;
                }
            }
        }
    };
</script>
