<template>

    <div class="flowchart-wrapper">

        <!-- Header -->
        <div class="flex justify-between items-center p-4 bg-gray-100 border-b border-gray-200">

            <div class="flex items-center space-x-2">

                <WorkflowIcon size="24" class="text-gray-600" />

                <h2 class="text-lg font-semibold text-gray-900 dark:text-white">
                    {{ isEditing ? 'Edit Call Flow' : 'Create Call Flow' }}
                </h2>

            </div>

            <div class="flex space-x-2">

                <Button type="light" size="md" :action="goBack">
                    <span>Cancel</span>
                </Button>

                <Button type="primary" size="md" :leftIcon="Save" leftIconSize="20" :action="saveCallFlow" :loading="isSaving">
                    <span>Save</span>
                </Button>

            </div>

        </div>

        <!-- Vue Flow Canvas -->
        <VueFlow
            :nodes="nodes"
            :edges="edges"
            class="bg-gray-50"
            :node-types="nodeTypes"
            @nodes-change="onNodesChange"
            @edges-change="onEdgesChange">

            <MiniMap />

            <Controls
                :show-zoom="true"
                :show-fit-view="true"
                position="top-right"
                :show-interactive="true"
                class="p-2 bg-white border border-gray-200 rounded shadow-sm"
            />

            <Background pattern-color="#aaa" :gap="16" />

        </VueFlow>

        <NodeManagerDrawer ref="drawer" @add-node="handleAddNode" />

    </div>

</template>

