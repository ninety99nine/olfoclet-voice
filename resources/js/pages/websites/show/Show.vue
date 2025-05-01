<template>
    <div class="select-none min-h-screen bg-gray-50 pt-8 p-4 sm:p-8 space-y-4 sm:space-y-6">
        <template v-if="showTable">
            <!-- Page Header -->
            <div class="flex justify-between">
                <div class="flex items-end space-x-2">
                    <Earth size="48" stroke-width="1" class="text-gray-400" />
                    <div>
                        <h2 class="text-xl font-semibold text-gray-900 dark:text-white">Websites</h2>
                        <p class="text-sm text-gray-600 dark:text-neutral-400">
                            Manage websites across knowledge bases
                        </p>
                    </div>
                </div>
                <div class="flex justify-end">
                    <Button type="primary" size="md" :leftIcon="Plus" leftIconSize="20" :action="showAddWebsiteModal">
                        <span>Add Website</span>
                    </Button>
                </div>
            </div>

            <!-- Websites Table -->
            <Table
                @search="search"
                resource="websites"
                :columns="columns"
                :perPage="perPage"
                @paginate="paginate"
                @refresh="fetchWebsites"
                :searchTerm="searchTerm"
                :pagination="pagination"
                :isLoading="isLoadingWebsites"
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
                        :key="website.id"
                        v-for="website in websites"
                        :class="[checkedRows[website.id] ? 'bg-blue-50' : 'bg-white hover:bg-gray-50', 'group cursor-pointer text-sm border-b border-gray-200']">
                        <!-- Checkbox -->
                        <td @click.stop class="whitespace-nowrap align-top px-4 py-4">
                            <Input type="checkbox" v-model="checkedRows[website.id]" />
                        </td>
                        <template v-for="(column, columnIndex) in columns" :key="columnIndex">
                            <template v-if="column.active">
                                <!-- URL -->
                                <td v-if="column.name == 'URL'" class="whitespace-nowrap align-center pr-4 py-4">
                                    <div>{{ website.url }}</div>
                                </td>
                                <!-- Organization -->
                                <td v-if="column.name == 'Organization'" class="whitespace-nowrap align-center pr-4 py-4">
                                    <div v-if="website.organization" class="flex items-center space-x-2">
                                        <img src="https://placehold.co/21x24" class="size-8 rounded-lg shrink-0" alt="Logo" />
                                        <div class="whitespace-nowrap">{{ website.organization.name }}</div>
                                    </div>
                                </td>
                                <!-- Knowledge Base -->
                                <td v-if="column.name == 'Knowledge Base'" class="whitespace-nowrap align-center pr-4 py-4">
                                    <div>{{ website.knowledge_base ? website.knowledge_base.name : 'N/A' }}</div>
                                </td>
                                <!-- AI Searchable -->
                                <td v-if="column.name == 'AI Searchable'" class="whitespace-nowrap align-center pr-4 py-4">
                                    <span>{{ website.ai_searchable ? 'Yes' : 'No' }}</span>
                                </td>
                                <!-- Sync Status -->
                                <td v-if="column.name == 'Sync Status'" class="whitespace-nowrap align-center pr-4 py-4">
                                    <span>{{ website.sync_status }}</span>
                                </td>
                                <!-- Last Synced -->
                                <td v-if="column.name == 'Last Synced'" class="whitespace-nowrap align-center pr-4 py-4">
                                    <div class="flex space-x-1 items-center">
                                        <span>{{ website.last_synced_at ? formattedDatetime(website.last_synced_at) : 'N/A' }}</span>
                                        <Popover v-if="website.last_synced_at" placement="top" class="opacity-0 group-hover:opacity-100" :content="formattedRelativeDate(website.last_synced_at)" />
                                    </div>
                                </td>
                                <!-- Created Date -->
                                <td v-if="column.name == 'Created Date'" class="whitespace-nowrap align-center pr-4 py-4">
                                    <div class="flex space-x-1 items-center">
                                        <span>{{ formattedDatetime(website.created_at) }}</span>
                                        <Popover placement="top" class="opacity-0 group-hover:opacity-100" :content="formattedRelativeDate(website.created_at)" />
                                    </div>
                                </td>
                            </template>
                        </template>
                        <td class="h-16 pr-4 py-4 flex align-middle items-center space-x-2">
                            <Button type="bare" size="xs" :leftIcon="Pencil" leftIconSize="16" :action="() => showUpdateWebsiteModal(website)" />
                            <Button type="bareDanger" size="xs" :leftIcon="Trash2" leftIconSize="16" :action="() => showDeleteWebsiteModal(website)" />
                        </td>
                    </tr>
                </template>
            </Table>
        </template>

        <!-- No Websites -->
        <div v-else class="select-none w-full flex justify-center py-16">
            <div class="max-w-sm w-full bg-white shadow-sm rounded-xl p-6 flex flex-col items-center text-center space-y-4 border border-dashed border-gray-200">
                <Earth size="48" class="text-gray-400" />
                <h2 class="text-2xl font-bold text-gray-800">No Websites Yet</h2>
                <p class="text-sm text-gray-500">Add your first website to start managing knowledge content.</p>
                <Button type="primary" size="md" :leftIcon="Plus" leftIconSize="20" :action="showAddWebsiteModal">
                    <span>Add Website</span>
                </Button>
            </div>
        </div>

        <!-- Modals -->
        <AddWebsiteModal ref="addWebsiteModal" @created="fetchWebsites" />
        <UpdateWebsiteModal ref="updateWebsiteModal" @updated="fetchWebsites" />
        <DeleteWebsiteModal ref="deleteWebsiteModal" @deleted="onWebsiteDeleted" />
        <DeleteWebsitesModal ref="deleteWebsitesModal" @deleted="onWebsitesDeleted" />
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
import AddWebsiteModal from '@Pages/websites/components/AddWebsiteModal.vue';
import UpdateWebsiteModal from '@Pages/websites/components/UpdateWebsiteModal.vue';
import DeleteWebsiteModal from '@Pages/websites/components/DeleteWebsiteModal.vue';
import DeleteWebsitesModal from '@Pages/websites/components/DeleteWebsitesModal.vue';
import { formattedDatetime, formattedRelativeDate } from '@Utils/dateUtils.js';
import { Plus, Pencil, Trash2, Earth } from 'lucide-vue-next';

