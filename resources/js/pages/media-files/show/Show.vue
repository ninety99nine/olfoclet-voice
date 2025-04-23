<template>

    <div class="select-none min-h-screen bg-gray-50 pt-8 p-4 sm:p-8 space-y-4 sm:space-y-6">

        <template v-if="showTable">

            <!-- Page Header -->
            <div class="flex justify-between">

                <div class="flex items-end space-x-2">
                    <CirclePlay size="48" stroke-width="1" class="text-gray-400" />
                    <div>
                        <h2 class="text-xl font-semibold text-gray-900 dark:text-white">Media Files</h2>
                        <p class="text-sm text-gray-600 dark:text-neutral-400">
                            Manage media files within your organization
                        </p>
                    </div>
                </div>

                <div class="flex justify-end space-x-2">
                    <Button type="primary" size="md" :leftIcon="Plus" leftIconSize="20" :action="showUploadModal">
                        <span>Add Media File</span>
                    </Button>
                </div>

            </div>

            <!-- Media Files Table -->
            <Table
                @search="search"
                :columns="columns"
                :perPage="perPage"
                @paginate="paginate"
                resource="media-files"
                @refresh="fetchMediaFiles"
                :searchTerm="searchTerm"
                :pagination="pagination"
                @updatedColumns="updatedColumns"
                @updatedFilters="updatedFilters"
                @updatedSorting="updatedSorting"
                @updatedPerPage="updatedPerPage"
                :isLoading="isLoadingMediaFiles"
                :filterExpressions="filterExpressions"
                :sortingExpressions="sortingExpressions">

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
                            <th v-if="column.active" scope="col" class="whitespace-nowrap align-top font-semibold pr-4 py-2.5">
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
                        v-for="mediaFile in mediaFiles"
                        :key="mediaFile.id"
                        :class="[checkedRows[mediaFile.id] ? 'bg-blue-50' : 'bg-white hover:bg-gray-50', 'group cursor-pointer text-sm border-b border-gray-200']">

                        <!-- Checkbox -->
                        <td @click.stop class="whitespace-nowrap align-top px-4 py-4">
                            <Input type="checkbox" v-model="checkedRows[mediaFile.id]" />
                        </td>

                        <!-- Columns -->
                        <template v-for="(column, columnIndex) in columns" :key="columnIndex">

                            <template v-if="column.active">

                                <template v-if="column.name === 'Name'">
                                    <td class="whitespace-nowrap align-center pr-4 py-4 max-w-60">
                                        <div class="font-medium truncate">{{ mediaFile.name }}</div>
                                    </td>
                                </template>

                                <template v-if="column.name === 'File Name'">
                                    <td class="whitespace-nowrap align-center pr-4 py-4 max-w-60">
                                        <div class="truncate">{{ mediaFile.file_name }}</div>
                                    </td>
                                </template>

                                <template v-if="column.name === 'Type'">
                                    <td class="whitespace-nowrap align-center pr-4 py-4 max-w-60">
                                        <span class="truncate">{{ mediaFile.mime_type }}</span>
                                    </td>
                                </template>

                                <template v-if="column.name === 'Size'">
                                    <td class="whitespace-nowrap align-center pr-4 py-4">
                                        <span>{{ formatSize(mediaFile.size) }}</span>
                                    </td>
                                </template>

                                <template v-if="column.name === 'Organization'">
                                    <td class="whitespace-nowrap align-center pr-4 py-4 max-w-60">
                                        <div v-if="mediaFile.organization" class="flex items-center space-x-2">
                                            <img src="https://placehold.co/21x24" class="size-8 rounded-lg shrink-0" alt="Logo" />
                                            <div class="whitespace-nowrap truncate">{{ mediaFile.organization.name }}</div>
                                        </div>
                                    </td>
                                </template>

                                <template v-if="column.name === 'Created Date'">
                                    <td class="whitespace-nowrap align-center pr-4 py-4">
                                        <div class="flex space-x-1 items-center">
                                            <span>{{ formattedDatetime(mediaFile.created_at) }}</span>
                                            <Popover placement="top" class="opacity-0 group-hover:opacity-100" :content="formattedRelativeDate(mediaFile.created_at)" />
                                        </div>
                                    </td>
                                </template>

                            </template>

                        </template>

                        <!-- Actions -->
                        <td class="h-16 pr-4 py-4 flex align-middle items-center space-x-2">

                            <Button
                                v-if="isAudioFile(mediaFile.mime_type)"
                                type="bare"
                                size="xs"
                                :leftIcon="Play"
                                leftIconSize="16"
                                :action="() => playAudio(mediaFile.id)"
                            />

                            <Button
                                type="bare"
                                size="xs"
                                :leftIcon="Pencil"
                                leftIconSize="16"
                                :action="() => showEditModal(mediaFile)"
                            />

                            <Button
                                type="bareDanger"
                                size="xs"
                                :leftIcon="Trash2"
                                leftIconSize="16"
                                :action="() => showDeleteMediaFileModal(mediaFile)"
                            />

                        </td>

                    </tr>

                </template>

            </Table>

        </template>

        <!-- No Media Files -->
        <div v-else class="select-none w-full flex justify-center py-16">

            <div class="max-w-sm w-full bg-white shadow-sm rounded-xl p-6 flex flex-col items-center text-center space-y-4 border border-dashed border-gray-200">
                <CirclePlay size="48" class="text-gray-400" />
                <h2 class="text-2xl font-bold text-gray-800">No Media Files Yet</h2>
                <p class="text-sm text-gray-500">Add a media file to manage audio and other resources.</p>
                <Button type="primary" size="md" :leftIcon="Plus" leftIconSize="20" :action="showUploadModal">
                    <span>Add Media File</span>
                </Button>
            </div>

        </div>

        <!-- Modals -->
        <AddMediaFileModal ref="uploadModal" @file-uploaded="fetchMediaFiles" />
        <PlayMediaFileModal ref="audioModal" :media-file-id="selectedMediaFileId" />
        <DeleteMediaFileModal ref="deleteMediaFileModal" @deleted="onMediaFileDeleted" />
        <DeleteMediaFilesModal ref="deleteMediaFilesModal" @deleted="onMediaFilesDeleted" />
        <UpdateMediaFileModal ref="editModal" :media-file="selectedMediaFile" @file-updated="fetchMediaFiles" />

    </div>

