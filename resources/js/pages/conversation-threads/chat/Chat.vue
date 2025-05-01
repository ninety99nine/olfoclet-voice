<template>
    <div class="flex min-h-[calc(100vh-60px)] flex-col bg-gray-50 pt-8 p-4 sm:p-8 relative">
        <!-- Header -->
        <div class="select-none flex justify-between items-center mb-6">
            <div class="flex items-center space-x-3">
                <Button type="light" size="sm" :leftIcon="ArrowLeft" leftIconSize="20" :action="goBack">
                    <span>Back</span>
                </Button>
                <div class="flex items-end space-x-2">
                    <div class="flex items-center space-x-2 text-lg font-semibold">
                        <span>Chat with</span>
                        <div class="flex items-center space-x-1 bg-indigo-50 px-2 py-1 border border-dashed border-indigo-200 text-indigo-500 rounded-sm text-sm">
                            <BotIcon size="20" />
                            <span>{{ copilot?.name || '...' }}</span>
                        </div>
                    </div>
                </div>
            </div>
            <!-- Thread Selector -->
            <div class="flex items-center space-x-2">
                <Dropdown
                    triggerSize="sm"
                    :options="threadOptions"
                    :triggerText="currentThread?.title || 'Select Thread'"
                    @select="selectThread"
                    :disabled="false" />
                <template v-if="currentThread">
                    <Button type="primary" size="sm" :leftIcon="Plus" leftIconSize="20" :action="startNewConversation">
                        <span>New Conversation</span>
                    </Button>
                    <Button
                        type="danger"
                        size="sm"
                        :leftIcon="Trash"
                        leftIconSize="20"
                        :action="deleteCurrentThread">
                        <span>Delete Thread</span>
                    </Button>
                </template>
            </div>
        </div>

        <!-- Chat Area -->
        <div class="flex-1">
            <div v-if="isLoadingCopilot || isLoadingThread || isLoadingMessages" class="h-80 flex items-center justify-center">
                <div class="select-none flex items-center space-x-2 py-2 px-4 rounded-full bg-indigo-500 text-white">
                    <BotIcon size="32"/>
                    <Loader size="20" class="animate-spin"></Loader>
                </div>
            </div>
            <div v-else-if="messages.length === 0" class="select-none h-80 flex flex-col items-center justify-center text-center border border-gray-400 border-dashed rounded-lg">
                <BotIcon size="48" class="text-gray-400 mb-4 animate-bounce" />
                <h3 class="text-lg font-semibold text-gray-800">Start a Conversation</h3>
                <p class="text-sm text-gray-500 mt-2">Ask a question to begin chatting with Copilot</p>
            </div>
            <div v-else class="space-y-6 pb-40">
                <div v-for="(message, index) in messages" :key="index" class="space-y-2 animate-fade-in">
                    <!-- User Message -->
                    <div v-if="message.role === 'user'" class="flex justify-end">
                        <div class="max-w-2xl bg-indigo-500 text-white rounded-2xl rounded-br-none px-4 py-2">
                            <p class="text-sm" v-html="renderMarkdown(message.content)"></p>
                        </div>
                    </div>

                    <!-- Copilot Message -->
                    <div v-if="message.role === 'assistant'" class="flex justify-start items-start space-x-3">
                        <BotIcon size="20" class="text-gray-600 mt-2" />
                        <div class="max-w-2xl bg-gray-100 border border-gray-200 rounded-2xl rounded-bl-none px-4 py-2">
                            <p class="text-sm text-gray-900 markdown-content" v-html="renderMarkdown(message.content)"></p>
                            <p class="text-xs text-gray-500 mt-2">{{ formattedDatetime(message.timestamp) }}</p>
                            <!-- Sources -->
                            <div v-if="message.context && message.context.length > 0" class="mt-3">
                                <button
                                    @click="toggleContext(index)"
                                    class="text-sm text-blue-600 hover:underline focus:outline-none transition-colors duration-200">
                                    {{ expandedContext[index] ? 'Hide Sources' : 'Show Sources' }}
                                </button>
                                <div v-if="expandedContext[index]" class="mt-3 space-y-3">
                                    <div v-for="(ctx, ctxIndex) in message.context" :key="ctxIndex" class="bg-gray-200 p-3 rounded-md shadow-inner">
                                        <p class="text-xs font-medium text-gray-700 flex items-center space-x-2">
                                            <span>{{ ctx.title }}</span>
                                            <span class="text-gray-500">({{ ctx.type }})</span>
                                        </p>
                                        <p class="text-xs text-gray-600 mt-1">{{ ctx.content }}</p>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Thinking Message -->
                    <div v-if="message.role === 'thinking'" class="flex justify-start items-start space-x-3">
                        <BotIcon size="20" class="text-gray-600 mt-2" />
                        <div class="max-w-2xl bg-gray-100 border border-gray-200 rounded-2xl rounded-bl-none px-4 py-2">
                            <div class="typing-dots">
                                <span class="dot">.</span>
                                <span class="dot">.</span>
                                <span class="dot">.</span>
                            </div>
                        </div>
                    </div>

                    <!-- Error Message -->
                    <div v-if="message.role === 'error'" class="flex justify-start items-start space-x-3">
                        <AlertCircle size="32" class="text-red-500 mt-2" />
                        <div class="max-w-2xl bg-red-100 text-red-700 rounded-lg p-4 shadow-sm">
                            <p class="text-sm">{{ message.content }}</p>
                            <p class="text-xs text-red-500 mt-2">{{ formattedDatetime(message.timestamp) }}</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Input Area -->
        <div class="fixed bottom-8 ml-30 w-1/2 left-1/2 transform -translate-x-1/2 bg-white border border-gray-200 rounded-2xl shadow-sm p-4">
            <form @submit.prevent="sendMessage" class="flex items-start space-x-2">
                <Input
                    rows="2"
                    type="textarea"
                    class="w-full"
                    v-model="newMessage"
                    :disabled="isSendingMessage || !copilot"
                    placeholder="Ask anything ..."
                    @keydown.enter.prevent="sendMessage" />
                <button
                    @click.prevent="sendMessage"
                    :class="['select-none p-2 rounded-full', isSendingMessage || newMessage.trim() ? 'bg-indigo-500 transition-all hover:bg-indigo-400 hover:scale-110 hover:cursor-pointer active:scale-100' : 'bg-gray-100']">
                    <Loader v-if="isSendingMessage" size="20" class="text-white animate-spin"></Loader>
                    <ArrowUp v-else size="20" :class="[newMessage.trim() ? 'text-white' : 'text-gray-300']"></ArrowUp>
                </button>
            </form>
        </div>
    </div>
