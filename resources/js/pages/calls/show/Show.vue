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
                                    <Input type="checkbox" v-model="selectAll" />
                                </th>
                                <!-- Table Column Names -->
                                <template v-for="(column, index) in columns" :key="index">
                                    <th v-if="column.active" scope="col" :class="['whitespace-nowrap align-top font-semibold pr-4 py-2.5', { 'text-center' : ['Users', 'Mood'].includes(column.name) }]">
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
                                        <div class="text-sm">{{ call.from.name }} <span class="text-gray-500">({{ call.from.number }})</span></div>
                                    </td>
                                </template>

                                <template v-if="columns.find(col => col.name === 'To')?.active">
                                    <td class="whitespace-nowrap align-center pr-4 py-4">
                                        <div class="text-sm">{{ call.to.name }} <span class="text-gray-500">({{ call.to.number }})</span></div>
                                    </td>
                                </template>

                                <template v-if="columns.find(col => col.name === 'Direction')?.active">
                                    <td class="whitespace-nowrap align-center pr-4 py-4">
                                        <PhoneIncoming v-if="call.direction == 'inbound'" size="16" class="text-indigo-500 mx-auto" />
                                        <PhoneOutgoing v-else size="16" class="text-orange-500 mx-auto" />
                                    </td>
                                </template>

                                <template v-if="columns.find(col => col.name === 'Status')?.active">
                                    <td class="whitespace-nowrap align-center pr-4 py-4">
                                        <Pill
                                            :type="getStatusPillType(call.status)"
                                            size="xs"
                                        >
                                            {{ call.status }}
                                        </Pill>
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

                                <template v-if="columns.find(col => col.name === 'Transfers')?.active">
                                    <td class="whitespace-nowrap align-center text-center pr-4 py-4">
                                        <span>{{ call.transfers_count }}</span>
                                    </td>
                                </template>

                                <template v-if="columns.find(col => col.name === 'Listen Often')?.active">
                                    <td class="whitespace-nowrap align-center text-center pr-4 py-4">
                                        <span>{{ call.listenFrequency || 'N/A' }}</span>
                                    </td>
                                </template>

                                <template v-if="columns.find(col => col.name === 'Mood')?.active">
                                    <td class="whitespace-nowrap align-center text-center pr-4 py-4">
                                        <span>{{ call.mood || 'N/A' }}</span>
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
                                    <!-- Delete Button -->
                                    <Button type="outlineDanger" size="xs" :leftIcon="Trash2" leftIconSize="12" :action="() => showDeleteCallModal(call)" />
                                    <!-- Listen to Call Button (Play Recording) -->
                                    <Button
                                        type="outline"
                                        size="xs"
                                        :leftIcon="Ear"
                                        leftIconSize="12"
                                        v-if="call.status === 'In Progress'"
                                        :action="() => listenToCall(call)"
                                        class="relative"
                                    >
                                    </Button>
                                    <!-- Download Recording Button -->
                                    <Button
                                        type="outline"
                                        size="xs"
                                        :leftIcon="Play"
                                        leftIconSize="12"
                                        :action="() => downloadRecording(call)"
                                        :disabled="!call.recordingUrl"
                                        class="relative"
                                    >
                                        <Popover
                                            v-if="!call.recordingUrl"
                                            placement="top"
                                            class="opacity-0 group-hover:opacity-100"
                                            content="No recording available for download"
                                        />
                                    </Button>
                                    <!-- Download Recording Button -->
                                    <Button
                                        type="outline"
                                        size="xs"
                                        :leftIcon="Download"
                                        leftIconSize="12"
                                        :action="() => downloadRecording(call)"
                                        :disabled="!call.recordingUrl"
                                        class="relative"
                                    >
                                        <Popover
                                            v-if="!call.recordingUrl"
                                            placement="top"
                                            class="opacity-0 group-hover:opacity-100"
                                            content="No recording available for download"
                                        />
                                    </Button>
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
import DeleteCallModal from '@Pages/calls/components/DeleteCallModal.vue';
import DeleteCallsModal from '@Pages/calls/components/DeleteCallsModal.vue';
import { formattedDatetime, formattedRelativeDate } from '@Utils/dateUtils.js';
import { Phone, Trash2, PhoneCall, PhoneIncoming, PhoneOutgoing, Ear, Download, Play } from 'lucide-vue-next';

