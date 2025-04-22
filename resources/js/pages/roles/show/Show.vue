<template>

    <div class="min-h-screen bg-gray-50 pt-8 p-4 sm:p-8 space-y-4 sm:space-y-6">

        <template v-if="showTable">

            <!-- Page Header -->
            <div class="space-y-1">

                <div class="flex items-end space-x-2">
                    <Lock size="48" stroke-width="1" class="text-gray-400" />

                    <div>
                        <h2 class="text-xl font-semibold text-gray-900 dark:text-white">Role Management</h2>
                        <p class="text-sm text-gray-600 dark:text-neutral-400">
                            Manage roles for your organization
                        </p>
                    </div>
                </div>

                <div class="flex justify-end">
                    <Button type="primary" size="md" :leftIcon="Plus" leftIconSize="20" :action="showAddRoleModal">
                        <span>Add Role</span>
                    </Button>
                </div>

            </div>

            <div class="bg-white dark:bg-neutral-800 border border-gray-200 dark:border-neutral-700 rounded-xl">

                <div class="p-4 sm:p-6">

                    <div class="flex justify-between items-center mb-4">
                        <div>
                            <h3 class="text-2xl font-semibold text-gray-900 dark:text-white mb-2">All Roles</h3>
                            <p class="text-sm text-gray-500 dark:text-neutral-400">
                                View and manage roles within this organization
                            </p>
                        </div>
                    </div>

                    <!-- Roles Table -->
                    <Table
                        resource="roles"
                        @search="search"
                        :columns="columns"
                        :perPage="perPage"
                        @paginate="paginate"
                        @refresh="fetchRoles"
                        :searchTerm="searchTerm"
                        :pagination="pagination"
                        :isLoading="isLoadingRoles"
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
                                <th class="whitespace-nowrap align-top px-4 py-4">
                                    <Input type="checkbox" v-model="selectAll" />
                                </th>
                                <template v-for="(column, index) in columns" :key="index">
                                    <th v-if="column.active" scope="col" class="whitespace-nowrap align-top pr-4 py-4">
                                        {{ column.name }}
                                    </th>
                                </template>
                                <th scope="col" class="whitespace-nowrap align-top pr-4 py-4">Actions</th>
                            </tr>
                        </template>

                        <template #body>

                            <tr @click.stop="onView(role)" v-for="role in roles" :key="role.id" :class="[checkedRows[role.id] ? 'bg-blue-50' : 'bg-white hover:bg-gray-50', 'group cursor-pointer border-b border-gray-200']">

                                <!-- Checkbox -->
                                <td @click.stop class="whitespace-nowrap align-top px-4 py-4">

                                    <Input
                                        type="checkbox"
                                        v-model="checkedRows[role.id]">
                                    </Input>

                                </td>

                                <template v-for="(column, columnIndex) in columns" :key="columnIndex">

                                    <template v-if="column.name == 'Name'">
                                        <td class="whitespace-nowrap align-center pr-4 py-4">
                                            <div class="font-medium text-sm">{{ role.name }}</div>
                                        </td>
                                    </template>

                                    <template v-if="column.name == 'Oraganization'">
                                        <td class="whitespace-nowrap align-center pr-4 py-4">
                                            <!-- Country Flag -->
                                            <div
                                                v-if="role.organization"
                                                class="flex items-center space-x-2">
                                                <img src="https://placehold.co/21x24" class="size-8 rounded-lg shrink-0" alt="Logo" />
                                                <div class="whitespace-nowrap font-medium text-sm">{{ role.organization.name }}</div>
                                            </div>
                                        </td>
                                    </template>

                                    <template v-if="column.name == 'Country'">
                                        <td class="whitespace-nowrap align-center pr-4 py-4">
                                            <!-- Country Flag -->
                                            <div
                                                v-if="role.organization"
                                                class="flex items-center space-x-2">
                                                <div class="flex items-center space-x-2">
                                                    <img
                                                        v-if="role.organization.country"
                                                        :src="`/svgs/country-flags/${role.organization.country.toLowerCase()}.svg`"
                                                        :alt="role.organization.country"
                                                        class="w-5 h-4 rounded-sm object-cover"
                                                    >
                                                    <span>{{ getCountryName(role.organization.country) }}</span>
                                                </div>
                                            </div>
                                        </td>
                                    </template>

                                    <template v-if="column.name == 'Guard'">
                                        <td class="whitespace-nowrap align-center pr-4 py-4">
                                            <span>{{ role.guard_name }}</span>
                                        </td>
                                    </template>

                                    <template v-if="column.name == 'Users'">
                                        <td class="whitespace-nowrap align-center text-center pr-4 py-4">
                                            <span>{{ role.users_count }}</span>
                                        </td>
                                    </template>

                                    <template v-if="column.name == 'Created Date'">
                                        <td class="whitespace-nowrap align-center pr-4 py-4">
                                            <div class="flex space-x-1 items-center">
                                                <span>{{ formattedDatetime(role.createdAt) }}</span>
                                                <Popover placement="top" class="opacity-0 group-hover:opacity-100" :content="formattedRelativeDate(role.createdAt)" />
                                            </div>
                                        </td>
                                    </template>

                                </template>

                                <td class="align-top pr-4 py-4 flex items-center space-x-1">
                                    <Button type="outline" size="xs" :leftIcon="Pencil" leftIconSize="12" :action="() => showUpdateRoleModal(role)" />
                                    <Button type="outlineDanger" size="xs" :leftIcon="Trash2" leftIconSize="12" :action="() => showDeleteRoleModal(role)" />
                                </td>
                            </tr>
                        </template>

                    </Table>

                </div>
            </div>

        </template>

        <!-- No Roles -->
        <div v-else class="select-none w-full flex justify-center py-16">
            <div class="max-w-sm w-full bg-white shadow-sm rounded-xl p-6 flex flex-col items-center text-center space-y-4 border border-dashed border-gray-200">
                <Lock size="48" class="text-gray-400" />
                <h2 class="text-2xl font-bold text-gray-800">No Roles Yet</h2>
                <p class="text-sm text-gray-500">Add a role to manage access control.</p>
                <Button type="primary" size="md" :leftIcon="Plus" leftIconSize="20" :action="showAddRoleModal">
                    <span>Add Role</span>
                </Button>
            </div>
        </div>

        <!-- Modals -->
        <AddRoleModal ref="addRoleModal" @created="fetchRoles" />
        <UpdateRoleModal ref="updateRoleModal" @updated="fetchRoles" />
        <DeleteRoleModal ref="deleteRoleModal" @deleted="onRoleDeleted" />
        <DeleteRolesModal ref="deleteRolesModal" @deleted="onRolesDeleted" />

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
import AddRoleModal from '@Pages/roles/components/AddRoleModal.vue';
import UpdateRoleModal from '@Pages/roles/components/UpdateRoleModal.vue';
import DeleteRoleModal from '@Pages/roles/components/DeleteRoleModal.vue';
import DeleteRolesModal from '@Pages/roles/components/DeleteRolesModal.vue';
import { formattedDatetime, formattedRelativeDate } from '@Utils/dateUtils.js';
import { Plus, Pencil, Trash2, Lock } from 'lucide-vue-next';

