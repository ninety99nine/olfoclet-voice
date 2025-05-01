<template>
    <div class="select-none min-h-screen bg-gray-50 pt-8 p-4 sm:p-8 space-y-4 sm:space-y-6">
        <template v-if="showTable">
            <!-- Page Header -->
            <div class="flex justify-between">
                <div class="flex items-end space-x-2">
                    <FileText size="48" stroke-width="1" class="text-gray-400" />
                    <div>
                        <h2 class="text-xl font-semibold text-gray-900 dark:text-white">Articles</h2>
                        <p class="text-sm text-gray-600 dark:text-neutral-400">
                            Manage articles across knowledge bases
                        </p>
                    </div>
                </div>
                <div class="flex justify-end">
                    <Button type="primary" size="md" :leftIcon="Plus" leftIconSize="20" :action="showAddArticleModal">
                        <span>Add Article</span>
                    </Button>
                </div>
            </div>

            <!-- Articles Table -->
            <Table
                @search="search"
                resource="articles"
                :columns="columns"
                :perPage="perPage"
                @paginate="paginate"
                @refresh="fetchArticles"
                :searchTerm="searchTerm"
                :pagination="pagination"
                :isLoading="isLoadingArticles"
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
                        :key="article.id"
                        v-for="article in articles"
                        :class="[checkedRows[article.id] ? 'bg-blue-50' : 'bg-white hover:bg-gray-50', 'group cursor-pointer text-sm border-b border-gray-200']">
                        <!-- Checkbox -->
                        <td @click.stop class="whitespace-nowrap align-top px-4 py-4">
                            <Input type="checkbox" v-model="checkedRows[article.id]" />
                        </td>
                        <template v-for="(column, columnIndex) in columns" :key="columnIndex">
                            <template v-if="column.active">
                                <!-- Title -->
                                <td v-if="column.name == 'Title'" class="whitespace-nowrap align-center pr-4 py-4">
                                    <div>{{ article.title }}</div>
                                </td>
                                <!-- Organization -->
                                <td v-if="column.name == 'Organization'" class="whitespace-nowrap align-center pr-4 py-4">
                                    <div v-if="article.organization" class="flex items-center space-x-2">
                                        <img src="https://placehold.co/21x24" class="size-8 rounded-lg shrink-0" alt="Logo" />
                                        <div class="whitespace-nowrap">{{ article.organization.name }}</div>
                                    </div>
                                </td>
                                <!-- Knowledge Base -->
                                <td v-if="column.name == 'Knowledge Base'" class="whitespace-nowrap align-center pr-4 py-4">
                                    <div>{{ article.knowledge_base ? article.knowledge_base.name : 'N/A' }}</div>
                                </td>
                                <!-- AI Searchable -->
                                <td v-if="column.name == 'AI Searchable'" class="whitespace-nowrap align-center pr-4 py-4">
                                    <span>{{ article.ai_searchable ? 'Yes' : 'No' }}</span>
                                </td>
                                <!-- Created Date -->
                                <td v-if="column.name == 'Created Date'" class="whitespace-nowrap align-center pr-4 py-4">
                                    <div class="flex space-x-1 items-center">
                                        <span>{{ formattedDatetime(article.created_at) }}</span>
                                        <Popover placement="top" class="opacity-0 group-hover:opacity-100" :content="formattedRelativeDate(article.created_at)" />
                                    </div>
                                </td>
                            </template>
                        </template>
                        <td class="h-16 pr-4 py-4 flex align-middle items-center space-x-2">
                            <Button type="bare" size="xs" :leftIcon="Pencil" leftIconSize="16" :action="() => showUpdateArticleModal(article)" />
                            <Button type="bareDanger" size="xs" :leftIcon="Trash2" leftIconSize="16" :action="() => showDeleteArticleModal(article)" />
                        </td>
                    </tr>
                </template>
            </Table>
        </template>

        <!-- No Articles -->
        <div v-else class="select-none w-full flex justify-center py-16">
            <div class="max-w-sm w-full bg-white shadow-sm rounded-xl p-6 flex flex-col items-center text-center space-y-4 border border-dashed border-gray-200">
                <FileText size="48" class="text-gray-400" />
                <h2 class="text-2xl font-bold text-gray-800">No Articles Yet</h2>
                <p class="text-sm text-gray-500">Add your first article to start managing knowledge content.</p>
                <Button type="primary" size="md" :leftIcon="Plus" leftIconSize="20" :action="showAddArticleModal">
                    <span>Add Article</span>
                </Button>
            </div>
        </div>

        <!-- Modals -->
        <AddArticleModal ref="addArticleModal" @created="fetchArticles" />
        <UpdateArticleModal ref="updateArticleModal" @updated="fetchArticles" />
        <DeleteArticleModal ref="deleteArticleModal" @deleted="onArticleDeleted" />
        <DeleteArticlesModal ref="deleteArticlesModal" @deleted="onArticlesDeleted" />
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
import AddArticleModal from '@Pages/articles/components/AddArticleModal.vue';
import UpdateArticleModal from '@Pages/articles/components/UpdateArticleModal.vue';
import DeleteArticleModal from '@Pages/articles/components/DeleteArticleModal.vue';
import DeleteArticlesModal from '@Pages/articles/components/DeleteArticlesModal.vue';
import { formattedDatetime, formattedRelativeDate } from '@Utils/dateUtils.js';
import { Plus, Pencil, Trash2, FileText } from 'lucide-vue-next';

