<template>

    <div class="select-none min-h-screen bg-gray-50 pt-8 p-4 sm:p-8 space-y-4 sm:space-y-6">

        <template v-if="showTable">

            <!-- Page Header -->
            <div class="flex justify-between">

                <div class="flex items-end space-x-2">

                    <PhoneCall size="48" stroke-width="1" class="text-gray-400" />

                    <div>
                        <h2 class="text-xl font-semibold text-gray-900 dark:text-white">Calls</h2>

                        <p class="text-sm text-gray-600 dark:text-neutral-400">
                            Manage all calls within your organization
                        </p>
                    </div>

                </div>

                <div class="flex justify-end">
                    <Button type="primary" size="md" :leftIcon="Phone" leftIconSize="20" :action="showAddCallModal">
                        <span class="ml-2">Make Call</span>
                    </Button>
                </div>

            </div>

            <div class="bg-white dark:bg-neutral-800 border border-gray-200 dark:border-neutral-700 rounded-xl">
                <div class="p-4 sm:p-6">
                    <div class="flex justify-between items-center mb-4">
                        <div>
                            <h3 class="text-2xl font-semibold text-gray-900 dark:text-white mb-2">All Calls</h3>
                            <p class="text-sm text-gray-500 dark:text-neutral-400">
                                View and manage calls on the Telcoflo platform
                            </p>
                        </div>
                    </div>

                    <!-- Calls Table -->
                    <Table
                        resource="calls"
                        @search="search"
                        :columns="columns"
                        :perPage="perPage"
                        @paginate="paginate"
                        @refresh="fetchCalls"
                        :searchTerm="searchTerm"
                        :pagination="pagination"
                        :isLoading="isLoadingCalls"
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

                                    <Input
                                        type="checkbox"
                                        v-model="selectAll">
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

                        <template #body>
                            <tr v-for="call in calls" :key="call.id" :class="[checkedRows[call.id] ? 'bg-blue-50' : 'bg-white hover:bg-gray-50', 'group cursor-pointer text-sm border-b border-gray-200']">
                                <td class="whitespace-nowrap align-top px-4 py-4">
                                    <Input type="checkbox" v-model="checkedRows[call.id]" />
                                </td>

                                <template v-if="columns.find(col => col.name === 'From')?.active">
                                    <td class="whitespace-nowrap align-center pr-4 py-4">
                                        <div class="text-sm">{{ call.from }}</div>
                                    </td>
                                </template>

                                <template v-if="columns.find(col => col.name === 'To')?.active">
                                    <td class="whitespace-nowrap align-center pr-4 py-4">
                                        <div class="text-sm">{{ call.to }}</div>
                                    </td>
                                </template>

                                <template v-if="columns.find(col => col.name === 'Direction')?.active">
                                    <td class="whitespace-nowrap align-center pr-4 py-4">
                                        <PhoneIncoming v-if="call.direction == 'inbound'" size="16" class="text-indigo-500 mx-auto">{{ call.direction }}</PhoneIncoming>
                                        <PhoneOutgoing v-else size="16" class="text-orange-500 mx-auto">{{ call.direction }}</PhoneOutgoing>
                                    </td>
                                </template>

                                <template v-if="columns.find(col => col.name === 'Status')?.active">
                                    <td class="whitespace-nowrap align-center pr-4 py-4">
                                        <span>{{ call.status }}</span>
                                    </td>
                                </template>

                                <template v-if="columns.find(col => col.name === 'Agent')?.active">
                                    <td class="whitespace-nowrap align-center pr-4 py-4">
                                        <span>{{ call.agent ? call.agent.name : 'N/A' }}</span>
                                    </td>
                                </template>

                                <template v-if="columns.find(col => col.name === 'Department')?.active">
                                    <td class="whitespace-nowrap align-center pr-4 py-4">
                                        <span>{{ call.department ? call.department.name : 'N/A' }}</span>
                                    </td>
                                </template>

                                <template v-if="columns.find(col => col.name === 'Activities')?.active">
                                    <td class="whitespace-nowrap align-center text-center pr-4 py-4">
                                        <span>{{ call.activities_count }}</span>
                                    </td>
                                </template>

                                <template v-if="columns.find(col => col.name === 'Created Date')?.active">
                                    <td class="whitespace-nowrap align-center pr-4 py-4">
                                        <div class="flex space-x-1 items-center">
                                            <span>{{ formattedDatetime(call.created_at) }}</span>
                                            <Popover placement="top" class="opacity-0 group-hover:opacity-100" :content="formattedRelativeDate(call.created_at)" />
                                        </div>
                                    </td>
                                </template>

                                <td class="align-top pr-4 py-4 flex items-center space-x-1">
                                    <Button type="outline" size="xs" :leftIcon="Pencil" leftIconSize="12" :action="() => showUpdateCallModal(call)" />
                                    <Button type="outlineDanger" size="xs" :leftIcon="Trash2" leftIconSize="12" :action="() => showDeleteCallModal(call)" />
                                </td>
                            </tr>
                        </template>
                    </Table>
                </div>
            </div>
        </template>

        <!-- No Calls -->
        <div v-else class="select-none w-full flex justify-center py-16">
            <div class="max-w-sm w-full bg-white shadow-sm rounded-xl p-6 flex flex-col items-center text-center space-y-4 border border-dashed border-gray-200">
                <PhoneCall size="48" class="text-gray-400" />
                <h2 class="text-2xl font-bold text-gray-800">No Calls Yet</h2>
                <p class="text-sm text-gray-500">Add a call to start managing communications.</p>
                <Button type="primary" size="md" :leftIcon="Phone" leftIconSize="20" :action="showAddCallModal">
                    <span>Add Call</span>
                </Button>
            </div>
        </div>

        <!-- Modals -->
        <AddCallModal ref="addCallModal" @created="fetchCalls" />
        <UpdateCallModal ref="updateCallModal" @updated="fetchCalls" />
        <DeleteCallModal ref="deleteCallModal" @deleted="onCallDeleted" />
        <DeleteCallsModal ref="deleteCallsModal" @deleted="onCallsDeleted" />
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
import AddCallModal from '@Pages/calls/components/AddCallModal.vue';
import UpdateCallModal from '@Pages/calls/components/UpdateCallModal.vue';
import DeleteCallModal from '@Pages/calls/components/DeleteCallModal.vue';
import DeleteCallsModal from '@Pages/calls/components/DeleteCallsModal.vue';
import { formattedDatetime, formattedRelativeDate } from '@Utils/dateUtils.js';
import { Phone, Pencil, Trash2, PhoneCall, PhoneIncoming, PhoneOutgoing } from 'lucide-vue-next';