</template>

<style>
.markdown-content h1 {
    font-size: 1.5rem;
    font-weight: 600;
    margin: 1rem 0 0.5rem;
    color: #1f2937;
}

.markdown-content h2 {
    font-size: 1.25rem;
    font-weight: 600;
    margin: 0.75rem 0 0.5rem;
    color: #1f2937;
}

.markdown-content h3 {
    font-size: 1.125rem;
    font-weight: 600;
    margin: 0.5rem 0 0.25rem;
    color: #1f2937;
}

.markdown-content p {
    margin: 0.5rem 0;
    color: #374151;
}

.markdown-content ul,
.markdown-content ol {
    margin: 0.5rem 0;
    padding-left: 1.5rem;
}

.markdown-content li {
    margin: 0.25rem 0;
    color: #374151;
}

.markdown-content strong {
    font-weight: 700;
    color: #1f2937;
}

.markdown-content em {
    font-style: italic;
}

.markdown-content code {
    background-color: #f3f4f6;
    padding: 0.15rem 0.3rem;
    border-radius: 0.25rem;
    font-family: 'Courier New', Courier, monospace;
    font-size: 0.875rem;
    color: #1f2937;
}

.markdown-content pre {
    background-color: #f3f4f6;
    padding: 1rem;
    border-radius: 0.5rem;
    overflow-x: auto;
    font-family: 'Courier New', Courier, monospace;
    font-size: 0.875rem;
    color: #1f2937;
    margin: 0.5rem 0;
}

.markdown-content a {
    color: #2563eb;
    text-decoration: underline;
    transition: color 0.2s;
}

.markdown-content a:hover {
    color: #1d4ed8;
}

.markdown-content blockquote {
    border-left: 4px solid #d1d5db;
    padding-left: 1rem;
    margin: 0.5rem 0;
    color: #4b5563;
    font-style: italic;
}

/* Typing dots animation */
.typing-dots {
    display: inline-flex;
    align-items: center;
    margin-top: -8px;
}

.typing-dots .dot {
    font-size: 1.5rem;
    line-height: 1;
    color: #6b7280;
    animation: blink 1.4s infinite both;
}

.typing-dots .dot:nth-child(2) {
    animation-delay: 0.2s;
}

.typing-dots .dot:nth-child(3) {
    animation-delay: 0.4s;
}

