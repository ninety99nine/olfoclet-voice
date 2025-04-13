<template>
    <div>

        <slot name="trigger" :showDrawer="showDrawer">
            <Button
                size="sm"
                type="primary"
                v-if="trigger"
                :action="showDrawer">
                {{ trigger }}
            </Button>
        </slot>

        <div
            :id="uniqueId"
            :class="[
                'hs-overlay hidden fixed inset-y-0 z-50 w-full max-w-xs bg-white shadow-lg border-r border-gray-200 transition-transform duration-300 ease-in-out dark:bg-neutral-800 dark:border-neutral-700',
                positionClass,
                { '[--overlay-backdrop:static]' : !dismissable }
            ]">

            <div class="relative flex flex-col h-full">

                <button
                    type="button"
                    aria-label="Close"
                    @click.stop.prevent="hideDrawer"
                    class="absolute top-2 right-2 size-8 inline-flex justify-center items-center gap-x-2 rounded-full border border-transparent bg-gray-100 text-gray-700 hover:bg-gray-200 focus:outline-hidden focus:bg-gray-200 dark:bg-neutral-700 dark:hover:bg-neutral-600 dark:text-neutral-400 dark:focus:bg-neutral-600">
                    <span class="sr-only">Close</span>
                    <svg class="shrink-0 size-4" xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                        <path d="M18 6 6 18"></path>
                        <path d="m6 6 12 12"></path>
                    </svg>
                </button>

                <div
                    v-if="header || $slots.header"
                    class="flex justify-between items-center py-3 px-4 border-b border-gray-200 dark:border-neutral-700">
                    <slot name="header" :hideDrawer="hideDrawer">
                        <h3 v-if="header" class="font-bold text-gray-700 dark:text-white">
                            {{ header }}
                        </h3>
                    </slot>
                </div>

                <div
                    v-if="content || $slots.content"
                    :class="['p-4 flex-1 overflow-y-auto', { 'max-h-80' : scrollOnContent }]">
                    <slot name="content" :hideDrawer="hideDrawer">
                        <p class="text-sm text-gray-700 dark:text-neutral-400">
                            {{ content }}
                        </p>
                    </slot>
                </div>

                <slot name="footer" :hideDrawer="hideDrawer">
                    <div
                        v-if="showFooter && (showCancelButton || showApproveButton)"
                        class="flex justify-end items-center gap-x-2 py-3 px-4 border-t border-gray-200 dark:border-neutral-700">

                        <Button
                            :size="declineSize"
                            :type="declineType"
                            v-if="showCancelButton"
                            :action="declineAction ? () => declineAction(hideDrawer) : hideDrawer">
                            {{ declineText }}
                        </Button>

                        <Button
                            :size="approveSize"
                            :type="approveType"
                            v-if="showApproveButton"
                            :action="approveAction ? () => approveAction(hideDrawer) : hideDrawer">
                            {{ approveText }}
                        </Button>

                    </div>
                </slot>

            </div>
        </div>

    </div>
</template>

<script>

import Button from '@Partials/Button.vue';
import { generateUniqueId } from '@Utils/generalUtils.js';

export default {
    components: { Button },
    props: {
        position: {
            type: String,
            default: "right",
            validator: (value) => ["left", "right"].includes(value),
        },
        trigger: {
            type: [String, null],
            default: null
        },
        header: {
            type: [String, null],
            default: null
        },
        content: {
            type: [String, null],
            default: null
        },
        showFooter: {
            type: Boolean,
            default: true
        },
        showCancelButton: {
            type: Boolean,
            default: true
        },
        declineText: {
            type: String,
            default: 'Cancel'
        },
        declineType: {
            type: String,
            default: 'light'
        },
        declineSize: {
            type: String,
            default: 'sm'
        },
        declineAction: {
            type: [Function, null],
            default: null
        },
        showApproveButton: {
            type: Boolean,
            default: true
        },
        approveText: {
            type: String,
            default: 'Confirm'
        },
        approveSize: {
            type: String,
            default: 'sm'
        },
        approveType: {
            type: String,
            default: 'primary'
        },
        approveAction: {
            type: [Function, null],
            default: null
        },
        scrollOnContent: {
            type: Boolean,
            default: true
        },
        dismissable: {
            type: Boolean,
            default: true
        },
        onShow: {
            type: [Function, null],
            default: null
        },
        onHide: {
            type: [Function, null],
            default: null
        },
    },
    data() {
        return {
            uniqueId: generateUniqueId('drawer'),
        }
    },
    computed: {
        positionClass() {
            return this.position === "right"
                ? "end-0 translate-x-full hs-overlay-open:translate-x-0"
                : "start-0 -translate-x-full hs-overlay-open:translate-x-0";
        }
    },
    mounted() {
        setTimeout(() => {
            if (window.HSOverlay) {
                window.HSOverlay.autoInit();
            }
        }, 500);
    },
    methods: {
        showDrawer() {
            const drawer = document.querySelector(`#${this.uniqueId}`);
            if (drawer) {
                window.HSOverlay.open(drawer);
                if(this.onShow) this.onShow();
            }
        },
        hideDrawer() {
            const drawer = document.querySelector(`#${this.uniqueId}`);
            if (drawer) {
                window.HSOverlay.close(drawer);
                if(this.onHide) this.onHide();
            }
        }
    }
};

</script>
