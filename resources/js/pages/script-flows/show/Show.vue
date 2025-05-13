<template>
    <div class="select-none min-h-screen bg-gray-50 pt-8 p-4 sm:p-8 space-y-4 sm:space-y-6">
        <template v-if="showTable">
            <!-- Page Header -->
            <div class="flex justify-between">
                <div class="flex items-end space-x-2">
                    <WorkflowIcon size="48" stroke-width="1" class="text-gray-400" />
                    <div>
                        <h2 class="text-xl font-semibold text-gray-900 dark:text-white">Call Flows</h2>
                        <p class="text-sm text-gray-600 dark:text-neutral-400">
                            Manage call flows within your organization
                        </p>
                    </div>
                </div>
                <div class="flex justify-end">
                    <Button type="primary" size="md" :leftIcon="Plus" leftIconSize="20" :action="navigateToCreateCallFlow">
                        <span>Add Call Flow</span>
                    </Button>
                </div>
            </div>

            <!-- Call Flows Table -->
            <Table
                @search="search"
                :columns="columns"
                :perPage="perPage"
                @paginate="paginate"
                resource="call-flows"
                @refresh="fetchCallFlows"
                :searchTerm="searchTerm"
                :pagination="pagination"
                @updatedColumns="updatedColumns"
                @updatedFilters="updatedFilters"
                @updatedSorting="updatedSorting"
                @updatedPerPage="updatedPerPage"
                :isLoading="isLoadingCallFlows"
                :filterExpressions="filterExpressions"
                :sortingExpressions="sortingExpressions"
            >
                <!-- Select Action -->
                <template #belowToolbar>
                    <div :class="[{ 'hidden': totalCheckedRows === 0 }, 'bg-gray-50 border flex items-center mb-2 p-4 rounded-lg shadow space-x-2']">
                        <span class="text-sm">Actions: </span>
                        <Dropdown
                            triggerSize="sm"
                            :options="dropdownOptions"
                            :triggerText="`Select Action (${totalCheckedRows} selected)`"
                        />
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
                            <th v-if="column.active" scope="col" :class="['whitespace-nowrap align-top font-semibold pr-4 py-2.5', { 'text-center': ['Numbers'].includes(column.name) }]">
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
                        v-for="callFlow in callFlows"
                        :key="callFlow.id"
                        :class="[checkedRows[callFlow.id] ? 'bg-blue-50' : 'bg-white hover:bg-gray-50', 'group cursor-pointer text-sm border-b border-gray-200']"
                    >
                        <!-- Checkbox -->
                        <td @click.stop class="whitespace-nowrap align-top px-4 py-4">
                            <Input type="checkbox" v-model="checkedRows[callFlow.id]" />
                        </td>
                        <!-- Columns -->
                        <template v-for="(column, columnIndex) in columns" :key="columnIndex">
                            <template v-if="column.active">
                                <template v-if="column.name === 'Name'">
                                    <td class="whitespace-nowrap align-center pr-4 py-4">
                                        <div>{{ callFlow.name }}</div>
                                    </td>
                                </template>
                                <template v-if="column.name === 'Organization'">
                                    <td class="whitespace-nowrap align-center pr-4 py-4">
                                        <div v-if="callFlow.organization" class="flex items-center space-x-2">
                                            <img src="https://placehold.co/21x24" class="size-8 rounded-lg shrink-0" alt="Logo" />
                                            <div class="whitespace-nowrap">{{ callFlow.organization.name }}</div>
                                        </div>
                                    </td>
                                </template>
                                <template v-if="column.name === 'Country'">
                                    <td class="whitespace-nowrap align-center pr-4 py-4">
                                        <div v-if="callFlow.organization" class="flex items-center space-x-2">
                                            <div class="flex items-center space-x-2">
                                                <img
                                                    v-if="callFlow.organization.country"
                                                    :src="`/svgs/country-flags/${callFlow.organization.country.toLowerCase()}.svg`"
                                                    :alt="callFlow.organization.country"
                                                    class="w-5 h-4 rounded-sm object-cover"
                                                >
                                                <span>{{ getCountryName(callFlow.organization.country) }}</span>
                                            </div>
                                        </div>
                                    </td>
                                </template>
                                <template v-if="column.name === 'Numbers'">
                                    <td class="whitespace-nowrap align-center text-center pr-4 py-4">
                                        <span>{{ callFlow.numbers_count }}</span>
                                    </td>
                                </template>
                                <template v-if="column.name === 'Created Date'">
                                    <td class="whitespace-nowrap align-center pr-4 py-4">
                                        <div class="flex space-x-1 items-center">
                                            <span>{{ formattedDatetime(callFlow.created_at) }}</span>
                                            <Popover placement="top" class="opacity-0 group-hover:opacity-100" :content="formattedRelativeDate(callFlow.created_at)" />
                                        </div>
                                    </td>
                                </template>
                            </template>
                        </template>
                        <!-- Actions -->
                        <td class="h-16 pr-4 py-4 flex align-middle items-center space-x-2">
                            <router-link :to="{ name: 'edit-call-flow', params: { call_flow_id: callFlow.id } }">
                                <Button type="bare" size="xs" :leftIcon="Pencil" leftIconSize="16" />
                            </router-link>
                            <Button
                                type="bareDanger"
                                size="xs"
                                :leftIcon="Trash2"
                                leftIconSize="16"
                                :action="() => showDeleteCallFlowModal(callFlow)"
                            />
                        </td>
                    </tr>
                </template>
            </Table>
        </template>

        <!-- No Call Flows -->
        <div v-else class="select-none w-full flex justify-center py-16">
            <div class="max-w-sm w-full bg-white shadow-sm rounded-xl p-6 flex flex-col items-center text-center space-y-4 border border-dashed border-gray-200">
                <WorkflowIcon size="48" class="text-gray-400" />
                <h2 class="text-2xl font-bold text-gray-800">No Call Flows Yet</h2>
                <p class="text-sm text-gray-500">Add a call flow to manage call routing.</p>
                <Button type="primary" size="md" :leftIcon="Plus" leftIconSize="20" :action="navigateToCreateCallFlow">
                    <span>Add Call Flow</span>
                </Button>
            </div>
        </div>

        <!-- Modals -->
        <DeleteCallFlowModal ref="deleteCallFlowModal" @deleted="onCallFlowDeleted" />
        <DeleteCallFlowsModal ref="deleteCallFlowsModal" @deleted="onCallFlowsDeleted" />
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
import { getCountryName } from '@Utils/generalUtils.js';
import { Plus, Pencil, Trash2, Workflow as WorkflowIcon } from 'lucide-vue-next';
import { formattedDatetime, formattedRelativeDate } from '@Utils/dateUtils.js';
import DeleteCallFlowModal from '@Pages/call-flows/components/DeleteCallFlowModal.vue';
import DeleteCallFlowsModal from '@Pages/call-flows/components/DeleteCallFlowsModal.vue';

