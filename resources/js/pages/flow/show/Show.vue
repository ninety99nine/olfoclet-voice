<template>
    <div class="text-center pt-4">
        Aagent Milestones for motivation - Remove header
    </div>
    <div class="flex h-[calc(100vh-60px)] bg-gray-50 space-x-2 py-4 px-2">
        <!-- Left Panel: Conversation List -->
        <div class="w-1/4 rounded-2xl shadow-md border border-gray-200 overflow-y-auto">
            <!-- Filters/Tabs -->
            <div class="flex border-b border-gray-200">
                <button class="flex-1 py-3 px-3 text-xs font-medium text-gray-900 bg-gray-100 border-b-2 border-indigo-600">Open</button>
                <button class="flex-1 py-3 px-3 text-xs font-medium text-gray-500 hover:bg-gray-100 cursor-pointer">Newest Activity</button>
            </div>

            <!-- Conversation List -->
            <div class="divide-y-4 divide-gray-500 p-2 space-y-1">
                <!-- Conversation Item -->
                <div v-for="(conversation, index) in conversations" :key="index" @click="selectConversation(index)"
                    :class="[
                        'px-3 py-2.5 rounded-xl border hover:shadow-md hover:border-gray-200 hover:bg-gray-100 cursor-pointer transition-all duration-300',
                        selectedConversation === index ? 'shadow-md border-gray-200 bg-gray-100' : 'border-transparent'
                    ]">
                    <div class="flex items-center space-x-3">
                        <div class="flex-shrink-0">
                            <span class="inline-flex items-center justify-center h-6 w-6 rounded-full text-xs font-medium" :class="conversation.badgeClass">{{ conversation.channelInitial }}</span>
                        </div>
                        <div class="flex-1">
                            <div class="flex justify-between items-center">
                                <p class="text-sm font-medium text-gray-900">{{ conversation.sender }}</p>
                                <p class="text-xs text-gray-500">{{ conversation.timestamp }}</p>
                            </div>
                            <p class="text-xs text-gray-500 truncate">{{ conversation.snippet }}</p>
                        </div>
                        <div v-if="conversation.unread" class="flex-shrink-0">
                            <span class="h-2 w-2 bg-green-500 rounded-full"></span>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Middle Panel: Conversation View -->
        <div class="w-1/2 flex flex-col">
            <!-- Dialer for Incoming Call -->
            <div v-if="conversations[selectedConversation].channel === 'Call' && conversations[selectedConversation].callStatus === 'incoming' && !isAnswered"
                class="bg-gray-100 border border-gray-200 rounded-2xl shadow-xl transition-all space-y-4 mb-4">
                <!-- Conversation Header -->
                <div class="pt-4 px-6 border-b border-gray-100 flex items-center justify-between">
                    <div class="flex items-center space-x-3">
                        <img :src="'/images/calling.gif'" class="w-8" />
                        <div>
                            <p class="font-semibold">{{ conversations[selectedConversation].name }}</p>
                            <p class="text-xs">{{ conversations[selectedConversation].sender }}</p>
                        </div>
                    </div>
                    <div class="flex flex-col items-end space-y-1 text-right">
                        <p class="text-xs text-gray-500 mr-2">Waiting: {{ conversations[selectedConversation].waitingTime }}</p>
                        <p class="bg-gray-200 text-gray-600 rounded-full px-3 py-1 text-xs font-medium cursor-pointer transition-all">Queue: Sales Support</p>
                        <p class="text-gray-500 text-sm">Transfer from Bonolo Tabona</p>
                    </div>
                </div>

                <div class="flex justify-end items-center px-6 pb-4">
                    <div class="flex space-x-4">
                        <button @click="answerCall" class="flex items-center justify-center h-10 px-4 text-white rounded-full shadow-md bg-blue-500 hover:bg-blue-600 cursor-pointer transition-all hover:scale-110 active:scale-100 space-x-1">
                            <IterationCcw size="16"></IterationCcw>
                            <span class="text-sm">Next Call</span>
                        </button>
                        <button @click="answerCall" class="flex items-center justify-center h-10 px-4 text-white rounded-full shadow-md bg-blue-500 hover:bg-blue-600 cursor-pointer transition-all hover:scale-110 active:scale-100 space-x-1">
                            <ArrowRightLeft size="16"></ArrowRightLeft>
                            <span class="text-sm">Transfer</span>
                        </button>
                        <button @click="answerCall" class="flex items-center justify-center w-10 h-10 text-white rounded-full shadow-md bg-green-600 hover:bg-green-700 cursor-pointer transition-all hover:scale-110 active:scale-100">
                            <Phone size="20" stroke-width="0" fill="#ffffff"></Phone>
                        </button>
                        <button @click="answerCall" class="flex items-center rotate-[135deg] justify-center w-10 h-10 text-white rounded-full shadow-lg bg-red-600 hover:bg-red-700 cursor-pointer transition-all hover:scale-110 active:scale-100">
                            <Phone size="20" stroke-width="0" fill="#ffffff"></Phone>
                        </button>
                    </div>
                </div>
            </div>

            <!-- Tabs (Pills) - Sticky at the top -->
            <div class="bg-white rounded-t-2xl shadow-sm border border-gray-200 py-3">
                <div class="flex justify-center space-x-2">
                    <button @click="selectTab('Script')" :class="selectedTab === 'Script' ? 'bg-indigo-600 text-white' : 'bg-gray-200 text-gray-700 hover:bg-gray-300'" class="rounded-full px-4 py-2 text-sm font-medium cursor-pointer transition-all">Script</button>
                    <button @click="selectTab('Calls')" :class="selectedTab === 'Calls' ? 'bg-indigo-600 text-white' : 'bg-gray-200 text-gray-700 hover:bg-gray-300'" class="rounded-full px-4 py-2 text-sm font-medium cursor-pointer transition-all">Calls</button>
                    <button @click="selectTab('SMS')" :class="selectedTab === 'SMS' ? 'bg-indigo-600 text-white' : 'bg-gray-200 text-gray-700 hover:bg-gray-300'" class="rounded-full px-4 py-2 text-sm font-medium cursor-pointer transition-all">SMS</button>
                    <button @click="selectTab('Whatsapp')" :class="selectedTab === 'Whatsapp' ? 'bg-indigo-600 text-white' : 'bg-gray-200 text-gray-700 hover:bg-gray-300'" class="rounded-full px-4 py-2 text-sm font-medium cursor-pointer transition-all">Whatsapp</button>
                    <button @click="selectTab('Email')" :class="selectedTab === 'Email' ? 'bg-indigo-600 text-white' : 'bg-gray-200 text-gray-700 hover:bg-gray-300'" class="rounded-full px-4 py-2 text-sm font-medium cursor-pointer transition-all">Email</button>
                </div>
            </div>

            <div class="flex flex-col flex-grow rounded-b-2xl shadow-md border border-gray-200 overflow-y-auto">

                <!-- Conversation Header -->
                <div v-if="conversations[selectedConversation].channel != 'Call'" class="p-3 border-b border-gray-200 flex items-center space-x-3">
                    <span class="shadow inline-flex items-center justify-center h-10 w-10 rounded-full text-xs font-medium" :class="conversations[selectedConversation].badgeClass">{{ conversations[selectedConversation].channelInitial }}</span>
                    <div>
                        <p class="font-semibold">{{ conversations[selectedConversation].name }}</p>
                        <p class="text-xs">{{ conversations[selectedConversation].sender }}</p>
                    </div>
                </div>

                <!-- Conversation Messages -->
                <div class="flex flex-col flex-grow">

                    <!-- Scrollable Content -->
                    <div class="p-3 overflow-y-auto flex-grow" :class="[{ 'bg-slate-50' : conversations[selectedConversation].channel === 'SMS' }, { 'whatsapp-background': conversations[selectedConversation].channel === 'WhatsApp' }]">

                        <!-- Script Section -->
                        <div v-if="selectedTab === 'Script'" class="bg-white p-6 rounded-lg shadow-sm border border-gray-200 space-y-6">

                            <!-- Greeting -->
                            <div>
                                <h3 class="text-lg font-semibold text-gray-900 mb-2">Greeting</h3>
                                <p class="text-sm text-gray-700">Hello {{ conversations[selectedConversation].name }}, thank you for contacting Orange Botswana. My name is [Your Name], and I’m here to assist you today. I see you had a missed call earlier and requested a callback. Let’s get started!</p>
                            </div>

                            <!-- Verification -->
                            <div>
                                <h3 class="text-lg font-semibold text-gray-900 mb-2">Verification</h3>
                                <p class="text-sm text-gray-700 mb-2">To ensure I’m speaking with the right person, please confirm your details:</p>
                                <div class="space-y-3">
                                    <div>
                                        <label class="text-sm text-gray-700">First Name:</label>
                                        <input type="text" :value="conversations[selectedConversation].name.split(' ')[0]" placeholder="Enter first name" class="w-full border border-gray-300 rounded-lg px-2 py-1 text-sm focus:outline-none focus:ring-2 focus:ring-indigo-600 mt-1">
                                    </div>
                                    <div>
                                        <label class="text-sm text-gray-700">Email:</label>
                                        <input type="email" placeholder="Enter email" class="w-full border border-gray-300 rounded-lg px-2 py-1 text-sm focus:outline-none focus:ring-2 focus:ring-indigo-600 mt-1">
                                    </div>
                                </div>
                            </div>

                            <!-- Issue Resolution -->
                            <div>
                                <h3 class="text-lg font-semibold text-gray-900 mb-2">Issue Resolution</h3>
                                <p class="text-sm text-gray-700 mb-2">I see you mentioned: "{{ conversations[selectedConversation].snippet }}". Let’s address this:</p>
                                <ul class="list-disc list-inside text-sm text-gray-700 space-y-1">
                                    <li>Confirm if the issue still persists: “Are you still experiencing this issue, or has it been resolved since your last call?”</li>
                                    <li>If unresolved, assure the client: “I’ll ensure we get this sorted out for you. Let’s check your account details and recent activity.”</li>
                                    <li>Provide a solution: “I can schedule a follow-up call with one of our technical specialists to assist you further. Alternatively, I can escalate this to our support team for immediate action.”</li>
                                </ul>
                            </div>

                            <!-- Promotions -->
                            <div>
                                <h3 class="text-lg font-semibold text-gray-900 mb-2">Promotions</h3>
                                <p class="text-sm text-gray-700 mb-2">Before we wrap up, I’d like to share a special offer with you, {{ conversations[selectedConversation].name.split(' ')[0] }}:</p>
                                <div class="bg-indigo-50 p-4 rounded-lg">
                                    <p class="text-sm text-indigo-800">Upgrade to our Family Plan today and get 20% off your next bill! This plan includes unlimited calls and data for up to 5 family members. Would you like to learn more?</p>
                                </div>
                            </div>

                            <!-- Farewell -->
                            <div>
                                <h3 class="text-lg font-semibold text-gray-900 mb-2">Farewell</h3>
                                <p class="text-sm text-gray-700">Thank you for choosing Orange Botswana, {{ conversations[selectedConversation].name.split(' ')[0] }}. It was a pleasure assisting you today. If you have any further questions, feel free to reach out. Have a great day!</p>
                            </div>

                            <!-- Follow-Up Actions -->
                            <div>
                                <h3 class="text-lg font-semibold text-gray-900 mb-2">Follow-Up Actions</h3>
                                <ul class="list-disc list-inside text-sm text-gray-700 space-y-1">
                                    <li>Log the call details in the CRM system.</li>
                                    <li>If the issue was escalated, confirm the ticket number: [Ticket Number].</li>
                                    <li>Schedule a follow-up call if requested: [Schedule Follow-Up Button].</li>
                                    <li>Send a promotional email about the Family Plan: [Send Email Button].</li>
                                </ul>
                            </div>
                        </div>

                        <!-- Expanded Dialer Options After Answering -->
                        <div v-else-if="selectedTab === 'Calls' && conversations[selectedConversation].channel === 'Call' && isAnswered" class="mb-3 flex justify-center">
                            <div class="bg-white p-6 rounded-lg shadow-sm border border-gray-200 hover:shadow-md transition-all text-center">
                                <div class="flex items-center justify-center space-x-3 mb-4">
                                    <Phone class="h-6 w-6 text-indigo-600" />
                                    <p class="text-sm font-medium text-gray-900">Call in Progress</p>
                                </div>
                                <div class="grid grid-cols-3 gap-3">
                                    <button class="p-3 bg-gray-100 rounded-lg hover:bg-gray-200 flex flex-col items-center">
                                        <MicOff class="h-5 w-5 text-gray-600" />
                                        <span class="text-xs text-gray-600 mt-1">Mute</span>
                                    </button>
                                    <button class="p-3 bg-gray-100 rounded-lg hover:bg-gray-200 flex flex-col items-center">
                                        <Pause class="h-5 w-5 text-gray-600" />
                                        <span class="text-xs text-gray-600 mt-1">Hold</span>
                                    </button>
                                    <button class="p-3 bg-gray-100 rounded-lg hover:bg-gray-200 flex flex-col items-center">
                                        <ArrowRight class="h-5 w-5 text-gray-600" />
                                        <span class="text-xs text-gray-600 mt-1">Transfer</span>
                                    </button>
                                    <button class="p-3 bg-gray-100 rounded-lg hover:bg-gray-200 flex flex-col items-center">
                                        <PlusCircle class="h-5 w-5 text-gray-600" />
                                        <span class="text-xs text-gray-600 mt-1">Add Call</span>
                                    </button>
                                    <button class="p-3 bg-gray-100 rounded-lg hover:bg-gray-200 flex flex-col items-center">
                                        <Tag class="h-5 w-5 text-gray-600" />
                                        <span class="text-xs text-gray-600 mt-1">Tag</span>
                                    </button>
                                </div>
                            </div>
                        </div>

                        <!-- Messages based on Channel -->
                        <div v-else-if="selectedTab === 'Calls' && conversations[selectedConversation].channel === 'Call'">
                            <!-- Call Channel Messages -->
                            <div class="mb-3 flex justify-center">
                                <span class="text-xs text-gray-500 bg-gray-100 px-2 py-1 rounded-full">Missed Call - 10:45 AM</span>
                            </div>
                            <div class="mb-3 flex justify-start">
                                <div class="max-w-md bg-gray-100 text-gray-900 text-sm rounded-lg p-2">
                                    <p>Hi, I called earlier but couldn’t get through. Can someone call me back?</p>
                                    <p class="text-xs text-gray-500 mt-1">10:46 AM</p>
                                </div>
                            </div>
                            <div v-if="isAnswered" class="mb-3 flex justify-end">
                                <div class="max-w-md bg-indigo-600 text-white text-sm rounded-lg p-2">
                                    <p>Sure, I’ll call you back shortly. Apologies for the inconvenience!</p>
                                    <p class="text-xs text-indigo-200 mt-1">10:48 AM</p>
                                </div>
                            </div>
                        </div>

                        <div v-else-if="selectedTab === 'Email' && conversations[selectedConversation].channel === 'Email'">
                            <!-- Email Channel Messages -->
                            <div class="mb-3 flex justify-start">
                                <div class="max-w-md bg-gray-100 text-gray-900 text-sm rounded-lg p-2">
                                    <p class="text-xs text-gray-500 mb-1">From: john.doe@email.com</p>
                                    <p class="text-xs text-gray-500 mb-1">To: support@orangebotswana.com</p>
                                    <p>Billing Inquiry - Urgent</p>
                                    <p class="mt-1">Dear Support Team, I noticed an error in my latest bill. Can you please assist?</p>
                                    <p class="text-xs text-gray-500 mt-1">10:30 AM</p>
                                </div>
                            </div>
                            <div class="mb-3 flex justify-end">
                                <div class="max-w-md bg-indigo-600 text-white text-sm rounded-lg p-2">
                                    <p class="text-xs text-indigo-200 mb-1">From: support@orangebotswana.com</p>
                                    <p class="text-xs text-indigo-200 mb-1">To: john.doe@email.com</p>
                                    <p>Dear John, I’m looking into your billing issue and will get back to you shortly.</p>
                                    <p class="text-xs text-indigo-200 mt-1">10:32 AM</p>
                                </div>
                            </div>
                        </div>

                        <div v-else-if="selectedTab === 'Whatsapp' && conversations[selectedConversation].channel === 'WhatsApp'">
                            <!-- WhatsApp Channel Messages -->
                            <!-- Date Separator -->
                            <div class="mb-3 flex justify-center">
                                <span class="text-xs text-gray-700 font-bold bg-white px-3 py-1 rounded-lg">Wed, 19 Mar</span>
                            </div>

                            <!-- Customer Message -->
                            <div class="mb-3 flex justify-start">
                                <div class="max-w-md bg-white text-gray-900 text-sm rounded-lg rounded-bl-none p-2 relative whatsapp-bubble-customer">
                                    <p>Need help with my plan. Can you suggest a better option?</p>
                                    <p class="text-xs text-gray-500 mt-1 text-right flex items-center justify-end">
                                        10:15 AM
                                    </p>
                                </div>
                            </div>

                            <!-- Agent Message -->
                            <div class="mb-3 flex justify-end">
                                <div class="max-w-md bg-green-200 text-gray-900 text-sm rounded-lg rounded-br-none p-2 relative whatsapp-bubble-agent">
                                    <p>Hi! I’d recommend our Family Plan for better value. I’ll send you the details.</p>
                                    <p class="text-xs text-gray-500 mt-1 text-right flex items-center justify-end">
                                        10:17 AM
                                        <CheckCheck size="16" class="text-gray-500 ml-1" />
                                    </p>
                                </div>
                            </div>
                        </div>

                        <div v-else-if="selectedTab === 'SMS' && conversations[selectedConversation].channel === 'SMS'">
                            <!-- SMS Channel Messages -->
                            <div class="mb-3 flex justify-start">
                                <div class="max-w-md bg-gray-200 text-gray-900 text-sm rounded-lg p-2">
                                    <p>Network issue reported. No signal since morning.</p>
                                    <p class="text-xs text-gray-500 mt-1">10:00 AM</p>
                                </div>
                            </div>
                            <div class="mb-3 flex justify-end">
                                <div class="max-w-md bg-indigo-600 text-white text-sm rounded-lg p-2">
                                    <p>We’re investigating the network issue in your area. Will update you soon.</p>
                                    <p class="text-xs text-indigo-200 mt-1">10:02 AM</p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Reply Input (Hidden during Incoming Call) -->
                <div v-if="conversations[selectedConversation].channel !== 'Call' || isAnswered" class="p-3 bg-slate-50 border-t border-gray-200">
                    <div class="flex items-center space-x-2 mb-2">
                        <select class="border border-gray-300 rounded-lg px-2 py-1 text-xs focus:outline-none focus:ring-2 focus:ring-indigo-600">
                            <option>Reply via Call</option>
                            <option>Reply via Email</option>
                            <option>Reply via WhatsApp</option>
                            <option>Reply via SMS</option>
                        </select>
                        <button class="p-1 text-gray-500 hover:text-gray-700">
                            <Bold class="h-4 w-4" />
                        </button>
                        <button class="p-1 text-gray-500 hover:text-gray-700">
                            <Italic class="h-4 w-4" />
                        </button>
                        <button class="p-1 text-gray-500 hover:text-gray-700">
                            <Link class="h-4 w-4" />
                        </button>
                        <button class="p-1 text-gray-500 hover:text-gray-700">
                            <Smile class="h-4 w-4" />
                        </button>
                    </div>
                    <div class="flex space-x-2">
                        <textarea placeholder="Type your reply..." rows="4" class="flex-1 border border-gray-300 rounded-lg px-2 py-1 text-sm focus:outline-none focus:ring-2 focus:ring-indigo-600"></textarea>
                        <button class="max-h-10 px-3 py-1 bg-indigo-600 text-white rounded-lg hover:bg-indigo-700 transition-all">
                            Send
                        </button>
                    </div>
                </div>
            </div>
        </div>

        <!-- Right Panel: Details Sidebar -->
        <div class="w-1/4 rounded-2xl shadow-md border border-gray-200 overflow-y-auto">
            <!-- Tabs -->
            <div class="flex border-b border-gray-200">
                <button class="flex-1 py-3 px-3 text-xs font-medium text-gray-900 bg-gray-100 border-b-2 border-indigo-600">Details</button>
                <button class="flex-1 py-3 px-3 text-xs font-medium text-gray-500 hover:bg-gray-100 cursor-pointer">Copilot</button>
            </div>

            <!-- Details Tab Content -->
            <div class="p-4">
                <!-- Details Header -->
                <div class="flex justify-between items-center mb-3">
                    <p class="text-sm font-semibold text-gray-900">Details</p>
                    <button class="text-gray-500 hover:text-gray-700">
                        <X class="h-4 w-4" />
                    </button>
                </div>

                <!-- Assignee -->
                <div class="mb-3">
                    <p class="text-xs text-gray-500">Assignee</p>
                    <p class="text-sm text-gray-900">Julian Tabona</p>
                </div>

                <!-- Team Inbox -->
                <div class="mb-3">
                    <p class="text-xs text-gray-500">Team Inbox</p>
                    <p class="text-sm text-gray-900">Unassigned</p>
                </div>

                <!-- User Data -->
                <div class="mb-3">
                    <p class="text-xs text-gray-500 mb-1">User Data</p>
                    <p class="text-sm text-gray-900">Phone: +267 123 4567</p>
                    <p class="text-sm text-gray-900">Email: Not provided</p>
                </div>

                <!-- User Notes -->
                <div class="mb-3">
                    <p class="text-xs text-gray-500 mb-1">User Notes</p>
                    <p class="text-sm text-gray-500">No notes available</p>
                </div>

                <!-- Similar Conversations -->
                <div class="mb-3">
                    <p class="text-xs text-gray-500 mb-1">Similar Conversations</p>
                    <p class="text-sm text-gray-500">No similar conversations found</p>
                </div>
            </div>

            <!-- Copilot Tab Content (Placeholder) -->
            <div class="hidden">
                <p class="text-sm font-semibold text-gray-900 mb-3">Copilot</p>
                <p class="text-sm text-gray-500">AI-powered suggestions coming soon. Use Copilot to get reply suggestions, customer insights, and more.</p>
            </div>
        </div>
    </div>
