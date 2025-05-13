<template>
    <div class="select-none space-y-4 sm:space-y-6">
        <template v-if="showSources">
            <!-- Page Header -->
            <div class="flex justify-between">
                <div class="flex items-end space-x-2">
                    <BookOpen size="48" stroke-width="1" class="text-gray-400" />
                    <div>
                        <h2 class="text-xl font-semibold text-gray-900 dark:text-white">Sources</h2>
                        <p class="text-sm text-gray-600 dark:text-neutral-400">
                            Manage sources for this knowledge base
                        </p>
                    </div>
                </div>
                <div class="flex justify-end">
                    <Button type="primary" size="md" :leftIcon="Plus" leftIconSize="20" :action="showAddSourceModal">
                        <span>Add Source</span>
                    </Button>
                </div>
            </div>

            <!-- Sources List -->
            <div class="space-y-6">
                <!-- Public Articles -->
                <div class="border rounded-lg p-4 bg-white shadow-sm">
                    <div class="flex justify-between items-center mb-2">
                        <div class="flex items-center space-x-2">
                            <BookOpen size="24" class="text-gray-600" />
                            <div>
                                <h3 class="text-lg font-medium text-gray-900">Public articles</h3>
                                <p class="text-sm text-gray-500">Let AI Agent and Copilot use public articles from your Help Center.</p>
                            </div>
                        </div>
                        <div class="flex items-center space-x-2">
                            <span class="text-sm text-gray-600">{{ publicArticlesCount }} {{ publicArticlesCount === 1 ? 'article' : 'articles' }}</span>
                            <Button type="light" size="sm" :action="() => showAddSourceModal('public_article')">Add article</Button>
                        </div>
                    </div>
                    <div class="flex items-center space-x-2">
                        <Input type="checkbox" v-model="publicArticlesEnabled" @change="togglePublicArticles" />
                        <span class="text-sm text-gray-600">Intercom</span>
                    </div>
                </div>

                <!-- Internal Articles -->
                <div class="border rounded-lg p-4 bg-white shadow-sm">
                    <div class="flex justify-between items-center mb-2">
                        <div class="flex items-center space-x-2">
                            <Lock size="24" class="text-gray-600" />
                            <div>
                                <h3 class="text-lg font-medium text-gray-900">Internal articles</h3>
                                <p class="text-sm text-gray-500">Give Copilot internal knowledge only available to you and your team.</p>
                            </div>
                        </div>
                        <div class="flex items-center space-x-2">
                            <span class="text-sm text-gray-600">{{ internalArticlesCount }} {{ internalArticlesCount === 1 ? 'article' : 'articles' }}</span>
                            <Button type="light" size="sm" :action="() => showAddSourceModal('internal_article')">Add article</Button>
                        </div>
                    </div>
                    <div class="flex items-center space-x-2">
                        <Input type="checkbox" v-model="internalArticlesEnabled" @change="toggleInternalArticles" />
                        <span class="text-sm text-gray-600">Intercom</span>
                    </div>
                </div>

                <!-- Websites -->
                <div class="border rounded-lg p-4 bg-white shadow-sm">
                    <div class="flex justify-between items-center mb-2">
                        <div class="flex items-center space-x-2">
                            <Globe size="24" class="text-gray-600" />
                            <div>
                                <h3 class="text-lg font-medium text-gray-900">Websites</h3>
                                <p class="text-sm text-gray-500">Let AI Agent and Copilot use any public website.</p>
                            </div>
                        </div>
                        <div class="flex items-center space-x-2">
                            <span class="text-sm text-gray-600" v-if="websites.length > 0">Last synced {{ formattedRelativeDate(lastSyncedAt) }}</span>
                            <Button type="light" size="sm" :action="() => showAddSourceModal('website')">Sync</Button>
                        </div>
                    </div>
                    <div v-for="website in websites" :key="website.id" class="flex items-center space-x-2 mt-2">
                        <Input type="checkbox" v-model="website.enabled" @change="toggleWebsite(website)" />
                        <span class="text-sm text-gray-600">{{ website.name }}</span>
                    </div>
                    <div v-if="websites.length === 0" class="text-sm text-gray-600">Not set up</div>
                </div>

                <!-- More Content Sources -->
                <div class="border rounded-lg p-4 bg-white shadow-sm">
                    <div class="flex justify-between items-center mb-2">
                        <div class="flex items-center space-x-2">
                            <MoreHorizontal size="24" class="text-gray-600" />
                            <div>
                                <h3 class="text-lg font-medium text-gray-900">More content sources</h3>
                                <p class="text-sm text-gray-500">Give AI Agent and Copilot sources that aren't visible to your customers.</p>
                            </div>
                        </div>
                    </div>
                    <div class="space-y-2">
                        <div class="flex items-center justify-between">
                            <div class="flex items-center space-x-2">
                                <Input type="checkbox" v-model="snippetsEnabled" @change="toggleSnippets" />
                                <span class="text-sm text-gray-600">Snippets</span>
                            </div>
                            <div class="flex items-center space-x-2">
                                <span class="text-sm text-gray-600">{{ snippetsCount }} {{ snippetsCount === 1 ? 'snippet' : 'snippets' }}</span>
                                <Button type="light" size="sm" :action="() => showAddSourceModal('snippet')">Add snippet</Button>
                            </div>
                        </div>
                        <div class="flex items-center justify-between">
                            <div class="flex items-center space-x-2">
                                <Input type="checkbox" disabled />
                                <span class="text-sm text-gray-600">Documents</span>
                            </div>
                            <div class="flex items-center space-x-2">
                                <span class="text-sm text-gray-600">No documents</span>
                                <Button type="light" size="sm" disabled>Upload document</Button>
                            </div>
                        </div>
                        <div class="flex items-center justify-between">
                            <div class="flex items-center space-x-2">
                                <Input type="checkbox" disabled />
                                <span class="text-sm text-gray-600">Custom answers</span>
                            </div>
                            <div class="flex items-center space-x-2">
                                <span class="text-sm text-gray-600">No answers</span>
                                <Button type="light" size="sm" disabled>Add answer</Button>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </template>

        <!-- No Sources -->
        <div v-else class="select-none w-full flex justify-center py-16">
            <div class="max-w-sm w-full bg-white shadow-sm rounded-xl p-6 flex flex-col items-center text-center space-y-4 border border-dashed border-gray-200">
                <BookOpen size="48" class="text-gray-400" />
                <h2 class="text-2xl font-bold text-gray-800">No Sources Yet</h2>
                <p class="text-sm text-gray-500">Add a source to start managing knowledge content.</p>
                <Button type="primary" size="md" :leftIcon="Plus" leftIconSize="20" :action="showAddSourceModal">
                    <span>Add Source</span>
                </Button>
            </div>
        </div>

        <!-- Modals -->
        <AddSourceModal ref="addSourceModal" :knowledgeBaseId="knowledgeBaseId" @created="fetchSources" />
    </div>
