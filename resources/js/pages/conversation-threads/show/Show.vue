<template>
    <div class="select-none min-h-screen bg-gray-50 pt-8 p-4 sm:p-8 space-y-4 sm:space-y-6">
        <template v-if="showTable">
            <!-- Page Header -->
            <div class="flex justify-between">
                <div class="flex items-end space-x-2">
                    <MessageSquareMoreIcon size="48" stroke-width="1" class="text-gray-400" />
                    <div>
                        <h2 class="text-xl font-semibold text-gray-900 dark:text-white">Conversations</h2>
                        <div v-if="copilotId" class="flex items-center space-x-2">
                            <span class="text-sm text-gray-600">Conversations with</span>
                            <div class="flex items-center space-x-1 bg-indigo-50 px-2 py-1 border border-dashed border-indigo-200 text-indigo-500 rounded-sm text-xs">
                                <BotIcon size="16" />
                                <span>{{ isLoadingCopilot ? '...' : copilot.name }}</span>
                            </div>
                        </div>
                        <p v-else class="text-sm text-gray-600 dark:text-neutral-400">
                            View all your conversation threads
                        </p>
                    </div>
                </div>
                <div class="flex justify-end">
                    <Button v-if="copilotId" type="primary" size="md" :leftIcon="Plus" leftIconSize="20" :action="startNewConversation">
                        <span>New Conversation</span>
                    </Button>
                </div>
            </div>

            <!-- Conversation Threads Table -->
            <Table
                @search="search"
                :columns="columns"
                :perPage="perPage"
                @paginate="paginate"
                @refresh="fetchThreads"
                :searchTerm="searchTerm"
                :pagination="pagination"
                resource="conversation-threads"
                @updatedColumns="updatedColumns"
                @updatedFilters="updatedFilters"
                @updatedSorting="updatedSorting"
                @updatedPerPage="updatedPerPage"
                :filterExpressions="filterExpressions"
                :sortingExpressions="sortingExpressions"
                :isLoading="isLoadingCopilot || isLoadingThreads">

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
                            <th v-if="column.active" scope="col" :class="['whitespace-nowrap align-top font-semibold pr-4 py-2.5', { 'text-center' : ['Messages'].includes(column.name) }]">
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
                        :key="thread.id"
                        @click.stop="onView(thread)"
                        v-for="thread in threads"
                        :class="[checkedRows[thread.id] ? 'bg-blue-50' : 'bg-white hover:bg-gray-50', 'group cursor-pointer text-sm border-b border-gray-200']">
                        <!-- Checkbox -->
                        <td @click.stop class="whitespace-nowrap align-top px-4 py-4">
                            <Input
                                type="checkbox"
                                v-model="checkedRows[thread.id]">
                            </Input>
                        </td>
                        <template v-for="(column, columnIndex) in columns" :key="columnIndex">
                            <template v-if="column.active">
                                <!-- Title -->
                                <td v-if="column.name == 'Title'" class="whitespace-nowrap align-center pr-4 py-4">
                                    <div>{{ thread.title }}</div>
                                </td>
                                <!-- Copilot -->
                                <td v-if="column.name == 'Copilot'" class="whitespace-nowrap align-center pr-4 py-4">
                                    <div v-if="thread.copilot" class="flex items-center space-x-2">
                                        <BotIcon size="20" class="text-gray-400" />
                                        <div class="whitespace-nowrap">{{ thread.copilot.name }}</div>
                                    </div>
                                </td>
                                <!-- Messages -->
                                <td v-if="column.name == 'Messages'" class="whitespace-nowrap align-center text-center pr-4 py-4">
                                    <span>{{ thread.messages_count || 0 }}</span>
                                </td>
                                <!-- Created Date -->
                                <td v-if="column.name == 'Created Date'" class="whitespace-nowrap align-center pr-4 py-4">
                                    <div class="flex space-x-1 items-center">
                                        <span>{{ formattedDatetime(thread.created_at) }}</span>
                                        <Popover placement="top" class="opacity-0 group-hover:opacity-100" :content="formattedRelativeDate(thread.created_at)" />
                                    </div>
                                </td>
                            </template>
                        </template>
                        <td class="h-16 pr-4 py-4 flex align-middle items-center space-x-2">
                            <Button type="bare" size="xs" :leftIcon="Pencil" leftIconSize="16" :action="() => showUpdateThreadModal(thread)" />
                            <Button type="bareDanger" size="xs" :leftIcon="Trash2" leftIconSize="16" :action="() => showDeleteThreadModal(thread)" />
                        </td>
                    </tr>
                </template>
            </Table>
        </template>

        <!-- No Threads -->
        <div v-else class="select-none w-full flex justify-center py-16">
            <div class="max-w-sm w-full bg-white shadow-sm rounded-xl p-6 flex flex-col items-center text-center space-y-4 border border-dashed border-gray-200">
                <MessageSquareMoreIcon size="48" class="text-gray-400" />
                <h2 class="text-2xl font-bold text-gray-800">No Conversations Yet</h2>
                <p class="text-sm text-gray-500">
                    {{ copilotId ? `Start a new conversation with ${copilot?.name || 'this Copilot'}.` : 'Start a new conversation with a Copilot via the Copilots page.' }}
                </p>
                <Button v-if="copilotId" type="primary" size="md" :leftIcon="Plus" leftIconSize="20" :action="startNewConversation">
                    <span>New Conversation</span>
                </Button>
            </div>
        </div>

        <!-- Modals -->
        <UpdateConversationThreadModal ref="updateThreadModal" @updated="onThreadUpdated" />
        <DeleteConversationThreadModal ref="deleteThreadModal" @deleted="onThreadDeleted" />
        <DeleteConversationThreadsModal ref="deleteThreadsModal" @deleted="onThreadsDeleted" />
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
    import { formattedDatetime, formattedRelativeDate } from '@Utils/dateUtils.js';
    import { Plus, Pencil, Trash2, BotIcon, MessageSquareMore as MessageSquareMoreIcon } from 'lucide-vue-next';
    import UpdateConversationThreadModal from '@Pages/conversation-threads/components/UpdateConversationThreadModal.vue';
    import DeleteConversationThreadModal from '@Pages/conversation-threads/components/DeleteConversationThreadModal.vue';
    import DeleteConversationThreadsModal from '@Pages/conversation-threads/components/DeleteConversationThreadsModal.vue';

    export default {
        props: {
            copilotId: {
                type: String,
                default: null
            }
        },
        inject: ['formState', 'notificationState'],
        components: {
            BotIcon, Pill, Modal, Input, Button, Popover, Dropdown, Table,
            UpdateConversationThreadModal, DeleteConversationThreadModal, DeleteConversationThreadsModal, MessageSquareMoreIcon
        },
        data() {
            return {
                Plus,
                Pencil,
                Trash2,
                copilot: null,
                threads: [],
                perPage: '15',
                checkedRows: [],
                pagination: null,
                searchTerm: null,
                selectAll: false,
                filterExpressions: [],
                MessageSquareMoreIcon,
                sortingExpressions: [],
                isLoadingThreads: false,
                isLoadingCopilot: false,
                columns: this.prepareColumns(),
                dropdownOptions: [
                    {
                        icon: Trash2,
                        label: 'Delete',
                        action: this.showDeleteThreadsModal,
                    }
                ],
            }
        },
        watch: {
            selectAll(newValue) {
                this.checkedRows = this.threads.reduce((acc, thread) => {
                    acc[thread.id] = newValue;
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
                return this.isLoadingThreads || (this.pagination ?? {}).meta?.total > 0 || this.hasSearchTerm || this.hasFilterExpressions || this.hasSortingExpressions;
            }
        },
        methods: {
            formattedDatetime: formattedDatetime,
            formattedRelativeDate: formattedRelativeDate,
            prepareColumns() {
                const columnNames = ['Title', 'Copilot', 'Messages', 'Created Date'];
                const defaultColumnNames = ['Title', 'Messages', 'Created Date'];
                if (!this.copilotId) {
                    defaultColumnNames.splice(1, 0, 'Copilot'); // Add Copilot column if not Copilot-specific
                }

                return columnNames.map(name => ({
                    name,
                    active: defaultColumnNames.includes(name),
                    priority: defaultColumnNames.includes(name)
                }));
            },
            showUpdateThreadModal(thread) {
                this.$refs.updateThreadModal.showModal(thread);
            },
            showDeleteThreadModal(thread) {
                this.$refs.deleteThreadModal.showModal(thread);
            },
            showDeleteThreadsModal() {
                const selectedIds = this.threads.filter(t => this.checkedRows[t.id]).map(t => t.id);
                this.$refs.deleteThreadsModal.showModal(selectedIds);
            },
            onThreadUpdated(updatedThread) {
                this.threads = this.threads.map(thread =>
                    thread.id === updatedThread.id ? { ...thread, ...updatedThread } : thread
                );
            },
            onThreadDeleted(id) {
                this.threads = this.threads.filter(t => t.id !== id);
                if (this.threads.length === 0) this.fetchThreads();
            },
            onThreadsDeleted(deletedIds = []) {
                deletedIds.forEach(id => {
                    if (this.checkedRows[id] !== undefined) {
                        this.checkedRows[id] = false;
                    }
                });
                this.selectAll = false;
                this.threads = this.threads.filter(thread => !deletedIds.includes(thread.id));
                if (this.threads.length === 0) {
                    this.fetchThreads();
                }
            },
            paginate(url) {
                this.fetchThreads(url);
            },
            search(searchTerm) {
                this.searchTerm = searchTerm;
                this.fetchThreads();
            },
            updatedColumns(columns) {
                this.columns = columns;
            },
            updatedFilters(filters) {
                const newFilterExpressions = filters.map((filter) => filter.expression);
                if(!isEqual(this.filterExpressions, newFilterExpressions)) {
                    this.filterExpressions = newFilterExpressions;
                    this.fetchThreads();
                }
            },
            updatedSorting(sorting) {
                const newSortingExpressions = sorting.map((sort) => sort.expression);
                if(!isEqual(this.sortingExpressions, newSortingExpressions)) {
                    this.sortingExpressions = newSortingExpressions;
                    this.fetchThreads();
                }
            },
            updatedPerPage(perPage) {
                this.perPage = perPage;
                this.fetchThreads();
            },
            onView(thread) {
                if (!thread.copilot || !thread.copilot.id) {
                    this.notificationState.showWarningNotification('Cannot open thread: Copilot information is missing.');
                    return;
                }
                this.$router.push({
                    name: 'chat-copilot-conversation-thread',
                    params: { copilotId: thread.copilot.id, conversationThreadId: thread.id }
                });
            },
            startNewConversation() {
                this.$router.push({
                    name: 'create-copilot-conversation-thread',
                    params: { copilotId: this.copilotId }
                });
            },
            async fetchCopilot() {
                if (!this.copilotId) return;

                try {
                    this.isLoadingCopilot = true;
                    const response = await axios.get(`/api/copilots/${this.copilotId}`, {
                        params: { '_relationships': 'organization,users,knowledgeBases' }
                    });
                    this.copilot = response.data;
                } catch (error) {
                    const message = error?.response?.data?.message || error?.message || 'Failed to load Copilot';
                    this.notificationState.showWarningNotification(message);
                    console.error('Failed to fetch copilot:', error);
                } finally {
                    this.isLoadingCopilot = false;
                }
            },
            async fetchThreads(url = null) {
                try {
                    this.isLoadingThreads = true;
                    url = url ?? '/api/conversation-threads';
                    let config = {
                        params: {
                            'per_page': this.perPage,
                            '_relationships': 'copilot'
                        }
                    };
                    if (this.copilotId) {
                        config.params['copilot_id'] = this.copilotId;
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
                    this.threads = this.pagination.data;
                    this.checkedRows = this.threads.reduce((acc, thread) => {
                        acc[thread.id] = false;
                        return acc;
                    }, {});
                } catch (error) {
                    const message = error?.response?.data?.message || error?.message || 'Something went wrong while fetching conversation threads';
                    this.notificationState.showWarningNotification(message);
                    console.error('Failed to fetch conversation threads:', error);
                } finally {
                    this.isLoadingThreads = false;
                }
            }
        },
        created() {
            this.isLoadingThreads = true;
            this.searchTerm = this.$route.query.searchTerm;
            if (this.$route.query.filterExpressions) this.filterExpressions = this.$route.query.filterExpressions.split('|');
            if (this.$route.query.sortingExpressions) this.sortingExpressions = this.$route.query.sortingExpressions.split('|');
            this.fetchCopilot();
            this.fetchThreads();
        }
    };
</script>
