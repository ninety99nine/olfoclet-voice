<template>

    <div class="flowchart-wrapper">

        <VueFlow :nodes="nodes" :edges="edges" :node-types="nodeTypes" class="bg-gray-50">
            <Background pattern-color="#aaa" :gap="16" />
            <MiniMap />
        </VueFlow>

    </div>

    <NodeManagerDrawer ref="drawer"></NodeManagerDrawer>

</template>

<script>

    import { markRaw } from 'vue';
    import { VueFlow } from '@vue-flow/core';
    import { MiniMap } from '@vue-flow/minimap';
    import { Background } from '@vue-flow/background';
    import FaxNode from '@Pages/call-flows/edit/components/nodes/FaxNode.vue';
    import IVRNode from '@Pages/call-flows/edit/components/nodes/IVRNode.vue';
    import GoToNode from '@Pages/call-flows/edit/components/nodes/GoToNode.vue';
    import AgentNode from '@Pages/call-flows/edit/components/nodes/AgentNode.vue';
    import GroupNode from '@Pages/call-flows/edit/components/nodes/GroupNode.vue';
    import PhoneNode from '@Pages/call-flows/edit/components/nodes/PhoneNode.vue';
    import AnsweredNode from '@Pages/call-flows/edit/components/nodes/AnsweredNode.vue';
    import PlaybackNode from '@Pages/call-flows/edit/components/nodes/PlaybackNode.vue';
    import RedirectNode from '@Pages/call-flows/edit/components/nodes/RedirectNode.vue';
    import RecordingNode from '@Pages/call-flows/edit/components/nodes/RecordingNode.vue';
    import TimeRouteNode from '@Pages/call-flows/edit/components/nodes/TimeRouteNode.vue';
    import VoicemailNode from '@Pages/call-flows/edit/components/nodes/VoicemailNode.vue';
    import UnansweredNode from '@Pages/call-flows/edit/components/nodes/UnansweredNode.vue';
    import GetAndDialNode from '@Pages/call-flows/edit/components/nodes/GetAndDialNode.vue';
    import ConferenceNode from '@Pages/call-flows/edit/components/nodes/ConferenceNode.vue';
    import HttpRequestNode from '@Pages/call-flows/edit/components/nodes/HttpRequestNode.vue';
    import CollectInputNode from '@Pages/call-flows/edit/components/nodes/CollectInputNode.vue';
    import MuskCallerIDNode from '@Pages/call-flows/edit/components/nodes/MuskCallerIDNode.vue';
    import FavouriteAgentNode from '@Pages/call-flows/edit/components/nodes/FavouriteAgentNode.vue';
    import MarkAsResolvedNode from '@Pages/call-flows/edit/components/nodes/MarkAsResolvedNode.vue';
    import ConditionSplitterNode from '@Pages/call-flows/edit/components/nodes/ConditionSplitterNode.vue';
    import NodeManagerDrawer from '@Pages/call-flows/edit/components/node-manage-drawer/NodeManagerDrawer.vue';

    export default {
        components: { VueFlow, Background, MiniMap, NodeManagerDrawer },
        data() {
            return {
                nodeTypes: {
                    faxNode: markRaw(FaxNode),
                    ivrNode: markRaw(IVRNode),
                    goToNode: markRaw(GoToNode),
                    agentNode: markRaw(AgentNode),
                    groupNode: markRaw(GroupNode),
                    phoneNode: markRaw(PhoneNode),
                    answeredNode: markRaw(AnsweredNode),
                    playbackNode: markRaw(PlaybackNode),
                    redirectNode: markRaw(RedirectNode),
                    timeRouteNode: markRaw(TimeRouteNode),
                    voicemailNode: markRaw(VoicemailNode),
                    recordingNode: markRaw(RecordingNode),
                    getAndDialNode: markRaw(GetAndDialNode),
                    conferenceNode: markRaw(ConferenceNode),
                    unansweredNode: markRaw(UnansweredNode),
                    httpRequestNode: markRaw(HttpRequestNode),
                    collectInputNode: markRaw(CollectInputNode),
                    muskCallerIDNode: markRaw(MuskCallerIDNode),
                    favouriteAgentNode: markRaw(FavouriteAgentNode),
                    markAsResolvedNode: markRaw(MarkAsResolvedNode),
                    conditionSplitterNode: markRaw(ConditionSplitterNode),
                },
                nodes: [
                    {
                        id: '1',
                        type: 'phoneNode',
                        position: { x: 100, y: 100 },
                        data: {
                            openDrawer: this.openDrawer,
                        }
                    },
                    {
                        id: '2',
                        type: 'ivrNode',
                        position: { x: 350, y: 100 },
                        data: {
                            openDrawer: this.openDrawer,
                        }
                    },
                    {
                        id: '3',
                        type: 'groupNode',
                        label: 'Group Node',
                        position: { x: 600, y: 100 },
                        data: {
                            name: 'Sales',
                            number: '+26775993221',
                            openDrawer: this.openDrawer,
                        }
                    },
                    {
                        id: '4',
                        type: 'agentNode',
                        label: 'Agent Node',
                        position: { x: 850, y: 100 },
                        data: {
                            name: 'Sales',
                            number: '+26775993221',
                            openDrawer: this.openDrawer,
                        }
                    },
                    {
                        id: '5',
                        type: 'favouriteAgentNode',
                        label: 'Favourite Agent Node',
                        position: { x: 100, y: 250 },
                        data: {
                            name: 'Sales',
                            number: '+26775993221',
                            openDrawer: this.openDrawer,
                        }
                    },
                    {
                        id: '6',
                        type: 'playbackNode',
                        label: 'Playback Node',
                        position: { x: 400, y: 250 },
                        data: {
                            name: 'Sales',
                            number: '+26775993221',
                            openDrawer: this.openDrawer,
                        }
                    },
                    {
                        id: '7',
                        type: 'voicemailNode',
                        label: 'Voicemail Node',
                        position: { x: 650, y: 250 },
                        data: {
                            name: 'Sales',
                            number: '+26775993221',
                            openDrawer: this.openDrawer,
                        }
                    },
                    {
                        id: '8',
                        type: 'recordingNode',
                        label: 'Recording Node',
                        position: { x: 900, y: 250 },
                        data: {
                            name: 'Sales',
                            number: '+26775993221',
                            openDrawer: this.openDrawer,
                        }
                    },
                    {
                        id: '9',
                        type: 'redirectNode',
                        label: 'Redirect Node',
                        position: { x: 100, y: 400 },
                        data: {
                            name: 'Sales',
                            number: '+26775993221',
                            openDrawer: this.openDrawer,
                        }
                    },
                    {
                        id: '10',
                        type: 'muskCallerIDNode',
                        label: 'Change Outbound Caller ID Node',
                        position: { x: 350, y: 400 },
                        data: {
                            name: 'Sales',
                            number: '+26775993221',
                            openDrawer: this.openDrawer,
                        }
                    },
                    {
                        id: '11',
                        type: 'conferenceNode',
                        label: 'Conference Node',
                        position: { x: 700, y: 400 },
                        data: {
                            name: 'Sales',
                            number: '+26775993221',
                            openDrawer: this.openDrawer,
                        }
                    },
                    {
                        id: '12',
                        type: 'getAndDialNode',
                        label: 'Get And Dial Node',
                        position: { x: 950, y: 400 },
                        data: {
                            name: 'Sales',
                            number: '+26775993221',
                            openDrawer: this.openDrawer,
                        }
                    },
                    {
                        id: '13',
                        type: 'collectInputNode',
                        label: 'Collect Input Node',
                        position: { x: 100, y: 550 },
                        data: {
                            name: 'Sales',
                            number: '+26775993221',
                            openDrawer: this.openDrawer,
                        }
                    },
                    {
                        id: '14',
                        type: 'faxNode',
                        label: 'Fax Node',
                        position: { x: 400, y: 550 },
                        data: {
                            name: 'Sales',
                            number: '+26775993221',
                            openDrawer: this.openDrawer,
                        }
                    },
                    {
                        id: '15',
                        type: 'conditionSplitterNode',
                        label: 'Condition Splitter Node',
                        position: { x: 650, y: 550 },
                        data: {
                            name: 'Sales',
                            number: '+26775993221',
                            openDrawer: this.openDrawer,
                        }
                    },
                    {
                        id: '16',
                        type: 'goToNode',
                        label: 'Go To Node',
                        position: { x: 950, y: 550 },
                        data: {
                            name: 'Sales',
                            number: '+26775993221',
                            openDrawer: this.openDrawer,
                        }
                    },
                    {
                        id: '17',
                        type: 'timeRouteNode',
                        label: 'Time Route Node',
                        position: { x: 100, y: 750 },
                        data: {
                            name: 'Sales',
                            number: '+26775993221',
                            openDrawer: this.openDrawer,
                        }
                    },
                    {
                        id: '18',
                        type: 'markAsResolvedNode',
                        label: 'Mark As Resolved Node',
                        position: { x: 400, y: 700 },
                        data: {
                            name: 'Sales',
                            number: '+26775993221',
                            openDrawer: this.openDrawer,
                        }
                    },
                    {
                        id: '19',
                        type: 'httpRequestNode',
                        label: 'Http Request Node',
                        position: { x: 700, y: 700 },
                        data: {
                            name: 'Sales',
                            number: '+26775993221',
                            openDrawer: this.openDrawer,
                        }
                    },
                    {
                        id: '20',
                        type: 'answeredNode',
                        label: 'Answered Node',
                        position: { x: 100, y: 900 },
                        data: {
                            name: 'Sales',
                            number: '+26775993221',
                            openDrawer: this.openDrawer,
                        }
                    },
                    {
                        id: '21',
                        type: 'unansweredNode',
                        label: 'Unanswered Node',
                        position: { x: 300, y: 900 },
                        data: {
                            name: 'Sales',
                            number: '+26775993221',
                            openDrawer: this.openDrawer,
                        }
                    },
                ],
                edges: [],
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

<style>

    .flowchart-wrapper {
        width: 100%;
        height: calc(100vh - 60px);
    }

</style>