export default {
    inject: ['formState', 'notificationState'],
    components: {
        Pill, Modal, Input, Button, Popover, Dropdown, Table,
        AddWebsiteModal, UpdateWebsiteModal, DeleteWebsiteModal, DeleteWebsitesModal, Earth
    },
    data() {
        return {
            Plus,
            Pencil,
            Trash2,
            Earth,
            websites: [],
            perPage: '15',
            checkedRows: [],
            pagination: null,
            searchTerm: null,
            selectAll: false,
            filterExpressions: [],
            sortingExpressions: [],
            isLoadingWebsites: false,
            columns: this.prepareColumns(),
            dropdownOptions: [
                {
                    icon: Trash2,
                    label: 'Delete',
                    action: this.showDeleteWebsitesModal,
                }
            ],
        };
    },
    watch: {
        selectAll(newValue) {
            this.checkedRows = this.websites.reduce((acc, website) => {
                acc[website.id] = newValue;
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
            return this.isLoadingWebsites || (this.pagination ?? {}).meta?.total > 0 || this.hasSearchTerm || this.hasFilterExpressions || this.hasSortingExpressions;
        }
    },
    methods: {
        formattedDatetime: formattedDatetime,
        formattedRelativeDate: formattedRelativeDate,
        prepareColumns() {
            const columnNames = ['URL', 'Organization', 'Knowledge Base', 'AI Searchable', 'Sync Status', 'Last Synced', 'Created Date'];
            const defaultColumnNames = ['URL', 'Organization', 'Knowledge Base', 'AI Searchable', 'Sync Status', 'Last Synced', 'Created Date'];
            return columnNames.map(name => ({
                name,
                active: defaultColumnNames.includes(name),
                priority: defaultColumnNames.includes(name)
            }));
        },
        showAddWebsiteModal() {
            this.$refs.addWebsiteModal.showModal();
        },
        showUpdateWebsiteModal(website) {
            this.$refs.updateWebsiteModal.showModal(website);
        },
        showDeleteWebsiteModal(website) {
            this.$refs.deleteWebsiteModal.showModal(website);
        },
        showDeleteWebsitesModal() {
            const selectedIds = this.websites.filter(w => this.checkedRows[w.id]).map(w => w.id);
            this.$refs.deleteWebsitesModal.showModal(selectedIds);
        },
        onWebsiteDeleted(id) {
            this.websites = this.websites.filter(w => w.id !== id);
            if (this.websites.length === 0) this.fetchWebsites();
        },
        onWebsitesDeleted(deletedIds = []) {
            deletedIds.forEach(id => {
                if (this.checkedRows[id] !== undefined) {
                    this.checkedRows[id] = false;
                }
            });
            this.selectAll = false;
            this.websites = this.websites.filter(website => !deletedIds.includes(website.id));
            if (this.websites.length === 0) {
                this.fetchWebsites();
            }
        },
        paginate(url) {
            this.fetchWebsites(url);
        },
        search(searchTerm) {
            this.searchTerm = searchTerm;
            this.fetchWebsites();
        },
        updatedColumns(columns) {
            this.columns = columns;
        },
        updatedFilters(filters) {
            const newFilterExpressions = filters.map((filter) => filter.expression);
            if (!isEqual(this.filterExpressions, newFilterExpressions)) {
                this.filterExpressions = newFilterExpressions;
                this.fetchWebsites();
            }
        },
        updatedSorting(sorting) {
            const newSortingExpressions = sorting.map((sort) => sort.expression);
            if (!isEqual(this.sortingExpressions, newSortingExpressions)) {
                this.sortingExpressions = newSortingExpressions;
                this.fetchWebsites();
            }
        },
        updatedPerPage(perPage) {
            this.perPage = perPage;
            this.fetchWebsites();
        },
        async fetchWebsites(url = null) {
            try {
                this.isLoadingWebsites = true;
                url = url ?? '/api/websites';
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
                this.websites = this.pagination.data;
                this.checkedRows = this.websites.reduce((acc, website) => {
                    acc[website.id] = false;
                    return acc;
                }, {});
            } catch (error) {
                const message = error?.response?.data?.message || error?.message || 'Something went wrong while fetching websites';
                this.notificationState.showWarningNotification(message);
                console.error('Failed to fetch websites:', error);
            } finally {
                this.isLoadingWebsites = false;
            }
        }
    },
    created() {
        this.isLoadingWebsites = true;
        this.searchTerm = this.$route.query.searchTerm;
        if (this.$route.query.filterExpressions) this.filterExpressions = this.$route.query.filterExpressions.split('|');
        if (this.$route.query.sortingExpressions) this.sortingExpressions = this.$route.query.sortingExpressions.split('|');
        this.fetchWebsites();
    }
};
</script>
