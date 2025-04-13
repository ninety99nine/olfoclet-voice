<template>

    <div class="w-full flex justify-between items-center">

        <div class="flex justify-center items-center space-x-2">

            <!-- Undo Button -->
            <svg
                :class="buttonClasses(canUndo)"
                @click="canUndo ? undo() : null"
                xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" d="M9 15 3 9m0 0 6-6M3 9h12a6 6 0 0 1 0 12h-3" />
            </svg>

            <!-- History Dropdown -->
            <Dropdown ref="dropdown">
                <template #trigger>
                    <button :class="buttonClasses(canUndo || canRedo)">
                        <svg xmlns="http://www.w3.org/2000/svg" class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.5">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M12 6v6h4.5m4.5 0a9 9 0 1 1-18 0 9 9 0 0 1 18 0Z" />
                        </svg>
                    </button>
                </template>
                <template #content>
                    <div class="z-20 w-96 bg-white divide-y divide-gray-100 rounded-lg shadow-md">
                        <div class="px-4 py-2 font-medium text-gray-700 bg-gray-50">History</div>

                        <div class="max-h-40 overflow-y-auto">
                            <div class="divide-y divide-gray-100">
                                <div
                                    :key="index"
                                    @click="jumpToHistory(index)"
                                    v-for="(item, index) in historyItems"
                                    class="space-x-2 px-4 py-3 hover:bg-gray-100 cursor-pointer flex justify-between items-center">
                                    <div class="flex items-center space-x-2">
                                        <span v-if="item.isActive" class="text-xs text-green-500 font-bold">✔</span>
                                        <span class="text-xs text-gray-900">{{ item.name }}</span>
                                    </div>
                                    <span class="text-xs text-gray-400 whitespace-nowrap">{{ formattedRelativeDate(item.timestamp) }}</span>
                                </div>
                            </div>
                        </div>

                        <p v-if="historyItems.length === 1" class="text-xs text-center bg-yellow-100 p-4 mt-4">
                            No history to undo or redo
                        </p>
                        <div v-else class="p-2">
                            <Button :action="resetHistoryToOriginal" type="light" size="sm" class="w-full">
                                <span>Reset to Original</span>
                            </Button>
                        </div>
                    </div>
                </template>
            </Dropdown>

            <!-- Redo Button -->
            <svg
                :class="buttonClasses(canRedo)"
                @click="canRedo ? redo() : null"
                xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" d="m15 15 6-6m0 0-6-6m6 6H9a6 6 0 0 0 0 12h3" />
            </svg>

        </div>

        <div class="flex items-center space-x-2">

            <Button
                size="xs"
                :key="index"
                :type="actionButton.type"
                :icon="actionButton.icon"
                :action="actionButton.action"
                :loading="actionButton.loading"
                v-for="(actionButton, index) in changeHistoryState.actionButtons">
                <span>{{ actionButton.label }}</span>
            </Button>

        </div>

    </div>

</template>

<script>

    import Button from '@Partials/Button.vue';
    import Dropdown from "@Partials/Dropdown.vue";
    import { formattedRelativeDate } from '@Utils/dateUtils.js';

    export default {
        inject: ['orderState', 'changeHistoryState'],
        components: { Button, Dropdown },
        emits: ['updatedProducts'],
        computed: {
            canUndo() {
                return this.changeHistoryState.canUndo;
            },
            canRedo() {
                return this.changeHistoryState.canRedo;
            },
            historyItems() {
                return this.changeHistoryState.historyItems;
            },
        },
        methods: {
            formattedRelativeDate: formattedRelativeDate,
            hideDropdown() {
                this.$refs.dropdown.hideDropdown();
            },
            undo() {
                this.orderState.orderForm = this.changeHistoryState.undo();
            },
            redo() {
                this.orderState.orderForm = this.changeHistoryState.redo();
            },
            jumpToHistory(index) {
                this.orderState.orderForm = this.changeHistoryState.jumpToHistory(index);
            },
            resetHistoryToOriginal() {
                this.hideDropdown();
                this.orderState.orderForm = this.changeHistoryState.resetHistoryToOriginal();
            },
            buttonClasses(enabled) {
                return [
                    "w-8 h-8 p-2 flex items-center justify-center rounded-full",
                    enabled
                        ? "cursor-pointer text-blue-600 border border-blue-400 bg-blue-50 hover:text-blue-700 hover:border-blue-500 hover:bg-blue-100 active:scale-90 transition-all"
                        : "cursor-not-allowed text-gray-300 border border-gray-200",
                ];
            }
        }
    };

</script>
