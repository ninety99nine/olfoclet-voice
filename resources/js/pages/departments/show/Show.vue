<template>
    <div class="min-h-screen bg-gray-50 pt-8 p-4 sm:p-8 space-y-4 sm:space-y-6">
        <template v-if="showTable">
            <!-- Page Header -->
            <div class="space-y-1">
                <div class="flex items-end space-x-2">
                    <Building2 size="48" stroke-width="1" class="text-gray-400" />
                    <div>
                        <h2 class="text-xl font-semibold text-gray-900 dark:text-white">Department Management</h2>
                        <p class="text-sm text-gray-600 dark:text-neutral-400">
                            Manage departments within your organization
                        </p>
                    </div>
                </div>
                <div class="flex justify-end">
                    <Button type="primary" size="md" :leftIcon="Plus" leftIconSize="20" :action="showAddDepartmentModal">
                        <span>Add Department</span>
                    </Button>
                </div>
            </div>

            <div class="bg-white dark:bg-neutral-800 border border-gray-200 dark:border-neutral-700 rounded-xl">
                <div class="p-4 sm:p-6">
                    <div class="flex justify-between items-center mb-4">
                        <div>
                            <h3 class="text-2xl font-semibold text-gray-900 dark:text-white mb-2">All Departments</h3>
                            <p class="text-sm text-gray-500 dark:text-neutral-400">
                                View and manage departments within this organization
                            </p>
                        </div>
                    </div>

                    <!-- Departments Table -->
                    <Table
                        resource="departments"
                        @search="search"
                        :columns="columns"
                        :perPage="perPage"
                        @paginate="paginate"
                        @refresh="fetchDepartments"
                        :searchTerm="searchTerm"
                        :pagination="pagination"
                        :isLoading="isLoadingDepartments"
                        @updatedColumns="updatedColumns"
                        @updatedFilters="updatedFilters"
                        @updatedSorting="updatedSorting"
                        @updatedPerPage="updatedPerPage"
                        :filterExpressions="filterExpressions"
                        :sortingExpressions="sortingExpressions">
                        <template #belowToolbar>
                            <div :class="[{ 'hidden': totalCheckedRows === 0 }, 'bg-gray-50 border flex items-center mb-2 p-4 rounded-lg shadow space-x-2']">
                                <span class="text-sm">Actions: </span>
                                <Dropdown
                                    triggerSize="sm"
                                    :options="dropdownOptions"
                                    :triggerText="`Select Action (${totalCheckedRows} selected)`" />
                            </div>
                        </template>

                        <template #head>
                            <tr class="border-b border-gray-200">
                                <th scope="col" class="whitespace-nowrap align-top px-4 py-4">
                                    <Input type="checkbox" v-model="selectAll" />
                                </th>
                                <template v-for="(column, index) in columns" :key="index">
                                    <th v-if="column.active" scope="col" :class="['whitespace-nowrap align-top pr-4 py-4', { 'text-center' : ['Queues'].includes(column.name) }]">
                                        {{ column.name }}
                                    </th>
                                </template>
                                <th scope="col" class="whitespace-nowrap align-top pr-4 py-4">Actions</th>
                            </tr>
                        </template>

                        <template #body>

                            <tr @click.stop="onView(department)" v-for="department in departments" :key="department.id" :class="[checkedRows[department.id] ? 'bg-blue-50' : 'bg-white hover:bg-gray-50', 'group cursor-pointer border-b border-gray-200']">

                                <!-- Checkbox -->
                                <td @click.stop class="whitespace-nowrap align-top px-4 py-4">

                                    <Input
                                        type="checkbox"
                                        v-model="checkedRows[department.id]">
                                    </Input>

                                </td>

                                <template v-for="(column, columnIndex) in columns" :key="columnIndex">

                                    <template v-if="column.name == 'Name'">
                                        <td class="whitespace-nowrap align-center pr-4 py-4">
                                            <div class="font-medium text-sm">{{ department.name }}</div>
                                        </td>
                                    </template>

                                    <template v-if="column.name == 'Organization'">
                                        <td class="whitespace-nowrap align-center pr-4 py-4">
                                            <div v-if="department.organization" class="flex items-center space-x-2">
                                                <img src="https://placehold.co/21x24" class="size-8 rounded-lg shrink-0" alt="Logo" />
                                                <div class="whitespace-nowrap font-medium text-sm">{{ department.organization.name }}</div>
                                            </div>
                                        </td>
                                    </template>

                                    <template v-if="column.name == 'Country'">
                                        <td class="whitespace-nowrap align-center pr-4 py-4">
                                            <div v-if="department.organization" class="flex items-center space-x-2">
                                                <div class="flex items-center space-x-2">
                                                    <img
                                                        v-if="department.organization.country"
                                                        :src="`/svgs/country-flags/${department.organization.country.toLowerCase()}.svg`"
                                                        :alt="department.organization.country"
                                                        class="w-5 h-4 rounded-sm object-cover"
                                                    >
                                                    <span>{{ getCountryName(department.organization.country) }}</span>
                                                </div>
                                            </div>
                                        </td>
                                    </template>

                                    <template v-if="column.name == 'Queues'">
                                        <td class="whitespace-nowrap align-center text-center pr-4 py-4">
                                            <span>{{ department.queues_count }}</span>
                                        </td>
                                    </template>

                                    <template v-if="column.name == 'Created Date'">
                                        <td class="whitespace-nowrap align-center pr-4 py-4">
                                            <div class="flex space-x-1 items-center">
                                                <span>{{ formattedDatetime(department.created_at) }}</span>
                                                <Popover placement="top" class="opacity-0 group-hover:opacity-100" :content="formattedRelativeDate(department.created_at)" />
                                            </div>
                                        </td>
                                    </template>

                                </template>

                                <td class="align-top pr-4 py-4 flex items-center space-x-1">
                                    <Button type="outline" size="xs" :leftIcon="Pencil" leftIconSize="12" :action="() => showUpdateDepartmentModal(department)" />
                                    <Button type="outlineDanger" size="xs" :leftIcon="Trash2" leftIconSize="12" :action="() => showDeleteDepartmentModal(department)" />
                                </td>

                            </tr>
                        </template>
                    </Table>
                </div>
            </div>
        </template>

        <!-- No Departments -->
        <div v-else class="select-none w-full flex justify-center py-16">
            <div class="max-w-sm w-full bg-white shadow-sm rounded-xl p-6 flex flex-col items-center text-center space-y-4 border border-dashed border-gray-200">
                <Building2 size="48" class="text-gray-400" />
                <h2 class="text-2xl font-bold text-gray-800">No Departments Yet</h2>
                <p class="text-sm text-gray-500">Add a department to manage organizational units.</p>
                <Button type="primary" size="md" :leftIcon="Plus" leftIconSize="20" :action="showAddDepartmentModal">
                    <span>Add Department</span>
                </Button>
            </div>
        </div>

        <!-- Modals -->
        <AddDepartmentModal ref="addDepartmentModal" @created="fetchDepartments" />
        <UpdateDepartmentModal ref="updateDepartmentModal" @updated="fetchDepartments" />
        <DeleteDepartmentModal ref="deleteDepartmentModal" @deleted="onDepartmentDeleted" />
        <DeleteDepartmentsModal ref="deleteDepartmentsModal" @deleted="onDepartmentsDeleted" />
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
import AddDepartmentModal from '@Pages/departments/components/AddDepartmentModal.vue';
import UpdateDepartmentModal from '@Pages/departments/components/UpdateDepartmentModal.vue';
import DeleteDepartmentModal from '@Pages/departments/components/DeleteDepartmentModal.vue';
import DeleteDepartmentsModal from '@Pages/departments/components/DeleteDepartmentsModal.vue';
import { formattedDatetime, formattedRelativeDate } from '@Utils/dateUtils.js';
import { Plus, Pencil, Trash2, Building2 } from 'lucide-vue-next';

