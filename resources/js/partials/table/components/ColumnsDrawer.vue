<template>

    <div>

        <!-- Columns Button -->
        <Button
            size="xs"
            type="outline"
            :leftIcon="Logs"
            :action="openColumnsDrawer">
            <span>Columns</span>
        </Button>

        <Drawer
            placement="right"
            ref="columnsDrawer"
            :showFooter="false"
            :scrollOnContent="false">

            <template #content>

                <!-- Header -->
                <div class="flex justify-between items-center space-x-2 bg-gray-100 border-b shadow-sm p-4">

                    <div class="flex items-center space-x-2">

                        <div class="flex items-center space-x-2 text-gray-700">

                            <!-- Columns Icon -->
                            <Logs size="20"></Logs>

                            <!-- Heading -->
                            <h2>Columns</h2>

                        </div>

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

                    <Switch
                        size="sm"
                        v-model="showEverything"
                        suffixText="Show Everything"/>

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

                                <Input
                                    @click.stop
                                    type="checkbox"
                                    v-model="column.active"
                                    :inputLabel="column.name">
                                </Input>

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

                    <Button
                        size="sm"
                        type="light"
                        class="w-full"
                        :action="resetColumns">
                        <span>Reset Columns</span>
                    </Button>

                </div>

            </template>

        </Drawer>

    </div>

</template>

<script>

    import { Logs } from 'lucide-vue-next';
    import Pill from '@Partials/Pill.vue';
    import Input from '@Partials/Input.vue';
    import cloneDeep from 'lodash/cloneDeep';
    import Button from '@Partials/Button.vue';
    import Drawer from '@Partials/Drawer.vue';
    import Switch from '@Partials/Switch.vue';
    import { VueDraggableNext } from 'vue-draggable-next';

    export default {
        inject: ['formState'],
        components: { Logs, draggable: VueDraggableNext, Input, Pill, Button, Drawer, Switch },
        props: {
            columns: {
                type: Array,
                default: () => []
            },
        },
        emits: ['updatedColumns'],
        data() {
            return {
                Logs,
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
