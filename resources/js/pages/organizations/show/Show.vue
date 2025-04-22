<template>

    <div class="min-h-screen bg-gray-50 pt-8 p-4 sm:p-8 space-y-4 sm:space-y-6">

        <template v-if="showTable">

            <!-- Page Header -->
            <div class="space-y-8">

                <div class="flex items-end space-x-2">

                    <Building size="48" stroke-width="1" class="text-gray-400" />

                    <div>
                        <h2 class="text-xl font-semibold text-gray-900 dark:text-white">Organization Management</h2>

                        <p class="text-sm text-gray-600 dark:text-neutral-400">
                            Manage all organizations and their settings
                        </p>
                    </div>

                </div>

                <div class="flex justify-between">

                    <!-- Tabs -->
                    <Tabs v-model="activeTab" :tabs="tabs" />

                    <Button type="primary" size="md" :leftIcon="Plus" leftIconSize="20" :action="showAddOrganizationsModal">
                        <span>Add Organization</span>
                    </Button>

                </div>

            </div>

            <div class="bg-white dark:bg-neutral-800 border border-gray-200 dark:border-neutral-700 rounded-xl">

                <div class="p-4 sm:p-6">

                    <div class="flex justify-between items-center mb-4">

                        <div>
                            <h3 class="text-2xl font-semibold text-gray-900 dark:text-white mb-2">All Organizations</h3>
                            <p class="text-sm text-gray-500 dark:text-neutral-400">
                                View and manage all organizations using the Telcoflo platform
                            </p>
                        </div>

                    </div>

                    <!-- Organizations Table -->
                    <Table
                        @search="search"
                        :columns="columns"
                        :perPage="perPage"
                        @paginate="paginate"
                        :searchTerm="searchTerm"
                        :pagination="pagination"
                        resource="organizations"
                        @refresh="fetchOrganizations"
                        @updatedColumns="updatedColumns"
                        @updatedFilters="updatedFilters"
                        @updatedSorting="updatedSorting"
                        @updatedPerPage="updatedPerPage"
                        :isLoading="isLoadingOrganizations"
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

                                <!-- Checkbox -->
                                <th scope="col" class="whitespace-nowrap align-top px-4 py-4">

                                    <Input
                                        type="checkbox"
                                        v-model="selectAll">
                                    </Input>

                                </th>

                                <!-- Table Column Names -->
                                <template v-for="(column, index) in columns" :key="index">

                                    <th v-if="column.active" scope="col" :class="['whitespace-nowrap align-top pr-4 py-4', { 'text-center' : ['Users'].includes(column.name) }]">
                                        {{ column.name }}
                                    </th>

                                </template>

                                <!-- Actions -->
                                <th scope="col" class="whitespace-nowrap align-top pr-4 py-4">Actions</th>

                            </tr>

                        </template>

                        <!-- Table Body -->
                        <template #body>

                            <tr @click.stop="onView(organization)" v-for="organization in organizations" :key="organization.id" :class="[checkedRows[organization.id] ? 'bg-blue-50' : 'bg-white hover:bg-gray-50', 'group cursor-pointer border-b border-gray-200']">

                                <!-- Checkbox -->
                                <td @click.stop class="whitespace-nowrap align-top px-4 py-4">

                                    <Input
                                        type="checkbox"
                                        v-model="checkedRows[organization.id]">
                                    </Input>

                                </td>

                                <template v-for="(column, columnIndex) in columns" :key="columnIndex">

                                    <template v-if="column.active">

                                        <!-- Organization -->
                                        <td v-if="column.name == 'Organization'" class="whitespace-nowrap align-center pr-4 py-4">
                                            <div class="flex items-center space-x-2">
                                                <img src="https://placehold.co/21x24" class="size-8 rounded-lg shrink-0" alt="Logo" />
                                                <div class="whitespace-nowrap font-medium text-sm">{{ organization.name }}</div>
                                            </div>
                                        </td>

                                        <!-- Country -->
                                        <td v-if="column.name == 'Country'" class="whitespace-nowrap align-center pr-4 py-4">
                                            <div class="flex items-center space-x-2">
                                                <img
                                                    v-if="organization.country"
                                                    :src="`/svgs/country-flags/${organization.country.toLowerCase()}.svg`"
                                                    :alt="organization.country"
                                                    class="w-5 h-4 rounded-sm object-cover"
                                                >
                                                <span>{{ getCountryName(organization.country) }}</span>
                                            </div>
                                        </td>

                                        <!-- Status -->
                                        <td v-if="column.name == 'Status'" class="whitespace-nowrap align-center pr-4 py-4">
                                            <Pill :type="organization.active ? 'success' : 'warning'" size="xs">{{ organization.active ? 'Active' : 'Inactive' }}</Pill>
                                        </td>

                                        <!-- Users -->
                                        <td v-if="column.name == 'Users'" class="whitespace-nowrap align-center text-center pr-4 py-4">
                                            <span>{{ organization.users_count }}/{{ organization.seats }}</span>
                                        </td>

                                        <!-- Created Date -->
                                        <td v-else-if="column.name == 'Created Date'" class="whitespace-nowrap align-center pr-4 py-4">
                                            <div class="flex space-x-1 items-center">
                                                <span>{{ formattedDatetime(organization.createdAt) }}</span>
                                                <Popover
                                                    placement="top"
                                                    class="opacity-0 group-hover:opacity-100"
                                                    :content="formattedRelativeDate(organization.createdAt)">
                                                </Popover>
                                            </div>
                                        </td>

                                    </template>

                                </template>

                                <!-- Actions -->
                                <td class="align-top pr-4 py-4 flex items-center space-x-1">

                                    <Button type="outline" size="xs" :leftIcon="ExternalLink" leftIconSize="12" :action="() => onViewLogin(organization)"></Button>
                                    <Button type="outline" size="xs" :leftIcon="UserPlus" leftIconSize="12"></Button>
                                    <Button type="outline" size="xs" :leftIcon="Pencil" leftIconSize="12" :action="() => showUpdateOrganizationModal(organization)"></Button>
                                    <Button type="outlineDanger" size="xs" :leftIcon="Trash2" leftIconSize="12" :action="() => showDeleteOrganizationModal(organization)"></Button>

                                </td>

                            </tr>

                        </template>

                    </Table>

                </div>

            </div>

        </template>

        <!-- No Organizations -->
        <div v-else class="select-none w-full flex justify-center py-16">

            <div class="max-w-sm w-full bg-white shadow-sm rounded-xl p-6 flex flex-col items-center text-center space-y-4 border border-dashed border-gray-200">

                <!-- Icon -->
                <Building size="48" class="text-gray-400" />

                <!-- Title -->
                <h2 class="text-2xl font-bold text-gray-800">
                    No Organizations Yet
                </h2>

                <!-- Description -->
                <p class="text-sm text-gray-500">
                    Add your first organization to manage agents, assign roles, and start handling calls.
                </p>

                <!-- Add Organization Button -->
                <Button type="primary" size="md" :leftIcon="Plus" leftIconSize="20" :action="showAddOrganizationsModal">
                    <span>Add Organization</span>
                </Button>

            </div>

        </div>

        <!-- Add Organization -->
        <AddOrganizationModal
            ref="addOrganizationModal"
            @created="fetchOrganizations">
        </AddOrganizationModal>

        <UpdateOrganizationModal
            ref="updateOrganizationModal"
            @updated="fetchOrganizations">
        </UpdateOrganizationModal>

        <DeleteOrganizationModal
            ref="deleteOrganizationModal"
            @deleted="onOrganizationDeleted">
        </DeleteOrganizationModal>

        <DeleteOrganizationsModal
            ref="deleteOrganizationsModal"
            @deleted="onOrganizationsDeleted">
        </DeleteOrganizationsModal>

    </div>

