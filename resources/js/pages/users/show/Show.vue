<template>
    <div class="select-none min-h-screen bg-gray-50 pt-8 p-4 sm:p-8 space-y-4 sm:space-y-6">
        <template v-if="showTable">
            <!-- Page Header -->
            <div class="flex justify-between">
                <div class="flex items-end space-x-2">
                    <UserRoundIcon size="48" stroke-width="1" class="text-gray-400" />
                    <div>
                        <h2 class="text-xl font-semibold text-gray-900 dark:text-white">Users</h2>
                        <p class="text-sm text-gray-600 dark:text-neutral-400">
                            {{ organizationId ? 'Manage users in your organization' : 'Manage all users across organizations' }}
                        </p>
                    </div>
                </div>
                <div class="flex justify-end">
                    <Button type="primary" size="md" :leftIcon="Plus" leftIconSize="20" :action="showAddUsersModal">
                        <span>Add User</span>
                    </Button>
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

                <!-- Table Body -->
                <template #body>
                    <tr v-for="(row, rowIndex) in displayRows" :key="row.key" :class="[checkedRows[row.key] ? 'bg-blue-50' : 'bg-white hover:bg-gray-50', 'group cursor-pointer text-sm border-b border-gray-200']">

                        <td class="whitespace-nowrap align-center px-4 py-4">
                            <Input type="checkbox" v-model="checkedRows[row.key]" />
                        </td>

                        <template v-for="(column, columnIndex) in columns" :key="columnIndex">
                            <template v-if="column.active">
                                <!-- Name -->
                                <td v-if="column.name == 'Name'" class="whitespace-nowrap align-center pr-4 py-4">
                                    <div v-if="row.showName">{{ row.user.name }}</div>
                                </td>
                                <!-- Email -->
                                <td v-if="column.name == 'Email'" class="whitespace-nowrap align-center pr-4 py-4">
                                    <span v-if="row.showName">{{ row.user.email }}</span>
                                </td>
                                <!-- Organization (super admin mode only) -->
                                <td v-if="column.name == 'Organization' && !organizationId" class="whitespace-nowrap align-center pr-4 py-4">
                                    <div class="flex items-center space-x-2">
                                        <img v-if="row.organization" src="https://placehold.co/21x24" class="size-8 rounded-lg shrink-0" alt="Logo" />
                                        <div class="whitespace-nowrap">{{ row.organization ? row.organization.name : '—' }}</div>
                                    </div>
                                </td>
                                <!-- Role -->
                                <td v-if="column.name == 'Role'" class="whitespace-nowrap align-center pr-4 py-4">
                                    <template v-if="row.role">
                                        <Pill :type="['admin', 'super_admin'].includes(row.role.name) ? 'primary' : 'success'" size="xs">
                                            {{ row.role.name }}
                                        </Pill>
                                    </template>
                                    <span v-else class="text-gray-500">—</span>
                                </td>
                                <!-- Created -->
                                <td v-if="column.name == 'Created Date'" class="whitespace-nowrap align-center pr-4 py-4">
                                    <div class="flex space-x-1 items-center">
                                        <span>{{ formattedDatetime(row.user.created_at) }}</span>
                                        <Popover placement="top" class="opacity-0 group-hover:opacity-100" :content="formattedRelativeDate(row.user.created_at)" />
                                    </div>
                                </td>
                            </template>
                        </template>
                        <td class="h-16 pr-4 py-4 flex align-middle items-center space-x-2">
                            <Button type="bare" size="xs" :leftIcon="Pencil" leftIconSize="16" :action="() => showUpdateUserModal(row.user)" />
                            <Button type="bareDanger" size="xs" :leftIcon="Trash2" leftIconSize="16" :action="() => showDeleteUserModal(row.user)" />
                        </td>
                    </tr>
                </template>
            </Table>
        </template>

        <!-- No Users -->
        <div v-else class="select-none w-full flex justify-center py-16">
            <div class="max-w-sm w-full bg-white shadow-sm rounded-xl p-6 flex flex-col items-center text-center space-y-4 border border-dashed border-gray-200">
                <UserRoundIcon size="48" class="text-gray-400" />
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
    import { useAuthStore } from '@Stores/auth-store.js';
    import AddUserModal from '@Pages/users/components/AddUserModal.vue';
    import UpdateUserModal from '@Pages/users/components/UpdateUserModal.vue';
    import DeleteUserModal from '@Pages/users/components/DeleteUserModal.vue';
    import DeleteUsersModal from '@Pages/users/components/DeleteUsersModal.vue';
    import { formattedDatetime, formattedRelativeDate } from '@Utils/dateUtils.js';
    import { Plus, Pencil, Trash2, UserRoundIcon, Building, ExternalLink } from 'lucide-vue-next';

    export default {
        inject: ['formState', 'notificationState'],
        components: {
            Pill, Modal, Input, Button, Popover, Dropdown, Table, Building,
            AddUserModal, UpdateUserModal, DeleteUserModal, DeleteUsersModal, UserRoundIcon
        },
        data() {
            return {
                Plus,
                Pencil,
                Trash2,
                Building,
                users: [],
                columns: [],
                ExternalLink,
                perPage: '15',
                displayRows: [],
                checkedRows: [],
                pagination: null,
                searchTerm: null,
                selectAll: false,
                filterExpressions: [],
                isLoadingUsers: false,
                sortingExpressions: [],
                dropdownOptions: [
                    {
                        icon: Trash2,
                        label: 'Delete',
                        action: this.showDeleteUsersModal,
                    }
                ],
            };
        },
        computed: {
            authState() {
                return useAuthStore();
            },
            organizationId() {
                return this.$route.query.organization_id || null;
            },
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
        watch: {
            selectAll(newValue) {
                this.checkedRows = this.displayRows.reduce((acc, row) => {
                    acc[row.key] = newValue;
                    return acc;
                }, {});
            },
            users(newUsers) {
                this.updateDisplayRows();
            }
        },
        methods: {
            formattedDatetime: formattedDatetime,
            formattedRelativeDate: formattedRelativeDate,
            prepareColumns() {
                const baseColumns = ['Name', 'Email', 'Role', 'Created Date'];
                const superAdminColumns = ['Name', 'Email', 'Organization', 'Role', 'Created Date'];
                const columnNames = this.organizationId ? baseColumns : superAdminColumns;
                const defaultColumnNames = columnNames;

                return columnNames.map(name => ({
                    name,
                    active: defaultColumnNames.includes(name),
                    priority: defaultColumnNames.includes(name)
                }));
            },
            updateDisplayRows() {
                this.displayRows = [];

                if (this.organizationId) {
                    // Non-super admin mode: one row per user with a single role
                    this.users.forEach(user => {
                        const role = user.roles && user.roles.length > 0 ? user.roles[0] : null;
                        this.displayRows.push({
                            key: user.id,
                            user: user,
                            role: role,
                            showName: true,
                        });
                    });
                } else {
                    // Super admin mode: one row per role, with organization from roles.organization
                    this.users.forEach(user => {
                        if (user.roles && user.roles.length > 0) {
                            user.roles.forEach((role, index) => {
                                const organization = role.organization || null;
                                this.displayRows.push({
                                    key: `${user.id}-${role.id}`,
                                    user: user,
                                    role: role,
                                    organization: organization,
                                    showName: index === 0, // Show name and email only on the first row
                                });
                            });
                        } else {
                            // Users with no roles
                            this.displayRows.push({
                                key: user.id,
                                user: user,
                                role: null,
                                organization: null,
                                showName: true,
                            });
                        }
                    });
                }
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
                const selectedIds = Object.keys(this.checkedRows)
                    .filter(key => this.checkedRows[key])
                    .map(key => {
                        const [userId] = key.split('-');
                        return userId;
                    });
                this.$refs.deleteUsersModal.showModal(selectedIds);
            },
            onUserDeleted(id) {
                this.users = this.users.filter(u => u.id !== id);
                if (this.users.length === 0) this.fetchUsers();
            },
            onUsersDeleted(deletedIds = []) {
                deletedIds.forEach(id => {
                    Object.keys(this.checkedRows).forEach(key => {
                        if (key.startsWith(`${id}-`) || key === id) {
                            this.checkedRows[key] = false;
                        }
                    });
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
                if (!isEqual(this.filterExpressions, newFilterExpressions)) {
                    this.filterExpressions = newFilterExpressions;
                    this.fetchUsers();
                }
            },
            updatedSorting(sorting) {
                const newSortingExpressions = sorting.map((sort) => sort.expression);
                if (!isEqual(this.sortingExpressions, newSortingExpressions)) {
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
                            '_relationships': 'roles' + (!this.organizationId ? '.organization' : ''),
                            '_countable_relationships': 'organizations',
                        }
                    };
                    if (this.organizationId) {
                        config.params['organization_id'] = this.organizationId;
                    }
                    if (this.hasSearchTerm) config.params['search'] = this.searchTerm;
                    if (this.hasFilterExpressions) {
                        config.params['_filters'] = this.filterExpressions.join('|');
                    }
                    if (this.hasSortingExpressions) {
                        config.params['_sort'] = this.sortingExpressions.join('|');
                    }
                    const response = await axios.get(url, config);
                    this.pagination = response.data;
                    this.users = this.pagination.data;
                    this.checkedRows = this.displayRows.reduce((acc, row) => {
                        acc[row.key] = false;
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
        mounted() {
            this.columns = this.prepareColumns();
        },
        created() {
            this.isLoadingUsers = true;
            this.searchTerm = this.$route.query.searchTerm;
            if (this.$route.query.filterExpressions) this.filterExpressions = this.$route.query.filterExpressions.split('|');
            if (this.$route.query.sortingExpressions) this.sortingExpressions = this.$route.query.sortingExpressions.split('|');
            this.fetchUsers();
        }
    };
</script>
