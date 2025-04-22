<template>

    <div class="select-none min-h-screen bg-gray-50 pt-8 p-4 sm:p-8 space-y-4 sm:space-y-6">

        <template v-if="showTable">

            <!-- Page Header -->
            <div class="flex justify-between">

                <div class="flex items-end space-x-2">

                    <ContactIcon size="48" stroke-width="1" class="text-gray-400" />

                    <div>
                        <h2 class="text-xl font-semibold text-gray-900 dark:text-white">Contacts</h2>

                        <p class="text-sm text-gray-600 dark:text-neutral-400">
                            Manage contacts within your organization
                        </p>
                    </div>

                </div>

                <div class="flex justify-end space-x-2">

                    <Button type="primary" size="md" :leftIcon="CloudUpload" leftIconSize="20" :action="showImportContactsModal">
                        <span>Import Contacts</span>
                    </Button>

                    <Button type="primary" size="md" :leftIcon="Plus" leftIconSize="20" :action="showAddContactModal">
                        <span>Add Contact</span>
                    </Button>

                </div>

            </div>

            <!-- Contacts Table -->
            <Table
                @search="search"
                :columns="columns"
                :perPage="perPage"
                resource="contacts"
                @paginate="paginate"
                @refresh="fetchContacts"
                :searchTerm="searchTerm"
                :pagination="pagination"
                :isLoading="isLoadingContacts"
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

                    <tr v-for="contact in contacts" :key="contact.id" :class="[checkedRows[contact.id] ? 'bg-blue-50' : 'bg-white hover:bg-gray-50', 'group cursor-pointer text-sm border-b border-gray-200']">

                        <td class="whitespace-nowrap align-center px-4 py-4">
                            <Input type="checkbox" v-model="checkedRows[contact.id]" />
                        </td>

                        <template v-for="column in columns" :key="column.id">

                            <template v-if="column.active && column.custom">

                                <td class="whitespace-nowrap align-center pr-4 py-4 truncate">
                                    <span>{{ getCustomAttributeValue(contact, column.name) }}</span>
                                </td>

                            </template>

                            <template v-else-if="column.active && !column.custom">

                                <template v-if="column.name === 'Name'">
                                    <td class="whitespace-nowrap align-center pr-4 py-4 max-w-60">
                                        <div class="font-medium truncate">{{ contact.name }}</div>
                                    </td>
                                </template>

                                <template v-if="column.name === 'Email'">
                                    <td class="whitespace-nowrap align-center pr-4 py-4 max-w-60">
                                        <span class="truncate">{{ contact.primary_email }}</span>
                                    </td>
                                </template>

                                <template v-if="column.name === 'Phone Number'">
                                    <td class="whitespace-nowrap align-center pr-4 py-4 max-w-60">
                                        <span class="truncate">{{ contact.primary_phone }}</span>
                                    </td>
                                </template>

                                <template v-if="column.name === 'Organization'">
                                    <td class="whitespace-nowrap align-center pr-4 py-4 max-w-60">
                                        <div v-if="contact.organization" class="flex items-center space-x-2">
                                            <img src="https://placehold.co/21x24" class="size-8 rounded-lg shrink-0" alt="Logo" />
                                            <div class="whitespace-nowrap truncate">{{ contact.organization.name }}</div>
                                        </div>
                                    </td>
                                </template>

                                <template v-if="column.name === 'Country'">
                                    <td class="whitespace-nowrap align-center pr-4 py-4">
                                        <div v-if="contact.organization" class="flex items-center space-x-2">
                                            <div class="flex items-center space-x-2">
                                                <img
                                                    v-if="contact.organization.country"
                                                    :alt="contact.organization.country"
                                                    class="w-5 h-4 rounded-sm object-cover"
                                                    :src="`/svgs/country-flags/${contact.organization.country.toLowerCase()}.svg`">
                                                <span>{{ getCountryName(contact.organization.country) }}</span>
                                            </div>
                                        </div>
                                    </td>
                                </template>

                                <template v-else-if="column.name === 'Calls'">
                                    <td class="whitespace-nowrap align-center text-center pr-4 py-4 max-w-60">
                                        <span class="truncate">{{ contact.calls_count }}</span>
                                    </td>
                                </template>

                                <template v-else-if="column.name === 'Created Date'">
                                    <td class="whitespace-nowrap align-center pr-4 py-4">
                                        <div class="flex space-x-1 items-center">
                                            <span>{{ formattedDatetime(contact.created_at) }}</span>
                                            <Popover placement="top" class="opacity-0 group-hover:opacity-100" :content="formattedRelativeDate(contact.created_at)" />
                                        </div>
                                    </td>
                                </template>

                            </template>

                        </template>

                        <td class="h-16 pr-4 py-4 flex align-middle items-center space-x-2">
                            <Button type="bare" size="xs" :leftIcon="Pencil" leftIconSize="16" :action="() => showUpdateContactModal(contact)" />
                            <Button type="bareDanger" size="xs" :leftIcon="Trash2" leftIconSize="16" :action="() => showDeleteContactModal(contact)" />
                        </td>

                    </tr>

                </template>

            </Table>

        </template>

        <!-- No Contacts -->
        <div v-else class="select-none w-full flex justify-center py-16">
            <div class="max-w-sm w-full bg-white shadow-sm rounded-xl p-6 flex flex-col items-center text-center space-y-4 border border-dashed border-gray-200">
                <ContactIcon size="48" class="text-gray-400" />
                <h2 class="text-2xl font-bold text-gray-800">No Contacts Yet</h2>
                <p class="text-sm text-gray-500">Add a contact to manage customer interactions.</p>
                <Button type="primary" size="md" :leftIcon="Plus" leftIconSize="20" :action="showAddContactModal">
                    <span>Add Contact</span>
                </Button>
            </div>
        </div>

        <!-- Modals -->
        <AddContactModal ref="addContactModal" @created="fetchContacts" />
        <UpdateContactModal ref="updateContactModal" @updated="fetchContacts" />
        <DeleteContactModal ref="deleteContactModal" @deleted="onContactDeleted" />
        <ImportContactsModal ref="importContactsModal" @imported="fetchContacts" />
        <DeleteContactsModal ref="deleteContactsModal" @deleted="onContactsDeleted" />
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
import { capitalize } from '@Utils/stringUtils.js';
import { getCountryName } from '@Utils/generalUtils.js';
import { generateUniqueId } from '@Utils/generalUtils.js';
import AddContactModal from '@Pages/contacts/components/AddContactModal.vue';
import { formattedDatetime, formattedRelativeDate } from '@Utils/dateUtils.js';
import UpdateContactModal from '@Pages/contacts/components/UpdateContactModal.vue';
import DeleteContactModal from '@Pages/contacts/components/DeleteContactModal.vue';
import ImportContactsModal from '@Pages/contacts/components/ImportContactsModal.vue';
import DeleteContactsModal from '@Pages/contacts/components/DeleteContactsModal.vue';
import { Plus, Pencil, Trash2, Contact as ContactIcon, CloudUpload } from 'lucide-vue-next';