</template>

<script>
import axios from 'axios';
import Input from '@Partials/Input.vue';
import Button from '@Partials/Button.vue';
import AddSourceModal from '@Pages/knowledge-bases/components/AddSourceModal.vue';
import { formattedRelativeDate } from '@Utils/dateUtils.js';
import { Plus, BookOpen, Lock, Globe, MoreHorizontal } from 'lucide-vue-next';

export default {
    inject: ['notificationState'],
    components: {
        Input, Button, AddSourceModal, BookOpen
    },
    props: {
        knowledgeBaseId: {
            type: String,
            required: true
        }
    },
    data() {
        return {
            Plus,
            BookOpen,
            Lock,
            Globe,
            MoreHorizontal,
            publicArticlesEnabled: false,
            publicArticlesCount: 0,
            internalArticlesEnabled: false,
            internalArticlesCount: 0,
            snippetsEnabled: false,
            snippetsCount: 0,
            websites: [],
            lastSyncedAt: null,
            isLoadingSources: false,
        };
    },
    computed: {
        showSources() {
            return this.isLoadingSources || this.publicArticlesCount > 0 || this.internalArticlesCount > 0 || this.snippetsCount > 0 || this.websites.length > 0;
        }
    },
    methods: {
        formattedRelativeDate: formattedRelativeDate,
        showAddSourceModal(type = null) {
            this.$refs.addSourceModal.showModal(type);
        },
        async togglePublicArticles() {
            // Simulate toggling public articles (Intercom integration)
            this.notificationState.showInfoNotification('Public articles toggling is not implemented in this demo.');
        },
        async toggleInternalArticles() {
            // Simulate toggling internal articles (Intercom integration)
            this.notificationState.showInfoNotification('Internal articles toggling is not implemented in this demo.');
        },
        async toggleSnippets() {
            // Simulate toggling snippets
            this.notificationState.showInfoNotification('Snippets toggling is not implemented in this demo.');
        },
        async toggleWebsite(website) {
            try {
                await axios.put(`/api/content-sources/${website.id}`, { enabled: website.enabled });
                this.notificationState.showSuccessNotification('Website updated successfully.');
            } catch (error) {
                website.enabled = !website.enabled; // Revert on error
                const message = error?.response?.data?.message || error?.message || 'Failed to update website';
                this.notificationState.showWarningNotification(message);
                console.error('Failed to update website:', error);
            }
        },
        async fetchSources() {
            try {
                this.isLoadingSources = true;
                const response = await axios.get('/api/content-sources', {
                    params: {
                        knowledge_base_id: this.knowledgeBaseId
                    }
                });
                const sources = response.data.data; // Access the 'data' array

                console.log('sources');
                console.log(sources);
                // Process sources based on type
                this.publicArticlesEnabled = sources.some(source => source.type === 'intercom' && source.name === 'Public Articles' && source.enabled);
                this.publicArticlesCount = sources.filter(source => source.type === 'intercom' && source.name === 'Public Articles').length;

                console.log('this.publicArticlesCount');
                console.log(this.publicArticlesCount);
                this.internalArticlesEnabled = sources.some(source => source.type === 'intercom' && source.name === 'Internal Articles' && source.enabled);
                this.internalArticlesCount = sources.filter(source => source.type === 'intercom' && source.name === 'Internal Articles').length;

                console.log('this.internalArticlesCount');
                console.log(this.internalArticlesCount);
                this.snippetsEnabled = sources.some(source => source.type === 'snippet' && source.enabled);
                this.snippetsCount = sources.filter(source => source.type === 'snippet').length;

                console.log('this.snippetsCount');
                console.log(this.snippetsCount);
                this.websites = sources.filter(source => source.type === 'website');
                this.lastSyncedAt = this.websites.length > 0 ? this.websites[0].last_synced_at : null;
                console.log('this.websites');
                console.log(this.websites);
            } catch (error) {
                const message = error?.response?.data?.message || error?.message || 'Something went wrong while fetching sources';
                this.notificationState.showWarningNotification(message);
                console.error('Failed to fetch sources:', error);
            } finally {
                this.isLoadingSources = false;
            }
        }
    },
    created() {
        this.fetchSources();
    }
};
</script>
