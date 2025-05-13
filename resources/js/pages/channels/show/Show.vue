<template>
    <div class="min-h-screen bg-gray-50 p-6">
        <!-- Header Section -->
        <div class="mb-8">
            <div class="flex justify-between items-center">
                <div>
                    <h1 class="text-3xl font-bold text-gray-900">Channels</h1>
                    <p class="text-lg text-gray-600">Connect multiple channels to manage customer interactions seamlessly</p>
                </div>
            </div>
        </div>

        <!-- Channels Cards -->
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-6 mb-8">
            <!-- WhatsApp Channel -->
            <div class="bg-white p-6 rounded-lg shadow-sm border border-gray-200 hover:shadow-md transition-all">
                <div class="flex flex-col h-full relative rounded-xl p-6">
                    <div class="flex justify-center mb-4">
                        <img :src="'/images/channels/whatsapp.png'" alt="WhatsApp Logo" class="w-12 h-12 object-cover" />
                    </div>
                    <div class="text-center mb-4">
                        <h3 class="text-lg font-semibold text-gray-900">WhatsApp</h3>
                        <span class="inline-flex px-2 py-1 text-xs font-semibold rounded-full bg-green-100 text-green-800">
                            Connected
                        </span>
                    </div>
                    <p class="flex-grow text-sm text-gray-500 mb-4 text-center">
                        Engage customers on WhatsApp for instant messaging, support, and automated responses.
                    </p>
                    <button class="w-full px-4 py-2 bg-indigo-600 text-white rounded-lg hover:bg-indigo-700 hover:scale-105 active:scale-100 cursor-pointer transition-all">
                        Connect Channel
                    </button>
                </div>
            </div>

            <!-- Telegram Channel -->
            <div class="bg-white p-6 rounded-lg shadow-sm border border-gray-200 hover:shadow-md transition-all">
                <div class="flex flex-col h-full relative rounded-xl p-6">
                    <div class="flex justify-center mb-4">
                        <img :src="'/images/channels/telegram.png'" alt="Telegram Logo" class="w-12 h-12 object-cover" />
                    </div>
                    <div class="text-center mb-4">
                        <h3 class="text-lg font-semibold text-gray-900">Telegram</h3>
                        <span class="inline-flex px-2 py-1 text-xs font-semibold rounded-full bg-gray-100 text-gray-800">
                            Not Connected
                        </span>
                    </div>
                    <p class="flex-grow text-sm text-gray-500 mb-4 text-center">
                        Engage customers on Telegram for instant messaging, support, and automated responses.
                    </p>
                    <button class="w-full px-4 py-2 bg-indigo-600 text-white rounded-lg hover:bg-indigo-700 hover:scale-105 active:scale-100 cursor-pointer transition-all">
                        Connect Channel
                    </button>
                </div>
            </div>

            <!-- Instagram Direct Channel -->
            <div class="bg-white p-6 rounded-lg shadow-sm border border-gray-200 hover:shadow-md transition-all">
                <div class="flex flex-col h-full relative rounded-xl p-6">
                    <div class="flex justify-center mb-4">
                        <img :src="'/images/channels/instagram.png'" alt="Instagram Logo" class="w-12 h-12 object-cover" />
                    </div>
                    <div class="text-center mb-4">
                        <h3 class="text-lg font-semibold text-gray-900">Instagram Direct</h3>
                        <span class="inline-flex px-2 py-1 text-xs font-semibold rounded-full bg-gray-100 text-gray-800">
                            Not Connected
                        </span>
                    </div>
                    <p class="flex-grow text-sm text-gray-500 mb-4 text-center">
                        Manage Instagram Direct messages for customer engagement and support.
                    </p>
                    <button class="w-full px-4 py-2 bg-indigo-600 text-white rounded-lg hover:bg-indigo-700 hover:scale-105 active:scale-100 cursor-pointer transition-all">
                        Connect Channel
                    </button>
                </div>
            </div>

            <!-- Email Channel -->
            <div class="bg-white p-6 rounded-lg shadow-sm border border-gray-200 hover:shadow-md transition-all">
                <div class="flex flex-col h-full relative rounded-xl p-6">
                    <div class="flex justify-center mb-4">
                        <img :src="'/images/channels/email.png'" alt="Email Logo" class="w-12 h-12 object-cover" />
                    </div>
                    <div class="text-center mb-4">
                        <h3 class="text-lg font-semibold text-gray-900">Email</h3>
                        <span class="inline-flex px-2 py-1 text-xs font-semibold rounded-full bg-green-100 text-green-800">
                            Connected
                        </span>
                    </div>
                    <p class="flex-grow text-sm text-gray-500 mb-4 text-center">
                        Handle email messages for customer engagement and support.
                    </p>
                    <button class="w-full px-4 py-2 bg-indigo-600 text-white rounded-lg hover:bg-indigo-700 hover:scale-105 active:scale-100 cursor-pointer transition-all">
                        Manage Channel
                    </button>
                </div>
            </div>

            <!-- SMS Channel -->
            <div class="bg-white p-6 rounded-lg shadow-sm border border-gray-200 hover:shadow-md transition-all">
                <div class="flex flex-col h-full relative rounded-xl p-6">
                    <div class="flex justify-center mb-4">
                        <img :src="'/images/channels/sms.png'" alt="SMS Logo" class="w-12 h-12 object-cover" />
                    </div>
                    <div class="text-center mb-4">
                        <h3 class="text-lg font-semibold text-gray-900">SMS</h3>
                        <span class="inline-flex px-2 py-1 text-xs font-semibold rounded-full bg-green-100 text-green-800">
                            Connected
                        </span>
                    </div>
                    <p class="flex-grow text-sm text-gray-500 mb-4 text-center">
                        Send and receive SMS for quick customer engagement and support.
                    </p>
                    <button class="w-full px-4 py-2 bg-indigo-600 text-white rounded-lg hover:bg-indigo-700 hover:scale-105 active:scale-100 cursor-pointer transition-all">
                        Connect Channel
                    </button>
                </div>
            </div>

            <!-- Twitter DM Channel -->
            <div class="bg-white p-6 rounded-lg shadow-sm border border-gray-200 hover:shadow-md transition-all">
                <div class="flex flex-col h-full relative rounded-xl p-6">
                    <div class="flex justify-center mb-4">
                        <img :src="'/images/channels/x.png'" alt="Twitter Logo" class="w-12 h-12 object-cover" />
                    </div>
                    <div class="text-center mb-4">
                        <h3 class="text-lg font-semibold text-gray-900">X (Twitter) DM</h3>
                        <span class="inline-flex px-2 py-1 text-xs font-semibold rounded-full bg-gray-100 text-gray-800">
                            Not Connected
                        </span>
                    </div>
                    <p class="flex-grow text-sm text-gray-500 mb-4 text-center">
                        Respond to X Direct messages for customer engagement and support.
                    </p>
                    <button class="w-full px-4 py-2 bg-indigo-600 text-white rounded-lg hover:bg-indigo-700 hover:scale-105 active:scale-100 cursor-pointer transition-all">
                        Connect Channel
                    </button>
                </div>
            </div>
        </div>
    </div>
</template>

<script>
export default {
    data() {
        return {};
    },
};
</script>

<style scoped>
/* No additional styles needed since we're using Tailwind classes */
</style>
