<template>

    <div class="pt-24 pb-80 px-8 relative">

        <div class="relative">

            <!-- Heading -->
            <div class="flex flex-col items-center mb-8">
                <h1 class="space-x-2 text-xl text-gray-700 font-semibold bg-white rounded-full py-2 px-8 mb-4">
                    <span>🎁</span>
                    <span>Organisations</span>
                </h1>
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
                @refresh="getOrganizations"
                @updatedColumns="updatedColumns"
                @updatedFilters="updatedFilters"
                @updatedSorting="updatedSorting"
                @updatedPerPage="updatedPerPage"
                :isLoading="isLoadingOrganizations"
                :filterExpressions="filterExpressions"
                :sortingExpressions="sortingExpressions"
                v-if="isLoadingOrganizations || (pagination ?? {}).meta?.total > 0 || hasSearchTerm || hasFilterExpressions || hasSortingExpressions">

                <template #beforeRefreshButton>

                    <!-- Export Organizations Button -->
                    <Button
                        size="xs"
                        type="outline"
                        :leftIcon="ArrowDownToLine"
                        :action="showExportOrganizationsModal"
                        v-if="(pagination ?? {}).meta?.total > 0">
                        <span>Export</span>
                    </Button>

                </template>

                <!-- Select Action -->
                <template #belowToolbar>

                    <div :class="[{ 'hidden' : totalCheckedRows == 0 }, 'bg-gray-50 border flex items-center mb-2 p-4 rounded-lg shadow space-x-2']">

                        <span class="text-sm">Actions: </span>

                        <!-- Action Trigger -->
                        <Button id="action-dropdown-trigger" type="light" size="xs" icon="chevron-down-arrow">
                            <span>Select Action ({{ `${totalCheckedRows} selected` }})</span>
                        </Button>

                        <!-- Actions Dropdown -->
                        <div id="action-dropdown" class="w-60 border z-50 hidden text-base list-none bg-white divide-y divide-gray-100 rounded-lg shadow-sm">

                            <!-- Action Items -->
                            <template
                                :key="index"
                                v-for="(bulkSelectionOption, index) in bulkSelectionOptions">

                                <div @click.stop="() => bulkSelectionOption.action()" class="cursor-pointer flex space-x-2 items-center py-2 px-4 text-gray-900 hover:bg-gray-100 group">

                                    <svg v-if="bulkSelectionOption.name == 'Assign Team Member'" class="w-4 h-4" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M15 19.128a9.38 9.38 0 0 0 2.625.372 9.337 9.337 0 0 0 4.121-.952 4.125 4.125 0 0 0-7.533-2.493M15 19.128v-.003c0-1.113-.285-2.16-.786-3.07M15 19.128v.106A12.318 12.318 0 0 1 8.624 21c-2.331 0-4.512-.645-6.374-1.766l-.001-.109a6.375 6.375 0 0 1 11.964-3.07M12 6.375a3.375 3.375 0 1 1-6.75 0 3.375 3.375 0 0 1 6.75 0Zm8.25 2.25a2.625 2.625 0 1 1-5.25 0 2.625 2.625 0 0 1 5.25 0Z" />
                                    </svg>

                                    <svg v-if="bulkSelectionOption.name == 'Change Status'" class="w-4 h-4" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M16.023 9.348h4.992v-.001M2.985 19.644v-4.992m0 0h4.992m-4.993 0 3.181 3.183a8.25 8.25 0 0 0 13.803-3.7M4.031 9.865a8.25 8.25 0 0 1 13.803-3.7l3.181 3.182m0-4.991v4.99" />
                                    </svg>

                                    <svg v-if="bulkSelectionOption.name == 'Send Whatsapp'" class="w-5 h-5 -ml-0.5 -mr-1" xmlns="http://www.w3.org/2000/svg"  viewBox="0 0 48 48" width="96px" height="96px" fill-rule="evenodd" clip-rule="evenodd">
                                        <path fill="#fff" d="M4.868,43.303l2.694-9.835C5.9,30.59,5.026,27.324,5.027,23.979C5.032,13.514,13.548,5,24.014,5c5.079,0.002,9.845,1.979,13.43,5.566c3.584,3.588,5.558,8.356,5.556,13.428c-0.004,10.465-8.522,18.98-18.986,18.98c-0.001,0,0,0,0,0h-0.008c-3.177-0.001-6.3-0.798-9.073-2.311L4.868,43.303z"/>
                                        <path fill="#fff" d="M4.868,43.803c-0.132,0-0.26-0.052-0.355-0.148c-0.125-0.127-0.174-0.312-0.127-0.483l2.639-9.636c-1.636-2.906-2.499-6.206-2.497-9.556C4.532,13.238,13.273,4.5,24.014,4.5c5.21,0.002,10.105,2.031,13.784,5.713c3.679,3.683,5.704,8.577,5.702,13.781c-0.004,10.741-8.746,19.48-19.486,19.48c-3.189-0.001-6.344-0.788-9.144-2.277l-9.875,2.589C4.953,43.798,4.911,43.803,4.868,43.803z"/>
                                        <path fill="#cfd8dc" d="M24.014,5c5.079,0.002,9.845,1.979,13.43,5.566c3.584,3.588,5.558,8.356,5.556,13.428c-0.004,10.465-8.522,18.98-18.986,18.98h-0.008c-3.177-0.001-6.3-0.798-9.073-2.311L4.868,43.303l2.694-9.835C5.9,30.59,5.026,27.324,5.027,23.979C5.032,13.514,13.548,5,24.014,5 M24.014,42.974C24.014,42.974,24.014,42.974,24.014,42.974C24.014,42.974,24.014,42.974,24.014,42.974 M24.014,42.974C24.014,42.974,24.014,42.974,24.014,42.974C24.014,42.974,24.014,42.974,24.014,42.974 M24.014,4C24.014,4,24.014,4,24.014,4C12.998,4,4.032,12.962,4.027,23.979c-0.001,3.367,0.849,6.685,2.461,9.622l-2.585,9.439c-0.094,0.345,0.002,0.713,0.254,0.967c0.19,0.192,0.447,0.297,0.711,0.297c0.085,0,0.17-0.011,0.254-0.033l9.687-2.54c2.828,1.468,5.998,2.243,9.197,2.244c11.024,0,19.99-8.963,19.995-19.98c0.002-5.339-2.075-10.359-5.848-14.135C34.378,6.083,29.357,4.002,24.014,4L24.014,4z"/>
                                        <path fill="#40c351" d="M35.176,12.832c-2.98-2.982-6.941-4.625-11.157-4.626c-8.704,0-15.783,7.076-15.787,15.774c-0.001,2.981,0.833,5.883,2.413,8.396l0.376,0.597l-1.595,5.821l5.973-1.566l0.577,0.342c2.422,1.438,5.2,2.198,8.032,2.199h0.006c8.698,0,15.777-7.077,15.78-15.776C39.795,19.778,38.156,15.814,35.176,12.832z"/>
                                        <path fill="#fff" fill-rule="evenodd" d="M19.268,16.045c-0.355-0.79-0.729-0.806-1.068-0.82c-0.277-0.012-0.593-0.011-0.909-0.011c-0.316,0-0.83,0.119-1.265,0.594c-0.435,0.475-1.661,1.622-1.661,3.956c0,2.334,1.7,4.59,1.937,4.906c0.237,0.316,3.282,5.259,8.104,7.161c4.007,1.58,4.823,1.266,5.693,1.187c0.87-0.079,2.807-1.147,3.202-2.255c0.395-1.108,0.395-2.057,0.277-2.255c-0.119-0.198-0.435-0.316-0.909-0.554s-2.807-1.385-3.242-1.543c-0.435-0.158-0.751-0.237-1.068,0.238c-0.316,0.474-1.225,1.543-1.502,1.859c-0.277,0.317-0.554,0.357-1.028,0.119c-0.474-0.238-2.002-0.738-3.815-2.354c-1.41-1.257-2.362-2.81-2.639-3.285c-0.277-0.474-0.03-0.731,0.208-0.968c0.213-0.213,0.474-0.554,0.712-0.831c0.237-0.277,0.316-0.475,0.474-0.791c0.158-0.317,0.079-0.594-0.04-0.831C20.612,19.329,19.69,16.983,19.268,16.045z" clip-rule="evenodd"/>
                                    </svg>

                                    <svg v-if="bulkSelectionOption.name == 'Download PDF'" class="w-4 h-4" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M3 16.5v2.25A2.25 2.25 0 0 0 5.25 21h13.5A2.25 2.25 0 0 0 21 18.75V16.5M16.5 12 12 16.5m0 0L7.5 12m4.5 4.5V3" />
                                    </svg>

                                    <svg v-if="bulkSelectionOption.name == 'Print PDF'" class="w-4 h-4" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M6.72 13.829c-.24.03-.48.062-.72.096m.72-.096a42.415 42.415 0 0 1 10.56 0m-10.56 0L6.34 18m10.94-4.171c.24.03.48.062.72.096m-.72-.096L17.66 18m0 0 .229 2.523a1.125 1.125 0 0 1-1.12 1.227H7.231c-.662 0-1.18-.568-1.12-1.227L6.34 18m11.318 0h1.091A2.25 2.25 0 0 0 21 15.75V9.456c0-1.081-.768-2.015-1.837-2.175a48.055 48.055 0 0 0-1.913-.247M6.34 18H5.25A2.25 2.25 0 0 1 3 15.75V9.456c0-1.081.768-2.015 1.837-2.175a48.041 48.041 0 0 1 1.913-.247m10.5 0a48.536 48.536 0 0 0-10.5 0m10.5 0V3.375c0-.621-.504-1.125-1.125-1.125h-8.25c-.621 0-1.125.504-1.125 1.125v3.659M18 10.5h.008v.008H18V10.5Zm-3 0h.008v.008H15V10.5Z" />
                                    </svg>

                                    <svg v-if="bulkSelectionOption.name == 'Delete'" class="w-4 h-4" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="m14.74 9-.346 9m-4.788 0L9.26 9m9.968-3.21c.342.052.682.107 1.022.166m-1.022-.165L18.16 19.673a2.25 2.25 0 0 1-2.244 2.077H8.084a2.25 2.25 0 0 1-2.244-2.077L4.772 5.79m14.456 0a48.108 48.108 0 0 0-3.478-.397m-12 .562c.34-.059.68-.114 1.022-.165m0 0a48.11 48.11 0 0 1 3.478-.397m7.5 0v-.916c0-1.18-.91-2.164-2.09-2.201a51.964 51.964 0 0 0-3.32 0c-1.18.037-2.09 1.022-2.09 2.201v.916m7.5 0a48.667 48.667 0 0 0-7.5 0" />
                                    </svg>

                                    <span class="text-sm text-gray-500 group-hover:text-gray-900">
                                        {{ bulkSelectionOption.name }}
                                    </span>

                                </div>

                            </template>

                        </div>

                    </div>

                </template>

                <!-- Table Head -->
                <template #head>

                    <tr>

                        <!-- Checkbox -->
                        <th scope="col" class="whitespace-nowrap align-top px-4 py-4">

                            <Input
                                type="checkbox"
                                v-model="selectAll">
                            </Input>

                        </th>

                        <!-- Table Column Names -->
                        <template v-for="(column, index) in columns" :key="index">

                            <th v-if="column.active" scope="col" class="whitespace-nowrap align-top pr-4 py-4">
                                {{ column.name }}
                            </th>

                        </template>

                        <!-- Actions -->
                        <th scope="col" class="whitespace-nowrap align-top pr-4 py-4"></th>

                    </tr>

                </template>

                <!-- Table Body -->
                <template #body>

                    <tr @click.stop="onView(organization)" v-for="organization in organizations" :key="organization.id" :class="[checkedRows[organization.id] ? 'bg-blue-50' : 'bg-white hover:bg-gray-50', 'group cursor-pointer border-b']">

                        <template v-for="(column, columnIndex) in columns" :key="columnIndex">

                            <!-- Checkbox -->
                            <td v-if="columnIndex == 0" @click.stop class="whitespace-nowrap align-top px-4 py-4">

                                <Input
                                    type="checkbox"
                                    v-model="checkedRows[organization.id]">
                                </Input>

                            </td>

                            <template v-if="column.active">

                                <!-- Number -->
                                <td v-if="column.name == 'Number'" class="whitespace-nowrap align-top pr-4 py-4">

                                    <div class="flex space-x-1 items-center">
                                        <span>#{{ organization.name }}</span>
                                    </div>

                                </td>

                            </template>

                            <!-- Actions -->
                            <td v-if="columnIndex == (columns.length - 1)" class="align-top pr-4 py-4 flex items-center space-x-4">

                                <!-- View Button -->
                                <span v-if="!isDeletingOrganization(organization)" @click.stop.prevent="onView(organization)" class="font-medium text-blue-600 dark:text-blue-500 hover:underline">View</span>

                                <!-- Deleting Loader -->
                                <Loader v-if="isDeletingOrganization(organization)" type="danger">
                                    <span class="text-xs ml-2">Deleting...</span>
                                </Loader>

                                <!-- Delete Button -->
                                <span v-else @click.stop.prevent="showDeleteConfirmationModal(organization)" class="font-medium text-red-600 dark:text-red-500 hover:underline">Delete</span>

                            </td>

                        </template>

                    </tr>

                </template>

            </Table>

            <!-- No Organizations -->
            <div v-else class="flex justify-center">

                <div
                    class="animated-border-blue w-96 bg-white py-4 px-4 shadow-sm space-y-4 rounded-xl">

                    <h1 class="text-xl font-bold">
                        Ready For Your First Sale?
                    </h1>

                    <p class="text-sm text-gray-500">
                        Your organizations will appear here once customers start shopping. Start promoting your store to attract buyers and generate sales. Promote your store on as many platforms as possible.
                    </p>

                    <div class="flex justify-end">

                        <!-- Add Button -->
                        <Button :action="onAddOrganization" type="primary" size="sm" icon="add">
                            <span>Add Organization</span>
                        </Button>

                    </div>
                </div>

            </div>

        </div>

        <!-- Export Organizations -->
        <Modal ref="exportOrganizationsModal" approveText="Export Organizations" :approveAction="exportOrganizations" approveType="primary" :isLoading="isExportingOrganizations">

            <template #content>

                <p class="text-lg font-bold border-b border-dashed pb-4 mb-4">Export Organizations</p>

                <div class="space-y-4 mb-8">

                    <!-- Limit -->
                    <Select
                        width="w-full"
                        v-model="exportLimit"
                        label="Number of organizations">
                        <option v-for="(exportLimit, index) in exportLimits" :value="exportLimit" :key="index">
                            Last {{ exportLimit }} organizations
                        </option>
                    </Select>

                    <!-- Arrangement -->
                    <Select
                        width="w-full"
                        label="Arrangement"
                        v-model="exportMode">
                        <option v-for="(exportMode, index) in exportModes" :value="exportMode.value" :key="index">
                            {{ exportMode.label }}
                        </option>
                    </Select>

                    <!-- Format -->
                    <Select
                        width="w-full"
                        label="Format"
                        v-model="exportFormat">
                        <option v-for="(exportFormat, index) in exportFormats" :value="exportFormat.value" :key="index">
                            {{ exportFormat.label }}
                        </option>
                    </Select>

                    <!-- Apply Filters -->
                    <Input
                        type="checkbox"
                        v-model="exportWithFilters"
                        v-if="hasFilterExpressions"
                        inputLabel="Apply Filters">
                    </Input>

                    <!-- Apply Sorting -->
                    <Input
                        type="checkbox"
                        v-model="exportWithSorting"
                        v-if="hasSortingExpressions"
                        inputLabel="Apply Sorting">
                    </Input>

                </div>

            </template>

        </Modal>

        <!-- Assign Team Member -->
        <Modal ref="assignTeamMemberModal" approveText="Assign" :approveAction="() => updateOrganizations('Assign Team Member')" approveType="primary" :isLoading="isUpdatingOrganizations">

            <template #content>

                <p class="text-lg font-bold border-b border-dashed pb-4 mb-4">Assign Team Member</p>

                <div class="space-y-4 mb-8">

                    <div v-if="isLoadingTeamMembers" class="flex items-center space-x-2">
                        <Loader></Loader>
                        <span class="text-sm text-gray-500">Preparing team members</span>
                    </div>

                    <!-- Team Members Select Input -->
                    <Select
                        v-else
                        width="w-full"
                        v-model="teamMemberId"
                        :label="`Assign ${totalCheckedRows == 1 ? 'Organization' : 'Organization(s)'} to`">
                        <option v-for="(teamMember, index) in teamMembers" :value="teamMember.id" :key="index">
                            {{ teamMember._attributes.name }}
                        </option>
                    </Select>

                </div>

            </template>

        </Modal>

        <!-- Change Status -->
        <Modal ref="changeStatusModal" approveText="Change Status" :approveAction="() => updateOrganizations('Change Status')" approveType="primary" approveIcon="refresh" :isLoading="isUpdatingOrganizations">

            <template #content>

                <p class="text-lg font-bold border-b border-dashed pb-4 mb-4">Change Status</p>

                <div class="space-y-4 mb-8">

                    <!-- Status Select Input -->
                    <Select
                        width="w-full"
                        label="Status"
                        v-model="status">
                        <option v-for="(status, index) in statuses" :value="status" :key="index">
                            {{ status }}
                        </option>
                    </Select>

                    <!-- Payment Status Select Input -->
                    <Select
                        width="w-full"
                        v-model="paymentStatus"
                        label="Payment Status">
                        <option v-for="(paymentStatus, index) in paymentStatuses" :value="paymentStatus" :key="index">
                            {{ paymentStatus }}
                        </option>
                    </Select>

                </div>

            </template>

        </Modal>

        <!-- Send To Whatsapp -->
        <Modal ref="sendToWhatsappModal" approveText="Send" :approveAction="sendToWhatsapp" approveType="success" approveIcon="whatsapp">

            <template #content>

                <p class="text-lg font-bold border-b border-dashed pb-4 mb-4">Send To Whatsapp</p>

                <div class="space-y-4 mb-8">

                    <div class="flex space-x-2 p-4 text-sm bg-green-100 rounded-lg">

                        <svg class="w-16 h-8" xmlns="http://www.w3.org/2000/svg"  viewBox="0 0 48 48" width="96px" height="96px" fill-rule="evenodd" clip-rule="evenodd">
                            <path fill="#fff" d="M4.868,43.303l2.694-9.835C5.9,30.59,5.026,27.324,5.027,23.979C5.032,13.514,13.548,5,24.014,5c5.079,0.002,9.845,1.979,13.43,5.566c3.584,3.588,5.558,8.356,5.556,13.428c-0.004,10.465-8.522,18.98-18.986,18.98c-0.001,0,0,0,0,0h-0.008c-3.177-0.001-6.3-0.798-9.073-2.311L4.868,43.303z"/>
                            <path fill="#fff" d="M4.868,43.803c-0.132,0-0.26-0.052-0.355-0.148c-0.125-0.127-0.174-0.312-0.127-0.483l2.639-9.636c-1.636-2.906-2.499-6.206-2.497-9.556C4.532,13.238,13.273,4.5,24.014,4.5c5.21,0.002,10.105,2.031,13.784,5.713c3.679,3.683,5.704,8.577,5.702,13.781c-0.004,10.741-8.746,19.48-19.486,19.48c-3.189-0.001-6.344-0.788-9.144-2.277l-9.875,2.589C4.953,43.798,4.911,43.803,4.868,43.803z"/>
                            <path fill="#cfd8dc" d="M24.014,5c5.079,0.002,9.845,1.979,13.43,5.566c3.584,3.588,5.558,8.356,5.556,13.428c-0.004,10.465-8.522,18.98-18.986,18.98h-0.008c-3.177-0.001-6.3-0.798-9.073-2.311L4.868,43.303l2.694-9.835C5.9,30.59,5.026,27.324,5.027,23.979C5.032,13.514,13.548,5,24.014,5 M24.014,42.974C24.014,42.974,24.014,42.974,24.014,42.974C24.014,42.974,24.014,42.974,24.014,42.974 M24.014,42.974C24.014,42.974,24.014,42.974,24.014,42.974C24.014,42.974,24.014,42.974,24.014,42.974 M24.014,4C24.014,4,24.014,4,24.014,4C12.998,4,4.032,12.962,4.027,23.979c-0.001,3.367,0.849,6.685,2.461,9.622l-2.585,9.439c-0.094,0.345,0.002,0.713,0.254,0.967c0.19,0.192,0.447,0.297,0.711,0.297c0.085,0,0.17-0.011,0.254-0.033l9.687-2.54c2.828,1.468,5.998,2.243,9.197,2.244c11.024,0,19.99-8.963,19.995-19.98c0.002-5.339-2.075-10.359-5.848-14.135C34.378,6.083,29.357,4.002,24.014,4L24.014,4z"/>
                            <path fill="#40c351" d="M35.176,12.832c-2.98-2.982-6.941-4.625-11.157-4.626c-8.704,0-15.783,7.076-15.787,15.774c-0.001,2.981,0.833,5.883,2.413,8.396l0.376,0.597l-1.595,5.821l5.973-1.566l0.577,0.342c2.422,1.438,5.2,2.198,8.032,2.199h0.006c8.698,0,15.777-7.077,15.78-15.776C39.795,19.778,38.156,15.814,35.176,12.832z"/>
                            <path fill="#fff" fill-rule="evenodd" d="M19.268,16.045c-0.355-0.79-0.729-0.806-1.068-0.82c-0.277-0.012-0.593-0.011-0.909-0.011c-0.316,0-0.83,0.119-1.265,0.594c-0.435,0.475-1.661,1.622-1.661,3.956c0,2.334,1.7,4.59,1.937,4.906c0.237,0.316,3.282,5.259,8.104,7.161c4.007,1.58,4.823,1.266,5.693,1.187c0.87-0.079,2.807-1.147,3.202-2.255c0.395-1.108,0.395-2.057,0.277-2.255c-0.119-0.198-0.435-0.316-0.909-0.554s-2.807-1.385-3.242-1.543c-0.435-0.158-0.751-0.237-1.068,0.238c-0.316,0.474-1.225,1.543-1.502,1.859c-0.277,0.317-0.554,0.357-1.028,0.119c-0.474-0.238-2.002-0.738-3.815-2.354c-1.41-1.257-2.362-2.81-2.639-3.285c-0.277-0.474-0.03-0.731,0.208-0.968c0.213-0.213,0.474-0.554,0.712-0.831c0.237-0.277,0.316-0.475,0.474-0.791c0.158-0.317,0.079-0.594-0.04-0.831C20.612,19.329,19.69,16.983,19.268,16.045z" clip-rule="evenodd"/>
                        </svg>
                        <span>Show, hide and move your data they way you want to see it on whatsapp</span>

                    </div>

                    <!-- Include Organization field names -->
                    <Input
                        @click.stop
                        type="checkbox"
                        v-model="includeOrganizationFieldNames"
                        inputLabel="Include Organization field names">
                    </Input>

                    <div class="border divide-y overflow-y-auto rounded-lg h-60 px-4 mb-4">

                        <!-- Draggable Whatsapp Fields -->
                        <draggable
                            class="divide-y mb-4"
                            v-model="whatsappFields"
                            handle=".draggable-handle"
                            ghost-class="bg-yellow-50">

                            <template
                                :key="index"
                                v-for="(whatsappField, index) in whatsappFields">

                                <div class="flex items-center justify-between p-4">

                                    <!-- Active Toogle Switch -->
                                    <Switch
                                        size="sm"
                                        v-model="whatsappField.active">
                                        {{ whatsappField.name }}
                                    </Switch>

                                    <div class="flex items-center space-x-4">

                                        <!-- Gap Checkbox -->
                                        <Input
                                            @click.stop
                                            type="checkbox"
                                            inputLabel="Gap"
                                            v-if="whatsappField.active"
                                            v-model="whatsappField.linebreak">
                                        </Input>

                                        <!-- Drag & Drop Handle -->
                                        <svg class="draggable-handle w-4 h-4 cursor-grab hover:text-yellow-500 visible:cursor-grabbing" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round" d="M3.75 3.75v4.5m0-4.5h4.5m-4.5 0L9 9M3.75 20.25v-4.5m0 4.5h4.5m-4.5 0L9 15M20.25 3.75h-4.5m4.5 0v4.5m0-4.5L15 9m5.25 11.25h-4.5m4.5 0v-4.5m0 4.5L15 15" />
                                        </svg>

                                    </div>

                                </div>

                            </template>

                        </draggable>

                    </div>

                </div>

            </template>

        </Modal>

        <!-- Download PDF -->
        <Modal ref="downloadPdfModal" approveText="Download PDF" :approveAction="downloadOrganizations" approveType="primary" approveIcon="download" :isLoading="isDownloadingOrganizations">

            <template #content>

                <p class="text-lg font-bold border-b border-dashed pb-4 mb-4">Download PDF</p>

                <div class="flex space-x-2 p-4 text-xs bg-blue-100 rounded-lg mb-8">

                    <svg class="w-6 h-6 shrink-0" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M12 6v6h4.5m4.5 0a9 9 0 1 1-18 0 9 9 0 0 1 18 0Z" />
                    </svg>

                    <span>Creating the PDF to download may take a moment depending on the number of organizations. Please do not close this window.</span>

                </div>

            </template>

        </Modal>

        <!-- Print PDF -->
        <Modal ref="printPdfModal" approveText="Print PDF" :approveAction="printOrganizations" approveType="primary" approveIcon="print" :isLoading="isDownloadingOrganizations">

            <template #content>

                <p class="text-lg font-bold border-b border-dashed pb-4 mb-4">Print PDF</p>

                <div class="flex space-x-2 p-4 text-xs bg-blue-100 rounded-lg mb-8">

                    <svg class="w-6 h-6 shrink-0" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M12 6v6h4.5m4.5 0a9 9 0 1 1-18 0 9 9 0 0 1 18 0Z" />
                    </svg>

                    <span>Creating the PDF to print may take a moment depending on the number of organizations. Please do not close this window.</span>

                </div>

            </template>

        </Modal>

        <!-- Delete Organizations -->
        <Modal ref="deleteOrganizationsModal" :approveText="totalCheckedRows == 1 ? 'Delete Organization' : 'Delete Organizations'" :approveAction="deleteOrganizations" approveType="danger" approveIcon="delete" :isLoading="isDeletingOrganizations">

            <template #content>

                <p class="text-lg font-bold border-b border-dashed pb-4 mb-4">Delete Organizations</p>

                <div class="flex space-x-2 items-center p-4 text-xs bg-red-50 border border-red-200 border-dashed rounded-lg mb-8">

                    <svg class="w-6 h-6 text-red-500 shrink-0" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M12 9v3.75m9-.75a9 9 0 1 1-18 0 9 9 0 0 1 18 0Zm-9 3.75h.008v.008H12v-.008Z" />
                    </svg>

                    <span class="text-red-500">Are you sure you want to delete {{ totalCheckedRows > 1 ? `these ${totalCheckedRows} organizations` : 'this Organization' }}?</span>

                </div>

            </template>

        </Modal>

        <!-- Confirm Delete Organization -->
        <Modal ref="deleteOrganizationModal" approveText="Delete Organization" :approveAction="deleteOrganization" :isLoading="isDeletingOrganization(deletableOrganization)">

            <template #content>
                <p class="text-lg font-bold border-b border-dashed pb-4 mb-4">Confirm Delete</p>
                <p v-if="deletableOrganization" class="mb-8">Are you sure you want to permanently delete <span class="font-bold text-black">Organization #{{ deletableOrganization._attributes.number }}</span>?</p>
            </template>

        </Modal>

    </div>