export default {
    inject: ['formState', 'notificationState'],
    components: {
        Pill, Modal, Input, Button, Popover, Dropdown, Table,
        AddContactModal, UpdateContactModal, ImportContactsModal, DeleteContactModal, DeleteContactsModal, ContactIcon
    },
    data() {
        return {
            Plus,
            Trash2,
            Pencil,
            capitalize,
            ContactIcon,
            CloudUpload,
            contacts: [],
            perPage: '15',
            getCountryName,
            checkedRows: [],
            generateUniqueId,
            pagination: null,
            searchTerm: null,
            selectAll: false,
            filterExpressions: [],
            sortingExpressions: [],
            isLoadingContacts: false,
            columns: this.prepareColumns(),
            dropdownOptions: [
                {
                    icon: Trash2,
                    label: 'Delete',
                    action: this.showDeleteContactsModal,
                }
            ]
        };
    },
    watch: {
        selectAll(newValue) {
            this.checkedRows = this.contacts.reduce((acc, contact) => {
                acc[contact.id] = newValue;
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
            return this.isLoadingContacts || (this.pagination ?? {}).meta?.total > 0 || this.hasSearchTerm || this.hasFilterExpressions || this.hasSortingExpressions;
        }
    },
    methods: {
        formattedDatetime,
        formattedRelativeDate,
        prepareColumns(customAttributeNames = []) {
            const columnNames = ['Name', 'Email', 'Phone Number', 'Organization', 'Country', 'Calls', 'Created Date'];
            const defaultColumnNames = ['Name', 'Email', 'Phone Number', 'Organization', 'Country', 'Calls', 'Created Date'];

            let columns = columnNames.map(name => ({
                name,
                id: generateUniqueId(),
                active: defaultColumnNames.includes(name),
                priority: defaultColumnNames.includes(name)
            }));

            const customColumns = customAttributeNames.map(name => ({
                name,
                custom: true,
                active: false,
                priority: false,
                id: generateUniqueId(),
            }));

            return [...columns, ...customColumns];
        },
        getCustomAttributeValue(contact, attributeName) {
            return contact.custom_attributes?.find(attr => attr.name.toLowerCase() === attributeName.toLowerCase())?.value || '';
        },
        showAddContactModal() {
            this.$refs.addContactModal.showModal();
        },
        showImportContactsModal() {
            this.$refs.importContactsModal.showModal();
        },
        showUpdateContactModal(contact) {
            this.$refs.updateContactModal.showModal(contact);
        },
        showDeleteContactModal(contact) {
            this.$refs.deleteContactModal.showModal(contact);
        },
        showDeleteContactsModal() {
            const selectedIds = this.contacts.filter(c => this.checkedRows[c.id]).map(c => c.id);
            this.$refs.deleteContactsModal.showModal(selectedIds);
        },
        onContactDeleted(id) {
            this.contacts = this.contacts.filter(c => c.id !== id);
            if (this.contacts.length === 0) this.fetchContacts();
        },
        onContactsDeleted(deletedIds = []) {
            deletedIds.forEach(id => {
                if (this.checkedRows[id] !== undefined) {
                    this.checkedRows[id] = false;
                }
            });
            this.selectAll = false;
            this.contacts = this.contacts.filter(contact => !deletedIds.includes(contact.id));
            if (this.contacts.length === 0) {
                this.fetchContacts();
            }
        },
        paginate(url) {
            this.fetchContacts(url);
        },
        search(searchTerm) {
            this.searchTerm = searchTerm;
            this.fetchContacts();
        },
        updatedColumns(columns) {
            this.columns = columns;
        },
        updatedFilters(filters) {
            const newFilterExpressions = filters.map(f => f.expression);
            if (!isEqual(this.filterExpressions, newFilterExpressions)) {
                this.filterExpressions = newFilterExpressions;
                this.fetchContacts();
            }
        },
        updatedSorting(sorting) {
            const newSortingExpressions = sorting.map(s => s.expression);
            if (!isEqual(this.sortingExpressions, newSortingExpressions)) {
                this.sortingExpressions = newSortingExpressions;
                this.fetchContacts();
            }
        },
        updatedPerPage(perPage) {
            this.perPage = perPage;
            this.fetchContacts();
        },
        async fetchContacts(url = null) {
            try {
                this.isLoadingContacts = true;
                url = url ?? '/api/contacts';
                let config = {
                    params: {
                        'per_page': this.perPage,
                        '_relationships': 'organization,favoriteUser,identifiers,customAttributes,tags',
                        '_countable_relationships': 'calls'
                    }
                };
                if (this.hasSearchTerm) config.params['search'] = this.searchTerm;
                if (this.hasFilterExpressions) config.params['_filters'] = this.filterExpressions.join('|');
                if (this.hasSortingExpressions) config.params['_sort'] = this.sortingExpressions.join('|');

                const response = await axios.get(url, config);
                this.pagination = response.data;
                this.contacts = this.pagination.data;

                // Update columns with custom attributes
                const customAttributeNames = [...new Set(
                    this.contacts.flatMap(contact =>
                        contact.custom_attributes.map(attr => capitalize(attr.name))
                    )
                )];

                this.columns = this.prepareColumns(customAttributeNames);

                this.checkedRows = this.contacts.reduce((acc, contact) => {
                    acc[contact.id] = false;
                    return acc;
                }, {});
            } catch (error) {
                const message = error?.response?.data?.message || error?.message || 'Something went wrong while fetching contacts';
                this.notificationState.showWarningNotification(message);
                console.error('Failed to fetch contacts:', error);
            } finally {
                this.isLoadingContacts = false;
            }
        }
    },
    created() {
        this.isLoadingContacts = true;
        this.searchTerm = this.$route.query.searchTerm;
        if (this.$route.query.filterExpressions) this.filterExpressions = this.$route.query.filterExpressions.split('|');
        if (this.$route.query.sortingExpressions) this.sortingExpressions = this.$route.query.sortingExpressions.split('|');
        this.fetchContacts();
    }
};
</script>
