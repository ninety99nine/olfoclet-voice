<template>
    <div class="select-none min-h-screen bg-gray-50 pt-8 p-4 sm:p-8 space-y-4 sm:space-y-6">
        <template v-if="showTable">
            <!-- Page Header -->
            <div class="flex justify-between">
                <div class="flex items-end space-x-2">
                    <LibraryBig size="48" stroke-width="1" class="text-gray-400" />
                    <div>
                        <h2 class="text-xl font-semibold text-gray-900 dark:text-white">Knowledge</h2>
                        <p class="text-sm text-gray-600 dark:text-neutral-400">
                            Manage knowledge bases for your organization
                        </p>
                    </div>
                </div>
                <div class="flex justify-end">
                    <Button type="primary" size="md" :leftIcon="Plus" leftIconSize="20" :action="showAddKnowledgeBaseModal">
                        <span>Add Knowledge Base</span>
                    </Button>
                </div>
            </div>

            <!-- Knowledge Bases Table -->
            <Table
                @search="search"
                resource="knowledge-bases"
                :columns="columns"
                :perPage="perPage"
                @paginate="paginate"
                @refresh="fetchKnowledgeBases"
                :searchTerm="searchTerm"
                :pagination="pagination"
                :isLoading="isLoadingKnowledgeBases"
                @updatedColumns="updatedColumns"
                @updatedFilters="updatedFilters"
                @updatedSorting="updatedSorting"
                @updatedPerPage="updatedPerPage"
                :filterExpressions="filterExpressions"
                :sortingExpressions="sortingExpressions">
                <!-- Select Action -->
                <template #belowToolbar>
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
                        <th scope="col" class="whitespace-nowrap align-top font-semibold px-4 py-2.5">
                            <Input type="checkbox" v-model="selectAll" />
                        </th>
                        <!-- Table Column Names -->
                        <template v-for="(column, index) in columns" :key="index">
                            <th v-if="column.active" scope="col" :class="['whitespace-nowrap align-top font-semibold pr-4 py-2.5', { 'text-center' : ['Content Items', 'Sources'].includes(column.name) }]">
                                {{ column.name }}
                            </th>
                        </template>
                        <!-- Actions -->
                        <th scope="col" class="whitespace-nowrap align-top font-semibold pr-4 py-2.5">Actions</th>
                    </tr>
                </template>

                <!-- Table Body -->
                <template #body>
                    <tr
                        :key="knowledgeBase.id"
                        @click.stop="onView(knowledgeBase)"
                        v-for="knowledgeBase in knowledgeBases"
                        :class="[checkedRows[knowledgeBase.id] ? 'bg-blue-50' : 'bg-white hover:bg-gray-50', 'group cursor-pointer text-sm border-b border-gray-200']">
                        <!-- Checkbox -->
                        <td @click.stop class="whitespace-nowrap align-top px-4 py-4">
                            <Input type="checkbox" v-model="checkedRows[knowledgeBase.id]" />
                        </td>
                        <template v-for="(column, columnIndex) in columns" :key="columnIndex">
                            <template v-if="column.active">
                                <!-- Name -->
                                <td v-if="column.name == 'Name'" class="whitespace-nowrap align-center pr-4 py-4">
                                    <div>{{ knowledgeBase.name }}</div>
                                </td>
                                <!-- Content Items -->
                                <td v-if="column.name == 'Content Items'" class="whitespace-nowrap align-center text-center pr-4 py-4">
                                    <span>{{ knowledgeBase.content_items_count }}</span>
                                </td>
                                <!-- Sources -->
                                <td v-if="column.name == 'Sources'" class="whitespace-nowrap align-center text-center pr-4 py-4">
                                    <span>{{ knowledgeBase.content_sources_count }}</span>
                                </td>
                                <!-- Created Date -->
                                <td v-if="column.name == 'Created Date'" class="whitespace-nowrap align-center pr-4 py-4">
                                    <div class="flex space-x-1 items-center">
                                        <span>{{ formattedDatetime(knowledgeBase.created_at) }}</span>
                                        <Popover placement="top" class="opacity-0 group-hover:opacity-100" :content="formattedRelativeDate(knowledgeBase.created_at)" />
                                    </div>
                                </td>
                            </template>
                        </template>
                        <td class="h-16 pr-4 py-4 flex align-middle items-center space-x-2">
                            <Button type="bare" size="xs" :leftIcon="Pencil" leftIconSize="16" :action="() => showUpdateKnowledgeBaseModal(knowledgeBase)" />
                            <Button type="bareDanger" size="xs" :leftIcon="Trash2" leftIconSize="16" :action="() => showDeleteKnowledgeBaseModal(knowledgeBase)" />
                        </td>
                    </tr>
                </template>
            </Table>
        </template>

        <!-- No Knowledge Bases -->
        <div v-else class="select-none w-full flex justify-center py-16">
            <div class="max-w-sm w-full bg-white shadow-sm rounded-xl p-6 flex flex-col items-center text-center space-y-4 border border-dashed border-gray-200">
                <LibraryBig size="48" class="text-gray-400" />
                <h2 class="text-2xl font-bold text-gray-800">No Knowledge Bases Yet</h2>
                <p class="text-sm text-gray-500">Add a knowledge base to manage content and sources.</p>
                <Button type="primary" size="md" :leftIcon="Plus" leftIconSize="20" :action="showAddKnowledgeBaseModal">
                    <span>Add Knowledge Base</span>
                </Button>
            </div>
        </div>

        <!-- Modals -->
        <AddKnowledgeBaseModal ref="addKnowledgeBaseModal" @created="fetchKnowledgeBases" />
        <UpdateKnowledgeBaseModal ref="updateKnowledgeBaseModal" @updated="fetchKnowledgeBases" />
        <DeleteKnowledgeBaseModal ref="deleteKnowledgeBaseModal" @deleted="onKnowledgeBaseDeleted" />
        <DeleteKnowledgeBasesModal ref="deleteKnowledgeBasesModal" @deleted="onKnowledgeBasesDeleted" />
    </div>