export default {
    inject: ['formState', 'notificationState'],
    components: {
        Pill,
        Modal,
        Input,
        Button,
        Popover,
        Dropdown,
        Table,
        DeleteCallFlowModal,
        DeleteCallFlowsModal,
        WorkflowIcon,
    },
    data() {
        return {
            Plus,
            Pencil,
            Trash2,
            perPage: '15',
            callFlows: [],
            getCountryName,
            checkedRows: [],
            pagination: null,
            searchTerm: null,
            selectAll: false,
            filterExpressions: [],
            sortingExpressions: [],
            isLoadingCallFlows: false,
            columns: this.prepareColumns(),
            dropdownOptions: [
                {
                    icon: Trash2,
                    label: 'Delete',
                    action: this.showDeleteCallFlowsModal,
                },
            ],
        };
    },
    watch: {
        selectAll(newValue) {
            this.checkedRows = this.callFlows.reduce((acc, callFlow) => {
                acc[callFlow.id] = newValue;
                return acc;
            }, {});
        },
    },
    computed: {
        hasSearchTerm() {
            return this.searchTerm != null && this.searchTerm.trim() !== '';
        },
        totalCheckedRows() {
            return Object.values(this.checkedRows).filter(Boolean).length;
        },
        hasFilterExpressions() {
            return this.filterExpressions.length > 0;
        },
        hasSortingExpressions() {
            return this.sortingExpressions.length > 0;
        },
        showTable() {
            return this.isLoadingCallFlows || (this.pagination ?? {}).meta?.total > 0 || this.hasSearchTerm || this.hasFilterExpressions || this.hasSortingExpressions;
        },
    },
    methods: {
        formattedDatetime,
        formattedRelativeDate,
        prepareColumns() {
            const columnNames = ['Name', 'Organization', 'Country', 'Numbers', 'Created Date'];
            const defaultColumnNames = ['Name', 'Organization', 'Country', 'Numbers', 'Created Date'];
            return columnNames.map(name => ({
                name,
                active: defaultColumnNames.includes(name),
                priority: defaultColumnNames.includes(name),
            }));
        },
        navigateToCreateCallFlow() {
            this.$router.push({ name: 'create-call-flow' });
        },
        showDeleteCallFlowModal(callFlow) {
            this.$refs.deleteCallFlowModal.showModal(callFlow);
        },
        showDeleteCallFlowsModal() {
            const selectedIds = this.callFlows.filter(c => this.checkedRows[c.id]).map(c => c.id);
            this.$refs.deleteCallFlowsModal.showModal(selectedIds);
        },
        onCallFlowDeleted(id) {
            this.callFlows = this.callFlows.filter(c => c.id !== id);
            if (this.callFlows.length === 0) this.fetchCallFlows();
        },
        onCallFlowsDeleted(deletedIds = []) {
            deletedIds.forEach(id => {
                if (this.checkedRows[id] !== undefined) {
                    this.checkedRows[id] = false;
                }
            });
            this.selectAll = false;
            this.callFlows = this.callFlows.filter(callFlow => !deletedIds.includes(callFlow.id));
            if (this.callFlows.length === 0) {
                this.fetchCallFlows();
            }
        },
        paginate(url) {
            this.fetchCallFlows(url);
        },
        search(searchTerm) {
            this.searchTerm = searchTerm;
            this.fetchCallFlows();
        },
        updatedColumns(columns) {
            this.columns = columns;
        },
        updatedFilters(filters) {
            const newFilterExpressions = filters.map(f => f.expression);
            if (!isEqual(this.filterExpressions, newFilterExpressions)) {
                this.filterExpressions = newFilterExpressions;
                this.fetchCallFlows();
            }
        },
        updatedSorting(sorting) {
            const newSortingExpressions = sorting.map(s => s.expression);
            if (!isEqual(this.sortingExpressions, newSortingExpressions)) {
                this.sortingExpressions = newSortingExpressions;
                this.fetchCallFlows();
            }
        },
        updatedPerPage(perPage) {
            this.perPage = perPage;
            this.fetchCallFlows();
        },
        async fetchCallFlows(url = null) {
            try {
                this.isLoadingCallFlows = true;
                url = url ?? '/api/call-flows';
                let config = {
                    params: {
                        'per_page': this.perPage,
                        '_relationships': 'organization',
                        '_countable_relationships': 'numbers',
                    },
                };
                if (this.hasSearchTerm) config.params['search'] = this.searchTerm;
                if (this.hasFilterExpressions) config.params['_filters'] = this.filterExpressions.join('|');
                if (this.hasSortingExpressions) config.params['_sort'] = this.sortingExpressions.join('|');

                const response = await axios.get(url, config);
                this.pagination = response.data;
                this.callFlows = this.pagination.data;
                this.checkedRows = this.callFlows.reduce((acc, callFlow) => {
                    acc[callFlow.id] = false;
                    return acc;
                }, {});
            } catch (error) {
                const message = error?.response?.data?.message || error?.message || 'Something went wrong while fetching call flows';
                this.$notificationState.showWarningNotification(message);
                console.error('Failed to fetch call flows:', error);
            } finally {
                this.isLoadingCallFlows = false;
            }
        },
    },
    created() {
        this.isLoadingCallFlows = true;
        this.searchTerm = this.$route.query.searchTerm;
        if (this.$route.query.filterExpressions) this.filterExpressions = this.$route.query.filterExpressions.split('|');
        if (this.$route.query.sortingExpressions) this.sortingExpressions = this.$route.query.sortingExpressions.split('|');
        this.fetchCallFlows();
    },
};
</script>
