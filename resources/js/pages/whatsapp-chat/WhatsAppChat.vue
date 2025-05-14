<template>
    <div class="flex flex-col h-[calc(100vh-59px)]">
        <!-- Chat Header -->
        <div class="bg-teal-800 text-white shadow-sm z-10 relative py-4 px-8 flex items-center space-x-3">
            <img :src="'/images/channels/whatsapp.png'" alt="WhatsApp" class="w-10 h-10" />
            <div>
                <h1 class="text-xl font-semibold">Julian Tabona</h1>
                <p class="text-sm">+26772882239</p>
            </div>
        </div>

        <!-- Chat Messages -->
        <div ref="chatContainer" class="flex-grow overflow-y-auto space-y-2 p-8" style="background-color: #e5ddd5;">
            <div v-if="loading" class="text-center text-gray-500">Loading messages...</div>
            <div v-else-if="error" class="text-center text-red-500">{{ error }}</div>
            <div v-else-if="sortedMessages.length === 0" class="text-center text-gray-500">No messages yet. Start a conversation!</div>
            <div v-else>
                <div v-for="message in sortedMessages" :key="message.id" :class="['flex', message.direction === 'outbound' ? 'justify-end' : 'justify-start']">
                    <div :class="[
                        'my-1 p-2 text-sm flex flex-col relative shadow',
                        message.direction === 'outbound' ? 'ml-auto rounded-lg rounded-tr-none bg-green-200 speech-bubble-right' : 'mr-auto rounded-lg rounded-tl-none bg-white speech-bubble-left'
                    ]">
                        <p class="text-black">{{ message.content }}</p>
                        <div v-if="message.direction === 'outbound'" class="flex items-center justify-end text-gray-600 text-xs text-right leading-none">
                            {{ new Date(message.created_at).toLocaleTimeString([], { hour: 'numeric', minute: '2-digit' }) }}
                            <Clock v-if="message.status === 'pending'" size="14" class="shrink-0 text-gray-500 ml-1" />
                            <CheckCheck v-else size="16" :class="[message.status === 'read' ? 'text-blue-500' : 'text-gray-500', 'shrink-0 ml-1']" />
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Message Input -->
        <div class="bg-gray-200 p-4 flex items-center space-x-3">
            <form @submit.prevent="sendMessage" class="flex items-center space-x-3 w-full">
                <input
                    v-model="newMessage"
                    type="text"
                    placeholder="Type a message..."
                    class="flex-1 px-4 py-2 bg-white border-none rounded-full focus:outline-none focus:ring-2 focus:ring-teal-500"
                    required
                />
                <button
                    type="submit"
                    class="p-2 bg-teal-600 text-white rounded-full hover:bg-teal-700 focus:outline-none focus:ring-2 focus:ring-teal-500 cursor-pointer"
                    :disabled="sending"
                >
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 19l9 2-9-18-9 18 9-2zm0 0v-8"></path>
                    </svg>
                </button>
            </form>
        </div>
    </div>
</template>

<script>
import axios from 'axios';
import { CheckCheck, Clock } from 'lucide-vue-next';

