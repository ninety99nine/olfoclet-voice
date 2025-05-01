<template>
    <Modal
        size="md"
        ref="modal"
        :showFooter="true"
        :dismissable="true"
        :scrollOnContent="false"
        :showApproveButton="true"
        :approveLoading="isUpdating"
        header="Update Article"
        approveText="Update Article"
        :approveAction="updateArticle"
        subheader="Modify the article's details">
        <template #content>
            <div class="space-y-4">
                <Input
                    v-model="form.title"
                    :showAsterisk="true"
                    label="Article Title"
                    placeholder="Enter article title"
                    :errorText="formState.getFormError('title')" />
                <Input
                    type="textarea"
                    v-model="form.content"
                    :showAsterisk="true"
                    label="Content"
                    placeholder="Enter article content"
                    :errorText="formState.getFormError('content')"
                    rows="6" />
                <Switch
                    size="sm"
                    v-model="form.ai_searchable"
                    suffixText="AI Searchable" />
            </div>
        </template>
    </Modal>
</template>

<script>
import axios from 'axios';
import Modal from '@Partials/Modal.vue';
import Input from '@Partials/Input.vue';
import Switch from '@Partials/Switch.vue';

export default {
    name: 'UpdateArticleModal',
    inject: ['formState', 'notificationState'],
    components: { Modal, Input, Switch },
    data() {
        return {
            isUpdating: false,
            article: null,
            form: {
                title: '',
                content: '',
                ai_searchable: true
            }
        };
    },
    methods: {
        showModal(article) {
            this.reset();
            this.article = article;
            this.form.title = article.title;
            this.form.content = article.content;
            this.form.ai_searchable = article.ai_searchable;
            this.$refs.modal.showModal();
        },
        reset() {
            this.form.title = '';
            this.form.content = '';
            this.form.ai_searchable = true;
            this.article = null;
        },
        async updateArticle(hideModal) {
            this.formState.hideFormErrors();

            if (this.form.title.trim() === '') {
                this.formState.setFormError('title', 'Article title is required');
            }
            if (this.form.content.trim() === '') {
                this.formState.setFormError('content', 'Article content is required');
            }

            if (this.formState.hasErrors) return;

            this.isUpdating = true;

            try {
                const url = this.article._links.update;
                const payload = {
                    title: this.form.title,
                    content: this.form.content,
                    ai_searchable: this.form.ai_searchable
                };

                await axios.put(url, payload);

                this.notificationState.showSuccessNotification('Article updated');
                this.$emit('updated');
                hideModal();
            } catch (error) {
                const message = error?.response?.data?.message || error?.message || 'Something went wrong updating the article';
                this.notificationState.showWarningNotification(message);
                this.formState.setServerFormErrors(error);
                console.error('Failed to update article:', error);
            } finally {
                this.isUpdating = false;
            }
        }
    }
};
</script>
