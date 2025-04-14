<template>

    <div class="bg-gray-50 p-4 sm:p-6 space-y-4 sm:space-y-6">

        <!-- Page Header -->
        <div class="space-y-1">

            <h2 class="text-xl font-semibold text-gray-900 dark:text-white">Organization Management</h2>

            <p class="text-sm text-gray-600 dark:text-neutral-400 mb-4">
                Manage all organizations and their settings
            </p>

            <div class="flex justify-between">

                <!-- Tabs -->
                <Tabs v-model="activeTab" :tabs="tabs" />

                <AddOrganizationModal @refresh="fetchOrganizations">
                    <template #trigger="{ showModal }">
                        <Button type="primary" size="md" :leftIcon="Plus" leftIconSize="20" :action="showModal">
                        <span>Add Organization</span>
                        </Button>
                    </template>
                </AddOrganizationModal>

            </div>

        </div>


        <!-- Card -->
        <div class="bg-white dark:bg-neutral-800 border border-gray-200 dark:border-neutral-700 rounded-xl">

            <div class="p-4 sm:p-6">

                <div class="flex justify-between items-center mb-4">

                    <div>
                        <h3 class="text-2xl font-semibold text-gray-900 dark:text-white">All Organizations</h3>
                        <p class="text-sm text-gray-500 dark:text-neutral-400">
                            View and manage all organizations using the Telcoflo platform
                        </p>
                    </div>

                </div>

                <!-- Table -->
                <div class="rounded-md border border-gray-200 bg-gray-50 overflow-x-auto">

                    <table class="min-w-full divide-y divide-gray-200">

                        <thead>
                            <tr class="bg-muted/50">
                                <th class="px-4 py-3 text-left text-sm font-semibold">Organization</th>
                                <th class="px-4 py-3 text-left text-sm font-semibold">Country</th>
                                <th class="px-4 py-3 text-left text-sm font-semibold">Status</th>
                                <th class="px-4 py-3 text-left text-sm font-semibold">Users</th>
                                <th class="px-4 py-3 text-left text-sm font-semibold">Created</th>
                                <th class="px-4 py-3 text-right text-sm font-semibold">Actions</th>
                            </tr>
                        </thead>

                        <tbody class="divide-y divide-gray-200 dark:divide-neutral-700">

                            <tr v-for="org in organizations" :key="org.id" class="hover:bg-gray-100">

                                <td class="px-4 py-3">
                                    <div class="flex items-center space-x-2">
                                    <img src="https://placehold.co/21x24" class="size-8 rounded-lg shrink-0" alt="Logo" />
                                    <div class="whitespace-nowrap px-4 py-3 font-medium text-sm">{{ org.name }}</div>
                                    </div>
                                </td>

                                <td class="whitespace-nowrap px-4 py-3 text-sm">{{ org.country }}</td>

                                <td class="px-4 py-3">
                                    <Pill :type="org.active ? 'success' : 'warning'" size="xs">{{ org.active ? 'Active' : 'Inactive' }}</Pill>
                                </td>

                                <td class="whitespace-nowrap px-4 py-3 text-sm">{{ org.users }}</td>

                                <td class="whitespace-nowrap px-4 py-3 text-sm">{{ org.created_at }}</td>

                                <td class="px-4 py-3">

                                    <div class="flex justify-end gap-1">
                                        <Button type="outline" size="xs" :leftIcon="UserPlus" leftIconSize="12"></Button>
                                        <Button type="outline" size="xs" :leftIcon="Pencil" leftIconSize="12"></Button>
                                        <Button type="outlineDanger" size="xs" :leftIcon="Trash2" leftIconSize="12"></Button>
                                    </div>

                                </td>

                            </tr>

                        </tbody>

                    </table>

                </div>

            </div>

        </div>

    </div>

  </template>

<script>

    import axios from 'axios';
    import Pill from '@Partials/Pill.vue';
    import Tabs from '@Partials/Tabs.vue';
    import Button from '@Partials/Button.vue';
    import AddOrganizationModal from '@Pages/Organizations/components/AddOrganizationModal.vue';
    import { Box, Plus, Lock, Trash2, Pencil, UserPlus, Building, ShieldCheck } from 'lucide-vue-next';

    export default {
        components: { Pill, Tabs, Button, AddOrganizationModal },
        data() {
            return {
                Lock,
                Plus,
                Trash2,
                Pencil,
                UserPlus,
                isLoading: true,
                organizations: [],
                activeTab: 'organizations',
                tabs: [
                    { label: 'Organizations', value: 'organizations', icon: Building },
                    { label: 'Subscription Plans', value: 'subscriptions', icon: Box },
                    { label: 'Security', value: 'security', icon: ShieldCheck },
                ],
            }
        },
        computed: {
            hasOrganizations() {
            return this.organizations.length > 0;
            }
        },
        methods: {
            async fetchOrganizations() {
                this.isLoading = true;
                try {
                    const response = await axios.get('/api/organizations');
                    this.organizations = response.data.data;
                } catch (error) {
                    console.error('Failed to fetch organizations:', error);
                } finally {
                    this.isLoading = false;
                }
            }
        },
        mounted() {
            this.fetchOrganizations();
        }
    }

</script>
