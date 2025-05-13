<template>
    <div class="flowchart-wrapper">
        <!-- Header -->
        <div class="flex justify-between items-center p-4 bg-gray-100 border-b border-gray-200">
            <div class="flex items-center space-x-2">
                <ScriptIcon size="24" class="text-gray-600" />
                <h2 class="text-lg font-semibold text-gray-900 dark:text-white">
                    {{ isEditing ? 'Edit Script Flow' : 'Create Script Flow' }}
                </h2>
            </div>
            <div class="flex space-x-2">
                <Button type="light" size="md" :action="goBack">
                    <span>Cancel</span>
                </Button>
                <Button type="primary" size="md" :leftIcon="Save" leftIconSize="20" :action="saveScriptFlow" :loading="isSaving">
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
            @edges-change="onEdgesChange"
        >
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
import { FileText as ScriptIcon, Save } from 'lucide-vue-next';
import PageNode from '@Pages/script-flows/edit/components/nodes/PageNode.vue';
import BranchNode from '@Pages/script-flows/edit/components/nodes/BranchNode.vue';
import RecordingNode from '@Pages/script-flows/edit/components/nodes/RecordingNode.vue';
import HttpRequestNode from '@Pages/script-flows/edit/components/nodes/HttpRequestNode.vue';
import StartScriptNode from '@Pages/script-flows/edit/components/nodes/StartScriptNode.vue';
import EndScriptNode from '@Pages/script-flows/edit/components/nodes/EndScriptNode.vue';
import NodeManagerDrawer from '@Pages/script-flows/edit/components/node-manage-drawer/NodeManagerDrawer.vue';

export default {
    components: {
        Button,
        VueFlow,
        Background,
        MiniMap,
        Controls,
        NodeManagerDrawer,
        ScriptIcon,
    },
    data() {
        return {
            Save,
            nodes: [],
            edges: [],
            scriptFlow: null,
            isSaving: false,
            isEditing: false,
            sourceNodeId: null,
            nodeTypes: {
                page: markRaw(PageNode),
                branch: markRaw(BranchNode),
                recording: markRaw(RecordingNode),
                http_request: markRaw(HttpRequestNode),
                start_script: markRaw(StartScriptNode),
                end_script: markRaw(EndScriptNode),
            },
        };
    },
    computed: {
        scriptFlowId() {
            return this.$route.params.script_flow_id;
        },
    },
    created() {
        this.isEditing = !!this.scriptFlowId;
        if (this.isEditing) {
            this.loadScriptFlow();
        } else {
            this.initializeNewScriptFlow();
        }
    },
    methods: {
        goBack() {
            this.$router.push({ name: 'show-script-flows' });
        },
        showDrawer(sourceNodeId) {
            this.sourceNodeId = sourceNodeId;
            this.$refs.drawer.showDrawer();
        },
        hideDrawer() {
            this.$refs.drawer.hideDrawer();
        },
        initializeNewScriptFlow() {
            const id = uuidv4();
            const defaultNode = {
                id: id,
                type: 'start_script',
                position: { x: 100, y: 250 },
                data: {
                    showDrawer: () => this.showDrawer(id),
                },
            };
            this.nodes = [defaultNode];
            this.edges = [];
            this.scriptFlow = {
                name: 'New Script Flow',
                is_active: true,
            };
        },
        async loadScriptFlow() {
            try {
                const response = await axios.get(`/api/script-flows/${this.scriptFlowId}`, {
                    params: {
                        '_relationships': 'nodes,organization',
                    },
                });
                this.scriptFlow = response.data.data;

                // Load nodes
                this.nodes = this.scriptFlow.nodes.map(node => ({
                    id: node.id,
                    type: node.type,
                    position: node.position,
                    data: {
                        ...node.metadata,
                        showDrawer: () => this.showDrawer(node.id),
                    },
                }));

                // Load edges
                this.edges = this.scriptFlow.nodes
                    .filter(node => node.next_step)
                    .map(node => ({
                        id: `e${node.id}-${node.next_step}`,
                        source: node.id,
                        target: node.next_step,
                    }));
            } catch (error) {
                console.error('Failed to load script flow:', error);
                this.$notificationState.showWarningNotification('Failed to load script flow.');
                this.$router.push({ name: 'show-script-flows' });
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
            this.edges = changes.reduce((acc, change) => {
                if (change.type === 'add') {
                    acc.push(change.item);
                } else if (change.type === 'remove') {
                    return acc.filter(edge => edge.id !== change.id);
                }
                return acc;
            }, [...this.edges]);
        },
        async saveScriptFlow() {
            this.isSaving = true;
            try {
                const payload = {
                    name: this.scriptFlow.name,
                    is_active: this.scriptFlow.is_active,
                };

                let response;
                if (this.isEditing) {
                    // Update existing script flow
                    response = await axios.put(`/api/script-flows/${this.scriptFlowId}`, payload);
                } else {
                    // Create new script flow
                    response = await axios.post('/api/script-flows', payload);
                    this.scriptFlowId = response.data.data.id;
                }

                // Save nodes and edges
                await this.saveNodesAndEdges();

                this.$notificationState.showSuccessNotification(
                    this.isEditing ? 'Script flow updated successfully!' : 'Script flow created successfully!'
                );
                this.$router.push({ name: 'show-script-flows' });
            } catch (error) {
                console.error('Failed to save script flow:', error);
                this.$notificationState.showWarningNotification('Failed to save script flow.');
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
                script_flow_id: this.scriptFlowId,
                next_step: this.getNextStep(node.id),
            }));

            // Save each node
            for (const node of nodesPayload) {
                try {
                    if (this.isEditing && this.scriptFlow.nodes.some(n => n.id === node.id)) {
                        // Update existing node
                        await axios.put(`/api/script-flow-nodes/${node.id}`, {
                            type: node.type,
                            next_step: node.next_step,
                            metadata: node.metadata,
                            position: node.position,
                        });
                    } else {
                        // Create new node
                        await axios.post('/api/script-flow-nodes', {
                            script_flow_id: this.scriptFlowId,
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
                const nodesToDelete = this.scriptFlow.nodes
                    .filter(node => !currentNodeIds.includes(node.id))
                    .map(node => node.id);
                if (nodesToDelete.length > 0) {
                    await axios.delete('/api/script-flow-nodes', {
                        params: {
                            script_flow_node_ids: nodesToDelete,
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

            // Calculate the new node's position (to the right of the source node)
            const newNodePosition = {
                x: sourceNode.position.x + 250, // 250px to the right
                y: sourceNode.position.y,
            };

            // Create the new node
            const newNodeId = uuidv4();
            const newNode = {
                id: newNodeId,
                type: nodeType,
                position: newNodePosition,
                data: {
                    showDrawer: () => this.showDrawer(newNodeId),
                },
            };

            // Create the edge connecting the source node to the new node
            const newEdge = {
                id: `e${this.sourceNodeId}-${newNodeId}`,
                source: this.sourceNodeId,
                target: newNodeId,
            };

            // Update the nodes and edges
            this.nodes = [...this.nodes, newNode];
            this.edges = [...this.edges, newEdge];

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
