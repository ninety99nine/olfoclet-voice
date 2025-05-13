<template>
    <Drawer
        ref="drawer"
        placement="right"
        maxWidth="max-w-md"
        :showFooter="false"
        :scrollOnContent="false"
    >
        <template #content>
            <!-- Header -->
            <div class="flex justify-between items-center space-x-2 bg-gray-100 border-b shadow-sm p-4">
                <div class="flex items-center space-x-2">
                    <div class="flex items-center space-x-2 text-gray-700">
                        <!-- Icon -->
                        <FileText size="20" />
                        <!-- Heading -->
                        <h2>Add New Action</h2>
                    </div>
                </div>
                <!-- Close Icon -->
                <svg
                    @click="closeDrawer"
                    class="w-6 h-6 text-gray-400 cursor-pointer hover:opacity-90 active:opacity-80 active:scale-90 transition-all"
                    xmlns="http://www.w3.org/2000/svg"
                    fill="none"
                    viewBox="0 0 24 24"
                    stroke-width="1.5"
                    stroke="currentColor"
                >
                    <path stroke-linecap="round" stroke-linejoin="round" d="M6 18 18 6M6 6l12 12" />
                </svg>
            </div>

            <p class="p-4 text-sm bg-blue-100">
                Pick an action to add to your script flow
            </p>

            <div class="grid grid-cols-3 p-2">
                <div
                    :key="index"
                    @click="addNode(option)"
                    v-for="(option, index) in options"
                    class="relative space-y-2 px-2 py-4 group hover:bg-blue-50 cursor-pointer transition-all duration-500 border border-gray-300 hover:border-blue-500"
                >
                    <Component :is="option.icon" size="20" class="mx-auto" />
                    <p class="text-xs text-center">{{ option.name }}</p>
                    <Tooltip
                        trigger="hover"
                        :content="option.description"
                        :position="(index + 1) % 3 === 0 ? 'left' : 'top'"
                        class="absolute bottom-2 right-2 opacity-0 group-hover:opacity-100 transition-all"
                    />
                </div>
            </div>
        </template>
    </Drawer>
</template>

<script>
import Drawer from '@Partials/Drawer.vue';
import Tooltip from '@Partials/Tooltip.vue';
import {
    FileText,
    File,
    Code,
    GitBranch,
    Mic,
    CheckCircle,
} from 'lucide-vue-next';

export default {
    components: { Drawer, Tooltip, FileText },
    data() {
        return {
            options: [
                {
                    name: 'Page',
                    icon: File,
                    type: 'page',
                    description: 'Displays a page with script prompts or instructions for the agent'
                },
                {
                    name: 'HTTP Request',
                    icon: Code,
                    type: 'http_request',
                    description: 'Makes an HTTP request to an external API (e.g., fetch customer data)'
                },
                {
                    name: 'Branch',
                    icon: GitBranch,
                    type: 'branch',
                    description: 'Branches the script based on caller responses'
                },
                {
                    name: 'Recording',
                    icon: Mic,
                    type: 'recording',
                    description: 'Records a section of the conversation for compliance or quality'
                },
                {
                    name: 'End Script',
                    icon: CheckCircle,
                    type: 'end_script',
                    description: 'Ends the script flow'
                },
            ],
        };
    },
    methods: {
        showDrawer() {
            this.$refs.drawer.showDrawer();
        },
        hideDrawer() {
            this.$refs.drawer.hideDrawer();
        },
        addNode(option) {
            this.$emit('add-node', option.type);
            this.hideDrawer();
        },
    },
};
</script>