export default {
    inject: ['formState', 'notificationState'],
    components: {
        Pill, Modal, Input, Button, Popover, Dropdown, Table,
        AddCallModal, DeleteCallModal, DeleteCallsModal,
        PhoneCall, PhoneIncoming, PhoneOutgoing, Ear, Download
    },
    data() {
        return {
            Phone,
            Trash2,
            PhoneCall,
            Ear,
            Play,
            Download, // Icon for downloading recordings
            calls: [
                // Mock data with fake names, agents, transfers, listen frequency, and mood
                {
                    id: 1,
                    from: { name: "Karabo Modise", number: "+26771123456" },
                    to: { name: "Naledi Mosweu", number: "+26772234567" },
                    direction: "inbound",
                    status: "Completed",
                    agent: { name: "Thapelo Letsholo" },
                    department: { name: "Customer Support" },
                    transfers_count: 1, // 1 transfer
                    listenFrequency: "3 times", // Mock listen frequency for ended call
                    mood: "😀", // Happy (call resolved)
                    created_at: "2025-05-09T08:30:00Z",
                    recordingUrl: "https://example.com/recordings/call1.mp3"
                },
                {
                    id: 2,
                    from: { name: "Keneilwe Letsatsi", number: "+26773345678" },
                    to: { name: "Richard Molefe", number: "+26774456789" },
                    direction: "outbound",
                    status: "Missed",
                    agent: { name: "Lesego Otsile" },
                    department: { name: "Sales" },
                    transfers_count: 0, // No transfers
                    listenFrequency: "Never", // Mock listen frequency for ended call (no recording)
                    mood: null, // No mood for missed calls
                    created_at: "2025-05-09T09:15:00Z",
                    recordingUrl: null // No recording available
                },
                {
                    id: 3,
                    from: { name: "Moses Oduetse", number: "+26775567890" },
                    to: { name: "Gorata Moesi", number: "+26776678901" },
                    direction: "inbound",
                    status: "Completed",
                    agent: { name: "Jacob Odirile" },
                    department: { name: "Technical Support" },
                    transfers_count: 2, // 2 transfers
                    listenFrequency: "5 times", // Mock listen frequency for ended call
                    mood: "😟", // Sad (maybe a technical issue took long to resolve)
                    created_at: "2025-05-09T10:00:00Z",
                    recordingUrl: "https://example.com/recordings/call3.mp3"
                },
                {
                    id: 4,
                    from: { name: "Thandiwe Kgotla", number: "+26777789012" },
                    to: { name: "Palesa Ncube", number: "+26778890123" },
                    direction: "outbound",
                    status: "Completed",
                    agent: { name: "Moses Ofentse" },
                    department: { name: "Customer Support" },
                    transfers_count: 0, // No transfers
                    listenFrequency: "1 time", // Mock listen frequency for ended call
                    mood: "😍", // Very happy (excellent service)
                    created_at: "2025-05-09T11:30:00Z",
                    recordingUrl: "https://example.com/recordings/call4.mp3"
                },
                {
                    id: 5,
                    from: { name: "Bontle Phiri", number: "+26779901234" },
                    to: { name: "Tshepo Dlamini", number: "+26770012345" },
                    direction: "inbound",
                    status: "In Progress",
                    agent: { name: "Kagiso Mooketsi" },
                    department: { name: "Billing" },
                    transfers_count: 1, // 1 transfer
                    listenFrequency: null, // No listen frequency for ongoing call
                    mood: "😡", // Angry (maybe a billing dispute)
                    created_at: "2025-05-09T12:45:00Z",
                    recordingUrl: null // No recording yet for ongoing call
                }
            ],
            perPage: '15',
            checkedRows: [],
            pagination: {
                data: [],
                meta: { total: 5 } // Mock pagination for 5 calls
            },
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
            ],
            isSuperAdmin: true // Mock super admin status for demo
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
            const columnNames = ['From', 'To', 'Direction', 'Status', 'Agent', 'Department', 'Transfers', 'Listen Often', 'Mood', 'Created Date'];
            const defaultColumnNames = ['From', 'To', 'Direction', 'Status', 'Agent', 'Department', 'Transfers', 'Listen Often', 'Mood', 'Created Date'];
            return columnNames.map(name => ({
                name,
                active: defaultColumnNames.includes(name),
                priority: defaultColumnNames.includes(name)
            }));
        },
        showAddCallModal() {
            this.$refs.addCallModal.showModal();
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
            // Mock fetch - since we have static data for now
            try {
                this.isLoadingCalls = true;
                this.pagination.data = this.calls;
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
        },
        canPlayRecording(call) {
            // Only allow playing recordings for completed calls with a recording URL
            return call.status === 'Completed' && !!call.recordingUrl;
        },
        listenToCall(call) {
            // Mock action to play the recording
            if (this.canPlayRecording(call)) {
                this.notificationState.showInfoNotification(`Playing recording for call ID ${call.id}... (Mock Action)`);
                console.log(`Playing recording: ${call.recordingUrl}`);
            }
        },
        downloadRecording(call) {
            // Mock action to download the recording
            if (call.recordingUrl) {
                this.notificationState.showInfoNotification(`Downloading recording for call ID ${call.id}... (Mock Action)`);
                console.log(`Downloading recording: ${call.recordingUrl}`);
            }
        },
        getStatusPillType(status) {
            switch (status) {
                case 'Completed':
                    return 'success';
                case 'Missed':
                    return 'danger';
                case 'In Progress':
                    return 'warning';
                default:
                    return 'light'; // Fallback for any unexpected status
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
