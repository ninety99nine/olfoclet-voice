<template>
    <Modal
        ref="modal"
        :showFooter="true"
        :dismissable="true"
        :scrollOnContent="false"
        header="Play Audio"
        approveText="Close"
        :showApproveButton="true"
        :approveAction="stopAndCloseModal"
        @modal-hidden="stopAndCloseModal"
    >
        <template #content>
            <div v-if="audioUrl" class="p-4">
                <audio ref="audioPlayer" controls :src="audioUrl" class="w-full">
                    Your browser does not support the audio element.
                </audio>
            </div>
            <div v-else class="p-4 text-center text-gray-500">
                Loading audio...
            </div>
        </template>
    </Modal>
</template>

<script>
import Modal from '@Partials/Modal.vue';
import axios from 'axios';

export default {
    components: {
        Modal,
    },
    props: {
        mediaFileId: {
            type: [String, null],
            required: true,
        },
    },
    data() {
        return {
            audioUrl: null,
        };
    },
    beforeUnmount() {
        this.stopAndCleanup();
    },
    methods: {
        async fetchAudioUrl() {
            try {
                const response = await axios.get(`/api/media-files/${this.mediaFileId}/presigned-url`);
                this.audioUrl = response.data.url;
            } catch (error) {
                console.error('Failed to fetch pre-signed URL for audio:', error);
                this.$notificationState.showWarningNotification('Failed to load audio file.');
                this.$refs.modal.hideModal();
            }
        },
        showModal() {
            this.audioUrl = null;
            this.fetchAudioUrl();
            this.$refs.modal.showModal();
        },
        stopAndCleanup() {
            const audioPlayer = this.$refs.audioPlayer;
            if (audioPlayer) {
                audioPlayer.pause();
                audioPlayer.currentTime = 0; // Reset to the beginning
            }
            this.audioUrl = null;
        },
        stopAndCloseModal() {
            this.stopAndCleanup();
            this.$refs.modal.hideModal();
        },
    },
};
</script>