<script>

    import axios from 'axios';
    import { markRaw } from 'vue';
    import { v4 as uuidv4 } from 'uuid';
    import { VueFlow } from '@vue-flow/core';
    import Button from '@Partials/Button.vue';
    import { MiniMap } from '@vue-flow/minimap';
    import { Controls } from '@vue-flow/controls';
    import { Background } from '@vue-flow/background';
    import { Workflow as WorkflowIcon, Save } from 'lucide-vue-next';
    import FaxNode from '@Pages/call-flows/edit/components/nodes/FaxNode.vue';
    import IVRNode from '@Pages/call-flows/edit/components/nodes/IVRNode.vue';
    import GoToNode from '@Pages/call-flows/edit/components/nodes/GoToNode.vue';
    import AgentNode from '@Pages/call-flows/edit/components/nodes/AgentNode.vue';
    import GroupNode from '@Pages/call-flows/edit/components/nodes/GroupNode.vue';
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
    import IncomingCallNode from '@Pages/call-flows/edit/components/nodes/IncomingCallNode.vue';
    import CollectInputNode from '@Pages/call-flows/edit/components/nodes/CollectInputNode.vue';
    import MuskCallerIDNode from '@Pages/call-flows/edit/components/nodes/MuskCallerIDNode.vue';
    import FavouriteAgentNode from '@Pages/call-flows/edit/components/nodes/FavouriteAgentNode.vue';
    import MarkAsResolvedNode from '@Pages/call-flows/edit/components/nodes/MarkAsResolvedNode.vue';
    import ConditionSplitterNode from '@Pages/call-flows/edit/components/nodes/ConditionSplitterNode.vue';
    import NodeManagerDrawer from '@Pages/call-flows/edit/components/node-manage-drawer/NodeManagerDrawer.vue';

    export default {
        components: {
            Button, VueFlow, Background, MiniMap, Controls, NodeManagerDrawer, WorkflowIcon,
        },
        data() {
            return {
                Save,
                nodes: [],
                edges: [],
                callFlow: null,
                isSaving: false,
                isEditing: false,
                sourceNodeId: null,
                nodeTypes: {
                    fax: markRaw(FaxNode),
                    ivr: markRaw(IVRNode),
                    go_to: markRaw(GoToNode),
                    agent: markRaw(AgentNode),
                    group: markRaw(GroupNode),
                    answered: markRaw(AnsweredNode),
                    playback: markRaw(PlaybackNode),
                    redirect: markRaw(RedirectNode),
                    time_route: markRaw(TimeRouteNode),
                    voicemail: markRaw(VoicemailNode),
                    recording: markRaw(RecordingNode),
                    conference: markRaw(ConferenceNode),
                    unanswered: markRaw(UnansweredNode),
                    get_and_dial: markRaw(GetAndDialNode),
                    http_request: markRaw(HttpRequestNode),
                    incoming_call: markRaw(IncomingCallNode),
                    collect_input: markRaw(CollectInputNode),
                    musk_caller_id: markRaw(MuskCallerIDNode),
                    favourite_agent: markRaw(FavouriteAgentNode),
                    mark_as_resolved: markRaw(MarkAsResolvedNode),
                    condition_splitter: markRaw(ConditionSplitterNode),
                },
            };
        },
        computed: {
            callFlowId() {
                return this.$route.params.call_flow_id;
            },
        },
        created() {
            this.isEditing = !!this.callFlowId;
            if (this.isEditing) {
                this.loadCallFlow();
            } else {
                this.initializeNewCallFlow();
            }
        },
        methods: {
            goBack() {
                this.$router.push({ name: 'show-call-flows' });
            },
            showDrawer(sourceNodeId) {
                this.sourceNodeId = sourceNodeId;
                this.$refs.drawer.showDrawer();
            },
            hideDrawer() {
                this.$refs.drawer.hideDrawer();
            },
            initializeNewCallFlow() {
                const id = uuidv4();
                const defaultNode = {
                    id: id,
                    type: 'incoming_call',
                    position: { x: 100, y: 250 },
                    data: {
                        showDrawer: () => this.showDrawer(id),
                    },
                };
                this.nodes = [defaultNode];
                this.edges = [];
                this.callFlow = {
                    name: 'New Call Flow',
                    is_active: true,
                };
            },
            async loadCallFlow() {
                try {
                    const response = await axios.get(`/api/call-flows/${this.callFlowId}`, {
                        params: {
                            '_relationships': 'nodes,organization',
                        },
                    });
                    this.callFlow = response.data.data;

                    // Load nodes
                    this.nodes = this.callFlow.nodes.map(node => ({
                        id: node.id,
                        type: node.type,
                        position: node.position,
                        data: {
                            ...node.metadata,
                            showDrawer: () => this.showDrawer(node.id),
                        },
                    }));

                    // Load edges
                    this.edges = this.callFlow.nodes
                        .filter(node => node.next_step)
                        .map(node => ({
                            id: `e${node.id}-${node.next_step}`,
                            source: node.id,
                            target: node.next_step,
                        }));
                } catch (error) {
                    console.error('Failed to load call flow:', error);
                    this.$notificationState.showWarningNotification('Failed to load call flow.');
                    this.$router.push({ name: 'show-call-flows' });
                }
            },
            onNodesChange(changes) {
                changes.forEach(change => {
                    if (change.type === 'position' && change.position) {
                        const node = this.nodes.find(n => n.id === change.id);
                        if (node) {
                            node.position = change.position;
                        }
                    }
                });
            },
            onEdgesChange(changes) {
                // Handle edge changes if needed (e.g., adding/removing edges)
                // For now, we'll update edges directly
                this.edges = changes.reduce((acc, change) => {
                    if (change.type === 'add') {
                        acc.push(change.item);
                    } else if (change.type === 'remove') {
                        return acc.filter(edge => edge.id !== change.id);
                    }
                    return acc;
                }, [...this.edges]);
            },
            async saveCallFlow() {
                this.isSaving = true;
                try {
                    const payload = {
                        name: this.callFlow.name,
                        is_active: this.callFlow.is_active,
                    };

                    let response;
                    if (this.isEditing) {
                        // Update existing call flow
                        response = await axios.put(`/api/call-flows/${this.callFlowId}`, payload);
                    } else {
                        // Create new call flow
                        response = await axios.post('/api/call-flows', payload);
                        this.callFlowId = response.data.data.id;
                    }

                    // Save nodes and edges
                    await this.saveNodesAndEdges();

                    this.$notificationState.showSuccessNotification(
                        this.isEditing ? 'Call flow updated successfully!' : 'Call flow created successfully!'
                    );
                    this.$router.push({ name: 'show-call-flows' });
                } catch (error) {
                    console.error('Failed to save call flow:', error);
                    this.$notificationState.showWarningNotification('Failed to save call flow.');
                } finally {
                    this.isSaving = false;
                }
            },
            async saveNodesAndEdges() {

                // Map nodes to the backend format
                const nodesPayload = this.nodes.map(node => ({
                    id: node.id,
                    type: node.type,
                    metadata: node.data,
                    position: node.position,
                    call_flow_id: this.callFlowId,
                    next_step: this.getNextStep(node.id)
                }));

                // Save each node
                for (const node of nodesPayload) {
                    try {
                        if (this.isEditing && this.callFlow.nodes.some(n => n.id === node.id)) {
                            // Update existing node
                            await axios.put(`/api/call-flow-nodes/${node.id}`, {
                                type: node.type,
                                next_step: node.next_step,
                                metadata: node.metadata,
                                position: node.position,
                            });
                        } else {
                            // Create new node
                            await axios.post('/api/call-flow-nodes', {
                                call_flow_id: this.callFlowId,
                                type: node.type,
                                next_step: node.next_step,
                                metadata: node.metadata,
                                position: node.position,
                            });
                        }
                    } catch (error) {
                        console.error(`Failed to save node ${node.id}:`, error);
                        throw error;
                    }
                }

                // Delete nodes that are no longer in the canvas
                if (this.isEditing) {
                    const currentNodeIds = this.nodes.map(node => node.id);
                    const nodesToDelete = this.callFlow.nodes
                        .filter(node => !currentNodeIds.includes(node.id))
                        .map(node => node.id);
                    if (nodesToDelete.length > 0) {
                        await axios.delete('/api/call-flow-nodes', {
                            params: {
                                call_flow_node_ids: nodesToDelete,
                            },
                        });
                    }
                }
            },
            getNextStep(nodeId) {
                const edge = this.edges.find(edge => edge.source === nodeId);
                return edge ? edge.target : null;
            },
            handleAddNode(nodeType) {
            if (!this.sourceNodeId) {
                console.error('No source node selected to connect the new node.');
                return;
            }

            const sourceNode = this.nodes.find(node => node.id === this.sourceNodeId);
            if (!sourceNode) {
                console.error('Source node not found:', this.sourceNodeId);
                return;
            }

            // Calculate the parent node's position (to the right of the source node)
            const parentNodePosition = {
                x: sourceNode.position.x + 250, // 250px to the right
                y: sourceNode.position.y,
            };

            // Create the parent node (e.g., Call Agent, Call Favourite Agent, Call Group)
            const parentNodeId = uuidv4();
            const parentNode = {
                id: parentNodeId,
                type: nodeType,
                position: parentNodePosition,
                data: {
                    showDrawer: () => this.showDrawer(parentNodeId),
                },
            };

            // Create the edge connecting the source node to the parent node
            const edgeToParent = {
                id: `e${this.sourceNodeId}-${parentNodeId}`,
                source: this.sourceNodeId,
                target: parentNodeId,
            };

            let newNodes = [parentNode];
            let newEdges = [edgeToParent];

            // If the node is Call Agent, Call Favourite Agent, or Call Group, add AnsweredNode and UnansweredNode
            const nodesWithOutcomes = ['agent', 'favourite_agent', 'group'];
            if (nodesWithOutcomes.includes(nodeType)) {
                // Calculate positions for AnsweredNode and UnansweredNode
                const answeredNodePosition = {
                    x: parentNodePosition.x + 250,  // 250px to the right of the parent
                    y: parentNodePosition.y - 50,   // 100px above the parent
                };
                const unansweredNodePosition = {
                    x: parentNodePosition.x + 250,  // 250px to the right of the parent
                    y: parentNodePosition.y + 50,   // 100px below the parent (200px vertical space between Answered and Unanswered)
                };

                // Create AnsweredNode
                const answeredNodeId = uuidv4();
                const answeredNode = {
                    id: answeredNodeId,
                    type: 'answered',
                    position: answeredNodePosition,
                    data: {
                        showDrawer: () => this.showDrawer(answeredNodeId),
                    },
                };

                // Create UnansweredNode
                const unansweredNodeId = uuidv4();
                const unansweredNode = {
                    id: unansweredNodeId,
                    type: 'unanswered',
                    position: unansweredNodePosition,
                    data: {
                        showDrawer: () => this.showDrawer(unansweredNodeId),
                    },
                };

                // Create edges from parent to AnsweredNode and UnansweredNode
                const edgeToAnswered = {
                    id: `e${parentNodeId}-${answeredNodeId}`,
                    source: parentNodeId,
                    target: answeredNodeId,
                };
                const edgeToUnanswered = {
                    id: `e${parentNodeId}-${unansweredNodeId}`,
                    source: parentNodeId,
                    target: unansweredNodeId,
                };

                // Add the child nodes and edges
                newNodes = [...newNodes, answeredNode, unansweredNode];
                newEdges = [...newEdges, edgeToAnswered, edgeToUnanswered];
            }

            // Update the nodes and edges
            this.nodes = [...this.nodes, ...newNodes];
            this.edges = [...this.edges, ...newEdges];

            // Reset the sourceNodeId
            this.sourceNodeId = null;
        },
        },
    };

</script>

<style>
.flowchart-wrapper {
    width: 100%;
    height: calc(100vh - 135px);
}

.vue-flow__controls {
    gap: 4px;
    padding: 8px;
    display: flex;
    background: white;
    z-index: 50 !important;
    flex-direction: column;
    border: 1px solid #e2e8f0;
    box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
}

.vue-flow__controls-button {
    width: 28px;
    height: 28px;
    display: flex;
    cursor: pointer;
    align-items: center;
    margin: 0 !important;
    padding: 6px !important;
    justify-content: center;
    color: #4a5568 !important;
    border-radius: 4px !important;
    background: #f7fafc !important;
    border: 1px solid #e2e8f0 !important;
    transition: background 0.2s, color 0.2s !important;
}

.vue-flow__controls-button:hover {
    color: #2d3748 !important;
    background: #edf2f7 !important;
}
</style>
