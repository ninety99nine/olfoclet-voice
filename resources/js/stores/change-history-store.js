import LZString from 'lz-string';
import { diff } from 'deep-diff';
import { defineStore } from 'pinia';
import debounce from 'lodash.debounce';

export const useChangeHistoryStore = defineStore('change-history', {
    state: () => {
        return {
            data: null,
            hasChanges: false,
            history: {
                timeline: [],
                currentIndex: null
            },
            actionButtons: []
        }
    },
    actions: {
        removeButtons() {
            this.actionButtons = [];
        },
        addDiscardButton() {
            this.actionButtons.push({
                icon: null,
                type: 'light',
                loading: false,
                label: 'Discard',
                action: this.resetHistoryToOriginal
            });
        },
        addActionButton(label, type, icon, action, loading) {
            this.actionButtons.push({
                icon: icon,
                type: type,
                label: label,
                action: action,
                loading: loading
            });
        },
        saveOriginalState(actionName, data) {
            this.data = data;
            this.resetHistory();
            this.saveState(actionName);
        },
        saveStateDebounced: debounce(function (actionName) {
            this.saveState(actionName);
        }, 500),
        saveState(actionName) {

            if (!actionName) {
                console.warn("Action name is required to save the state.");
                return;
            }

            //  Save to storage
            //  localStorage.setItem(`pageForm:${pageHref}`, this.data);

            if (this.history.timeline.length === 0) {
                this.history.timeline.unshift({
                    state: LZString.compress(JSON.stringify(this.data)),
                    timestamp: new Date().toISOString(),
                    name: actionName,
                });
                this.history.currentIndex = 0;
                return;
            }

            if (this.history.currentIndex > 0) {
                this.history.timeline = this.history.timeline.slice(this.history.currentIndex);
                this.history.currentIndex = 0;
            }

            const lastState = JSON.parse(LZString.decompress(this.history.timeline[0].state));
            const differences = diff(lastState, this.data);

            if (differences && differences.length > 0) {
                this.history.timeline.unshift({
                    state: LZString.compress(JSON.stringify(this.data)),
                    timestamp: new Date().toISOString(),
                    name: actionName,
                });
                this.history.currentIndex = 0;
                this.hasChanges = true;
            }
        },
        undo() {
            if (this.canUndo) {
                this.history.currentIndex += 1;
                const previousState = this.history.timeline[this.history.currentIndex];
                this.updateChangeStatus();
                this.data = JSON.parse(LZString.decompress(previousState.state));
            } else {
                console.warn("Cannot undo. Already at the earliest state.");
            }
        },
        redo() {
            if (this.canRedo) {
                this.history.currentIndex -= 1;
                const nextState = this.history.timeline[this.history.currentIndex];
                this.updateChangeStatus();
                this.data = JSON.parse(LZString.decompress(nextState.state));
            } else {
                console.warn("Cannot redo. Already at the latest state.");
            }
        },
        jumpToHistory(index) {
            if (index >= 0 && index < this.history.timeline.length) {
                this.history.currentIndex = index;
                const selectedState = this.history.timeline[index];
                this.updateChangeStatus();
                this.data = JSON.parse(LZString.decompress(selectedState.state));
            } else {
                console.warn("Invalid history index.");
            }
        },
        resetHistory() {
            this.history.currentIndex = null;
            this.history.timeline = [];
            this.hasChanges = false;
        },
        resetHistoryToOriginal() {
            if (this.history.timeline.length > 0) {
                this.history.currentIndex = 0;
                const originalState = this.history.timeline[this.history.timeline.length - 1];
                this.history.timeline = [originalState];
                this.updateChangeStatus();
                this.data = JSON.parse(LZString.decompress(originalState.state));
            }
        },
        resetHistoryToCurrent() {
            if (this.history.timeline.length > 0) {
                this.history.currentIndex = 0;
                const currentState = this.history.timeline[0];
                this.history.timeline = [currentState];
                this.updateChangeStatus();
                this.data = JSON.parse(LZString.decompress(currentState.state));
            }
        },
        updateChangeStatus() {
            if (this.history.timeline.length >= 2 && this.history.currentIndex !== null) {
                const originalState = JSON.parse(LZString.decompress(this.history.timeline[this.history.timeline.length - 1].state));
                const currentState = JSON.parse(LZString.decompress(this.history.timeline[this.history.currentIndex].state));
                const differences = diff(originalState, currentState);
                this.hasChanges = differences && differences.length > 0;
            } else {
                this.hasChanges = false;
            }
        }
    },
    getters: {
        canUndo: (state) => {
            return state.history.currentIndex < state.history.timeline.length - 1;
        },
        canRedo: (state) => {
            return state.history.currentIndex > 0;
        },
        historyItems: (state) => {
            return state.history.timeline.map((item, index) => ({
                ...item,
                isActive: index === state.history.currentIndex
            }));
        },
        hasHistoryItems: (state) => {
            return state.history.timeline.length >= 2;
        },
    }
})
