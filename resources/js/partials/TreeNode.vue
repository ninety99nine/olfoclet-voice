<template>
    <!-- Folder Node -->
    <div
        :class="[
            'rounded-lg',
            selectedNode && selectedNode.id === node.id ? 'bg-gray-100 dark:bg-neutral-700' : '',
            'hs-accordion',
            expandedNodes[node.id] ? 'active' : ''
        ]"
        role="treeitem"
        :aria-expanded="expandedNodes[node.id] ? 'true' : 'false'"
        :id="'hs-customize-tree-heading-' + node.id">
        <!-- Folder Node Heading -->
        <div class="hs-accordion-heading py-0.5 flex items-center gap-x-0.5 w-full">
            <!-- Drag Handle -->
            <div v-if="draggable" class="size-6 flex justify-center items-center cursor-move">
                <GripVertical class="size-4 text-gray-500 dark:text-neutral-500" />
            </div>
            <div v-else class="size-6 flex justify-center items-center"></div>

            <button
                v-if="node.subfolders && node.subfolders.length > 0"
                class="hs-accordion-toggle size-6 flex justify-center items-center hover:bg-gray-100 rounded-md focus:outline-none focus:bg-gray-100 disabled:opacity-50 disabled:pointer-events-none dark:hover:bg-neutral-700 dark:focus:bg-neutral-700"
                :aria-expanded="expandedNodes[node.id] ? 'true' : 'false'"
                :aria-controls="'hs-customize-tree-collapse-' + node.id"
                @click="toggleNode(node.id)">
                <svg
                    :class="expandedNodes[node.id] ? 'rotate-90' : ''"
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
                v-else
                class="size-6 flex justify-center items-center">
                <!-- Spacer for nodes without children -->
            </div>
            <div
                class="grow hs-accordion-selectable px-1.5 rounded-md cursor-pointer"
                @click="selectNode(node)">
                <div class="flex items-center gap-x-2">
                    <Folder :class="node.hasContent ? 'shrink-0 size-4 text-blue-500 dark:text-blue-500' : 'shrink-0 size-4 text-yellow-500 dark:text-neutral-500'" />
                    <div class="w-20 grow truncate -mt-0.5">
                        <span class="text-xs text-gray-800 dark:text-neutral-200">{{ node.title }}</span>
                    </div>
                </div>
            </div>
        </div>
        <!-- End Folder Node Heading -->

        <!-- Folder Node Collapse -->
        <div
            v-if="node.subfolders && node.subfolders.length > 0"
            :id="'hs-customize-tree-collapse-' + node.id"
            :class="[
                'hs-accordion-content w-full overflow-hidden transition-[height] duration-300',
                expandedNodes[node.id] ? '' : 'hidden'
            ]"
            role="group"
            :aria-labelledby="'hs-customize-tree-heading-' + node.id">
            <div
                class="hs-accordion-group ps-4 relative before:absolute before:top-0 before:start-3 before:w-0.5 before:-ms-px before:h-full before:bg-gray-100 dark:before:bg-neutral-700"
                role="group"
                data-hs-accordion-always-open="">

                <!-- Draggable Subfolders -->
                <draggable
                    :disabled="!draggable"
                    :list="node.subfolders"
                    @remove="onRemove"
                    animation="200"
                    group="folders"
                    @add="onAdd">
                    <TreeNode
                        @on-child-add="(childId, parentId, _nodes) => onChildAdd(index, childId, parentId, _nodes)"
                        v-for="(node, index) in localNodes"
                        :expandedNodes="expandedNodes"
                        :selectedNode="selectedNode"
                        @node-selected="selectNode"
                        :node="localNodes[index]"
                        @toggle-node="toggleNode"
                        :draggable="draggable"
                        :key="node.id"
                    />
                </draggable>


            </div>

        </div>
        <!-- End Folder Node Collapse -->

    </div>
    <!-- End Folder Node -->

</template>

<script>
import { Folder, GripVertical } from 'lucide-vue-next';
import { VueDraggableNext } from 'vue-draggable-next';

export default {
    components: {
        Folder,
        GripVertical,
        draggable: VueDraggableNext
    },
    props: {
        node: {
            type: Object,
            required: true
        },
        selectedNode: {
            type: Object,
            default: null
        },
        expandedNodes: {
            type: Object,
            required: true
        },
        draggable: {
            type: Boolean,
            default: false
        }
    },
    emits: ['toggle-node', 'node-selected', 'on-child-add', 'updated-child-nodes'],
    data() {
        return {
            localNodes: JSON.parse(JSON.stringify(this.node.subfolders)) // Deep clone to avoid mutating prop
        }
    },
    methods: {
        toggleNode(nodeId) {
            this.$emit('toggle-node', nodeId);
        },
        selectNode(node) {
            if (node.subfolders && node.subfolders.length > 0) {
                this.$emit('toggle-node', node.id, true);
            }
            this.$emit('node-selected', node);
        },
        onAdd(event) {
            console.log('onAdd 2');
            console.log(this.localNodes);
            const parentId = this.node.id;
            const childId = this.localNodes[event.newIndex].id;
            const nodes = JSON.parse(JSON.stringify(this.localNodes)); // Deep clone to avoid mutating prop

            console.log('send child nodes 2');
            console.log(nodes);

            //this.$emit('on-child-add', childId, parentId, nodes);
        },
        onChildAdd(index, childId, parentId, nodes) {
            console.log('onChildAdd 2');
            let updatedNodes = JSON.parse(JSON.stringify(this.localNodes)); // Deep clone to avoid mutating prop;
            updatedNodes[index].subfolders = nodes;
            //this.$emit('on-child-add', childId, parentId, updatedNodes);
        },
        onRemove(evt) {
            console.log('onRemove 2');
            console.log(this.localNodes);
        }
    }
};
</script>