@keyframes blink {
    0% { opacity: 0.2; }
    20% { opacity: 1; }
    100% { opacity: 0.2; }
}
</style>

<script>
    import axios from 'axios';
    import { marked } from 'marked';
    import DOMPurify from 'dompurify';
    import Input from '@Partials/Input.vue';
    import Button from '@Partials/Button.vue';
    import Dropdown from '@Partials/Dropdown.vue';
    import { formattedDatetime } from '@Utils/dateUtils.js';
    import { ArrowUp, BotIcon, Loader, ArrowLeft, AlertCircle, Send, Plus, Trash } from 'lucide-vue-next';

    export default {
        inject: ['notificationState'],
        components: { Input, Button, Dropdown, Loader, BotIcon, ArrowLeft, ArrowUp, AlertCircle, Send, Plus, Trash },
        data() {
            return {
                Plus,
                Trash,
                ArrowLeft,
                threads: [],
                messages: [],
                copilot: null,
                newMessage: '',
                currentThread: null,
                expandedContext: {},
                isLoadingThread: false,
                isLoadingCopilot: false,
                isSendingMessage: false,
                isLoadingMessages: false,
                isNewConversation: false
            };
        },
        watch: {
            $route(to, from) {
                if(to.name === 'create-copilot-conversation-thread') {
                    this.messages = [],
                    this.newMessage = '';
                    this.currentThread = null;
                    this.expandedContext = {};
                    this.isNewConversation = true;
                    this.conversationThreadId = null;
                    this.fetchThreads();
                }
            }
        },
        computed: {
            threadOptions() {
                return this.threads.map(thread => ({
                    label: thread.title,
                    value: thread.id,
                    action: () => this.selectThread(thread.id)
                }));
            }
        },
        methods: {
            formattedDatetime,
            renderMarkdown(content) {
                // Parse Markdown and sanitize the output
                const html = marked.parse(content);
                return DOMPurify.sanitize(html);
            },
            goBack() {
                this.$router.push({ name: 'show-copilot-conversation-threads', params: { copilotId: this.copilotId } });
            },
            async fetchCopilot() {
                try {
                    this.isLoadingCopilot = true;
                    const response = await axios.get(`/api/copilots/${this.copilotId}`, {
                        params: { '_relationships': 'organization,users,knowledgeBases' }
                    });
                    this.copilot = response.data;
                } catch (error) {
                    const message = error?.response?.data?.message || error?.message || 'Failed to load Copilot';
                    this.notificationState.showWarningNotification(message);
                    console.error('Failed to fetch copilot:', error);
                } finally {
                    this.isLoadingCopilot = false;
                }
            },
            async fetchThread() {
                if (!this.conversationThreadId) return;

                try {
                    this.isLoadingThread = true;
                    const response = await axios.get(`/api/conversation-threads/${this.conversationThreadId}`, {
                        params: { '_relationships': 'copilot' }
                    });
                    const thread = response.data.data || response.data;
                    this.currentThread = thread;
                    if (!this.copilot && thread.copilot) {
                        this.copilot = thread.copilot;
                    }
                    // Fetch messages for the selected thread
                    await this.fetchMessages();
                } catch (error) {
                    const message = error?.response?.data?.message || error?.message || 'Failed to load thread';
                    this.notificationState.showWarningNotification(message);
                    console.error('Failed to fetch thread:', error);
                } finally {
                    this.isLoadingThread = false;
                }
            },
            async fetchThreads() {
                if (!this.copilotId) return;

                try {
                    const response = await axios.get('/api/conversation-threads', {
                        params: { copilot_id: this.copilotId }
                    });
                    this.threads = response.data.data || response.data.threads || [];
                } catch (error) {
                    const message = error?.response?.data?.message || error?.message || 'Failed to load threads';
                    this.notificationState.showWarningNotification(message);
                    console.error('Failed to fetch threads:', error);
                }
            },
            async startNewConversation() {
                this.$router.push({
                    name: 'create-copilot-conversation-thread',
                    params: { copilotId: this.copilotId }
                });
            },
            async deleteCurrentThread() {
                if (!this.currentThread) return;

                if (!confirm('Are you sure you want to delete this thread?')) {
                    return;
                }

                try {
                    const response = await axios.delete('/api/conversation-threads', {
                        data: { thread_ids: [this.currentThread.id] }
                    });
                    if (response.data.deleted) {
                        this.notificationState.showSuccessNotification(response.data.message);
                        this.threads = this.threads.filter(thread => thread.id !== this.currentThread.id);
                        this.currentThread = null;
                        this.messages = [];
                        if (this.threads.length > 0) {
                            this.selectThread(this.threads[0].id);
                        } else {
                            this.goBack();
                        }
                    } else {
                        this.notificationState.showWarningNotification(response.data.message);
                    }
                } catch (error) {
                    const message = error?.response?.data?.message || error?.message || 'Failed to delete thread';
                    this.notificationState.showWarningNotification(message);
                    console.error('Failed to delete thread:', error);
                }
            },
            async selectThread(threadId) {
                this.currentThread = this.threads.find(thread => thread.id === threadId);
                if (!this.currentThread) return;

                // Update the URL without reloading the page
                this.$router.push({ name: 'chat-copilot-conversation-thread', params: { copilotId: this.copilotId, conversationThreadId: threadId } });

                // Fetch messages for the selected thread
                await this.fetchMessages();
            },
            async fetchMessages() {
                if (!this.currentThread) return;

                try {
                    this.isLoadingMessages = true;
                    const response = await axios.get('/api/conversation-messages', {
                        params: { thread_id: this.currentThread.id }
                    });
                    this.messages = (response.data.data || response.data.messages || []).reverse().map(message => ({
                            ...message,
                            timestamp: new Date(message.timestamp || message.created_at)
                        }));
                } catch (error) {
                    const message = error?.response?.data?.message || error?.message || 'Failed to load messages';
                    this.notificationState.showWarningNotification(message);
                    console.error('Failed to fetch messages:', error);
                } finally {
                    this.isLoadingMessages = false;
                }
            },
            async sendMessage() {
                if (!this.newMessage.trim() || this.isSendingMessage || !this.copilot) return;

                // Add the user's message locally (optimistic update)
                const userMessage = {
                    role: 'user',
                    content: this.newMessage,
                    timestamp: new Date()
                };
                this.messages.push(userMessage);

                // Add a "thinking" message
                this.isSendingMessage = true;
                const thinkingMessage = {
                    role: 'thinking',
                    timestamp: new Date()
                };
                this.messages.push(thinkingMessage);

                const query = this.newMessage;
                this.newMessage = '';

                try {
                    const response = await axios.post(`/api/copilots/${this.copilotId}/query`, {
                        query,
                        conversation_thread_id: this.currentThread ? this.currentThread.id : null
                    });
                    const result = response.data;

                    // Remove the "thinking" message
                    this.messages = this.messages.filter(message => message.role !== 'thinking');

                    if (result.success) {
                        this.messages.push({
                            role: 'assistant',
                            content: result.response,
                            context: result.context,
                            timestamp: new Date()
                        });

                        // If this is a new conversation, update the thread and URL
                        if (result.thread && !this.currentThread) {
                            this.currentThread = result.thread;
                            this.threads.push(result.thread);

                            // Update the URL to reflect the new thread
                            this.$router.push({
                                name: 'chat-copilot-conversation-thread',
                                params: { copilotId: this.copilotId, conversationThreadId: result.thread.id }
                            });
                        }
                    } else {
                        this.messages.push({
                            role: 'error',
                            content: result.message,
                            timestamp: new Date()
                        });

                        // If a thread was created, still update the URL
                        if (result.thread && !this.currentThread) {
                            this.currentThread = result.thread;
                            this.threads.push(result.thread);

                            this.$router.push({
                                name: 'chat-copilot-conversation-thread',
                                params: { copilotId: this.copilotId, conversationThreadId: result.thread.id }
                            });
                        }
                    }
                } catch (error) {
                    // Remove the "thinking" message on error
                    this.messages = this.messages.filter(message => message.role !== 'thinking');
                    const message = error?.response?.data?.message || error?.message || 'Failed to get response from Copilot';
                    this.messages.push({
                        role: 'error',
                        content: message,
                        timestamp: new Date()
                    });
                    console.error('Failed to query copilot:', error);
                } finally {
                    this.isSendingMessage = false;
                }
            },
            toggleContext(index) {
                this.expandedContext[index] = !this.expandedContext[index];
                this.$forceUpdate();
            }
        },
        created() {

            this.copilotId = this.$route.params.copilotId;
            this.conversationThreadId = this.$route.params.conversationThreadId;

            // Check if this is a new conversation (create route)
            this.isNewConversation = this.$route.name === 'create-copilot-conversation-thread';

            // Fetch Copilot (required in all cases)
            this.fetchCopilot();

            // Fetch thread if conversationThreadId is provided
            if (this.conversationThreadId) {
                this.fetchThread();
            }

            // Fetch threads for the Copilot
            this.fetchThreads();
        }
    };
</script>