</template>

<script>
import { Bold, Italic, Link, Smile, X, MicOff, Pause, ArrowRight, PlusCircle, Tag, Moon, Phone, Check, CheckCheck, IterationCcw, ArrowRightLeft } from 'lucide-vue-next';

export default {
    components: {
        Bold,
        Italic,
        Link,
        Smile,
        X,
        MicOff,
        Pause,
        ArrowRight,
        PlusCircle,
        Tag,
        Moon,
        Phone,
        Check,
        CheckCheck,
        IterationCcw,
        ArrowRightLeft
    },
    data() {
        return {
            selectedConversation: 0, // Default to the first conversation
            selectedTab: 'Script', // Default to the first tab
            isAnswered: false, // Tracks if the call has been answered
            conversations: [
                {
                    name: 'Julian Tabona',
                    channel: 'Call',
                    channelInitial: 'C',
                    badgeClass: 'bg-indigo-100 text-indigo-600',
                    sender: '+267 123 4567',
                    snippet: 'Missed call - Callback requested',
                    timestamp: '10:45 AM',
                    unread: true,
                    callStatus: 'incoming', // Indicates this is an incoming call
                    waitingTime: '24 secs'
                },
                {
                    name: 'Tony Stark',
                    channel: 'Email',
                    channelInitial: 'E',
                    badgeClass: 'bg-indigo-100 text-indigo-600',
                    sender: 'john.doe@email.com',
                    snippet: 'Billing inquiry - Urgent',
                    timestamp: '10:30 AM',
                    unread: false
                },
                {
                    name: 'David Phelps',
                    channel: 'WhatsApp',
                    channelInitial: 'W',
                    badgeClass: 'bg-green-100 text-green-600',
                    sender: '+267 987 6543',
                    snippet: 'Need help with plan',
                    timestamp: '10:15 AM',
                    unread: false
                },
                {
                    name: 'Bruno Mars',
                    channel: 'SMS',
                    channelInitial: 'S',
                    badgeClass: 'bg-blue-100 text-blue-600',
                    sender: '+267 555 1234',
                    snippet: 'Network issue reported',
                    timestamp: '10:00 AM',
                    unread: false
                }
            ]
        };
    },
    methods: {
        selectConversation(index) {
            this.selectedConversation = index;
            // Reset isAnswered when switching conversations
            this.isAnswered = false;
        },
        answerCall() {
            this.isAnswered = true;
            // In a real implementation, this would also trigger the actual call answering logic
        },
        selectTab(tab) {
            this.selectedTab = tab;
        }
    }
};
</script>

<style>
.whatsapp-background {
    background-image: url('/images/whatsapp/background.png');
    background-repeat: repeat;
    background-size: auto;
}

.whatsapp-bubble-customer {
    position: relative;
}

.whatsapp-bubble-customer::before {
    content: '';
    position: absolute;
    bottom: 0px;
    left: -11px;
    width: 15px;
    height: 16px;
    background: #ffffff;
    clip-path: polygon(90% 0, 90% 100%, 0 100%);
}

.whatsapp-bubble-agent {
    position: relative;
}

.whatsapp-bubble-agent::before {
    content: '';
    position: absolute;
    bottom: 0px;
    right: -12px;
    width: 15px;
    height: 16px;
    background: #b9f8cf;
    clip-path: polygon(0% 0, 70% 100%, 0 100%);
}
</style>
