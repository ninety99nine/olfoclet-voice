<template>
    <!-- Tree Root -->
    <div class="hs-accordion-treeview-root" role="tree" aria-orientation="vertical">
        <!-- Accordion Group -->
        <div class="hs-accordion-group" role="group" data-hs-accordion-always-open="">
            <!-- Root Node: "All" -->
            <div
                :class="[
                    'rounded-lg',
                    selectedNode && selectedNode.id === 'all' ? 'bg-gray-100 dark:bg-neutral-700' : '',
                    'hs-accordion',
                    expandedNodes['all'] ? 'active' : ''
                ]"
                role="treeitem"
                :aria-expanded="expandedNodes['all'] ? 'true' : 'false'"
                id="hs-customize-tree-heading-all">
                <!-- Root Node Heading -->
                <div class="hs-accordion-heading py-0.5 flex items-center gap-x-0.5 w-full">
                    <button
                        class="hs-accordion-toggle size-6 flex justify-center items-center hover:bg-gray-100 rounded-md focus:outline-none focus:bg-gray-100 disabled:opacity-50 disabled:pointer-events-none dark:hover:bg-neutral-700 dark:focus:bg-neutral-700"
                        :aria-expanded="expandedNodes['all'] ? 'true' : 'false'"
                        aria-controls="hs-customize-tree-collapse-all"
                        @click="toggleNode('all')">
                        <svg
                            :class="expandedNodes['all'] ? 'rotate-90' : ''"
                            class="transition duration-300 size-2.5 text-gray-600 dark:text-neutral-400"
                            xmlns="http://www.w3.org/2000/svg"
                            width="16"
                            height="16"
                            fill="currentColor"
                            viewBox="0 0 16 16">
                            <path d="m12.14 8.753-5.482 4.796c-.646.566-1.658.106-1.658-.753V3.204a1 1 0 0 1 1.659-.753l5.48 4.796a1 1 0 0 1 0 1.506z"></path>
                        </svg>
                    </button>
                    <div
                        class="grow hs-accordion-selectable px-1.5 rounded-md cursor-pointer"
                        @click="selectNode({ id: 'all', title: 'All' })">
                        <div class="flex items-center gap-x-2">
                            <Folder :class="hasContentInTree ? 'shrink-0 size-4 text-blue-500 dark:text-blue-500' : 'shrink-0 size-4 text-gray-500 dark:text-neutral-500'" />
                            <div class="grow -mt-0.5">
                                <span class="text-xs text-gray-800 dark:text-neutral-200">All</span>
                            </div>
                        </div>
                    </div>
                </div>
                <!-- End Root Node Heading -->

                <!-- Root Node Collapse -->
                <div
                    id="hs-customize-tree-collapse-all"
                    :class="[
                        'hs-accordion-content w-full overflow-hidden transition-[height] duration-300',
                        expandedNodes['all'] ? '' : 'hidden'
                    ]"
                    role="group"
                    aria-labelledby="hs-customize-tree-heading-all">

                    <div
                        class="hs-accordion-group ps-4 relative before:absolute before:top-0 before:start-3 before:w-0.5 before:-ms-px before:h-full before:bg-gray-100 dark:before:bg-neutral-700"
                        role="group"
                        data-hs-accordion-always-open="">

                        <!-- Draggable Folder Nodes -->
                        <draggable
                            :disabled="!draggable"
                            :list="nodes"
                            @remove="onRemove"
                            animation="200"
                            group="folders"
                            @add="onAdd"
                            :key="key">
                            <TreeNode
                                @on-child-add="(childId, parentId, _nodes) => onChildAdd(index, childId, parentId, _nodes)"
                                v-for="(node, index) in localNodes"
                                :expandedNodes="expandedNodes"
                                :selectedNode="selectedNode"
                                @node-selected="selectNode"
                                @toggle-node="toggleNode"
                                :node="localNodes[index]"
                                :draggable="draggable"
                                :key="node.id"
                            />
                        </draggable>

                    </div>

                </div>
                <!-- End Root Node Collapse -->

            </div>
            <!-- End Root Node: "All" -->

        </div>
        <!-- End Accordion Group -->

    </div>
    <!-- End Tree Root -->


</template>

<script>
import { Folder } from 'lucide-vue-next';
import TreeNode from './TreeNode.vue';
import { VueDraggableNext } from 'vue-draggable-next';