export default {
    name: 'WhatsAppChat',
    components: { CheckCheck, Clock },
    data() {
        return {
            messages: [],
            newMessage: '',
            loading: true,
            error: null,
            sending: false,
            organizationId: '2ab65459-5232-308f-8f36-e6a95187dd11'
        };
    },
    computed: {
        sortedMessages() {
            return [...this.messages].sort((a, b) => new Date(a.created_at) - new Date(b.created_at));
        },
    },
    async mounted() {
        console.log('mounted');
        await this.fetchMessages();
        this.setupEcho();
        this.scrollToBottom();
    },
    updated() {
        this.scrollToBottom();
    },
    methods: {
        async fetchMessages() {
            if (!this.organizationId) {
                this.error = 'Organization ID not found. Please log in again.';
                this.loading = false;
                return;
            }

            try {
                const response = await axios.get('/api/omni-channel-messages', {
                    params: {
                        organization_id: this.organizationId,
                        channel: 'whatsapp',
                    },
                });
                this.messages = (response.data.data || []).reverse();
                this.loading = false;
            } catch (error) {
                this.error = 'Failed to load messages. Please try again.';
                this.loading = false;
                console.error('Error fetching messages:', error);
            }
        },
        async sendMessage() {
            if (!this.newMessage.trim()) return;

            this.sending = true;

            // Store the message content before clearing the input
            const messageContent = this.newMessage;

            // Immediately add the message to the UI with a pending status
            const tempId = `temp-${Date.now()}-${Math.random()}`;
            const pendingMessage = {
                id: tempId,
                organization_id: this.organizationId,
                channel: 'whatsapp',
                direction: 'outbound',
                from: 'telcoflo',
                to: '26775993221',
                content: messageContent,
                message_type: 'text',
                status: 'pending',
                created_at: new Date().toISOString(),
                updated_at: new Date().toISOString(),
            };
            this.messages.push(pendingMessage);
            const tempMessageIndex = this.messages.findIndex(msg => msg.id === tempId);
            this.newMessage = '';

            try {
                const response = await axios.post('/api/omni-channel-messages', {
                    organization_id: this.organizationId,
                    channel: 'whatsapp',
                    direction: 'outbound',
                    from: 'telcoflo',
                    to: '26775993221',
                    content: messageContent,
                    message_type: 'text',
                });

                if (response.data.created && response.data.data) {
                    // Replace the pending message with the response data
                    this.messages.splice(tempMessageIndex, 1, response.data.data);
                } else {
                    this.error = response.data.message || 'Failed to send message. No message data returned.';
                    console.error('Unexpected response structure:', response.data);
                    // Remove the pending message on failure
                    this.messages.splice(tempMessageIndex, 1);
                }
            } catch (error) {
                console.error('Error sending message:', error);
                this.error = 'Failed to send message. Please try again.';
                // Remove the pending message on failure
                this.messages.splice(tempMessageIndex, 1);
            } finally {
                this.sending = false;
            }
        },
        setupEcho() {
            if (!this.organizationId) {
                this.error = 'Organization ID not found. Cannot set up real-time messaging.';
                return;
            }

            console.log('Subscribing to channel:', `whatsapp-messages.${this.organizationId}`);

            window.Echo.channel(`whatsapp-messages.${this.organizationId}`)
                .listen('.new-message', (data) => {
                    console.log('Received new-message event:', data);
                    if (data.channel === 'whatsapp') {
                        const exists = this.messages.some(msg => msg.id === data.id);
                        if (!exists) {
                            this.messages.push(data);
                        } else {
                            console.log('Message already exists, skipping:', data.id);
                        }
                    }
                })
                .subscribed(() => {
                    console.log('Successfully subscribed to channel:', `whatsapp-messages.${this.organizationId}`);
                })
                .error((error) => {
                    console.error('Failed to subscribe to channel:', `whatsapp-messages.${this.organizationId}`, error);
                });
        },
        scrollToBottom() {
            const container = this.$refs.chatContainer;
            if (container) {
                container.scrollTop = container.scrollHeight;
            }
        },
    },
};
</script>

<style scoped>
/* Ensure the chat messages scroll smoothly */
.overflow-y-auto {
    scroll-behavior: smooth;
}

/* Add spacing between messages */
.space-y-2 > div + div {
    margin-top: 0.5rem;
}

/* Speech bubble styles */
.speech-bubble-right::before {
    content: "";
    width: 0px;
    height: 0px;
    position: absolute;
    border-left: 5px solid #b9f8cf;
    border-right: 5px solid transparent;
    border-top: 5px solid #b9f8cf;
    border-bottom: 5px solid transparent;
    right: -10px;
    top: 0;
}

.speech-bubble-left::before {
    content: "";
    width: 0px;
    height: 0px;
    position: absolute;
    border-left: 5px solid transparent;
    border-right: 5px solid white;
    border-top: 5px solid white;
    border-bottom: 5px solid transparent;
    left: -10px;
    top: 0;
}
</style>
