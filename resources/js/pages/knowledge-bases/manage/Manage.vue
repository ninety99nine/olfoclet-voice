<template>
    <div class="select-none min-h-screen bg-gray-50 pt-8 p-4 sm:p-8 space-y-4 sm:space-y-6">
        <!-- Page Header -->
        <div class="flex justify-between items-start">
            <div class="flex items-end space-x-2">
                <LibraryBig size="48" stroke-width="1" class="text-gray-400" />
                <div>
                    <h2 class="text-xl font-semibold text-gray-900 dark:text-white">{{ knowledgeBase?.name || 'Knowledge Base' }}</h2>
                    <p class="text-sm text-gray-600 dark:text-neutral-400">
                        Manage sources and content for this knowledge base
                    </p>
                </div>
            </div>
            <Button type="light" size="sm" :leftIcon="ArrowLeft" leftIconSize="20" :action="goBack">
                <span>Back to Knowledge Bases</span>
            </Button>
        </div>

        <!-- Tabs -->
        <div class="border-b border-gray-200">
            <nav class="-mb-px flex space-x-8" aria-label="Tabs">
                <button
                    v-for="tab in tabs"
                    :key="tab.name"
                    @click="currentTab = tab.name"
                    :class="[currentTab === tab.name ? 'border-indigo-500 text-indigo-600' : 'border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300', 'whitespace-nowrap py-4 px-1 border-b-2 font-medium text-sm cursor-pointer']">
                    {{ tab.name }} <span v-if="tab.count !== null" class="ml-1 text-xs">({{ tab.count }})</span>
                </button>
            </nav>
        </div>

        <!-- Sources Tab -->
        <div v-if="currentTab === 'Sources'">
            <SourceManage :knowledgeBaseId="knowledgeBaseId" />
        </div>

        <!-- Content Tab -->
        <div v-if="currentTab === 'Content'">
            <ContentManage :knowledgeBaseId="knowledgeBaseId" />
        </div>
    </div>
</template>

<script>
import axios from 'axios';
import Button from '@Partials/Button.vue';
import { ArrowLeft, LibraryBig } from 'lucide-vue-next';
import SourceManage from '@Pages/knowledge-bases/components/SourceManage.vue';
import ContentManage from '@Pages/knowledge-bases/components/ContentManage.vue';

export default {
    inject: ['notificationState'],
    components: {
        Button, SourceManage, ContentManage, LibraryBig
    },
    props: {
        knowledgeBaseId: {
            type: String,
            required: true
        }
    },
    data() {
        return {
            ArrowLeft,
            knowledgeBase: null,
            currentTab: 'Sources',
            tabs: [
                { name: 'Sources', count: null },
                { name: 'Content', count: null },
            ],
        };
    },
    methods: {
        goBack() {
            this.$router.push({ name: 'show-knowledge-bases' });
        },
        async fetchKnowledgeBase() {
            try {
                const response = await axios.get(`/api/knowledge-bases/${this.knowledgeBaseId}`, {
                    params: {
                        '_countable_relationships': 'contentSources,contentItems'
                    }
                });
                this.knowledgeBase = response.data;
                this.tabs.find(tab => tab.name === 'Sources').count = response.data.content_sources_count;
                this.tabs.find(tab => tab.name === 'Content').count = response.data.content_items_count;
            } catch (error) {
                const message = error?.response?.data?.message || error?.message || 'Something went wrong while fetching the knowledge base';
                this.notificationState.showWarningNotification(message);
                console.error('Failed to fetch knowledge base:', error);
            }
        }
    },
    created() {
        this.fetchKnowledgeBase();
    }
};
</script>