export default {
    components: {
        Folder,
        TreeNode,
        draggable: VueDraggableNext
    },
    props: {
        nodes: {
            type: Array,
            default: () => []
        },
        selectedNode: {
            type: Object,
            default: null
        },
        draggable: {
            type: Boolean,
            default: false
        }
    },
    emits: ['update-expansion-state', 'node-selected', 'on-child-add'],
    data() {
        return {
            key: 0,
            localNodes: JSON.parse(JSON.stringify(this.nodes)),
            expandedNodes: { 'all': true }, // "All" node is expanded by default
        };
    },
    watch: {
        expandedNodes: {
            handler() {
                if (this.areAllNodesExpanded) {
                    this.$emit('update-expansion-state', true);
                } else if (this.areAllNodesCollapsed) {
                    this.$emit('update-expansion-state', false);
                }
            },
            deep: true
        }
    },
    computed: {
        areAllNodesExpanded() {
            if (!this.expandedNodes['all']) return false;
            return this.checkNodesExpanded(this.localNodes);
        },
        areAllNodesCollapsed() {
            if (this.expandedNodes['all']) return false;
            return this.checkNodesCollapsed(this.localNodes);
        },
        hasContentInTree() {
            // Check if any node in the tree has content
            const checkContent = (nodes) => {
                if (!Array.isArray(nodes)) return false; // Ensure nodes is an array
                return nodes.some(node => {
                    if (node.hasContent) return true;
                    return checkContent(node.subfolders || []);
                });
            };
            return checkContent(this.localNodes);
        }
    },
    methods: {
        toggleNode(nodeId, setExpanded = null) {
            if (setExpanded !== null) {
                this.expandedNodes[nodeId] = setExpanded;
            } else {
                this.expandedNodes[nodeId] = !this.expandedNodes[nodeId];
            }
        },
        selectNode(node) {
            this.expandedNodes[node.id] = true;
            this.$emit('node-selected', node);
        },
        expandAll() {
            this.expandedNodes['all'] = true;
            this.expandNodes(this.localNodes);
        },
        collapseAll() {
            this.expandedNodes['all'] = false;
            this.collapseNodes(this.localNodes);
        },
        expandNodes(nodes) {
            nodes.forEach(node => {
                this.expandedNodes[node.id] = true;
                if (node.subfolders && node.subfolders.length > 0) {
                    this.expandNodes(node.subfolders);
                }
            });
        },
        collapseNodes(nodes) {
            nodes.forEach(node => {
                this.expandedNodes[node.id] = false;
                if (node.subfolders && node.subfolders.length > 0) {
                    this.collapseNodes(node.subfolders);
                }
            });
        },
        checkNodesExpanded(nodes) {
            return nodes.every(node => {
                const isExpanded = this.expandedNodes[node.id] || false;
                if (node.subfolders && node.subfolders.length > 0) {
                    return isExpanded && this.checkNodesExpanded(node.subfolders);
                }
                return true;
            });
        },
        checkNodesCollapsed(nodes) {
            return nodes.every(node => {
                const isCollapsed = !this.expandedNodes[node.id];
                if (node.subfolders && node.subfolders.length > 0) {
                    return isCollapsed && this.checkNodesCollapsed(node.subfolders);
                }
                return true;
            });
        },
        onAdd(event) {
            console.log('onAdd 1');
            console.log(this.localNodes);
            const parentId = null;
            const childId = this.localNodes[event.newIndex].id;
            const nodes = JSON.parse(JSON.stringify(this.localNodes)); // Deep clone to avoid mutating prop

            console.log('send child nodes 1');
            console.log(nodes);

            //this.$emit('on-child-add', childId, parentId, nodes);
        },
        onChildAdd(index, childId, parentId, nodes) {
            console.log('onChildAdd 1');
            let updatedNodes = JSON.parse(JSON.stringify(this.localNodes)); // Deep clone to avoid mutating prop;
            updatedNodes[index].subfolders = nodes;
            //this.$emit('on-child-add', childId, parentId, updatedNodes);
        },
        onRemove() {
            console.log('onRemove 1');
            console.log(this.localNodes);
        }
    },
    mounted() {
        this.$emit('update-expansion-state', this.areAllNodesExpanded);
    }
};
</script>
