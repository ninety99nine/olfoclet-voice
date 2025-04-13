<template>
    <div
        v-if="visible"
        :class="[
            'flex p-4 rounded-lg text-sm border',
            alertClass,
        ]"
        role="alert"
        tabindex="-1"
        aria-labelledby="alert-label"
    >
        <!-- Icon -->
        <div
            v-if="showIcon"
            class="shrink-0">

            <svg
                v-if="type == 'light'"
                class="shrink-0 size-4 mt-0.5" xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                <circle cx="12" cy="12" r="10"></circle>
                <path d="M12 16v-4"></path>
                <path d="M12 8h.01"></path>
            </svg>

            <svg
                v-if="type == 'primary'"
                class="shrink-0 size-4 mt-0.5" xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                <circle cx="12" cy="12" r="10"></circle>
                <path d="M12 16v-4"></path>
                <path d="M12 8h.01"></path>
            </svg>

            <svg
                v-if="type == 'success'"
                class="shrink-0 size-4 mt-0.5" xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                <path d="M12 22c5.523 0 10-4.477 10-10S17.523 2 12 2 2 6.477 2 12s4.477 10 10 10z"></path>
                <path d="m9 12 2 2 4-4"></path>
            </svg>

            <svg
                v-else-if="type == 'warning'"
                class="shrink-0 size-4 mt-0.5" xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                <path d="m21.73 18-8-14a2 2 0 0 0-3.48 0l-8 14A2 2 0 0 0 4 21h16a2 2 0 0 0 1.73-3Z"></path>
                <path d="M12 9v4"></path>
                <path d="M12 17h.01"></path>
            </svg>

            <svg
                v-else-if="type == 'danger'"
                class="shrink-0 size-4 mt-0.5" xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                <circle cx="12" cy="12" r="10"></circle>
                <path d="m15 9-6 6"></path>
                <path d="m9 9 6 6"></path>
            </svg>

        </div>

        <!-- Content -->
        <div class="ms-4">
            <h3 id="alert-label" class="text-sm font-semibold">
                <slot name="title">{{ title }}</slot>
            </h3>
            <div v-if="description" class="mt-1 text-sm">
                <slot name="description">{{ description }}</slot>
            </div>
        </div>

        <!-- Close Button -->
        <button
            v-if="dismissable"
            @click="visible = false"
            class="ms-auto flex items-center justify-center size-5 rounded-lg border border-transparent text-gray-500 hover:bg-gray-100 hover:text-gray-800 focus:outline-none dark:text-gray-400 dark:hover:bg-gray-700 dark:hover:text-white"
        >
            <span class="sr-only">Close</span>
            <svg class="size-3" xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                <path d="M18 6 6 18"></path>
                <path d="m6 6 12 12"></path>
            </svg>
        </button>
    </div>
</template>

<script>
    export default {
        props: {
            type: {
                type: String,
                default: "light",
                validator: (value) => ["light", "primary", "success", "warning", "danger"].includes(value),
            },
            title: {
                type: String,
                default: "Alert Title"
            },
            description: {
                type: String,
                default: ""
            },
            dismissable: {
                type: Boolean,
                default: true
            },
            showIcon: {
                type: Boolean,
                default: true
            }
        },
        data() {
            return {
                visible: true
            };
        },
        computed: {
            alertClass() {
                const classes = {
                    light: "bg-gray-50 border-gray-200 text-gray-800 dark:bg-gray-800/10 dark:border-gray-700 dark:text-gray-500",
                    primary: "bg-blue-50 border-blue-200 text-blue-800 dark:bg-blue-800/10 dark:border-blue-900 dark:text-blue-500",
                    success: "bg-green-50 border-green-200 text-green-800 dark:bg-green-800/10 dark:border-green-900 dark:text-green-500",
                    warning: "bg-yellow-50 border-yellow-200 text-yellow-800 dark:bg-yellow-800/10 dark:border-yellow-900 dark:text-yellow-500",
                    danger: "bg-red-50 border-red-200 text-red-800 dark:bg-red-800/10 dark:border-red-900 dark:text-red-500",
                };
                return classes[this.type] || classes.light;
            }
        }
    };
</script>