export default {
    inject: ['formState', 'notificationState'],
    components: {
        Pill, Modal, Input, Button, Popover, Dropdown, Table,
        AddRoleModal, UpdateRoleModal, DeleteRoleModal, DeleteRolesModal, Lock
    },
    data() {
        return {
            Plus,
            Pencil,
            Trash2,
            Lock,
            roles: [],
            perPage: '15',
            getCountryName,
            checkedRows: [],
            pagination: null,
            searchTerm: null,
            selectAll: false,
            filterExpressions: [],
            sortingExpressions: [],
            isLoadingRoles: false,
            columns: this.prepareColumns(),
            dropdownOptions: [
                {
                    icon: Trash2,
                    label: 'Delete',
                    action: this.showDeleteRolesModal,
                }
            ]
        }
    },
    watch: {
        selectAll(newValue) {
            this.checkedRows = this.roles.reduce((acc, role) => {
                acc[role.id] = newValue;
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
            return this.isLoadingRoles || (this.pagination ?? {}).meta?.total > 0 || this.hasSearchTerm || this.hasFilterExpressions || this.hasSortingExpressions;
        }
    },
    methods: {
        formattedDatetime,
        formattedRelativeDate,
        prepareColumns() {
            const columnNames = ['Name', 'Oraganization', 'Country', 'Guard', 'Users', 'Created Date'];
            const defaultColumnNames = ['Name', 'Oraganization', 'Country', 'Guard', 'Users', 'Created Date'];
            return columnNames.map(name => ({
                name,
                active: defaultColumnNames.includes(name),
                priority: defaultColumnNames.includes(name)
            }));
        },
        showAddRoleModal() {
            this.$refs.addRoleModal.showModal();
        },
        showUpdateRoleModal(role) {
            this.$refs.updateRoleModal.showModal(role);
        },
        showDeleteRoleModal(role) {
            this.$refs.deleteRoleModal.showModal(role);
        },
        showDeleteRolesModal() {
            const selectedIds = this.roles.filter(r => this.checkedRows[r.id]).map(r => r.id);
            this.$refs.deleteRolesModal.showModal(selectedIds);
        },
        onRoleDeleted(id) {
            this.roles = this.roles.filter(r => r.id !== id);
            if (this.roles.length === 0) this.fetchRoles();
        },
        onRolesDeleted(deletedIds = []) {
            deletedIds.forEach(id => {
                if (this.checkedRows[id] !== undefined) {
                    this.checkedRows[id] = false;
                }
            });
            this.selectAll = false;
            this.roles = this.roles.filter(role => !deletedIds.includes(role.id));
            if (this.roles.length === 0) {
                this.fetchRoles();
            }
        },
        paginate(url) {
            this.fetchRoles(url);
        },
        search(searchTerm) {
            this.searchTerm = searchTerm;
            this.fetchRoles();
        },
        updatedColumns(columns) {
            this.columns = columns;
        },
        updatedFilters(filters) {
            const newFilterExpressions = filters.map(f => f.expression);
            if (!isEqual(this.filterExpressions, newFilterExpressions)) {
                this.filterExpressions = newFilterExpressions;
                this.fetchRoles();
            }
        },
        updatedSorting(sorting) {
            const newSortingExpressions = sorting.map(s => s.expression);
            if (!isEqual(this.sortingExpressions, newSortingExpressions)) {
                this.sortingExpressions = newSortingExpressions;
                this.fetchRoles();
            }
        },
        updatedPerPage(perPage) {
            this.perPage = perPage;
            this.fetchRoles();
        },
        async fetchRoles(url = null) {
            try {
                this.isLoadingRoles = true;
                url = url ?? '/api/roles';
                let config = {
                    params: {
                        'per_page': this.perPage,
                        '_relationships': 'organization',
                        '_countable_relationships': 'users'
                    }
                }
                if (this.hasSearchTerm) config.params['search'] = this.searchTerm;
                if (this.hasFilterExpressions) config.params['_filters'] = this.filterExpressions.join('|');
                if (this.hasSortingExpressions) config.params['_sort'] = this.sortingExpressions.join('|');

                const response = await axios.get(url, config);
                this.pagination = response.data;
                this.roles = this.pagination.data;
                this.checkedRows = this.roles.reduce((acc, role) => {
                    acc[role.id] = false;
                    return acc;
                }, {});
            } catch (error) {
                const message = error?.response?.data?.message || error?.message || 'Something went wrong while fetching roles';
                this.notificationState.showWarningNotification(message);
                console.error('Failed to fetch roles:', error);
            } finally {
                this.isLoadingRoles = false;
            }
        }
    },
    created() {
        this.isLoadingRoles = true;
        this.searchTerm = this.$route.query.searchTerm;
        if (this.$route.query.filterExpressions) this.filterExpressions = this.$route.query.filterExpressions.split('|');
        if (this.$route.query.sortingExpressions) this.sortingExpressions = this.$route.query.sortingExpressions.split('|');
        this.fetchRoles();
    }
};

</script>
