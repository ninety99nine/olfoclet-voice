<template>

    <div
        class="hs-tooltip inline-block"
        :class="[triggerClass, positionClass]"
        :style="`--trigger:${trigger}; --placement:${position};`">

        <div class="hs-tooltip-toggle">

            <slot name="trigger">
                <svg class="w-4 h-4 text-gray-500 hover:text-gray-400" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" d="m11.25 11.25.041-.02a.75.75 0 0 1 1.063.852l-.708 2.836a.75.75 0 0 0 1.063.853l.041-.021M21 12a9 9 0 1 1-18 0 9 9 0 0 1 18 0Zm-9-3.75h.008v.008H12V8.25Z" />
                </svg>
            </slot>

            <div
                class="hs-tooltip-content hs-tooltip-shown:opacity-100 hs-tooltip-shown:visible hidden opacity-0 transition-opacity absolute invisible z-50 w-auto max-w-xs bg-white border border-gray-100 text-start rounded-xl shadow-md after:absolute after:top-0 after:-start-4 after:w-4 after:h-full dark:bg-neutral-800 dark:border-neutral-700"
                role="tooltip">

                <slot name="content">
                    <p class="text-xs leading-5 px-4 py-2 whitespace-normal">{{ content }}</p>
                </slot>

            </div>

        </div>

    </div>

</template>

<script>
    export default {
        props: {
            content: {
                type: String,
                default: "This is a hover popover."
            },
            trigger: {
                type: String,
                default: "hover",
                validator: (value) => ["click", "hover"].includes(value),
            },
            position: {
                type: String,
                default: "bottom",
                validator: (value) => ["top", "bottom", "left", "right"].includes(value),
            }
        },
        computed: {
            triggerClass() {
                return `[--trigger:${this.trigger}]`;
            },
            positionClass() {
                return `[--placement:${this.position}]`;
            }
        },
        mounted() {
            setTimeout(() => {
                if (window.HSTooltip) {
                    window.HSTooltip.autoInit();
                }
            }, 500);
        }
    };
</script>