export default {
    inject: ['formState', 'notificationState'],
    components: {
        Pill, Modal, Input, Button, Popover, Dropdown, Table,
        AddCallModal, UpdateCallModal, DeleteCallModal, DeleteCallsModal, PhoneCall, PhoneIncoming, PhoneOutgoing
    },
    data() {
        return {
            Phone,
            Pencil,
            Trash2,
            PhoneCall,
            calls: [],
            perPage: '15',
            checkedRows: [],
            pagination: null,
            searchTerm: null,
            selectAll: false,
            filterExpressions: [],
            sortingExpressions: [],
            isLoadingCalls: false,
            columns: this.prepareColumns(),
            dropdownOptions: [
                {
                    icon: Trash2,
                    label: 'Delete',
                    action: this.showDeleteCallsModal,
                }
            ]
        }
    },
    watch: {
        selectAll(newValue) {
            this.checkedRows = this.calls.reduce((acc, call) => {
                acc[call.id] = newValue;
                return acc;
            }, {});
        }
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
            return this.isLoadingCalls || (this.pagination ?? {}).meta?.total > 0 || this.hasSearchTerm || this.hasFilterExpressions || this.hasSortingExpressions;
        }
    },
    methods: {
        formattedDatetime,
        formattedRelativeDate,
        prepareColumns() {
            const columnNames = ['From', 'To', 'Direction', 'Status', 'Agent', 'Department', 'Activities', 'Created Date'];
            const defaultColumnNames = ['From', 'To', 'Direction', 'Status', 'Agent', 'Department', 'Activities', 'Created Date'];
            return columnNames.map(name => ({
                name,
                active: defaultColumnNames.includes(name),
                priority: defaultColumnNames.includes(name)
            }));
        },
        showAddCallModal() {
            this.$refs.addCallModal.showModal();
        },
        showUpdateCallModal(call) {
            this.$refs.updateCallModal.showModal(call);
        },
        showDeleteCallModal(call) {
            this.$refs.deleteCallModal.showModal(call);
        },
        showDeleteCallsModal() {
            const selectedIds = this.calls.filter(c => this.checkedRows[c.id]).map(c => c.id);
            this.$refs.deleteCallsModal.showModal(selectedIds);
        },
        onCallDeleted(id) {
            this.calls = this.calls.filter(c => c.id !== id);
            if (this.calls.length === 0) this.fetchCalls();
        },
        onCallsDeleted(deletedIds = []) {
            deletedIds.forEach(id => {
                if (this.checkedRows[id] !== undefined) {
                    this.checkedRows[id] = false;
                }
            });
            this.selectAll = false;
            this.calls = this.calls.filter(call => !deletedIds.includes(call.id));
            if (this.calls.length === 0) {
                this.fetchCalls();
            }
        },
        paginate(url) {
            this.fetchCalls(url);
        },
        search(searchTerm) {
            this.searchTerm = searchTerm;
            this.fetchCalls();
        },
        updatedColumns(columns) {
            this.columns = columns;
        },
        updatedFilters(filters) {
            const newFilterExpressions = filters.map(f => f.expression);
            if (!isEqual(this.filterExpressions, newFilterExpressions)) {
                this.filterExpressions = newFilterExpressions;
                this.fetchCalls();
            }
        },
        updatedSorting(sorting) {
            const newSortingExpressions = sorting.map(s => s.expression);
            if (!isEqual(this.sortingExpressions, newSortingExpressions)) {
                this.sortingExpressions = newSortingExpressions;
                this.fetchCalls();
            }
        },
        updatedPerPage(perPage) {
            this.perPage = perPage;
            this.fetchCalls();
        },
        async fetchCalls(url = null) {
            try {
                this.isLoadingCalls = true;
                url = url ?? '/api/calls';
                let config = {
                    params: {
                        'per_page': this.perPage,
                        '_relationships': 'organization,agent,department',
                        '_countable_relationships': 'callActivities'
                    }
                };
                if (this.hasSearchTerm) config.params['search'] = this.searchTerm;
                if (this.hasFilterExpressions) config.params['_filters'] = this.filterExpressions.join('|');
                if (this.hasSortingExpressions) config.params['_sort'] = this.sortingExpressions.join('|');

                const response = await axios.get(url, config);
                this.pagination = response.data;
                this.calls = this.pagination.data;
                this.checkedRows = this.calls.reduce((acc, call) => {
                    acc[call.id] = false;
                    return acc;
                }, {});
            } catch (error) {
                const message = error?.response?.data?.message || error?.message || 'Something went wrong while fetching calls';
                this.notificationState.showWarningNotification(message);
                console.error('Failed to fetch calls:', error);
            } finally {
                this.isLoadingCalls = false;
            }
        }
    },
    created() {
        this.isLoadingCalls = true;
        this.searchTerm = this.$route.query.searchTerm;
        if (this.$route.query.filterExpressions) this.filterExpressions = this.$route.query.filterExpressions.split('|');
        if (this.$route.query.sortingExpressions) this.sortingExpressions = this.$route.query.sortingExpressions.split('|');
        this.fetchCalls();
    }
};
</script>
