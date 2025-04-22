<template>

    <Drawer
        ref="drawer"
        placement="right"
        maxWidth="max-w-md"
        :showFooter="false"
        :scrollOnContent="false">

        <template #content>

            <!-- Header -->
            <div class="flex justify-between items-center space-x-2 bg-gray-100 border-b shadow-sm p-4">

                <div class="flex items-center space-x-2">

                    <div class="flex items-center space-x-2 text-gray-700">

                        <!-- Columns Icon -->
                        <Zap size="20"></Zap>

                        <!-- Heading -->
                        <h2>Add New Action</h2>

                    </div>

                </div>

                <!-- Close Icon -->
                <svg
                    @click="closeDrawer"
                    class="w-6 h-6 text-gray-400 cursor-pointer hover:opacity-90 active:opacity-80 active:scale-90 transition-all"
                    xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M6 18 18 6M6 6l12 12" />
                </svg>

            </div>

            <p class="p-4 text-sm bg-blue-100">
                Pick an action to add to your call flow
            </p>

            <div class="grid grid-cols-3 p-2">
                <div
                    :key="index"
                    v-for="(node, index) in nodes"
                    class="relative space-y-2 px-2 py-4 group hover:bg-blue-50 cursor-pointer transition-all duration-500 border border-gray-300 hover:border-blue-500">
                    <Component :is="node.icon" size="20" class="mx-auto"></Component>
                    <p class="text-xs text-center">{{ node.name }}</p>
                    <Tooltip
                        trigger="hover"
                        :content="node.description"
                        :position="(index + 1) % 3 === 0 ? 'left' : 'top'"
                        class="absolute bottom-2 right-2 opacity-0 group-hover:opacity-100 transition-all">
                    </Tooltip>
                </div>
            </div>

        </template>

    </Drawer>

</template>

<script>

    import Drawer from '@Partials/Drawer.vue';
    import Tooltip from '@Partials/Tooltip.vue';
    import { Zap, Mic, Redo, Code, Clock, Split, Printer, UserRound, Drama, UserRoundCheck, ChevronRight, UsersRound, CirclePlay, Keyboard, Handshake, PhoneCall, Network, Voicemail, CircleCheckBig, Import } from 'lucide-vue-next';

    export default {
        components: { Zap, Drawer, Tooltip },
        data() {
            return {
                nodes: [
                    {
                        name: 'Call Agent',
                        icon: UserRound,
                        description: 'Routes calls to a specific agent'
                    },
                    {
                        name: 'Call Favourite Agent',
                        icon: UserRoundCheck,
                        description: 'Routes calls to the contacts favourite agent'
                    },
                    {
                        name: 'Call Group',
                        icon: UsersRound,
                        description: 'Distributes calls to a group of agents for load balancing'
                    },
                    {
                        name: 'Playback',
                        icon: CirclePlay,
                        description: 'Plays a pre-recorded audio message to callers'
                    },
                    {
                        name: 'Voicemail',
                        icon: Voicemail,
                        description: 'Directs calls to voicemail for message recording'
                    },
                    {
                        name: 'Redirect',
                        icon: Redo,
                        description: 'Forwards calls to an external number'
                    },
                    {
                        name: 'Recording',
                        icon: Mic,
                        description: 'Records the call for quality or compliance purposes'
                    },
                    {
                        name: 'Fax',
                        icon: Printer,
                        description: 'Sends or receives fax documents during a call'
                    },
                    {
                        name: 'Conference',
                        icon: Handshake,
                        description: 'Initiates a multi-party conference call'
                    },
                    {
                        name: 'Musk Caller ID',
                        icon: Drama,
                        description: 'Masks the caller’s ID for privacy or branding'
                    },
                    {
                        name: 'Get And Dial An Extension',
                        icon: PhoneCall,
                        description: 'Prompts caller to input an extension and dials it'
                    },
                    {
                        name: 'IVR',
                        icon: Network,
                        description: 'Guides callers through an interactive voice response menu'
                    },
                    {
                        name: 'Time Route',
                        icon: Clock,
                        description: 'Routes calls based on time of day or week'
                    },
                    {
                        name: 'Go To',
                        icon: ChevronRight,
                        description: 'Jumps to another point in the call flow'
                    },
                    {
                        name: 'Condition Splitter',
                        icon: Split,
                        description: 'Splits call flow based on conditions (e.g., caller input)'
                    },
                    {
                        name: 'HTTP Request',
                        icon: Code,
                        description: 'Makes an HTTP request to an external API'
                    },
                    {
                        name: 'Collect Input',
                        icon: Keyboard,
                        description: 'Collects DTMF or voice input from callers'
                    },
                    {
                        name: 'Mark Call As Resolved',
                        icon: CircleCheckBig,
                        description: 'Tags a call as resolved for reporting purposes'
                    },
                    {
                        name: 'Add Saved Flow',
                        icon: Import,
                        description: 'Inserts a pre-saved call flow segment'
                    }
                ]
            };
        },
        methods: {
            openDrawer() {
                this.$refs.drawer.showDrawer();
            },
            closeDrawer() {
                this.$refs.drawer.hideDrawer();
            }
        },
    };
</script>