</template>

<script>
import axios from 'axios';
import isEqual from 'lodash/isEqual';
import Pill from '@Partials/Pill.vue';
import Modal from '@Partials/Modal.vue';
import Input from '@Partials/Input.vue';
import Button from '@Partials/Button.vue';
import Popover from '@Partials/Popover.vue';
import Dropdown from '@Partials/Dropdown.vue';
import Table from '@Partials/table/Table.vue';
import AddKnowledgeBaseModal from '@Pages/knowledge-bases/components/AddKnowledgeBaseModal.vue';
import UpdateKnowledgeBaseModal from '@Pages/knowledge-bases/components/UpdateKnowledgeBaseModal.vue';
import DeleteKnowledgeBaseModal from '@Pages/knowledge-bases/components/DeleteKnowledgeBaseModal.vue';
import DeleteKnowledgeBasesModal from '@Pages/knowledge-bases/components/DeleteKnowledgeBasesModal.vue';
import { formattedDatetime, formattedRelativeDate } from '@Utils/dateUtils.js';
import { Plus, Pencil, Trash2, LibraryBig } from 'lucide-vue-next';

export default {
    inject: ['formState', 'notificationState'],
    components: {
        Pill, Modal, Input, Button, Popover, Dropdown, Table,
        AddKnowledgeBaseModal, UpdateKnowledgeBaseModal, DeleteKnowledgeBaseModal, DeleteKnowledgeBasesModal, LibraryBig
    },
    data() {
        return {
            Plus,
            Pencil,
            Trash2,
            LibraryBig,
            knowledgeBases: [
                {
                    id: 1,
                    name: 'Billing Inquiries',
                    content_items_count: 45,
                    content_sources_count: 12,
                    created_at: '2025-04-15T08:30:00Z'
                },
                {
                    id: 2,
                    name: 'Network Troubleshooting',
                    content_items_count: 60,
                    content_sources_count: 18,
                    created_at: '2025-03-20T14:45:00Z'
                },
                {
                    id: 3,
                    name: 'All My Friends Bundles',
                    content_items_count: 25,
                    content_sources_count: 8,
                    created_at: '2025-05-01T10:15:00Z'
                },
                {
                    id: 4,
                    name: 'Prepaid Plan FAQs',
                    content_items_count: 35,
                    content_sources_count: 10,
                    created_at: '2025-02-10T09:00:00Z'
                },
                {
                    id: 5,
                    name: 'Customer Support Scripts',
                    content_items_count: 50,
                    content_sources_count: 15,
                    created_at: '2025-01-25T16:20:00Z'
                }
            ],
            perPage: '15',
            checkedRows: [],
            pagination: {
                meta: {
                    total: 5,
                    per_page: 15,
                    current_page: 1,
                    last_page: 1
                },
                data: []
            },
            searchTerm: null,
            selectAll: false,
            filterExpressions: [],
            sortingExpressions: [],
            isLoadingKnowledgeBases: false,
            columns: this.prepareColumns(),
            dropdownOptions: [
                {
                    icon: Trash2,
                    label: 'Delete',
                    action: this.showDeleteKnowledgeBasesModal,
                }
            ],
        };
    },
    watch: {
        selectAll(newValue) {
            this.checkedRows = this.knowledgeBases.reduce((acc, knowledgeBase) => {
                acc[knowledgeBase.id] = newValue;
                return acc;
            }, {});
        }
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
        showTable() {
            return this.isLoadingKnowledgeBases || (this.pagination ?? {}).meta?.total > 0 || this.hasSearchTerm || this.hasFilterExpressions || this.hasSortingExpressions;
        }
    },
    methods: {
        formattedDatetime: formattedDatetime,
        formattedRelativeDate: formattedRelativeDate,
        prepareColumns() {
            const columnNames = ['Name', 'Content Items', 'Sources', 'Created Date'];
            const defaultColumnNames = ['Name', 'Content Items', 'Sources', 'Created Date'];
            return columnNames.map(name => ({
                name,
                active: defaultColumnNames.includes(name),
                priority: defaultColumnNames.includes(name)
            }));
        },
        showAddKnowledgeBaseModal() {
            this.$refs.addKnowledgeBaseModal.showModal();
        },
        showUpdateKnowledgeBaseModal(knowledgeBase) {
            this.$refs.updateKnowledgeBaseModal.showModal(knowledgeBase);
        },
        showDeleteKnowledgeBaseModal(knowledgeBase) {
            this.$refs.deleteKnowledgeBaseModal.showModal(knowledgeBase);
        },
        showDeleteKnowledgeBasesModal() {
            const selectedIds = this.knowledgeBases.filter(kb => this.checkedRows[kb.id]).map(kb => kb.id);
            this.$refs.deleteKnowledgeBasesModal.showModal(selectedIds);
        },
        onKnowledgeBaseDeleted(id) {
            this.knowledgeBases = this.knowledgeBases.filter(kb => kb.id !== id);
            if (this.knowledgeBases.length === 0) this.fetchKnowledgeBases();
        },
        onKnowledgeBasesDeleted(deletedIds = []) {
            deletedIds.forEach(id => {
                if (this.checkedRows[id] !== undefined) {
                    this.checkedRows[id] = false;
                }
            });
            this.selectAll = false;
            this.knowledgeBases = this.knowledgeBases.filter(knowledgeBase => !deletedIds.includes(knowledgeBase.id));
            if (this.knowledgeBases.length === 0) {
                this.fetchKnowledgeBases();
            }
        },
        paginate(url) {
            this.fetchKnowledgeBases(url);
        },
        search(searchTerm) {
            this.searchTerm = searchTerm;
            this.fetchKnowledgeBases();
        },
        updatedColumns(columns) {
            this.columns = columns;
        },
        updatedFilters(filters) {
            const newFilterExpressions = filters.map((filter) => filter.expression);
            if (!isEqual(this.filterExpressions, newFilterExpressions)) {
                this.filterExpressions = newFilterExpressions;
                this.fetchKnowledgeBases();
            }
        },
        updatedSorting(sorting) {
            const newSortingExpressions = sorting.map((sort) => sort.expression);
            if (!isEqual(this.sortingExpressions, newSortingExpressions)) {
                this.sortingExpressions = newSortingExpressions;
                this.fetchKnowledgeBases();
            }
        },
        updatedPerPage(perPage) {
            this.perPage = perPage;
            this.fetchKnowledgeBases();
        },
        onView(knowledgeBase) {
            this.$router.push({ name: 'manage-knowledge-base', params: { knowledgeBaseId: knowledgeBase.id } });
        },
        async fetchKnowledgeBases(url = null) {
            // Mock implementation - in real app, this would fetch from API
            try {
                this.isLoadingKnowledgeBases = true;
                // Simulate API response with mock data
                this.pagination = {
                    meta: {
                        total: this.knowledgeBases.length,
                        per_page: parseInt(this.perPage),
                        current_page: 1,
                        last_page: 1
                    },
                    data: this.knowledgeBases
                };
                this.checkedRows = this.knowledgeBases.reduce((acc, knowledgeBase) => {
                    acc[knowledgeBase.id] = false;
                    return acc;
                }, {});
            } catch (error) {
                const message = error?.response?.data?.message || error?.message || 'Something went wrong while fetching knowledge bases';
                this.notificationState.showWarningNotification(message);
                console.error('Failed to fetch knowledge bases:', error);
            } finally {
                this.isLoadingKnowledgeBases = false;
            }
        }
    },
    created() {
        this.isLoadingKnowledgeBases = true;
        this.searchTerm = this.$route.query.searchTerm;
        if (this.$route.query.filterExpressions) this.filterExpressions = this.$route.query.filterExpressions.split('|');
        if (this.$route.query.sortingExpressions) this.sortingExpressions = this.$route.query.sortingExpressions.split('|');
        this.fetchKnowledgeBases();
    }
};
</script>
