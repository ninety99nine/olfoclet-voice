<template>
    <div class="min-h-screen bg-gradient-to-br from-blue-50 to-purple-50 p-6">
        <!-- Header Section -->
        <div class="mb-8">
            <h1 class="text-3xl font-bold text-gray-900">NexFlo AI</h1>
            <p class="text-lg text-gray-600 mt-2">Empower your call center with AI agents that handle inbound calls seamlessly. Rent a virtual agent below and pair them with your IVR call flows to deliver exceptional customer experiences.</p>
        </div>

        <!-- Agent Cards -->
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-6">
            <!-- Agent Card 1 -->
            <div class="relative bg-gradient-to-br from-blue-100 to-purple-100 rounded-2xl shadow-md border border-transparent hover:shadow-lg transition-all p-6 gradient-border">
                <div class="flex justify-center mb-4">
                    <img :src="'/images/avatars/agent1.webp'" alt="Agent Ava Avatar" class="w-16 h-16 rounded-full object-cover">
                </div>
                <div class="text-center mb-4">
                    <h3 class="text-lg font-semibold text-gray-900">Ava</h3>
                    <p class="text-sm text-gray-600">Friendly & Efficient</p>
                </div>
                <p class="text-sm text-gray-700 mb-4 text-center">Ava handles inquiries with a warm tone, resolving issues quickly.</p>
                <button class="w-full px-4 py-2 bg-gradient-to-r from-indigo-600 to-blue-600 text-white rounded-lg hover:from-indigo-700 hover:to-blue-700 transition-all">Rent Agent</button>
            </div>

            <!-- Agent Card 2 -->
            <div class="relative bg-gradient-to-br from-blue-100 to-purple-100 rounded-2xl shadow-md border border-transparent hover:shadow-lg transition-all p-6 gradient-border">
                <div class="flex justify-center mb-4">
                    <img :src="'/images/avatars/agent2.webp'" alt="Agent Max Avatar" class="w-16 h-16 rounded-full object-cover">
                </div>
                <div class="text-center mb-4">
                    <h3 class="text-lg font-semibold text-gray-900">Max</h3>
                    <p class="text-sm text-gray-600">Professional & Calm</p>
                </div>
                <p class="text-sm text-gray-700 mb-4 text-center">Max stays calm under pressure, ensuring precise call handling.</p>
                <button class="w-full px-4 py-2 bg-gradient-to-r from-indigo-600 to-blue-600 text-white rounded-lg hover:from-indigo-700 hover:to-blue-700 transition-all">Rent Agent</button>
            </div>

            <!-- Agent Card 3 -->
            <div class="relative bg-gradient-to-br from-blue-100 to-purple-100 rounded-2xl shadow-md border border-transparent hover:shadow-lg transition-all p-6 gradient-border">
                <div class="flex justify-center mb-4">
                    <img :src="'/images/avatars/agent3.webp'" alt="Agent Luna Avatar" class="w-16 h-16 rounded-full object-cover">
                </div>
                <div class="text-center mb-4">
                    <h3 class="text-lg font-semibold text-gray-900">Luna</h3>
                    <p class="text-sm text-gray-600">Empathetic & Supportive</p>
                </div>
                <p class="text-sm text-gray-700 mb-4 text-center">Luna offers empathetic support, perfect for sensitive issues.</p>
                <button class="w-full px-4 py-2 bg-gradient-to-r from-indigo-600 to-blue-600 text-white rounded-lg hover:from-indigo-700 hover:to-blue-700 transition-all">Rent Agent</button>
            </div>

            <!-- Agent Card 4 -->
            <div class="relative bg-gradient-to-br from-blue-100 to-purple-100 rounded-2xl shadow-md border border-transparent hover:shadow-lg transition-all p-6 gradient-border">
                <div class="flex justify-center mb-4">
                    <img :src="'/images/avatars/agent4.jpg'" alt="Agent Theo Avatar" class="w-16 h-16 rounded-full object-cover">
                </div>
                <div class="text-center mb-4">
                    <h3 class="text-lg font-semibold text-gray-900">Theo</h3>
                    <p class="text-sm text-gray-600">Tech-Savvy & Solution-Oriented</p>
                </div>
                <p class="text-sm text-gray-700 mb-4 text-center">Theo excels in technical support, solving complex issues fast.</p>
                <button class="w-full px-4 py-2 bg-gradient-to-r from-indigo-600 to-blue-600 text-white rounded-lg hover:from-indigo-700 hover:to-blue-700 transition-all">Rent Agent</button>
            </div>
        </div>
    </div>
</template>

<script>
export default {
    data() {
        return {};
    }
};
</script>

<style>
.gradient-border {
    position: relative;
    overflow: hidden;
}

.gradient-border::before {
    content: '';
    position: absolute;
    inset: -2px;
    background: linear-gradient(to right, #3b82f6, #a855f7);
    z-index: -1;
    border-radius: 1.5rem;
}
</style>
