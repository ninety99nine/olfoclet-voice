<template>
    <div class="select-none space-y-4 sm:space-y-6">
        <div class="flex justify-between">
            <div class="flex items-end space-x-2">
                <Folder size="48" stroke-width="1" class="text-gray-400" />
                <div>
                    <h3 class="text-xl font-semibold text-gray-900 dark:text-white">Content</h3>
                    <p class="text-sm text-gray-600 dark:text-neutral-400">
                        Manage folders, articles, snippets, and webpages
                    </p>
                </div>
            </div>
            <div class="flex justify-end space-x-2">
                <!-- Toggle Drag-and-Drop -->
                <Button
                    :type="isDraggable ? 'primary' : 'light'"
                    size="md"
                    :leftIcon="isDraggable ? Lock : Unlock"
                    leftIconSize="20"
                    :action="toggleDraggable"
                >
                    <span>{{ isDraggable ? 'Lock Position' : 'Unlock Position' }}</span>
                </Button>
                <Button type="light" size="md" :leftIcon="FolderPlus" leftIconSize="20" :action="showAddFolderModal">
                    <span>New folder</span>
                </Button>
                <Button type="primary" size="md" :leftIcon="Plus" leftIconSize="20" :action="showAddContentModal">
                    <span>New content</span>
                </Button>
            </div>
        </div>

        <div class="flex space-x-4">
            Tree: {{ treeNodes.map((node) => node.title) }}
            <!-- Tree Navigation -->
            <div class="w-1/4 h-fit bg-white border border-gray-300 rounded-lg p-4">
                <div class="flex justify-between items-center mb-4">
                    <h3 class="text-lg font-medium text-gray-900">Folders</h3>
                    <Button
                        size="xs"
                        type="ghost"
                        :action="toggleTreeExpansion"
                        :leftIcon="isTreeExpanded ? SquareMinus : SquarePlus">
                        <span>{{ isTreeExpanded ? 'Collapse All' : 'Expand All' }}</span>
                    </Button>
                </div>
                <Tree
                    ref="tree"
                    :nodes="treeNodes"
                    v-if="treeNodes.length"
                    :draggable="isDraggable"
                    :selectedNode="selectedNode"
                    @node-selected="onNodeSelected"
                    @on-child-add="onUpdatePosition"
                    @update-expansion-state="updateTreeExpansionState"
                />
            </div>

            <!-- Content Table -->
            <div class="w-3/4">
                <Table
                    @search="search"
                    resource="content-items"
                    :columns="columns"
                    :perPage="perPage"
                    @paginate="paginate"
                    @refresh="fetchContentItems"
                    :searchTerm="searchTerm"
                    :pagination="pagination"
                    :isLoading="isLoadingContentItems"
                    @updatedColumns="updatedColumns"
                    @updatedFilters="updatedFilters"
                    @updatedSorting="updatedSorting"
                    @updatedPerPage="updatedPerPage"
                    :filterExpressions="filterExpressions"
                    :sortingExpressions="sortingExpressions">
                    <!-- Select Action -->
                    <template #belowToolbar>
                        <!-- Breadcrumbs -->
                        <Breadcrumbs :path="breadcrumbPath" :action="onBreadcrumbClick" class="border border-gray-300 rounded-lg py-2 px-4 mb-2" />

                        <div :class="[{ 'hidden': totalCheckedRows === 0 }, 'bg-gray-50 border flex items-center mb-2 p-4 rounded-lg shadow space-x-2']">
                            <span class="text-sm">Actions: </span>
                            <Dropdown
                                triggerSize="sm"
                                :options="dropdownOptions"
                                :triggerText="`Select Action (${totalCheckedRows} selected)`" />
                        </div>
                    </template>

                    <!-- Table Head -->
                    <template #head>
                        <tr class="border-b border-indigo-200">
                            <!-- Checkbox -->
                            <th scope="col" class="whitespace-nowrap align-center font-semibold px-4 py-2.5">
                                <Input
                                    type="checkbox"
                                    v-model="selectAll"
                                    alignItems="items-center">
                                </Input>
                            </th>

                            <!-- Table Column Names -->
                            <template v-for="(column, index) in columns" :key="index">
                                <th v-if="column.active" scope="col" :class="['whitespace-nowrap align-top font-semibold pr-4 py-2.5', { 'text-center' : ['Users'].includes(column.name) }]">
                                    {{ column.name }}
                                </th>
                            </template>

                            <!-- Actions -->
                            <th scope="col" class="whitespace-nowrap align-top font-semibold pr-4 py-2.5">Actions</th>
                        </tr>
                    </template>

                    <!-- Table Body -->
                    <template #body>
                        <tr v-for="contentItem in contentItems" :key="contentItem.id" :class="[checkedRows[contentItem.id] ? 'bg-blue-50' : 'bg-white hover:bg-gray-50', 'group cursor-pointer text-sm border-b border-gray-200']">
                            <!-- Checkbox -->
                            <td class="whitespace-nowrap align-center px-4 py-4">
                                <Input
                                    type="checkbox"
                                    alignItems="items-center"
                                    v-model="checkedRows[contentItem.id]" />
                            </td>

                            <template v-for="(column, columnIndex) in columns" :key="columnIndex">
                                <template v-if="column.active">
                                    <!-- Title -->
                                    <td v-if="column.name == 'Title'" class="whitespace-nowrap align-center pr-4 py-4">
                                        <span>{{ contentItem.title }}</span>
                                    </td>

                                    <!-- Type -->
                                    <td v-if="column.name == 'Type'" class="whitespace-nowrap align-center pr-4 py-4">
                                        <Pill :type="contentItem.visibility === 'public' ? 'primary' : 'light'" size="xs">
                                            <span>
                                                <span v-if="contentItem.type === 'folder'" class="flex items-center space-x-1">
                                                    <Folder size="16" />
                                                    <span>Folder</span>
                                                </span>
                                                <span v-else-if="contentItem.type === 'article'" class="flex items-center space-x-1">
                                                    <FileText size="16" />
                                                    <span>{{ contentItem.visibility === 'public' ? 'Public article' : 'Internal article' }}</span>
                                                </span>
                                                <span v-else-if="contentItem.type === 'snippet'" class="flex items-center space-x-1">
                                                    <StickyNote size="16" />
                                                    <span>Snippet</span>
                                                </span>
                                                <span v-else-if="contentItem.type === 'webpage'" class="flex items-center space-x-1">
                                                    <Earth size="16" />
                                                    <span>Webpage</span>
                                                </span>
                                            </span>
                                        </Pill>
                                    </td>

                                    <!-- Copilot -->
                                    <td v-if="column.name == 'Copilot'" class="whitespace-nowrap align-center pr-4 py-4">
                                        <div v-if="contentItem.type !== 'folder'">
                                            <Check v-if="contentItem.copilot_enabled" size="16" class="text-green-500" />
                                            <div v-else>—</div>
                                        </div>
                                        <div v-else>—</div>
                                    </td>

                                    <!-- AI Agent -->
                                    <td v-if="column.name == 'AI Agent'" class="whitespace-nowrap align-center pr-4 py-4">
                                        <div v-if="contentItem.type !== 'folder'">
                                            <Check v-if="contentItem.ai_agent_enabled" size="16" class="text-green-500" />
                                            <div v-else>—</div>
                                        </div>
                                        <div v-else>—</div>
                                    </td>

                                    <!-- Help Center -->
                                    <td v-if="column.name == 'Help Center'" class="whitespace-nowrap align-center pr-4 py-4">
                                        <div v-if="contentItem.type !== 'folder'">
                                            <Check v-if="contentItem.help_center_enabled" size="16" class="text-green-500" />
                                            <div v-else>—</div>
                                        </div>
                                        <div v-else>—</div>
                                    </td>

                                    <!-- Help Center Collection -->
                                    <td v-if="column.name == 'Help Center collection'" class="whitespace-nowrap align-center pr-4 py-4">
                                        <div v-if="contentItem.help_center_collection">{{ contentItem.help_center_collection.name }}</div>
                                        <div v-else>—</div>
                                    </td>
                                </template>
                            </template>

                            <td class="h-16 pr-4 py-4 flex align-middle items-center space-x-2">
                                <Button type="bare" size="xs" :leftIcon="Pencil" leftIconSize="16" :action="() => showUpdateContentModal(contentItem)" />
                                <Button type="bareDanger" size="xs" :leftIcon="Trash2" leftIconSize="16" :action="() => showDeleteContentModal(contentItem)" />
                            </td>
                        </tr>
                    </template>
                </Table>
            </div>
        </div>

        <!-- Modals -->
        <AddFolderModal
            ref="addFolderModal"
            :knowledgeBaseId="knowledgeBaseId"
            :parentId="selectedNode?.id === 'all' ? null : selectedNode?.id"
            :treeNodes="treeNodes"
            :selectedNode="selectedNode"
            @created="fetchTreeNodes" />
        <AddContentModal
            ref="addContentModal"
            :knowledgeBaseId="knowledgeBaseId"
            :folderId="selectedNode?.id === 'all' ? null : selectedNode?.id"
            :treeNodes="treeNodes"
            :selectedNode="selectedNode"
            @created="fetchContentItems" />
        <UpdateContentModal ref="updateContentModal" @updated="fetchContentItems" />
        <DeleteContentModal ref="deleteContentModal" @deleted="onContentDeleted" />
        <DeleteContentsModal ref="deleteContentsModal" @deleted="onContentsDeleted" />
    </div>