export default {
    inject: ['formState', 'notificationState'],
    components: {
        Pill, Modal, Input, Button, Popover, Dropdown, Table,
        AddArticleModal, UpdateArticleModal, DeleteArticleModal, DeleteArticlesModal, FileText
    },
    data() {
        return {
            Plus,
            Pencil,
            Trash2,
            FileText,
            articles: [],
            perPage: '15',
            checkedRows: [],
            pagination: null,
            searchTerm: null,
            selectAll: false,
            filterExpressions: [],
            sortingExpressions: [],
            isLoadingArticles: false,
            columns: this.prepareColumns(),
            dropdownOptions: [
                {
                    icon: Trash2,
                    label: 'Delete',
                    action: this.showDeleteArticlesModal,
                }
            ],
        };
    },
    watch: {
        selectAll(newValue) {
            this.checkedRows = this.articles.reduce((acc, article) => {
                acc[article.id] = newValue;
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
            return this.isLoadingArticles || (this.pagination ?? {}).meta?.total > 0 || this.hasSearchTerm || this.hasFilterExpressions || this.hasSortingExpressions;
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
        showAddArticleModal() {
            this.$refs.addArticleModal.showModal();
        },
        showUpdateArticleModal(article) {
            this.$refs.updateArticleModal.showModal(article);
        },
        showDeleteArticleModal(article) {
            this.$refs.deleteArticleModal.showModal(article);
        },
        showDeleteArticlesModal() {
            const selectedIds = this.articles.filter(a => this.checkedRows[a.id]).map(a => a.id);
            this.$refs.deleteArticlesModal.showModal(selectedIds);
        },
        onArticleDeleted(id) {
            this.articles = this.articles.filter(a => a.id !== id);
            if (this.articles.length === 0) this.fetchArticles();
        },
        onArticlesDeleted(deletedIds = []) {
            deletedIds.forEach(id => {
                if (this.checkedRows[id] !== undefined) {
                    this.checkedRows[id] = false;
                }
            });
            this.selectAll = false;
            this.articles = this.articles.filter(article => !deletedIds.includes(article.id));
            if (this.articles.length === 0) {
                this.fetchArticles();
            }
        },
        paginate(url) {
            this.fetchArticles(url);
        },
        search(searchTerm) {
            this.searchTerm = searchTerm;
            this.fetchArticles();
        },
        updatedColumns(columns) {
            this.columns = columns;
        },
        updatedFilters(filters) {
            const newFilterExpressions = filters.map((filter) => filter.expression);
            if (!isEqual(this.filterExpressions, newFilterExpressions)) {
                this.filterExpressions = newFilterExpressions;
                this.fetchArticles();
            }
        },
        updatedSorting(sorting) {
            const newSortingExpressions = sorting.map((sort) => sort.expression);
            if (!isEqual(this.sortingExpressions, newSortingExpressions)) {
                this.sortingExpressions = newSortingExpressions;
                this.fetchArticles();
            }
        },
        updatedPerPage(perPage) {
            this.perPage = perPage;
            this.fetchArticles();
        },
        async fetchArticles(url = null) {
            try {
                this.isLoadingArticles = true;
                url = url ?? '/api/articles';
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
                this.articles = this.pagination.data;
                this.checkedRows = this.articles.reduce((acc, article) => {
                    acc[article.id] = false;
                    return acc;
                }, {});
            } catch (error) {
                const message = error?.response?.data?.message || error?.message || 'Something went wrong while fetching articles';
                this.notificationState.showWarningNotification(message);
                console.error('Failed to fetch articles:', error);
            } finally {
                this.isLoadingArticles = false;
            }
        }
    },
    created() {
        this.isLoadingArticles = true;
        this.searchTerm = this.$route.query.searchTerm;
        if (this.$route.query.filterExpressions) this.filterExpressions = this.$route.query.filterExpressions.split('|');
        if (this.$route.query.sortingExpressions) this.sortingExpressions = this.$route.query.sortingExpressions.split('|');
        this.fetchArticles();
    }
};
</script>
