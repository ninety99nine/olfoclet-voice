<template>

    <div class="min-h-screen bg-gray-50 pt-8 p-4 sm:p-8 space-y-4 sm:space-y-6">

        <template v-if="showTable">

            <!-- Page Header -->
            <div class="space-y-1">

                <div class="flex items-end space-x-2">

                    <UserIcon size="48" stroke-width="1" class="text-gray-400" />

                    <div>
                        <h2 class="text-xl font-semibold text-gray-900 dark:text-white">User Management</h2>

                        <p class="text-sm text-gray-600 dark:text-neutral-400">
                            Manage all users across organizations
                        </p>
                    </div>

                </div>

                <div class="flex justify-end">

                    <Button type="primary" size="md" :leftIcon="Plus" leftIconSize="20" :action="showAddUsersModal">
                        <span>Add User</span>
                    </Button>

                </div>

            </div>

            <div class="bg-white dark:bg-neutral-800 border border-gray-200 dark:border-neutral-700 rounded-xl">

                <div class="p-4 sm:p-6">

                    <div class="flex justify-between items-center mb-4">

                        <div>
                            <h3 class="text-2xl font-semibold text-gray-900 dark:text-white mb-2">All Users</h3>
                            <p class="text-sm text-gray-500 dark:text-neutral-400">
                                View and manage all users on the Telcoflo platform
                            </p>
                        </div>

                    </div>

                    <!-- Users Table -->
                    <Table
                        @search="search"
                        resource="users"
                        :columns="columns"
                        :perPage="perPage"
                        @paginate="paginate"
                        @refresh="fetchUsers"
                        :searchTerm="searchTerm"
                        :pagination="pagination"
                        :isLoading="isLoadingUsers"
                        @updatedColumns="updatedColumns"
                        @updatedFilters="updatedFilters"
                        @updatedSorting="updatedSorting"
                        @updatedPerPage="updatedPerPage"
                        :filterExpressions="filterExpressions"
                        :sortingExpressions="sortingExpressions">

                        <!-- Select Action -->
                        <template #belowToolbar>

                            <div :class="[{ 'hidden' : totalCheckedRows == 0 }, 'bg-gray-50 border flex items-center mb-2 p-4 rounded-lg shadow space-x-2']">

                                <span class="text-sm">Actions: </span>

                                <Dropdown
                                    triggerSize="sm"
                                    :options="dropdownOptions"
                                    :triggerText="`Select Action (${totalCheckedRows} selected)`">
                                </Dropdown>

                            </div>

                        </template>

                        <!-- Table Head -->
                        <template #head>

                            <tr class="border-b border-gray-200">

                                <th scope="col" class="whitespace-nowrap align-top px-4 py-4">
                                    <Input type="checkbox" v-model="selectAll" />
                                </th>

                                <template v-for="(column, index) in columns" :key="index">
                                    <th v-if="column.active" scope="col" :class="['whitespace-nowrap align-top pr-4 py-4', { 'text-center' : ['Organizations'].includes(column.name) }]">
                                        {{ column.name }}
                                    </th>
                                </template>

                                <th scope="col" class="whitespace-nowrap align-top pr-4 py-4">Actions</th>

                            </tr>

                        </template>

                        <!-- Table Body -->
                        <template #body>

                            <tr @click.stop="onView(user)" v-for="user in users" :key="user.id" :class="[checkedRows[user.id] ? 'bg-blue-50' : 'bg-white hover:bg-gray-50', 'group cursor-pointer border-b border-gray-200']">

                                <td @click.stop class="whitespace-nowrap align-top px-4 py-4">
                                    <Input type="checkbox" v-model="checkedRows[user.id]" />
                                </td>

                                <template v-for="(column, columnIndex) in columns" :key="columnIndex">

                                    <template v-if="column.active">

                                        <!-- Name -->
                                        <td v-if="column.name == 'Name'" class="whitespace-nowrap align-center pr-4 py-4">
                                            <div class="font-medium text-sm">{{ user.name }}</div>
                                        </td>

                                        <!-- Email -->
                                        <td v-if="column.name == 'Email'" class="whitespace-nowrap align-center pr-4 py-4">
                                            <span>{{ user.email }}</span>
                                        </td>

                                        <!-- Organizations -->
                                        <td v-if="column.name == 'Organizations'" class="whitespace-nowrap align-center text-center pr-4 py-4">
                                            <span>{{ user.organizations_count }}</span>
                                        </td>

                                        <!-- Created -->
                                        <td v-else-if="column.name == 'Created Date'" class="whitespace-nowrap align-center pr-4 py-4">
                                            <div class="flex space-x-1 items-center">
                                                <span>{{ formattedDatetime(user.createdAt) }}</span>
                                                <Popover placement="top" class="opacity-0 group-hover:opacity-100" :content="formattedRelativeDate(user.createdAt)" />
                                            </div>
                                        </td>

                                    </template>

                                </template>

                                <td class="align-top pr-4 py-4 flex items-center space-x-1">
                                    <Button type="outline" size="xs" :leftIcon="Pencil" leftIconSize="12" :action="() => showUpdateUserModal(user)" />
                                    <Button type="outlineDanger" size="xs" :leftIcon="Trash2" leftIconSize="12" :action="() => showDeleteUserModal(user)" />
                                </td>

                            </tr>

                        </template>

                    </Table>

                </div>

            </div>

        </template>

        <!-- No Users -->
        <div v-else class="select-none w-full flex justify-center py-16">

            <div class="max-w-sm w-full bg-white shadow-sm rounded-xl p-6 flex flex-col items-center text-center space-y-4 border border-dashed border-gray-200">

                <UserIcon size="48" class="text-gray-400" />

                <h2 class="text-2xl font-bold text-gray-800">No Users Yet</h2>
                <p class="text-sm text-gray-500">Add your first user to start managing the platform.</p>

                <Button type="primary" size="md" :leftIcon="Plus" leftIconSize="20" :action="showAddUsersModal">
                    <span>Add User</span>
                </Button>

            </div>

        </div>

        <!-- Modals -->
        <AddUserModal ref="addUserModal" @created="fetchUsers" />
        <UpdateUserModal ref="updateUserModal" @updated="fetchUsers" />
        <DeleteUserModal ref="deleteUserModal" @deleted="onUserDeleted" />
        <DeleteUsersModal ref="deleteUsersModal" @deleted="onUsersDeleted" />

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
    import AddUserModal from '@Pages/users/components/AddUserModal.vue';
    import UpdateUserModal from '@Pages/users/components/UpdateUserModal.vue';
    import DeleteUserModal from '@Pages/users/components/DeleteUserModal.vue';
    import DeleteUsersModal from '@Pages/users/components/DeleteUsersModal.vue';
    import { formattedDatetime, formattedRelativeDate } from '@Utils/dateUtils.js';
    import { Plus, Pencil, Trash2, UserIcon, Building, ExternalLink } from 'lucide-vue-next';

    export default {
        inject: ['formState', 'notificationState'],
        components: {
            Pill, Modal, Input, Button, Popover, Dropdown, Table, Building,
            AddUserModal, UpdateUserModal, DeleteUserModal, DeleteUsersModal, UserIcon
        },
        data() {
            return {
                Plus,
                Pencil,
                Trash2,
                Building,
                users: [],
                ExternalLink,
                perPage: '15',
                checkedRows: [],
                pagination: null,
                searchTerm: null,
                selectAll: false,
                filterExpressions: [],
                sortingExpressions: [],
                isLoadingUsers: false,
                columns: this.prepareColumns(),
                dropdownOptions: [
                    {
                        icon: Trash2,
                        label: 'Delete',
                        action: this.showDeleteUsersModal,
                    }
                ],
            }
        },
        watch: {
            selectAll(newValue) {
                this.checkedRows = this.users.reduce((acc, user) => {
                    acc[user.id] = newValue;
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
                return this.isLoadingUsers || (this.pagination ?? {}).meta?.total > 0 || this.hasSearchTerm || this.hasFilterExpressions || this.hasSortingExpressions;
            }
        },
        methods: {
            formattedDatetime: formattedDatetime,
            formattedRelativeDate: formattedRelativeDate,
            prepareColumns() {
                const columnNames = ['Name', 'Email', 'Organizations', 'Created Date'];
                const defaultColumnNames  = ['Name', 'Email', 'Organizations', 'Created Date'];

                return columnNames.map(name => ({
                    name,
                    active: defaultColumnNames.includes(name),
                    priority: defaultColumnNames.includes(name)
                }));
            },
            showAddUsersModal() {
                this.$refs.addUserModal.showModal();
            },
            showUpdateUserModal(user) {
                this.$refs.updateUserModal.showModal(user);
            },
            showDeleteUserModal(user) {
                this.$refs.deleteUserModal.showModal(user);
            },
            showDeleteUsersModal() {
                const selectedIds = this.users.filter(u => this.checkedRows[u.id]).map(u => u.id);
                this.$refs.deleteUsersModal.showModal(selectedIds);
            },
            onUserDeleted(id) {
                this.users = this.users.filter(u => u.id !== id);
                if (this.users.length === 0) this.fetchUsers();
            },
            onUsersDeleted(deletedIds = []) {
                deletedIds.forEach(id => {
                    if (this.checkedRows[id] !== undefined) {
                        this.checkedRows[id] = false;
                    }
                });
                this.selectAll = false;
                this.users = this.users.filter(user => !deletedIds.includes(user.id));
                if (this.users.length === 0) {
                    this.fetchUsers();
                }
            },
            paginate(url) {
                this.fetchUsers(url);
            },
            search(searchTerm) {
                this.searchTerm = searchTerm;
                this.fetchUsers();
            },
            updatedColumns(columns) {
                this.columns = columns;
            },
            updatedFilters(filters) {
                const newFilterExpressions = filters.map((filter) => filter.expression);
                if(!isEqual(this.filterExpressions, newFilterExpressions)) {
                    this.filterExpressions = newFilterExpressions;
                    this.fetchUsers();
                }
            },
            updatedSorting(sorting) {
                const newSortingExpressions = sorting.map((sort) => sort.expression);
                if(!isEqual(this.sortingExpressions, newSortingExpressions)) {
                    this.sortingExpressions = newSortingExpressions;
                    this.fetchUsers();
                }
            },
            updatedPerPage(perPage) {
                this.perPage = perPage;
                this.fetchUsers();
            },
            async fetchUsers(url = null) {
                try {
                    this.isLoadingUsers = true;
                    url = url ?? '/api/users';
                    let config = {
                        params: {
                            'per_page': this.perPage,
                            '_countable_relationships': 'organizations'
                        }
                    }
                    if(this.hasSearchTerm) config.params['search'] = this.searchTerm;
                    if(this.hasFilterExpressions) {
                        config.params['_filters'] = this.filterExpressions.join('|');
                    }
                    if(this.hasSortingExpressions) {
                        config.params['_sort'] = this.sortingExpressions.join('|');
                    }
                    const response = await axios.get(url, config);
                    this.pagination = response.data;
                    this.users = this.pagination.data;
                    this.checkedRows = this.users.reduce((acc, user) => {
                        acc[user.id] = false;
                        return acc;
                    }, {});
                } catch (error) {
                    const message = error?.response?.data?.message || error?.message || 'Something went wrong while fetching users';
                    this.notificationState.showWarningNotification(message);
                    console.error('Failed to fetch users:', error);
                } finally {
                    this.isLoadingUsers = false;
                }
            }
        },
        created() {
            this.isLoadingUsers = true;
            this.searchTerm = this.$route.query.searchTerm;
            if(this.$route.query.filterExpressions) this.filterExpressions = this.$route.query.filterExpressions.split('|');
            if(this.$route.query.sortingExpressions) this.sortingExpressions = this.$route.query.sortingExpressions.split('|');
            this.fetchUsers();
        }
    };

</script>

