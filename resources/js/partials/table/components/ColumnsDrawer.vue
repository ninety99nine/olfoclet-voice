<template>

    <div>

        <!-- Columns Button -->
        <Button :action="openColumnsDrawer" type="outline" size="sm" icon="bars-4"></Button>

        <Drawer ref="columnsDrawer" placement="right" :bodyScrolling="true" :backdrop="true" :hideCloseIcon="true">

            <template #content>

                <!-- Header -->
                <div class="flex justify-between items-center space-x-2 bg-gray-100 border-b shadow-sm p-4">

                    <div class="flex items-center space-x-2">

                        <!-- Columns Icon -->
                        <svg class="w-5 h-5 text-gray-700" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M3.75 6.75h16.5M3.75 12h16.5m-16.5 5.25h16.5" />
                        </svg>

                        <!-- Heading -->
                        <h2 class="text-gray-700">
                            Columns
                        </h2>

                    </div>

                    <!-- Close Icon -->
                    <svg
                        @click="closeColumnsDrawer"
                        class="w-6 h-6 text-gray-400 cursor-pointer hover:opacity-90 active:opacity-80 active:scale-90 transition-all"
                        xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M6 18 18 6M6 6l12 12" />
                    </svg>

                </div>

                <p class="p-4 text-sm bg-blue-100">
                    Show, hide and move your data they way you want to see it
                </p>

                <!-- Show Everything Toggle Switch -->
                <div class="p-4 border-b shadow-sm">
                    <ToggleSwitch
                        size="md"
                        v-model="showEverything">
                        Show Everything
                    </ToggleSwitch>
                </div>

                <div class="divide-y mb-4">

                    <draggable
                        class="divide-y mb-4"
                        v-model="localColumns"
                        handle=".draggable-handle"
                        ghost-class="bg-yellow-50">

                        <template
                            :key="index"
                            v-for="(column, index) in localColumns">

                            <div class="flex items-center justify-between p-4">

                                <Checkbox
                                    @click.stop
                                    v-model="column.active">
                                    <span class="text-sm block">{{ column.name }}</span>
                                </Checkbox>

                                <!-- Drag & Drop Handle -->
                                <svg class="draggable-handle w-4 h-4 cursor-grab hover:text-yellow-500 visible:cursor-grabbing" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M3.75 3.75v4.5m0-4.5h4.5m-4.5 0L9 9M3.75 20.25v-4.5m0 4.5h4.5m-4.5 0L9 15M20.25 3.75h-4.5m4.5 0v4.5m0-4.5L15 9m5.25 11.25h-4.5m4.5 0v-4.5m0 4.5L15 15" />
                                </svg>

                            </div>

                        </template>

                    </draggable>

                </div>

                <!-- Reset Columns Button -->
                <div class="px-4 mb-60">

                    <Button :action="resetColumns" type="light" size="sm" class="w-full">
                        <span>Rest Columns</span>
                    </Button>

                </div>

            </template>

        </Drawer>

    </div>

</template>

<script>

    import cloneDeep from 'lodash/cloneDeep';
    import Pill from '@Partials/pills/Pill.vue';
    import Button from '@Partials/buttons/Button.vue';
    import Drawer from '@Partials/drawers/Drawer.vue';
    import { VueDraggableNext } from 'vue-draggable-next';
    import Checkbox from '@Partials/checkboxes/Checkbox.vue';
    import ToggleSwitch from '@Partials/toggle-switches/ToggleSwitch.vue';

    export default {
        inject: ['formState'],
        components: { draggable: VueDraggableNext, Pill, Button, Drawer, Checkbox, ToggleSwitch },
        props: {
            columns: {
                type: Array,
                default: () => []
            },
        },
        emits: ['updatedColumns'],
        data() {
            return {
                showMore: false,
                columnsDrawer: null,
                showEverything: false,
                localColumns: cloneDeep(this.columns),
                originalColumns: cloneDeep(this.columns)
            }
        },
        watch: {
            localColumns: {
                handler(newVal) {
                    if(this.showEverything != this.isShowingEverything) {
                        this.showEverything = this.isShowingEverything;
                    }
                    this.$emit('updatedColumns', newVal);
                },
                deep: true
            },
            showEverything(newVal) {
                if(newVal) {
                    this.localColumns.map((localColumn) => localColumn.active = true);
                }else if(this.isShowingEverything) {
                    this.localColumns.map((localColumn) => localColumn.active = localColumn.priority);
                }
            },
        },
        computed: {
            isShowingEverything() {
                return this.localColumns.filter((localColumn) => localColumn.active == true).length == this.localColumns.length;
            }
        },
        methods: {
            openColumnsDrawer() {
                this.columnsDrawer.showDrawer();
            },
            closeColumnsDrawer() {
                this.columnsDrawer.hideDrawer();
            },
            resetColumns() {
                this.localColumns = cloneDeep(this.originalColumns);
            }
        },
        mounted() {
            this.columnsDrawer = this.$refs.columnsDrawer;
        },
    };
</script>
