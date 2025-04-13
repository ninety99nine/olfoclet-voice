<template>

    <div class="inline-flex items-center bg-gray-100 rounded-lg p-1 dark:bg-neutral-800 space-x-1">

        <template v-for="tab in tabs" :key="tab.value">

            <button
                type="button"
                @click="selectTab(tab.value)"
                :class="[
                    'flex items-center gap-x-2 px-4 py-2 text-sm font-medium rounded-md transition cursor-pointer',
                    tab.value === modelValue
                    ? 'bg-white shadow text-gray-900 dark:bg-neutral-700 dark:text-white'
                    : 'text-gray-500 hover:text-gray-700 hover:bg-white dark:text-neutral-400 dark:hover:text-white'
                ]">

                <!-- Icon -->
                <component v-if="tab.icon" :is="tab.icon" class="w-4 h-4" />

                <!-- Label -->
                <span>{{ tab.label }}</span>

            </button>

        </template>

    </div>

</template>

<script>
    export default {
        props: {
            modelValue: {
                type: String,
                required: true
            },
            tabs: {
                type: Array,
                required: true,
                default: () => [],
            },
        },
        methods: {
            selectTab(value) {
                this.$emit('update:modelValue', value)
                this.$emit('change', value)
            },
        },
    }
</script>