</template>

<script>
import axios from 'axios';
import isEqual from 'lodash/isEqual';
import Pill from '@Partials/Pill.vue';
import Tree from '@Partials/Tree.vue';
import Input from '@Partials/Input.vue';
import Button from '@Partials/Button.vue';
import Dropdown from '@Partials/Dropdown.vue';
import Table from '@Partials/table/Table.vue';
import Breadcrumbs from '@Partials/Breadcrumbs.vue';
import AddFolderModal from '@Pages/knowledge-bases/components/AddFolderModal.vue';
import AddContentModal from '@Pages/knowledge-bases/components/AddContentModal.vue';
import UpdateContentModal from '@Pages/knowledge-bases/components/UpdateContentModal.vue';
import DeleteContentModal from '@Pages/knowledge-bases/components/DeleteContentModal.vue';
import DeleteContentsModal from '@Pages/knowledge-bases/components/DeleteContentsModal.vue';
import { Plus, Folder, FolderPlus, FileText, StickyNote, Earth, Lock, Unlock, Check, X, Pencil, Trash2, SquarePlus, SquareMinus } from 'lucide-vue-next';

export default {
    inject: ['formState', 'notificationState'],
    components: {
        Input, Button, Dropdown, Table, Pill, Tree, Breadcrumbs,
        AddFolderModal, AddContentModal, UpdateContentModal, DeleteContentModal, DeleteContentsModal,
        Folder, FileText, StickyNote, Earth, Lock, Unlock, Check, X
    },
    props: {
        knowledgeBaseId: {
            type: String,
            required: true
        }
    },
    data() {
        return {
            Plus,
            Pencil,
            Trash2,
            FolderPlus,
            SquarePlus,
            SquareMinus,
            Lock,
            Unlock,
            contentItems: [],
            treeNodes: [],
            selectedNode: { id: 'all', title: 'All' },
            perPage: '15',
            checkedRows: [],
            pagination: null,
            searchTerm: null,
            selectAll: false,
            filterExpressions: [],
            sortingExpressions: [],
            isLoadingContentItems: false,
            isTreeExpanded: true,
            isDraggable: true,
            columns: this.prepareColumns(),
            dropdownOptions: [
                {
                    icon: Trash2,
                    label: 'Delete',
                    action: this.showDeleteContentsModal,
                }
            ],
        };
    },
    computed: {
        hasSearchTerm() {
            return this.searchTerm != null && this.searchTerm.trim() != '';
        },
        totalCheckedRows() {
            return Object.values(this.checkedRows).filter(checked => checked).length;
        },
        hasFilterExpressions() {
            return this.filterExpressions.length > 0;
        },
        hasSortingExpressions() {
            return this.sortingExpressions.length > 0;
        },
        breadcrumbPath() {
            if (!this.selectedNode || this.selectedNode.id === 'all') {
                return [{ id: 'all', title: 'All' }];
            }

            const path = [{ id: 'all', title: 'All' }];

            const findPathToNode = (nodes, targetId, currentPath = []) => {
                for (const node of nodes) {
                    const newPath = [...currentPath, { id: node.id, title: node.title }];
                    if (node.id === targetId) {
                        return newPath;
                    }
                    if (node.subfolders && node.subfolders.length > 0) {
                        const foundPath = findPathToNode(node.subfolders, targetId, newPath);
                        if (foundPath) {
                            return foundPath;
                        }
                    }
                }
                return null;
            };

            const foundPath = findPathToNode(this.treeNodes, this.selectedNode.id);
            if (foundPath) {
                return [...path, ...foundPath];
            }

            return path;
        }
    },
    watch: {
        selectedNode() {
            this.fetchContentItems();
        },
        selectAll(newValue) {
            this.checkedRows = this.contentItems.reduce((acc, contentItem) => {
                acc[contentItem.id] = newValue;
                return acc;
            }, {});
        }
    },
    methods: {
        prepareColumns() {
            const columnNames = ['Title', 'Type', 'Copilot', 'AI Agent', 'Help Center', 'Help Center collection'];
            const defaultColumnNames = ['Title', 'Type', 'Copilot', 'AI Agent', 'Help Center', 'Help Center collection'];
            return columnNames.map(name => ({
                name,
                active: defaultColumnNames.includes(name),
                priority: defaultColumnNames.includes(name)
            }));
        },
        showAddFolderModal() {
            this.$refs.addFolderModal.showModal();
        },
        showAddContentModal() {
            this.$refs.addContentModal.showModal();
        },
        showUpdateContentModal(contentItem) {
            this.$refs.updateContentModal.showModal(contentItem);
        },
        showDeleteContentModal(contentItem) {
            this.$refs.deleteContentModal.showModal(contentItem);
        },
        showDeleteContentsModal() {
            const selectedIds = this.contentItems.filter(c => this.checkedRows[c.id]).map(c => c.id);
            this.$refs.deleteContentsModal.showModal(selectedIds);
        },
        onNodeSelected(node) {
            if (typeof node === 'string') {
                const findNode = (nodes, targetId) => {
                    for (const node of nodes) {
                        if (node.id === targetId) {
                            return node;
                        }
                        if (node.subfolders && node.subfolders.length > 0) {
                            const found = findNode(node.subfolders, targetId);
                            if (found) return found;
                        }
                    }
                    return null;
                };
                const fullNode = findNode(this.treeNodes, node);
                this.selectedNode = fullNode || { id: 'all', title: 'All' };
            } else {
                this.selectedNode = node;
            }
        },
        onBreadcrumbClick(nodeId) {
            if (nodeId === 'all') {
                this.selectedNode = { id: 'all', title: 'All' };
            } else {
                const node = this.breadcrumbPath.find(n => n.id === nodeId);
                if (node) {
                    this.selectedNode = { id: node.id, title: node.title };
                } else {
                    this.selectedNode = { id: 'all', title: 'All' };
                }
            }

            if (this.$refs.tree && typeof this.$refs.tree.selectNode === 'function') {
                this.$refs.tree.selectNode(this.selectedNode);
            }
        },
        onContentDeleted(id) {
            this.contentItems = this.contentItems.filter(c => c.id !== id);
            this.fetchTreeNodes();
            if (this.contentItems.length === 0) this.fetchContentItems();
        },
        onContentsDeleted(deletedIds = []) {
            deletedIds.forEach(id => {
                if (this.checkedRows[id] !== undefined) {
                    this.checkedRows[id] = false;
                }
            });
            this.selectAll = false;
            this.contentItems = this.contentItems.filter(contentItem => !deletedIds.includes(contentItem.id));
            this.fetchTreeNodes();
            if (this.contentItems.length === 0) {
                this.fetchContentItems();
            }
        },
        paginate(url) {
            this.fetchContentItems(url);
        },
        search(searchTerm) {
            this.searchTerm = searchTerm;
            this.fetchContentItems();
        },
        updatedColumns(columns) {
            this.columns = columns;
        },
        updatedFilters(filters) {
            const newFilterExpressions = filters.map((filter) => filter.expression);
            if (!isEqual(this.filterExpressions, newFilterExpressions)) {
                this.filterExpressions = newFilterExpressions;
                this.fetchContentItems();
            }
        },
        updatedSorting(sorting) {
            const newSortingExpressions = sorting.map((sort) => sort.expression);
            if (!isEqual(this.sortingExpressions, newSortingExpressions)) {
                this.sortingExpressions = newSortingExpressions;
                this.fetchContentItems();
            }
        },
        updatedPerPage(perPage) {
            this.perPage = perPage;
            this.fetchContentItems();
        },
        async fetchTreeNodes() {
            try {
                const response = await axios.get('/api/content-items', {
                    params: {
                        knowledge_base_id: this.knowledgeBaseId,
                        type: 'folder'
                    }
                });
                this.treeNodes = response.data;
            } catch (error) {
                const message = error?.response?.data?.message || error?.message || 'Something went wrong while fetching content tree';
                this.notificationState.showWarningNotification(message);
                console.error('Failed to fetch content tree:', error);
            }
        },
        async fetchContentItems(url = null) {
            try {
                this.isLoadingContentItems = true;
                url = url ?? '/api/content-items';
                let config = {
                    params: {
                        'per_page': this.perPage,
                        '_relationships': 'helpCenterCollection',
                        'knowledge_base_id': this.knowledgeBaseId,
                        'parent_id': this.selectedNode && this.selectedNode.id !== 'all' ? this.selectedNode.id : null
                    }
                };
                if (this.hasSearchTerm) config.params['search'] = this.searchTerm;
                if (this.hasFilterExpressions) {
                    config.params['_filters'] = this.filterExpressions.join('|');
                }
                if (this.hasSortingExpressions) {
                    config.params['_sort'] = this.sortingExpressions.join('|');
                }
                const response = await axios.get(url, config);
                this.pagination = response.data;
                this.contentItems = response.data.data || [];
                this.checkedRows = this.contentItems.reduce((acc, contentItem) => {
                    acc[contentItem.id] = false;
                    return acc;
                }, {});
            } catch (error) {
                const message = error?.response?.data?.message || error?.message || 'Something went wrong while fetching content items';
                this.notificationState.showWarningNotification(message);
                console.error('Failed to fetch content items:', error);
            } finally {
                this.isLoadingContentItems = false;
            }
        },
        toggleTreeExpansion() {
            if (this.isTreeExpanded) {
                this.$refs.tree.collapseAll();
                this.isTreeExpanded = false;
            } else {
                this.$refs.tree.expandAll();
                this.isTreeExpanded = true;
            }
        },
        updateTreeExpansionState(isExpanded) {
            this.isTreeExpanded = isExpanded;
        },
        toggleDraggable() {
            this.isDraggable = !this.isDraggable;
        },
        async onUpdatePosition(childId, parentId, updatedTreeNodes) {

            console.log(this.treeNodes);
            console.log(updatedTreeNodes);

            this.treeNodes = updatedTreeNodes;
            console.log('onUpdatePosition');
            console.log(`childId: ${childId}`);
            console.log(`parentId: ${parentId}`);
            console.log(this.treeNodes);
            return;

            try {
                // Save the change to the backend using the existing update endpoint
                await axios.put(`/api/content-items/${childId}`, {
                    parent_id: parentId
                });

                this.notificationState.showSuccessNotification('Folder moved successfully');
            } catch (error) {
                const message = error?.response?.data?.message || error?.message || 'Failed to move folder';
                this.notificationState.showWarningNotification(message);
                console.error('Failed to move folder:', error);

                // Revert the tree on failure
                this.fetchTreeNodes();
            }
        }
    },
    created() {
        this.fetchTreeNodes();
        this.fetchContentItems();
    }
};
</script>