export default {
    inject: ['formState', 'notificationState'],
    components: {
        Pill, Modal, Input, Button, Popover, Dropdown, Table,
        AddDepartmentModal, UpdateDepartmentModal, DeleteDepartmentModal, DeleteDepartmentsModal, Building2
    },
    data() {
        return {
            Plus,
            Pencil,
            Trash2,
            Building2,
            departments: [],
            perPage: '15',
            getCountryName,
            checkedRows: [],
            pagination: null,
            searchTerm: null,
            selectAll: false,
            filterExpressions: [],
            sortingExpressions: [],
            isLoadingDepartments: false,
            columns: this.prepareColumns(),
            dropdownOptions: [
                {
                    icon: Trash2,
                    label: 'Delete',
                    action: this.showDeleteDepartmentsModal,
                }
            ]
        }
    },
    watch: {
        selectAll(newValue) {
            this.checkedRows = this.departments.reduce((acc, department) => {
                acc[department.id] = newValue;
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
            return this.isLoadingDepartments || (this.pagination ?? {}).meta?.total > 0 || this.hasSearchTerm || this.hasFilterExpressions || this.hasSortingExpressions;
        }
    },
    methods: {
        formattedDatetime,
        formattedRelativeDate,
        prepareColumns() {
            const columnNames = ['Name', 'Organization', 'Country', 'Queues', 'Created Date'];
            const defaultColumnNames = ['Name', 'Organization', 'Country', 'Queues', 'Created Date'];
            return columnNames.map(name => ({
                name,
                active: defaultColumnNames.includes(name),
                priority: defaultColumnNames.includes(name)
            }));
        },
        showAddDepartmentModal() {
            this.$refs.addDepartmentModal.showModal();
        },
        showUpdateDepartmentModal(department) {
            this.$refs.updateDepartmentModal.showModal(department);
        },
        showDeleteDepartmentModal(department) {
            this.$refs.deleteDepartmentModal.showModal(department);
        },
        showDeleteDepartmentsModal() {
            const selectedIds = this.departments.filter(d => this.checkedRows[d.id]).map(d => d.id);
            this.$refs.deleteDepartmentsModal.showModal(selectedIds);
        },
        onDepartmentDeleted(id) {
            this.departments = this.departments.filter(d => d.id !== id);
            if (this.departments.length === 0) this.fetchDepartments();
        },
        onDepartmentsDeleted(deletedIds = []) {
            deletedIds.forEach(id => {
                if (this.checkedRows[id] !== undefined) {
                    this.checkedRows[id] = false;
                }
            });
            this.selectAll = false;
            this.departments = this.departments.filter(department => !deletedIds.includes(department.id));
            if (this.departments.length === 0) {
                this.fetchDepartments();
            }
        },
        paginate(url) {
            this.fetchDepartments(url);
        },
        search(searchTerm) {
            this.searchTerm = searchTerm;
            this.fetchDepartments();
        },
        updatedColumns(columns) {
            this.columns = columns;
        },
        updatedFilters(filters) {
            const newFilterExpressions = filters.map(f => f.expression);
            if (!isEqual(this.filterExpressions, newFilterExpressions)) {
                this.filterExpressions = newFilterExpressions;
                this.fetchDepartments();
            }
        },
        updatedSorting(sorting) {
            const newSortingExpressions = sorting.map(s => s.expression);
            if (!isEqual(this.sortingExpressions, newSortingExpressions)) {
                this.sortingExpressions = newSortingExpressions;
                this.fetchDepartments();
            }
        },
        updatedPerPage(perPage) {
            this.perPage = perPage;
            this.fetchDepartments();
        },
        async fetchDepartments(url = null) {
            try {
                this.isLoadingDepartments = true;
                url = url ?? '/api/departments';
                let config = {
                    params: {
                        'per_page': this.perPage,
                        '_relationships': 'organization',
                        '_countable_relationships': 'queues'
                    }
                };
                if (this.hasSearchTerm) config.params['search'] = this.searchTerm;
                if (this.hasFilterExpressions) config.params['_filters'] = this.filterExpressions.join('|');
                if (this.hasSortingExpressions) config.params['_sort'] = this.sortingExpressions.join('|');

                const response = await axios.get(url, config);
                this.pagination = response.data;
                this.departments = this.pagination.data;
                this.checkedRows = this.departments.reduce((acc, department) => {
                    acc[department.id] = false;
                    return acc;
                }, {});
            } catch (error) {
                const message = error?.response?.data?.message || error?.message || 'Something went wrong while fetching departments';
                this.notificationState.showWarningNotification(message);
                console.error('Failed to fetch departments:', error);
            } finally {
                this.isLoadingDepartments = false;
            }
        }
    },
    created() {
        this.isLoadingDepartments = true;
        this.searchTerm = this.$route.query.searchTerm;
        if (this.$route.query.filterExpressions) this.filterExpressions = this.$route.query.filterExpressions.split('|');
        if (this.$route.query.sortingExpressions) this.sortingExpressions = this.$route.query.sortingExpressions.split('|');
        this.fetchDepartments();
    }
};
</script>
