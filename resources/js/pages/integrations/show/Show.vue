<template>
    <div class="min-h-screen bg-gray-50 p-6">
        <!-- Header Section -->
        <div class="mb-8">
            <div class="flex justify-between items-center">
                <div>
                    <h1 class="text-3xl font-bold text-gray-900">Integrations</h1>
                    <p class="text-lg text-gray-600">Manage your integrations with third-party services</p>
                </div>
            </div>
        </div>

        <!-- Integrations Cards -->
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-6 mb-8">
            <!-- Microsoft Dynamics Integration -->
            <div class="bg-white p-6 rounded-lg shadow-sm border border-gray-200 hover:shadow-md transition-all">
                <div class="flex items-start space-x-3">
                    <div class="p-3 bg-gray-100 rounded-full">
                        <img :src="'/images/integrations/microsoftdynamics.svg'" alt="Microsoft Dynamics Logo" class="h-6 w-6">
                    </div>
                    <div class="flex-1">
                        <div class="flex justify-between items-center">
                            <p class="text-lg font-semibold text-gray-900">Microsoft Dynamics</p>
                            <span class="inline-flex px-2 py-1 text-xs font-semibold rounded-full bg-green-100 text-green-800">
                                Connected
                            </span>
                        </div>
                        <p class="text-sm text-gray-500 mt-1">
                            Sync customer interactions and data for a unified view and streamlined support.
                        </p>
                    </div>
                </div>
            </div>

            <!-- HubSpot Integration -->
            <div class="bg-white p-6 rounded-lg shadow-sm border border-gray-200 hover:shadow-md transition-all">
                <div class="flex items-start space-x-3">
                    <div class="p-3 bg-gray-100 rounded-full">
                        <img :src="'/images/integrations/hubspot.svg'" alt="HubSpot Logo" class="h-6 w-6">
                    </div>
                    <div class="flex-1">
                        <div class="flex justify-between items-center">
                            <p class="text-lg font-semibold text-gray-900">HubSpot</p>
                            <span class="inline-flex px-2 py-1 text-xs font-semibold rounded-full bg-green-100 text-green-800">
                                Connected
                            </span>
                        </div>
                        <p class="text-sm text-gray-500 mt-1">
                            Centralize customer relationships, automate workflows, and capture leads from all channels.
                        </p>
                    </div>
                </div>
            </div>

            <!-- Salesforce Integration -->
            <div class="bg-white p-6 rounded-lg shadow-sm border border-gray-200 hover:shadow-md transition-all">
                <div class="flex items-start space-x-3">
                    <div class="p-3 bg-gray-100 rounded-full">
                        <img :src="'/images/integrations/salesforce.svg'" alt="Salesforce Logo" class="h-6 w-6">
                    </div>
                    <div class="flex-1">
                        <div class="flex justify-between items-center">
                            <p class="text-lg font-semibold text-gray-900">Salesforce</p>
                            <span class="inline-flex px-2 py-1 text-xs font-semibold rounded-full bg-green-100 text-green-800">
                                Connected
                            </span>
                        </div>
                        <p class="text-sm text-gray-500 mt-1">
                            Unify customer interactions and CRM data for smarter, data-driven engagement.
                        </p>
                    </div>
                </div>
            </div>

            <!-- Zendesk Integration -->
            <div class="bg-white p-6 rounded-lg shadow-sm border border-gray-200 hover:shadow-md transition-all">
                <div class="flex items-start space-x-3">
                    <div class="p-3 bg-gray-100 rounded-full">
                        <img :src="'/images/integrations/zendesk.svg'" alt="Zendesk Logo" class="h-6 w-6">
                    </div>
                    <div class="flex-1">
                        <div class="flex justify-between items-center">
                            <p class="text-lg font-semibold text-gray-900">Zendesk</p>
                            <span class="inline-flex px-2 py-1 text-xs font-semibold rounded-full bg-green-100 text-green-800">
                                Connected
                            </span>
                        </div>
                        <p class="text-sm text-gray-500 mt-1">
                            Manage support tickets and customer interactions from a single dashboard.
                        </p>
                    </div>
                </div>
            </div>

            <!-- Google Sheets Integration -->
            <div class="bg-white p-6 rounded-lg shadow-sm border border-gray-200 hover:shadow-md transition-all">
                <div class="flex items-start space-x-3">
                    <div class="p-3 bg-gray-100 rounded-full">
                        <img :src="'/images/integrations/google-sheets.svg'" alt="Google Sheets Logo" class="h-6 w-6">
                    </div>
                    <div class="flex-1">
                        <div class="flex justify-between items-center">
                            <p class="text-lg font-semibold text-gray-900">Google Sheets</p>
                            <span class="inline-flex px-2 py-1 text-xs font-semibold rounded-full bg-green-100 text-green-800">
                                Connected
                            </span>
                        </div>
                        <p class="text-sm text-gray-500 mt-1">
                            Log customer data and generate real-time reports in one place.
                        </p>
                    </div>
                </div>
            </div>

            <!-- Pipedrive Integration -->
            <div class="bg-white p-6 rounded-lg shadow-sm border border-gray-200 hover:shadow-md transition-all">
                <div class="flex items-start space-x-3">
                    <div class="p-3 bg-gray-100 rounded-full">
                        <img :src="'/images/integrations/pipedrive.svg'" alt="Pipedrive Logo" class="h-6 w-6">
                    </div>
                    <div class="flex-1">
                        <div class="flex justify-between items-center">
                            <p class="text-lg font-semibold text-gray-900">Pipedrive</p>
                            <span class="inline-flex px-2 py-1 text-xs font-semibold rounded-full bg-green-100 text-green-800">
                                Connected
                            </span>
                        </div>
                        <p class="text-sm text-gray-500 mt-1">
                            Track sales communications and customer touchpoints from your dashboard.
                        </p>
                    </div>
                </div>
            </div>

            <!-- Zoho Integration -->
            <div class="bg-white p-6 rounded-lg shadow-sm border border-gray-200 hover:shadow-md transition-all">
                <div class="flex items-start space-x-3">
                    <div class="p-3 bg-gray-100 rounded-full">
                        <img :src="'/images/integrations/zoho.svg'" alt="Zoho Logo" class="h-6 w-6">
                    </div>
                    <div class="flex-1">
                        <div class="flex justify-between items-center">
                            <p class="text-lg font-semibold text-gray-900">Zoho</p>
                            <span class="inline-flex px-2 py-1 text-xs font-semibold rounded-full bg-gray-100 text-gray-800">
                                Not Connected
                            </span>
                        </div>
                        <p class="text-sm text-gray-500 mt-1">
                            Manage customer data, sales, and support from a unified platform.
                        </p>
                    </div>
                </div>
            </div>

            <!-- Freshdesk Integration -->
            <div class="bg-white p-6 rounded-lg shadow-sm border border-gray-200 hover:shadow-md transition-all">
                <div class="flex items-start space-x-3">
                    <div class="p-3 bg-gray-100 rounded-full">
                        <img :src="'/images/integrations/freshdesk.svg'" alt="Freshdesk Logo" class="h-6 w-6">
                    </div>
                    <div class="flex-1">
                        <div class="flex justify-between items-center">
                            <p class="text-lg font-semibold text-gray-900">Freshdesk</p>
                            <span class="inline-flex px-2 py-1 text-xs font-semibold rounded-full bg-gray-100 text-gray-800">
                                Not Connected
                            </span>
                        </div>
                        <p class="text-sm text-gray-500 mt-1">
                            Streamline customer support and manage service tickets efficiently.
                        </p>
                    </div>
                </div>
            </div>

            <!-- Copper Integration -->
            <div class="bg-white p-6 rounded-lg shadow-sm border border-gray-200 hover:shadow-md transition-all">
                <div class="flex items-start space-x-3">
                    <div class="p-3 bg-gray-100 rounded-full">
                        <img :src="'/images/integrations/copper.svg'" alt="Copper Logo" class="h-6 w-6">
                    </div>
                    <div class="flex-1">
                        <div class="flex justify-between items-center">
                            <p class="text-lg font-semibold text-gray-900">Copper</p>
                            <span class="inline-flex px-2 py-1 text-xs font-semibold rounded-full bg-gray-100 text-gray-800">
                                Not Connected
                            </span>
                        </div>
                        <p class="text-sm text-gray-500 mt-1">
                            Manage customer relationships directly from your Gmail inbox for efficient outreach.
                        </p>
                    </div>
                </div>
            </div>
        </div>
    </div>
</template>

<script>
export default {};
</script>

<style>
/* Add any custom styles if needed */
</style>
