<template>
    <ol class="flex items-center whitespace-nowrap text-sm">
        <!-- Loop through the path except the last item -->
        <li v-for="(node, index) in path.slice(0, -1)" :key="index" class="inline-flex items-center">
            <button
                @click="handleClick(node.id)"
                :class="[{ 'hover:text-blue-600 active:text-blue-500 cursor-pointer' : action }, 'flex items-center text-gray-500']">
                {{ node.title }}
            </button>
            <svg
                class="shrink-0 mx-2 size-4 text-gray-400 dark:text-neutral-600"
                xmlns="http://www.w3.org/2000/svg"
                width="24"
                height="24"
                viewBox="0 0 24 24"
                fill="none"
                stroke="currentColor"
                stroke-width="2"
                stroke-linecap="round"
                stroke-linejoin="round">
                <path d="m9 18 6-6-6-6"></path>
            </svg>
        </li>
        <!-- Last item in the path (current location) -->
        <li
            class="inline-flex items-center font-semibold text-gray-800 truncate dark:text-neutral-200"
            aria-current="page">
            {{ path[path.length - 1].title }}
        </li>
    </ol>
</template>

<script>
export default {
    props: {
        path: {
            type: Array,
            required: true,
            default: () => []
        },
        action: {
            type: Function,
            default: null
        }
    },
    methods: {
        handleClick(nodeId) {
            if (this.action) {
                this.action(nodeId);
            }
        }
    }
};
</script>
