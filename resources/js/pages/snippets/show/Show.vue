<template>
    <div class="select-none min-h-screen bg-gray-50 pt-8 p-4 sm:p-8 space-y-4 sm:space-y-6">
        <template v-if="showTable">
            <!-- Page Header -->
            <div class="flex justify-between">
                <div class="flex items-end space-x-2">
                    <SquareDashedBottom size="48" stroke-width="1" class="text-gray-400" />
                    <div>
                        <h2 class="text-xl font-semibold text-gray-900 dark:text-white">Snippets</h2>
                        <p class="text-sm text-gray-600 dark:text-neutral-400">
                            Manage snippets across knowledge bases
                        </p>
                    </div>
                </div>
                <div class="flex justify-end">
                    <Button type="primary" size="md" :leftIcon="Plus" leftIconSize="20" :action="showAddSnippetModal">
                        <span>Add Snippet</span>
                    </Button>
                </div>
            </div>

            <!-- Snippets Table -->
            <Table
                @search="search"
                resource="snippets"
                :columns="columns"
                :perPage="perPage"
                @paginate="paginate"
                @refresh="fetchSnippets"
                :searchTerm="searchTerm"
                :pagination="pagination"
                :isLoading="isLoadingSnippets"
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
                            <th v-if="column.active" scope="col" class="whitespace-nowrap align-top font-semibold pr-4 py-2.5">
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
                        :key="snippet.id"
                        v-for="snippet in snippets"
                        :class="[checkedRows[snippet.id] ? 'bg-blue-50' : 'bg-white hover:bg-gray-50', 'group cursor-pointer text-sm border-b border-gray-200']">
                        <!-- Checkbox -->
                        <td @click.stop class="whitespace-nowrap align-top px-4 py-4">
                            <Input type="checkbox" v-model="checkedRows[snippet.id]" />
                        </td>
                        <template v-for="(column, columnIndex) in columns" :key="columnIndex">
                            <template v-if="column.active">
                                <!-- Title -->
                                <td v-if="column.name == 'Title'" class="whitespace-nowrap align-center pr-4 py-4">
                                    <div>{{ snippet.title }}</div>
                                </td>
                                <!-- Organization -->
                                <td v-if="column.name == 'Organization'" class="whitespace-nowrap align-center pr-4 py-4">
                                    <div v-if="snippet.organization" class="flex items-center space-x-2">
                                        <img src="https://placehold.co/21x24" class="size-8 rounded-lg shrink-0" alt="Logo" />
                                        <div class="whitespace-nowrap">{{ snippet.organization.name }}</div>
                                    </div>
                                </td>
                                <!-- Knowledge Base -->
                                <td v-if="column.name == 'Knowledge Base'" class="whitespace-nowrap align-center pr-4 py-4">
                                    <div>{{ snippet.knowledge_base ? snippet.knowledge_base.name : 'N/A' }}</div>
                                </td>
                                <!-- AI Searchable -->
                                <td v-if="column.name == 'AI Searchable'" class="whitespace-nowrap align-center pr-4 py-4">
                                    <span>{{ snippet.ai_searchable ? 'Yes' : 'No' }}</span>
                                </td>
                                <!-- Created Date -->
                                <td v-if="column.name == 'Created Date'" class="whitespace-nowrap align-center pr-4 py-4">
                                    <div class="flex space-x-1 items-center">
                                        <span>{{ formattedDatetime(snippet.created_at) }}</span>
                                        <Popover placement="top" class="opacity-0 group-hover:opacity-100" :content="formattedRelativeDate(snippet.created_at)" />
                                    </div>
                                </td>
                            </template>
                        </template>
                        <td class="h-16 pr-4 py-4 flex align-middle items-center space-x-2">
                            <Button type="bare" size="xs" :leftIcon="Pencil" leftIconSize="16" :action="() => showUpdateSnippetModal(snippet)" />
                            <Button type="bareDanger" size="xs" :leftIcon="Trash2" leftIconSize="16" :action="() => showDeleteSnippetModal(snippet)" />
                        </td>
                    </tr>
                </template>
            </Table>
        </template>

        <!-- No Snippets -->
        <div v-else class="select-none w-full flex justify-center py-16">
            <div class="max-w-sm w-full bg-white shadow-sm rounded-xl p-6 flex flex-col items-center text-center space-y-4 border border-dashed border-gray-200">
                <SquareDashedBottom size="48" class="text-gray-400" />
                <h2 class="text-2xl font-bold text-gray-800">No Snippets Yet</h2>
                <p class="text-sm text-gray-500">Add your first snippet to start managing knowledge content.</p>
                <Button type="primary" size="md" :leftIcon="Plus" leftIconSize="20" :action="showAddSnippetModal">
                    <span>Add Snippet</span>
                </Button>
            </div>
        </div>

        <!-- Modals -->
        <AddSnippetModal ref="addSnippetModal" @created="fetchSnippets" />
        <UpdateSnippetModal ref="updateSnippetModal" @updated="fetchSnippets" />
        <DeleteSnippetModal ref="deleteSnippetModal" @deleted="onSnippetDeleted" />
        <DeleteSnippetsModal ref="deleteSnippetsModal" @deleted="onSnippetsDeleted" />
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
import AddSnippetModal from '@Pages/snippets/components/AddSnippetModal.vue';
import UpdateSnippetModal from '@Pages/snippets/components/UpdateSnippetModal.vue';
import DeleteSnippetModal from '@Pages/snippets/components/DeleteSnippetModal.vue';
import DeleteSnippetsModal from '@Pages/snippets/components/DeleteSnippetsModal.vue';
import { formattedDatetime, formattedRelativeDate } from '@Utils/dateUtils.js';
import { Plus, Pencil, Trash2, SquareDashedBottom } from 'lucide-vue-next';

