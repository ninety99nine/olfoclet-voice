<template>
    <div class="select-none min-h-screen bg-gray-50 pt-8 p-4 sm:p-8 space-y-4 sm:space-y-6">
        <template v-if="showTable">
            <!-- Page Header -->
            <div class="flex justify-between">
                <div class="flex items-end space-x-2">
                    <BotIcon size="48" stroke-width="1" class="text-gray-400" />
                    <div>
                        <h2 class="text-xl font-semibold text-gray-900 dark:text-white">Copilots</h2>
                        <p class="text-sm text-gray-600 dark:text-neutral-400">
                            Manage AI Copilots for your organization
                        </p>
                    </div>
                </div>
                <div class="flex justify-end">
                    <Button type="primary" size="md" :leftIcon="Plus" leftIconSize="20" :action="showAddCopilotModal">
                        <span>Add Copilot</span>
                    </Button>
                </div>
            </div>

            <!-- Copilots Table -->
            <Table
                @search="search"
                resource="copilots"
                :columns="columns"
                :perPage="perPage"
                @paginate="paginate"
                @refresh="fetchCopilots"
                :searchTerm="searchTerm"
                :pagination="pagination"
                :isLoading="isLoadingCopilots"
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
                            <th v-if="column.active" scope="col" :class="['whitespace-nowrap align-top font-semibold pr-4 py-2.5', { 'text-center' : ['Knowledge Bases', 'Users'].includes(column.name) }]">
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
                        :key="copilot.id"
                        @click.stop="onView(copilot)"
                        v-for="copilot in copilots"
                        :class="[checkedRows[copilot.id] ? 'bg-blue-50' : 'bg-white hover:bg-gray-50', 'group cursor-pointer text-sm border-b border-gray-200']">
                        <!-- Checkbox -->
                        <td @click.stop class="whitespace-nowrap align-top px-4 py-4">
                            <Input
                                type="checkbox"
                                v-model="checkedRows[copilot.id]">
                            </Input>
                        </td>
                        <template v-for="(column, columnIndex) in columns" :key="columnIndex">
                            <template v-if="column.active">
                                <!-- Name -->
                                <td v-if="column.name == 'Name'" class="whitespace-nowrap align-center pr-4 py-4">
                                    <div>{{ copilot.name }}</div>
                                </td>
                                <!-- Organization -->
                                <td v-if="column.name == 'Organization'" class="whitespace-nowrap align-center pr-4 py-4">
                                    <div v-if="copilot.organization" class="flex items-center space-x-2">
                                        <img src="https://placehold.co/21x24" class="size-8 rounded-lg shrink-0" alt="Logo" />
                                        <div class="whitespace-nowrap">{{ copilot.organization.name }}</div>
                                    </div>
                                </td>
                                <!-- Knowledge Bases -->
                                <td v-if="column.name == 'Knowledge Bases'" class="whitespace-nowrap align-center text-center pr-4 py-4">
                                    <span>{{ copilot.knowledge_bases?.length || 0 }}</span>
                                </td>
                                <!-- Users -->
                                <td v-if="column.name == 'Users'" class="whitespace-nowrap align-center text-center pr-4 py-4">
                                    <span>{{ copilot.users?.length || 0 }}</span>
                                </td>
                                <!-- Created Date -->
                                <td v-if="column.name == 'Created Date'" class="whitespace-nowrap align-center pr-4 py-4">
                                    <div class="flex space-x-1 items-center">
                                        <span>{{ formattedDatetime(copilot.created_at) }}</span>
                                        <Popover placement="top" class="opacity-0 group-hover:opacity-100" :content="formattedRelativeDate(copilot.created_at)" />
                                    </div>
                                </td>
                            </template>
                        </template>
                        <td class="h-16 pr-4 py-4 flex align-middle items-center space-x-2">
                            <Button type="bare" size="xs" :leftIcon="Pencil" leftIconSize="16" :action="() => showUpdateCopilotModal(copilot)" />
                            <Button type="bareDanger" size="xs" :leftIcon="Trash2" leftIconSize="16" :action="() => showDeleteCopilotModal(copilot)" />
                        </td>
                    </tr>
                </template>
            </Table>
        </template>

        <!-- No Copilots -->
        <div v-else class="select-none w-full flex justify-center py-16">
            <div class="max-w-sm w-full bg-white shadow-sm rounded-xl p-6 flex flex-col items-center text-center space-y-4 border border-dashed border-gray-200">
                <BotIcon size="48" class="text-gray-400" />
                <h2 class="text-2xl font-bold text-gray-800">No Copilots Yet</h2>
                <p class="text-sm text-gray-500">Add a Copilot to assist your organization.</p>
                <Button type="primary" size="md" :leftIcon="Plus" leftIconSize="20" :action="showAddCopilotModal">
                    <span>Add Copilot</span>
                </Button>
            </div>
        </div>

        <!-- Modals -->
        <AddCopilotModal ref="addCopilotModal" @created="fetchCopilots" />
        <UpdateCopilotModal ref="updateCopilotModal" @updated="fetchCopilots" />
        <DeleteCopilotModal ref="deleteCopilotModal" @deleted="onCopilotDeleted" />
        <DeleteCopilotsModal ref="deleteCopilotsModal" @deleted="onCopilotsDeleted" />
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
    import { Plus, Pencil, Trash2, BotIcon } from 'lucide-vue-next';
    import AddCopilotModal from '@Pages/copilots/components/AddCopilotModal.vue';
    import { formattedDatetime, formattedRelativeDate } from '@Utils/dateUtils.js';
    import UpdateCopilotModal from '@Pages/copilots/components/UpdateCopilotModal.vue';
    import DeleteCopilotModal from '@Pages/copilots/components/DeleteCopilotModal.vue';
    import DeleteCopilotsModal from '@Pages/copilots/components/DeleteCopilotsModal.vue';

    export default {
        inject: ['formState', 'notificationState'],
        components: {
            Pill, Modal, Input, Button, Popover, Dropdown, Table,
            AddCopilotModal, UpdateCopilotModal, DeleteCopilotModal, DeleteCopilotsModal, BotIcon
        },
        data() {
            return {
                Plus,
                Pencil,
                Trash2,
                BotIcon,
                copilots: [],
                perPage: '15',
                checkedRows: [],
                pagination: null,
                searchTerm: null,
                selectAll: false,
                filterExpressions: [],
                sortingExpressions: [],
                isLoadingCopilots: false,
                columns: this.prepareColumns(),
                dropdownOptions: [
                    {
                        icon: Trash2,
                        label: 'Delete',
                        action: this.showDeleteCopilotsModal,
                    }
                ],
            }
        },
        watch: {
            selectAll(newValue) {
                this.checkedRows = this.copilots.reduce((acc, copilot) => {
                    acc[copilot.id] = newValue;
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
                return this.isLoadingCopilots || (this.pagination ?? {}).meta?.total > 0 || this.hasSearchTerm || this.hasFilterExpressions || this.hasSortingExpressions;
            }
        },
        methods: {
            formattedDatetime: formattedDatetime,
            formattedRelativeDate: formattedRelativeDate,
            prepareColumns() {
                const columnNames = ['Name', 'Organization', 'Knowledge Bases', 'Users', 'Created Date'];
                const defaultColumnNames  = ['Name', 'Organization', 'Knowledge Bases', 'Users', 'Created Date'];

                return columnNames.map(name => ({
                    name,
                    active: defaultColumnNames.includes(name),
                    priority: defaultColumnNames.includes(name)
                }));
            },
            showAddCopilotModal() {
                this.$refs.addCopilotModal.showModal();
            },
            showUpdateCopilotModal(copilot) {
                this.$refs.updateCopilotModal.showModal(copilot);
            },
            showDeleteCopilotModal(copilot) {
                this.$refs.deleteCopilotModal.showModal(copilot);
            },
            showDeleteCopilotsModal() {
                const selectedIds = this.copilots.filter(c => this.checkedRows[c.id]).map(c => c.id);
                this.$refs.deleteCopilotsModal.showModal(selectedIds);
            },
            onCopilotDeleted(id) {
                this.copilots = this.copilots.filter(c => c.id !== id);
                if (this.copilots.length === 0) this.fetchCopilots();
            },
            onCopilotsDeleted(deletedIds = []) {
                deletedIds.forEach(id => {
                    if (this.checkedRows[id] !== undefined) {
                        this.checkedRows[id] = false;
                    }
                });
                this.selectAll = false;
                this.copilots = this.copilots.filter(copilot => !deletedIds.includes(copilot.id));
                if (this.copilots.length === 0) {
                    this.fetchCopilots();
                }
            },
            paginate(url) {
                this.fetchCopilots(url);
            },
            search(searchTerm) {
                this.searchTerm = searchTerm;
                this.fetchCopilots();
            },
            updatedColumns(columns) {
                this.columns = columns;
            },
            updatedFilters(filters) {
                const newFilterExpressions = filters.map((filter) => filter.expression);
                if(!isEqual(this.filterExpressions, newFilterExpressions)) {
                    this.filterExpressions = newFilterExpressions;
                    this.fetchCopilots();
                }
            },
            updatedSorting(sorting) {
                const newSortingExpressions = sorting.map((sort) => sort.expression);
                if(!isEqual(this.sortingExpressions, newSortingExpressions)) {
                    this.sortingExpressions = newSortingExpressions;
                    this.fetchCopilots();
                }
            },
            updatedPerPage(perPage) {
                this.perPage = perPage;
                this.fetchCopilots();
            },
            onView(copilot) {
                this.$router.push({ name: 'show-copilot-conversation-threads', params: { copilotId: copilot.id } });
            },
            async fetchCopilots(url = null) {
                try {
                    this.isLoadingCopilots = true;
                    url = url ?? '/api/copilots';
                    let config = {
                        params: {
                            'per_page': this.perPage,
                            '_relationships': 'organization,users,knowledgeBases'
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
                    this.copilots = this.pagination.data;
                    this.checkedRows = this.copilots.reduce((acc, copilot) => {
                        acc[copilot.id] = false;
                        return acc;
                    }, {});
                } catch (error) {
                    const message = error?.response?.data?.message || error?.message || 'Something went wrong while fetching copilots';
                    this.notificationState.showWarningNotification(message);
                    console.error('Failed to fetch copilots:', error);
                } finally {
                    this.isLoadingCopilots = false;
                }
            }
        },
        created() {
            this.isLoadingCopilots = true;
            this.searchTerm = this.$route.query.searchTerm;
            if(this.$route.query.filterExpressions) this.filterExpressions = this.$route.query.filterExpressions.split('|');
            if(this.$route.query.sortingExpressions) this.sortingExpressions = this.$route.query.sortingExpressions.split('|');
            this.fetchCopilots();
        }
    };
</script>