</template>

<script>
    import axios from 'axios';
    import isEqual from 'lodash/isEqual';
    import Modal from '@Partials/Modal.vue';
    import Input from '@Partials/Input.vue';
    import Button from '@Partials/Button.vue';
    import Popover from '@Partials/Popover.vue';
    import Dropdown from '@Partials/Dropdown.vue';
    import Table from '@Partials/table/Table.vue';
    import { getCountryName } from '@Utils/generalUtils.js';
    import { Plus, Pencil, Trash2, Play, CirclePlay } from 'lucide-vue-next';
    import { formattedDatetime, formattedRelativeDate } from '@Utils/dateUtils.js';
    import AddMediaFileModal from '@Pages/media-files/components/AddMediaFileModal.vue';
    import PlayMediaFileModal from '@Pages/media-files/components/PlayMediaFileModal.vue';
    import UpdateMediaFileModal from '@Pages/media-files/components/UpdateMediaFileModal.vue';
    import DeleteMediaFileModal from '@Pages/media-files/components/DeleteMediaFileModal.vue';
    import DeleteMediaFilesModal from '@Pages/media-files/components/DeleteMediaFilesModal.vue';

    export default {
        inject: ['formState', 'notificationState'],
        components: {
            Modal,
            Input,
            Table,
            Button,
            Popover,
            Dropdown,
            CirclePlay,
            AddMediaFileModal,
            PlayMediaFileModal,
            UpdateMediaFileModal,
            DeleteMediaFileModal,
            DeleteMediaFilesModal,
        },
        data() {
            return {
                Plus,
                Pencil,
                Trash2,
                Play,
                CirclePlay,
                perPage: '15',
                mediaFiles: [],
                getCountryName,
                checkedRows: [],
                pagination: null,
                searchTerm: null,
                selectAll: false,
                filterExpressions: [],
                sortingExpressions: [],
                selectedMediaFile: null,
                selectedMediaFileId: null,
                isLoadingMediaFiles: false,
                columns: this.prepareColumns(),
                dropdownOptions: [
                    {
                        icon: Trash2,
                        label: 'Delete',
                        action: this.showDeleteMediaFilesModal,
                    },
                ],
            };
        },
        watch: {
            selectAll(newValue) {
                this.checkedRows = this.mediaFiles.reduce((acc, mediaFile) => {
                    acc[mediaFile.id] = newValue;
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
                return this.isLoadingMediaFiles || (this.pagination ?? {}).meta?.total > 0 || this.hasSearchTerm || this.hasFilterExpressions || this.hasSortingExpressions;
            },
        },
        methods: {
            formattedDatetime,
            formattedRelativeDate,
            prepareColumns() {
                const columnNames = ['Name', 'File Name', 'Type', 'Size', 'Organization', 'Created Date'];
                const defaultColumnNames = ['Name', 'File Name', 'Type', 'Size', 'Organization', 'Created Date'];
                return columnNames.map(name => ({
                    name,
                    active: defaultColumnNames.includes(name),
                    priority: defaultColumnNames.includes(name),
                }));
            },
            showUploadModal() {
                this.$refs.uploadModal.showModal();
            },
            showEditModal(mediaFile) {
                this.selectedMediaFile = mediaFile;
                this.$refs.editModal.showModal();
            },
            showDeleteMediaFileModal(mediaFile) {
                this.$refs.deleteMediaFileModal.showModal(mediaFile);
            },
            showDeleteMediaFilesModal() {
                const selectedIds = this.mediaFiles.filter(m => this.checkedRows[m.id]).map(m => m.id);
                this.$refs.deleteMediaFilesModal.showModal(selectedIds);
            },
            onMediaFileDeleted(id) {
                this.mediaFiles = this.mediaFiles.filter(m => m.id !== id);
                if (this.mediaFiles.length === 0) this.fetchMediaFiles();
            },
            onMediaFilesDeleted(deletedIds = []) {
                deletedIds.forEach(id => {
                    if (this.checkedRows[id] !== undefined) {
                        this.checkedRows[id] = false;
                    }
                });
                this.selectAll = false;
                this.mediaFiles = this.mediaFiles.filter(mediaFile => !deletedIds.includes(mediaFile.id));
                if (this.mediaFiles.length === 0) {
                    this.fetchMediaFiles();
                }
            },
            paginate(url) {
                this.fetchMediaFiles(url);
            },
            search(searchTerm) {
                this.searchTerm = searchTerm;
                this.fetchMediaFiles();
            },
            updatedColumns(columns) {
                this.columns = columns;
            },
            updatedFilters(filters) {
                const newFilterExpressions = filters.map(f => f.expression);
                if (!isEqual(this.filterExpressions, newFilterExpressions)) {
                    this.filterExpressions = newFilterExpressions;
                    this.fetchMediaFiles();
                }
            },
            updatedSorting(sorting) {
                const newSortingExpressions = sorting.map(s => s.expression);
                if (!isEqual(this.sortingExpressions, newSortingExpressions)) {
                    this.sortingExpressions = newSortingExpressions;
                    this.fetchMediaFiles();
                }
            },
            updatedPerPage(perPage) {
                this.perPage = perPage;
                this.fetchMediaFiles();
            },
            async fetchMediaFiles(url = null) {
                try {
                    this.isLoadingMediaFiles = true;
                    url = url ?? '/api/media-files';
                    let config = {
                        params: {
                            'per_page': this.perPage,
                            '_relationships': 'organization,callFlowNodes'
                        },
                    };
                    if (this.hasSearchTerm) config.params['search'] = this.searchTerm;
                    if (this.hasFilterExpressions) config.params['_filters'] = this.filterExpressions.join('|');
                    if (this.hasSortingExpressions) config.params['_sort'] = this.sortingExpressions.join('|');

                    const response = await axios.get(url, config);
                    this.pagination = response.data;
                    this.mediaFiles = this.pagination.data;
                    this.checkedRows = this.mediaFiles.reduce((acc, mediaFile) => {
                        acc[mediaFile.id] = false;
                        return acc;
                    }, {});
                } catch (error) {
                    const message = error?.response?.data?.message || error?.message || 'Something went wrong while fetching media files';
                    this.notificationState.showWarningNotification(message);
                    console.error('Failed to fetch media files:', error);
                } finally {
                    this.isLoadingMediaFiles = false;
                }
            },
            formatSize(size) {
                if (size < 1024) return `${size} B`;
                if (size < 1024 * 1024) return `${(size / 1024).toFixed(2)} KB`;
                return `${(size / (1024 * 1024)).toFixed(2)} MB`;
            },
            isAudioFile(mimeType) {
                return mimeType.startsWith('audio/');
            },
            playAudio(mediaFileId) {
                if (!mediaFileId) {
                    console.error('Media file ID is null or undefined');
                    this.$notificationState.showWarningNotification('Cannot play audio: Invalid media file ID.');
                    return;
                }
                this.selectedMediaFileId = mediaFileId;
                this.$nextTick(() => {
                    this.$refs.audioModal.showModal();
                });
            },
        },
        created() {
            this.isLoadingMediaFiles = true;
            this.searchTerm = this.$route.query.searchTerm;
            if (this.$route.query.filterExpressions) this.filterExpressions = this.$route.query.filterExpressions.split('|');
            if (this.$route.query.sortingExpressions) this.sortingExpressions = this.$route.query.sortingExpressions.split('|');
            this.fetchMediaFiles();
        },
    };
</script>