export default {
    inject: ['formState', 'notificationState'],
    components: {
        Pill, Modal, Input, Button, Popover, Dropdown, Table,
        AddSnippetModal, UpdateSnippetModal, DeleteSnippetModal, DeleteSnippetsModal, SquareDashedBottom
    },
    data() {
        return {
            Plus,
            Pencil,
            Trash2,
            SquareDashedBottom,
            snippets: [],
            perPage: '15',
            checkedRows: [],
            pagination: null,
            searchTerm: null,
            selectAll: false,
            filterExpressions: [],
            sortingExpressions: [],
            isLoadingSnippets: false,
            columns: this.prepareColumns(),
            dropdownOptions: [
                {
                    icon: Trash2,
                    label: 'Delete',
                    action: this.showDeleteSnippetsModal,
                }
            ],
        };
    },
    watch: {
        selectAll(newValue) {
            this.checkedRows = this.snippets.reduce((acc, snippet) => {
                acc[snippet.id] = newValue;
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
            return this.isLoadingSnippets || (this.pagination ?? {}).meta?.total > 0 || this.hasSearchTerm || this.hasFilterExpressions || this.hasSortingExpressions;
        }
    },
    methods: {
        formattedDatetime: formattedDatetime,
        formattedRelativeDate: formattedRelativeDate,
        prepareColumns() {
            const columnNames = ['Title', 'Organization', 'Knowledge Base', 'AI Searchable', 'Created Date'];
            const defaultColumnNames = ['Title', 'Organization', 'Knowledge Base', 'AI Searchable', 'Created Date'];
            return columnNames.map(name => ({
                name,
                active: defaultColumnNames.includes(name),
                priority: defaultColumnNames.includes(name)
            }));
        },
        showAddSnippetModal() {
            this.$refs.addSnippetModal.showModal();
        },
        showUpdateSnippetModal(snippet) {
            this.$refs.updateSnippetModal.showModal(snippet);
        },
        showDeleteSnippetModal(snippet) {
            this.$refs.deleteSnippetModal.showModal(snippet);
        },
        showDeleteSnippetsModal() {
            const selectedIds = this.snippets.filter(s => this.checkedRows[s.id]).map(s => s.id);
            this.$refs.deleteSnippetsModal.showModal(selectedIds);
        },
        onSnippetDeleted(id) {
            this.snippets = this.snippets.filter(s => s.id !== id);
            if (this.snippets.length === 0) this.fetchSnippets();
        },
        onSnippetsDeleted(deletedIds = []) {
            deletedIds.forEach(id => {
                if (this.checkedRows[id] !== undefined) {
                    this.checkedRows[id] = false;
                }
            });
            this.selectAll = false;
            this.snippets = this.snippets.filter(snippet => !deletedIds.includes(snippet.id));
            if (this.snippets.length === 0) {
                this.fetchSnippets();
            }
        },
        paginate(url) {
            this.fetchSnippets(url);
        },
        search(searchTerm) {
            this.searchTerm = searchTerm;
            this.fetchSnippets();
        },
        updatedColumns(columns) {
            this.columns = columns;
        },
        updatedFilters(filters) {
            const newFilterExpressions = filters.map((filter) => filter.expression);
            if (!isEqual(this.filterExpressions, newFilterExpressions)) {
                this.filterExpressions = newFilterExpressions;
                this.fetchSnippets();
            }
        },
        updatedSorting(sorting) {
            const newSortingExpressions = sorting.map((sort) => sort.expression);
            if (!isEqual(this.sortingExpressions, newSortingExpressions)) {
                this.sortingExpressions = newSortingExpressions;
                this.fetchSnippets();
            }
        },
        updatedPerPage(perPage) {
            this.perPage = perPage;
            this.fetchSnippets();
        },
        async fetchSnippets(url = null) {
            try {
                this.isLoadingSnippets = true;
                url = url ?? '/api/snippets';
                let config = {
                    params: {
                        'per_page': this.perPage,
                        '_relationships': 'organization,knowledgeBase'
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
                this.snippets = this.pagination.data;
                this.checkedRows = this.snippets.reduce((acc, snippet) => {
                    acc[snippet.id] = false;
                    return acc;
                }, {});
            } catch (error) {
                const message = error?.response?.data?.message || error?.message || 'Something went wrong while fetching snippets';
                this.notificationState.showWarningNotification(message);
                console.error('Failed to fetch snippets:', error);
            } finally {
                this.isLoadingSnippets = false;
            }
        }
    },
    created() {
        this.isLoadingSnippets = true;
        this.searchTerm = this.$route.query.searchTerm;
        if (this.$route.query.filterExpressions) this.filterExpressions = this.$route.query.filterExpressions.split('|');
        if (this.$route.query.sortingExpressions) this.sortingExpressions = this.$route.query.sortingExpressions.split('|');
        this.fetchSnippets();
    }
};
</script>