</template>

<script>

    import axios from 'axios';
    import isEqual from 'lodash/isEqual';
    import Modal from '@Partials/Modal.vue';
    import Input from '@Partials/Input.vue';
    import Loader from '@Partials/Loader.vue';
    import Switch from '@Partials/Switch.vue';
    import Select from '@Partials/Select.vue';
    import Button from '@Partials/Button.vue';
    import Popover from '@Partials/Popover.vue';
    import Table from '@Partials/table/Table.vue';
    import { ArrowDownToLine } from 'lucide-vue-next';
    import { VueDraggableNext } from 'vue-draggable-next';
    import { formattedDatetime, formattedRelativeDate } from '@Utils/dateUtils.js';

    export default {
        inject: ['formState', 'notificationState'],
        components: {
            Button, Input, draggable: VueDraggableNext, Table, Select, Modal, Popover,
            Loader, Switch
        },
        data() {
            return {
                ArrowDownToLine,
                organizations: [],
                perPage: '15',
                checkedRows: [],
                teamMembers: [],
                pagination: null,
                searchTerm: null,
                selectAll: false,
                teamMemberId: null,
                exportLimit: '100',
                exportFormat: 'csv',
                status: 'No change',
                printPdfModal: null,
                actionDropdown: null,
                deletableOrganization: null,
                filterExpressions: [],
                sortingExpressions: [],
                isDeletingOrganizationIds: [],
                isLoadingOrganizations: false,
                downloadPdfModal: null,
                deleteOrganizationModal: null,
                exportOrganizationsModal: null,
                changeStatusModal: null,
                isUpdatingOrganizations: false,
                exportWithFilters: true,
                exportWithSorting: true,
                exportMode: 'by_organizations',
                isExportingOrganizations: false,
                sendToWhatsappModal: null,
                paymentStatus: 'No change',
                isDownloadingOrganizations: false,
                isLoadingTeamMembers: false,
                assignTeamMemberModal: null,
                includeOrganizationFieldNames: true,
                columns: this.prepareColumns(),
                whatsappFields: this.prepareWhatsappFields(),
                exportLimits: ['100', '500', '1000', '2000', '3000', '4000', '5000'],
                paymentStatuses: ['No change','Paid','Unpaid','Pending Payment','Partially Paid'],
                statuses: ['No change','Waiting','Cancelled','Completed','On Its Way','Ready For Pickup'],
                exportModes: [
                    {
                        label: 'One organization per row',
                        value: 'by_organizations'
                    },
                    {
                        label: 'One product per row',
                        value: 'by_products'
                    },
                    {
                        label: 'One product per row (blanking)',
                        value: 'by_products_blanking'
                    },
                ],
                exportFormats: [
                    {
                        label: 'CSV (Plain Data File)',
                        value: 'csv'
                    },
                    {
                        label: 'Excel (XLSX)',
                        value: 'xlsx'
                    },
                    {
                        label: 'PDF (Printable Document)',
                        value: 'pdf'
                    },
                ],
                bulkSelectionOptions: [
                    {
                        name: 'Assign Team Member',
                        action: this.showAssignTeamMemberModal,
                    },
                    {
                        name: 'Change Status',
                        action: this.showChangeStatusModal,
                    },
                    {
                        name: 'Send Whatsapp',
                        action: this.showSendToWhatsappModal,
                    },
                    {
                        name: 'Download PDF',
                        action: this.showDownloadPdfModal,
                    },
                    {
                        name: 'Print PDF',
                        action: this.showPrintPdfModal,
                    },
                    {
                        name: 'Delete',
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
            },
        },
        computed: {
            hasSearchTerm() {
                return this.searchTerm != null && this.searchTerm.trim() != '';
            },
            isDeletingOrganizations() {
                return this.isDeletingOrganizationIds.length > 0;
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
        },
        methods: {
            formattedDatetime: formattedDatetime,
            formattedRelativeDate: formattedRelativeDate,
            prepareColumns() {
                const columnNames = ['Number', 'Customer', 'Summary', 'Status', 'Payment Status', 'Collection Status', 'Grand Total', 'Paid', 'Pending', 'Outstanding', 'Customer Note', 'Created Date'];
                const defaultColumnNames  = ['Number', 'Customer', 'Summary', 'Status', 'Grand Total', 'Created Date'];

                return columnNames.map(name => ({
                    name,
                    active: defaultColumnNames.includes(name),
                    priority: defaultColumnNames.includes(name)
                }));
            },
            prepareWhatsappFields() {
                const whatsappFieldNames = ['Number', 'Customer', 'Summary', 'Status', 'Payment Status', 'Collection Status', 'Grand Total', 'Paid', 'Pending', 'Outstanding', 'Customer Note', 'Created Date', 'Organization Link'];
                const defaultWhatsappFieldNames  = ['Number', 'Customer', 'Summary', 'Status', 'Grand Total', 'Created Date', 'Organization Link'];

                return whatsappFieldNames.map(name => ({
                    name,
                    linebreak: false,
                    active: defaultWhatsappFieldNames.includes(name),
                    priority: defaultWhatsappFieldNames.includes(name)
                }));
            },
            showPrintPdfModal() {
                this.actionDropdown.hide();
                this.printPdfModal.showModal();
            },
            showDeleteOrganizationsModal() {
                this.actionDropdown.hide();
                this.deleteOrganizationsModal.showModal();
            },
            showDownloadPdfModal() {
                this.actionDropdown.hide();
                this.downloadPdfModal.showModal();
            },
            showExportOrganizationsModal() {
                this.exportOrganizationsModal.showModal();
            },
            showChangeStatusModal() {
                this.actionDropdown.hide();
                this.changeStatusModal.showModal();
            },
            showSendToWhatsappModal() {
                this.actionDropdown.hide();
                this.sendToWhatsappModal.showModal();
            },
            showAssignTeamMemberModal() {
                this.getTeamMembers();
                this.actionDropdown.hide();
                this.assignTeamMemberModal.showModal();
            },
            showDeleteConfirmationModal(organization) {
                this.deletableOrganization = organization;
                this.deleteOrganizationModal.showModal();
            },
            isDeletingOrganization(organization) {
                if(organization == null) return false;
                return this.isDeletingOrganizationIds.findIndex((id) => id == organization.id) != -1;
            },
            onView(organization) {
                this.$router.push({
                    name: 'show-store-organization',
                    params: {
                        'store_href': this.store._links.showStore,
                        'organization': organization._links.showOrganization
                    },
                    query: {
                        searchTerm: this.searchTerm,
                        filterExpressions: this.filterExpressions.join('|'),
                        sortingExpressions: this.sortingExpressions.join('|'),
                    }
                });
            },
            onAddOrganization() {
                this.$router.push({
                    name: 'create-store-organization',
                    params: { 'store_href': this.store._links.showStore }
                });
            },
            paginate(url) {
                this.getOrganizations(url);
            },
            search(searchTerm) {
                this.searchTerm = searchTerm;
                this.getOrganizations();
            },
            updatedColumns(columns) {
                this.columns = columns;
            },
            updatedFilters(filters) {
                const newFilterExpressions = filters.map((filter) => filter.expression);
                if(!isEqual(this.filterExpressions, newFilterExpressions)) {
                    this.filterExpressions = newFilterExpressions;
                    this.getOrganizations();
                }
            },
            updatedSorting(sorting) {
                const newSortingExpressions = sorting.map((sort) => sort.expression);
                if(!isEqual(this.sortingExpressions, newSortingExpressions)) {
                    this.sortingExpressions = newSortingExpressions;
                    this.getOrganizations();
                }
            },
            updatedPerPage(perPage) {
                this.perPage = perPage;
                this.getOrganizations();
            },
            async getOrganizations(url = null) {

                this.isLoadingOrganizations = true;

                url = url ?? '/api/organizations';

                let config = {
                    params: {
                        'per_page': this.perPage
                    }
                }

                if(this.hasSearchTerm) config.params['search'] = this.searchTerm;

                if(this.hasFilterExpressions) {
                    config.params['_filters'] = this.filterExpressions.join('|');
                }

                if(this.hasSortingExpressions) {
                    config.params['_sort'] = this.sortingExpressions.join('|');
                }

                await axios.get(url, config).then(response => {

                    if(response.status == 200) {

                        this.pagination = response.data;
                        this.organizations = this.pagination.data;
                        this.checkedRows = this.organizations.reduce((acc, organization) => {
                            acc[organization.id] = false;
                            return acc;
                        }, {});

                    }else{

                        this.formState.setFormError('general', response.data.message);
                        this.notificationState.showWarningNotification(response.data.message);

                    }

                }).catch(errorException => {
                    this.formState.setServerFormErrors(errorException);
                });

                this.isLoadingOrganizations = false;

            },
            async exportOrganizations(url = null) {

                if(this.isExportingOrganizations) return;
                this.isExportingOrganizations = true;

                url = this.store._links.showStoreOrganizations;

                let config = {
                    params:  {
                        '_export': '1',
                        'association': 'team member',
                        'export_mode': this.exportMode,
                        'export_limit': this.exportLimit,
                        'export_format': this.exportFormat
                    },
                    responseType: 'blob'
                };

                if(this.hasSearchTerm) params['search'] = this.searchTerm;

                if(this.exportWithFilters && this.hasFilterExpressions) {
                    config.params['_filters'] = this.filterExpressions.join('|');
                }

                if(this.exportWithSorting && this.hasSortingExpressions) {
                    config.params['_sort'] = this.sortingExpressions.join('|');
                }

                await axios.get(url, config).then(response => {

                    if (response.status === 200) {

                        const url = window.URL.createObjectURL(new Blob([response.data]));
                        const link = document.createElement('a');

                        link.href = url;
                        link.setAttribute('download', `organizations.${this.exportFormat}`);

                        document.body.appendChild(link);
                        link.click();
                        document.body.removeChild(link);

                    }

                }).catch(errorException => {

                    this.formState.setServerFormErrors(errorException);

                });

                this.isExportingOrganizations = false;

            },
            async updateOrganizations(type) {

                if(this.isUpdatingOrganizations) return;

                const organizationIds = this.organizations.filter(organization => this.checkedRows[organization.id]).map(organization => organization.id);

                const data = {
                    organization_ids: organizationIds,
                    store_id: this.store.id
                };

                const isChangingStatus = (type == 'Change Status');
                const isAssigningTeamMember = (type == 'Assign Team Member');

                if(isChangingStatus) {
                    if(this.status != 'No change') data['status'] = this.status.toLowerCase();
                    if(this.paymentStatus != 'No change') data['payment_status'] = this.paymentStatus.toLowerCase();
                }else if(isAssigningTeamMember) {
                    if(this.teamMemberId) data['assigned_to_user_id'] = this.teamMemberId;
                }

                if(Object.keys(data).length <= 1) return;

                this.isUpdatingOrganizations = true;

                await axios.put(this.apiState.apiHome['_links']['updateOrganizations'], data).then(response => {

                    if (response.status === 200 && response.data['updated'] == true) {

                        this.getOrganizations();

                        if(isChangingStatus) {
                            this.notificationState.showSuccessNotification('Organization status updated');
                        }else if(isAssigningTeamMember) {
                            const teamMember = this.teamMembers.find(member => member.id === this.teamMemberId);
                            if (teamMember) this.notificationState.showSuccessNotification(`Organizations assigned to ${teamMember.firstName}`);
                        }

                    }else{

                        this.formState.setFormError('general', response.data.message);
                        this.notificationState.showWarningNotification(response.data.message);

                    }

                }).catch(errorException => {
                    this.formState.setServerFormErrors(errorException);
                });

                this.isUpdatingOrganizations = false;

                // Uncheck only the related rows
                organizationIds.forEach(organizationId => {
                    if (this.checkedRows[organizationId] !== undefined) {
                        this.checkedRows[organizationId] = false;
                    }
                });

                this.selectAll = false;
            },
            async downloadOrganizations() {

                if(this.isDownloadingOrganizations) return;

                const organizationIds = this.organizations.filter(organization => this.checkedRows[organization.id]).map(organization => organization.id);

                const data = {
                    store_id: this.store.id,
                    organization_ids: organizationIds
                };

                const config = {
                    responseType: "blob"
                };

                this.isDownloadingOrganizations = true;

                await axios.post(this.apiState.apiHome['_links']['downloadOrganizations'], data, config).then(response => {

                    if (response.status === 200 && !response.data.hasOwnProperty('downloaded')) {

                        const blob = new Blob([response.data], { type: "application/pdf" });
                        const link = document.createElement("a");

                        link.href = window.URL.createObjectURL(blob);
                        link.download = "organizations.pdf";
                        document.body.appendChild(link);
                        link.click();
                        document.body.removeChild(link);

                    }else{

                        this.formState.setFormError('general', response.data.message);
                        this.notificationState.showWarningNotification(response.data.message);

                    }

                }).catch(errorException => {
                    this.formState.setServerFormErrors(errorException);
                });

                this.isDownloadingOrganizations = false;

                // Uncheck only the related rows
                organizationIds.forEach(organizationId => {
                    if (this.checkedRows[organizationId] !== undefined) {
                        this.checkedRows[organizationId] = false;
                    }
                });

                this.selectAll = false;

            },
            async printOrganizations() {

                if(this.isDownloadingOrganizations) return;

                const organizationIds = this.organizations.filter(organization => this.checkedRows[organization.id]).map(organization => organization.id);

                const data = {
                    store_id: this.store.id,
                    organization_ids: organizationIds
                };

                const config = {
                    responseType: "blob"
                };

                this.isDownloadingOrganizations = true;

                await axios.post(this.apiState.apiHome['_links']['downloadOrganizations'], data, config).then(response => {

                    if (response.status === 200 && !response.data.hasOwnProperty('downloaded')) {

                        const blob = new Blob([response.data], { type: "application/pdf" });
                        const blobUrl = window.URL.createObjectURL(blob);

                        const printWindow = window.open(blobUrl);
                        if (printWindow) {
                            printWindow.onload = () => {
                                printWindow.focus();
                                printWindow.print();
                            };
                        }

                    }else{

                        this.formState.setFormError('general', response.data.message);
                        this.notificationState.showWarningNotification(response.data.message);

                    }

                }).catch(errorException => {
                    this.formState.setServerFormErrors(errorException);
                });

                this.isDownloadingOrganizations = false;

                // Uncheck only the related rows
                organizationIds.forEach(organizationId => {
                    if (this.checkedRows[organizationId] !== undefined) {
                        this.checkedRows[organizationId] = false;
                    }
                });

                this.selectAll = false;
            },
            async deleteOrganizations() {

                const organizationIds = this.organizations.filter(organization => this.checkedRows[organization.id]).map(organization => organization.id)
                                            .filter(organizationId => !this.isDeletingOrganizationIds.includes(organizationId));

                if (organizationIds.length === 0) return;

                this.isDeletingOrganizationIds.push(...organizationIds);

                const config = {
                    data: {
                        store_id: this.store.id,
                        organization_ids: organizationIds
                    }
                }

                await axios.delete(this.apiState.apiHome['_links']['deleteOrganizations'], config).then(response => {

                    if (response.status === 200 && response.data['deleted'] == true) {

                        this.notificationState.showSuccessNotification(organizationIds == 1 ? 'Organization deleted' : 'Organizations deleted');
                        this.organizations = this.organizations.filter(organization => !organizationIds.includes(oorganizationrder.id));
                        if(this.organizations.length == 0) this.getOrganizations();

                    }else{

                        this.formState.setFormError('general', response.data.message);
                        this.notificationState.showWarningNotification(response.data.message);

                    }

                }).catch(errorException => {
                    this.formState.setServerFormErrors(errorException);
                });

                this.isDeletingOrganizationIds = this.isDeletingOrganizationIds.filter(id => !organizationIds.includes(id));

                // Uncheck only the related rows
                organizationIds.forEach(organizationId => {
                    if (this.checkedRows[organizationId] !== undefined) {
                        this.checkedRows[organizationId] = false;
                    }
                });

                this.selectAll = false;
            },
            async deleteOrganization() {

                if(this.isDeletingOrganizationIds.includes(this.deletableOrganization.id)) return;

                this.isDeletingOrganizationIds.push(this.deletableOrganization.id);

                await axios.delete(this.deletableOrganization._links.deleteOrganization).then(response => {

                    if (response.status === 200 && response.data['deleted'] == true) {

                        this.notificationState.showSuccessNotification('Organization deleted');
                        this.organizations = this.organizations.filter(organization => organization.id != this.deletableOrganization.id);
                        if(this.organizations.length == 0) this.getOrganizations();

                    }else{

                        this.formState.setFormError('general', response.data.message);
                        this.notificationState.showWarningNotification(response.data.message);

                    }

                }).catch(errorException => {
                    this.formState.setServerFormErrors(errorException);
                });

                this.isDeletingOrganizationIds.splice(this.isDeletingOrganizationIds.findIndex((id) => id == this.deletableOrganization.id, 1));

            },
            async getTeamMembers() {

                this.isLoadingTeamMembers = true;

                await axios.get(this.store._links.showStoreTeamMembers).then(response => {

                    if(response.status == 200) {
                        const pagination = response.data;
                        this.teamMembers = pagination.data;
                        this.teamMemberId = this.teamMembers[0].id;
                    }else{

                        this.formState.setFormError('general', response.data.message);
                        this.notificationState.showWarningNotification(response.data.message);

                    }

                }).catch(errorException => {
                    this.formState.setServerFormErrors(errorException);
                });

                this.isLoadingTeamMembers = false;

            },
            async sendToWhatsapp() {

                const checkedOrganizations = this.organizations.filter(organization => this.checkedRows[organization.id]);

                if (checkedOrganizations.length === 0) {
                    alert("No organizations selected to send to WhatsApp.");
                    return;
                }

                let message = "";
                let tempMessage = "";
                const maxLength = 58000;

                for (let i = 0; i < checkedOrganizations.length; i++) {
                    let organizationMessage = `Organization #${i + 1}\n` +
                        `----------------------\n`;

                    this.whatsappFields.forEach(field => {

                        if (field.active) {

                            if(field.linebreak) organizationMessage += `\n`;
                            if(this.includeOrganizationFieldNames) organizationMessage += `${field.name}: `;

                            switch (field.name) {
                                case "Number":
                                    organizationMessage += `${checkedOrganizations[i]._attributes.number}\n`;
                                    break;
                                case "Customer":
                                    organizationMessage += `${checkedOrganizations[i]._attributes.customerName}\n`;
                                    break;
                                case "Summary":
                                    organizationMessage += `${checkedOrganizations[i].summary}\n`;
                                    break;
                                case "Status":
                                    organizationMessage += `${checkedOrganizations[i].status.name}\n`;
                                    break;
                                case "Payment Status":
                                    organizationMessage += `${checkedOrganizations[i].paymentStatus.name}\n`;
                                    break;
                                case "Collection Status":
                                    organizationMessage += `${checkedOrganizations[i].collectionVerified.name}\n`;
                                    break;
                                case "Grand Total":
                                    organizationMessage += `${checkedOrganizations[i].grandTotal.amountWithCurrency}\n`;
                                    break;
                                case "Paid":
                                    organizationMessage += `${checkedOrganizations[i].paidTotal.amountWithCurrency}\n`;
                                    break;
                                case "Pending":
                                    organizationMessage += `${checkedOrganizations[i].pendingTotal.amountWithCurrency}\n`;
                                    break;
                                case "Outstanding":
                                    organizationMessage += `${checkedOrganizations[i].outstandingTotal.amountWithCurrency}\n`;
                                    break;
                                case "Customer Note":
                                    organizationMessage += `${checkedOrganizations[i].customerNote || 'None'}\n`;
                                    break;
                                case "Created Date":
                                    organizationMessage += `${checkedOrganizations[i].createdAt}\n`;
                                    break;
                                case "Organization Link":
                                    organizationMessage += `${window.location.origin + this.$router.resolve({ name: 'show-store-organization', params: { 'store_href': this.store._links.showStore, 'organization_href': checkedOrganizations[i]._links.showOrganization } }).href}\n`;
                                    break;
                            }

                        }

                    });

                    // Add separator only if it's not the last organization
                    if (i < checkedOrganizations.length - 1) {
                        organizationMessage += `\n\n`;
                    }

                    if ((tempMessage.length + organizationMessage.length) > maxLength) {
                        break;
                    }

                    tempMessage += organizationMessage;
                }

                message = tempMessage.trim();

                if (message.length > 0) {
                    const whatsappUrl = `https://wa.me/?text=${encodeURIComponent(message)}`;
                    window.open(whatsappUrl, "_blank");
                }

                this.sendToWhatsappModal.hideModal();
            }
        },
        mounted() {
            this.printPdfModal = this.$refs.printPdfModal;
            this.deleteOrganizationModal = this.$refs.deleteOrganizationModal;
            this.downloadPdfModal = this.$refs.downloadPdfModal;
            this.exportOrganizationsModal = this.$refs.exportOrganizationsModal;
            this.changeStatusModal = this.$refs.changeStatusModal;
            this.deleteOrganizationsModal = this.$refs.deleteOrganizationsModal;
            this.sendToWhatsappModal = this.$refs.sendToWhatsappModal;
            this.assignTeamMemberModal = this.$refs.assignTeamMemberModal;
        },
        created() {
            this.isLoadingOrganizations = true;
            this.searchTerm = this.$route.query.searchTerm;
            if(this.$route.query.filterExpressions) this.filterExpressions = this.$route.query.filterExpressions.split('|');
            if(this.$route.query.sortingExpressions) this.sortingExpressions = this.$route.query.sortingExpressions.split('|');
            this.getOrganizations();
        }
    };

</script>
