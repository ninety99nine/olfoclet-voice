<template>

    <div
        class="hs-tooltip inline-block"
        :class="[triggerClass, positionClass]"
        :style="`--trigger:${trigger}; --placement:${position};`">

        <div class="hs-tooltip-toggle">

            <slot name="trigger">
                <svg class="w-4 h-4 text-gray-300 hover:text-gray-400" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="currentColor">
                    <path fill-rule="evenodd" d="M2.25 12c0-5.385 4.365-9.75 9.75-9.75s9.75 4.365 9.75 9.75-4.365 9.75-9.75 9.75S2.25 17.385 2.25 12Zm11.378-3.917c-.89-.777-2.366-.777-3.255 0a.75.75 0 0 1-.988-1.129c1.454-1.272 3.776-1.272 5.23 0 1.513 1.324 1.513 3.518 0 4.842a3.75 3.75 0 0 1-.837.552c-.676.328-1.028.774-1.028 1.152v.75a.75.75 0 0 1-1.5 0v-.75c0-1.279 1.06-2.107 1.875-2.502.182-.088.351-.199.503-.331.83-.727.83-1.857 0-2.584ZM12 18a.75.75 0 1 0 0-1.5.75.75 0 0 0 0 1.5Z" clip-rule="evenodd" />
                </svg>
            </slot>

            <span
                class="hs-tooltip-content hs-tooltip-shown:opacity-100 hs-tooltip-shown:visible hidden opacity-0 transition-opacity absolute invisible z-50 w-auto max-w-xs bg-gray-900 text-white text-xs px-2 py-1 rounded-lg shadow-md dark:bg-gray-700"
                role="tooltip">

                <slot name="content">
                    <p class="text-xs leading-5 px-4 py-2 whitespace-normal">{{ content }}</p>
                </slot>

            </span>

        </div>

    </div>

</template>

<script>
export default {
    props: {
        trigger: {
            type: String,
            default: "hover",
            validator: (value) => ["click", "hover"].includes(value),
        },
        position: {
            type: String,
            default: "top",
            validator: (value) => ["top", "bottom", "left", "right"].includes(value),
        },
        content: {
            type: String,
            default: "This is a tooltip.",
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