</template>

<script>

    import axios from 'axios';
    import isEqual from 'lodash/isEqual';
    import Tabs from '@Partials/Tabs.vue';
    import Pill from '@Partials/Pill.vue';
    import Modal from '@Partials/Modal.vue';
    import Input from '@Partials/Input.vue';
    import Button from '@Partials/Button.vue';
    import Popover from '@Partials/Popover.vue';
    import Dropdown from '@Partials/Dropdown.vue';
    import Table from '@Partials/table/Table.vue';
    import { getCountryName } from '@Utils/generalUtils.js';
    import { formattedDatetime, formattedRelativeDate } from '@Utils/dateUtils.js';
    import AddOrganizationModal from '@Pages/organizations/components/AddOrganizationModal.vue';
    import UpdateOrganizationModal from '@Pages/organizations/components/UpdateOrganizationModal.vue';
    import DeleteOrganizationModal from '@Pages/organizations/components/DeleteOrganizationModal.vue';
    import DeleteOrganizationsModal from '@Pages/organizations/components/DeleteOrganizationsModal.vue';
    import { Box, Plus, ShieldCheck, Pencil, Trash2, UserPlus, Building, ExternalLink } from 'lucide-vue-next';

    export default {
        inject: ['formState', 'notificationState'],
        components: {
            Tabs, Pill, Modal, Input, Button, Popover, Dropdown, Table, Building,
            AddOrganizationModal, UpdateOrganizationModal, DeleteOrganizationModal, DeleteOrganizationsModal
        },
        data() {
            return {
                Plus,
                Pencil,
                Trash2,
                UserPlus,
                Building,
                ExternalLink,
                perPage: '15',
                getCountryName,
                checkedRows: [],
                pagination: null,
                searchTerm: null,
                selectAll: false,
                organizations: [],
                filterExpressions: [],
                sortingExpressions: [],
                activeTab: 'organizations',
                isLoadingOrganizations: false,
                columns: this.prepareColumns(),
                tabs: [
                    { label: 'Organizations', value: 'organizations', icon: Building },
                    { label: 'Subscription Plans', value: 'subscriptions', icon: Box },
                    { label: 'Security', value: 'security', icon: ShieldCheck },
                ],
                dropdownOptions: [
                    {
                        icon: Trash2,
                        label: 'Delete',
                        action: this.showDeleteOrganizationsModal,
                    }
                ],
            }
        },
        watch: {
            selectAll(newValue) {
                this.checkedRows = this.organizations.reduce((acc, organization) => {
                    acc[organization.id] = newValue;
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
                return this.isLoadingOrganizations || (this.pagination ?? {}).meta?.total > 0 || this.hasSearchTerm || this.hasFilterExpressions || this.hasSortingExpressions;
            }
        },
        methods: {
            formattedDatetime: formattedDatetime,
            formattedRelativeDate: formattedRelativeDate,
            prepareColumns() {
                const columnNames = ['Organization', 'Country', 'Status', 'Users', 'Created Date'];
                const defaultColumnNames  = ['Organization', 'Country', 'Status', 'Users', 'Created Date'];

                return columnNames.map(name => ({
                    name,
                    active: defaultColumnNames.includes(name),
                    priority: defaultColumnNames.includes(name)
                }));
            },
            showAddOrganizationsModal() {
                this.$refs.addOrganizationModal.showModal();
            },
            showUpdateOrganizationModal(organization) {
                this.$refs.updateOrganizationModal.showModal(organization);
            },
            showDeleteOrganizationModal(organization) {
                this.$refs.deleteOrganizationModal.showModal(organization);
            },
            showDeleteOrganizationsModal() {
                const selectedIds = this.organizations.filter(o => this.checkedRows[o.id]).map(o => o.id);
                this.$refs.deleteOrganizationsModal.showModal(selectedIds);
            },
            onOrganizationDeleted(id) {
                this.organizations = this.organizations.filter(o => o.id !== id);
                if (this.organizations.length === 0) this.fetchOrganizations();
            },
            onOrganizationsDeleted(deletedIds = []) {

                // Uncheck rows
                deletedIds.forEach(id => {
                    if (this.checkedRows[id] !== undefined) {
                        this.checkedRows[id] = false;
                    }
                });

                // Reset bulk selection toggle
                this.selectAll = false;

                // Remove from local list
                this.organizations = this.organizations.filter(org => !deletedIds.includes(org.id));

                // Refresh if list is empty
                if (this.organizations.length === 0) {
                    this.fetchOrganizations();
                }

            },
            onViewLogin(organization) {
                const url = `${window.location.origin}/${organization.alias}/login`;
                window.open(url, '_blank');
            },
            paginate(url) {
                this.fetchOrganizations(url);
            },
            search(searchTerm) {
                this.searchTerm = searchTerm;
                this.fetchOrganizations();
            },
            updatedColumns(columns) {
                this.columns = columns;
            },
            updatedFilters(filters) {
                const newFilterExpressions = filters.map((filter) => filter.expression);
                if(!isEqual(this.filterExpressions, newFilterExpressions)) {
                    this.filterExpressions = newFilterExpressions;
                    this.fetchOrganizations();
                }
            },
            updatedSorting(sorting) {
                const newSortingExpressions = sorting.map((sort) => sort.expression);
                if(!isEqual(this.sortingExpressions, newSortingExpressions)) {
                    this.sortingExpressions = newSortingExpressions;
                    this.fetchOrganizations();
                }
            },
            updatedPerPage(perPage) {
                this.perPage = perPage;
                this.fetchOrganizations();
            },
            async fetchOrganizations(url = null) {

                try {

                    this.isLoadingOrganizations = true;

                    url = url ?? '/api/organizations';

                    let config = {
                        params: {
                            'per_page': this.perPage,
                            '_countable_relationships': 'users'
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
                    this.organizations = this.pagination.data;
                    this.checkedRows = this.organizations.reduce((acc, organization) => {
                        acc[organization.id] = false;
                        return acc;
                    }, {});

                } catch (error) {

                    const message = error?.response?.data?.message || error?.message || 'Something went wrong while fetching organizations';
                    this.notificationState.showWarningNotification(message);

                    console.error('Failed to fetch organizations:', error);

                } finally {

                    this.isLoadingOrganizations = false;

                }

            }
        },
        created() {
            this.isLoadingOrganizations = true;
            this.searchTerm = this.$route.query.searchTerm;
            if(this.$route.query.filterExpressions) this.filterExpressions = this.$route.query.filterExpressions.split('|');
            if(this.$route.query.sortingExpressions) this.sortingExpressions = this.$route.query.sortingExpressions.split('|');
            this.fetchOrganizations();
        }
    };

</script>
