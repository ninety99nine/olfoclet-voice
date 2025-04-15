<template>

    <div>

        <!-- Sort Button -->
        <Button
            size="xs"
            type="outline"
            :action="openSortDrawer"
            :leftIcon="ArrowDownWideNarrow">
            <span>Sort</span>
        </Button>

        <Drawer
            ref="sortDrawer"
            placement="right"
            :showFooter="false"
            :scrollOnContent="false">

            <template #content>

                <!-- Header -->
                <div class="flex justify-between items-center space-x-2 bg-gray-100 border-b shadow-sm p-4">

                    <div class="flex items-center space-x-2">

                        <!-- Sort Icon -->
                        <svg class="w-5 h-5 text-gray-700" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M3 4.5h14.25M3 9h9.75M3 13.5h9.75m4.5-4.5v12m0 0-3.75-3.75M17.25 21 21 17.25" />
                        </svg>

                        <!-- Heading -->
                        <h2 class="text-gray-700">
                            Sorting
                        </h2>

                    </div>

                    <!-- Close Icon -->
                    <svg
                        @click="closeSortDrawer"
                        class="w-6 h-6 text-gray-400 cursor-pointer hover:opacity-90 active:opacity-80 active:scale-90 transition-all"
                        xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M6 18 18 6M6 6l12 12" />
                    </svg>

                </div>

                <p class="p-4 text-sm bg-blue-100">
                    When using two or more sorting options, you can rearrange their order to prioritize which criteria are applied first.
                </p>

                <div v-if="isLoadingSorting" class="flex justify-center my-8">

                    <!-- Loader -->
                    <Loader></Loader>

                </div>

                <draggable
                    v-else
                    v-model="localSorting"
                    class="divide-y mb-4"
                    handle=".draggable-handle"
                    ghost-class="bg-yellow-50">

                    <template
                        :key="index"
                        v-for="(sorting, index) in localSorting">

                        <div v-if="showMore ? true : sorting.priority">

                            <div
                                @click="toggleSortingVisibility(index)"
                                class="text-sm p-4 bg-gray-50 hover:bg-gray-100 cursor-pointer flex items-center justify-between">

                                <span>{{ sorting.label }}</span>

                                <div class="flex items-center space-x-2">

                                    <!-- Total Active Options -->
                                    <Pill v-if="countActiveOptions(sorting)" type="primary" size="xs" class="flex-shrink-0">{{ countActiveOptions(sorting) }}</Pill>

                                    <!-- Drag & Drop Handle -->
                                    <svg v-if="countActiveOptions(sorting)" @click.stop class="draggable-handle w-4 h-4 cursor-grab hover:text-yellow-500 visible:cursor-grabbing" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M3.75 3.75v4.5m0-4.5h4.5m-4.5 0L9 9M3.75 20.25v-4.5m0 4.5h4.5m-4.5 0L9 15M20.25 3.75h-4.5m4.5 0v4.5m0-4.5L15 9m5.25 11.25h-4.5m4.5 0v-4.5m0 4.5L15 15" />
                                    </svg>

                                    <!-- Up Arrow Icon -->
                                    <svg v-if="sorting.active" class="w-4 h-4" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="m4.5 15.75 7.5-7.5 7.5 7.5" />
                                    </svg>

                                    <!-- Down Arrow Icon -->
                                    <svg v-else class="w-4 h-4" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="m19.5 8.25-7.5 7.5-7.5-7.5" />
                                    </svg>

                                </div>
                            </div>

                            <div v-if="sorting.active" class="text-sm p-4">

                                <div v-for="(option, index2) in sorting.options" :key="index2">

                                    <Input
                                        type="checkbox"
                                        v-model="option.active"
                                        :inputLabel="option.label"
                                        @click.stop="() => toggleSortOption(index, index2)">
                                    </Input>

                                </div>

                            </div>

                        </div>

                    </template>

                </draggable>

                <!-- Clear Sorting Button -->
                <div v-if="!isLoadingSorting" class="flex flex-col items-center px-4 space-y-8 mb-60">

                    <Button
                        size="xs"
                        type="outline"
                        :action="showMoreOrLess"
                        :leftIcon="showMore ? ArrowUp : ArrowDown"
                        v-if="hasPrioritySortOptions && hasNonPrioritySortOptions">
                        <span>{{ showMore ? 'show less options' : 'show more options' }}</span>
                    </Button>

                    <Button
                        size="sm"
                        type="light"
                        class="w-full"
                        :action="clearSorting"
                        v-if="totalActiveSortOptions">
                        <span>Clear Sorting</span>
                    </Button>

                </div>

            </template>

        </Drawer>

    </div>

</template>

<script>

    import isEqual from 'lodash/isEqual';
    import Pill from '@Partials/Pill.vue';
    import Input from '@Partials/Input.vue';
    import cloneDeep from 'lodash/cloneDeep';
    import Button from '@Partials/Button.vue';
    import Drawer from '@Partials/Drawer.vue';
    import Loader from '@Partials/Loader.vue';
    import { VueDraggableNext } from 'vue-draggable-next';
    import { ArrowDownWideNarrow, ArrowUp, ArrowDown } from 'lucide-vue-next';

    export default {
        inject: ['notificationState'],
        components: { draggable: VueDraggableNext, Pill, Input, Button, Drawer, Loader },
        props: {
            sortingExpressions: {
                type: Array,
                default: () => []
            },
            resource: {
                type: String
            },
        },
        emits: ['updatedSorting'],
        data() {
            return {
                ArrowUp,
                ArrowDown,
                showMore: false,
                sortDrawer: null,
                localSorting: null,
                ArrowDownWideNarrow,
                originalSorting: null,
                isLoadingSorting: false,
                lastEmittedSorting: null,
            }
        },
        watch: {
            localSorting: {
                handler(newVal) {
                    this.createSortingExpressions();
                },
                deep: true
            },
        },
        computed: {
            hasSortingExpressions() {
                return this.sortingExpressions.length > 0;
            },
            totalActiveSortOptions() {
                if(this.localSorting == null) return 0;

                return this.localSorting.filter((sort) => {
                    return sort.options.some((option) => option.active);
                }).length;
            },
            hasPrioritySortOptions() {
                if(this.localSorting == null) return false;

                return this.localSorting.filter((sort) => {
                    return sort.priority;
                }).length > 0;
            },
            hasNonPrioritySortOptions() {
                if(this.localSorting == null) return false;

                return this.localSorting.filter((sort) => {
                    return !sort.priority;
                }).length > 0;
            }
        },
        methods: {
            toggleSortOption(index, index2) {
                this.localSorting[index].options.forEach((option, optionIndex) => {
                    if (optionIndex !== index2) {
                        option.active = false;
                    }
                });
            },
            countActiveOptions(sorting) {
                return sorting.options.filter((option) => option.active).length;
            },
            createSortingExpressions() {

                let sorting = [];

                for (let sortIndex = 0; sortIndex < this.localSorting.length; sortIndex++) {

                    const localSorting = this.localSorting[sortIndex];
                    const hasActiveOptions = localSorting.options.some((option) => option.active);

                    if(hasActiveOptions) {

                        const activeSortingOptions = localSorting.options.filter((option) => option.active);
                        const firstActiveSortingOption = activeSortingOptions[0];

                        const label = `${localSorting.label} (${firstActiveSortingOption.label})`;
                        const expression = `${localSorting.target}:${firstActiveSortingOption.value}`;

                        sorting.push({
                            label: label,
                            sortIndex: sortIndex,
                            expression: expression
                        });

                    }

                }

                if(this.sortingHasChanged(sorting, this.lastEmittedSorting)) {
                    this.$emit('updatedSorting', sorting);
                    this.lastEmittedSorting = cloneDeep(sorting);
                }
            },
            applySortingExpressions() {
                this.sortingExpressions.forEach((sortingExpression) => {

                    // Example: ['created_at', 'asc']
                    const parts = sortingExpression.split(':');

                    const target = parts[0];           // 'created_at'
                    const value = parts[1];            // 'asc'

                    // Find the localSorting that matches the target
                    const matchingSort = this.localSorting.find(sort => sort.target === target);

                    if (matchingSort) {

                        matchingSort.options.forEach((option) => {
                            if (option.value == value) {
                                option.active = true;
                            }
                        });

                    }
                });
            },
            sortingHasChanged(sorting1, sorting2) {
                // Clone the objects to avoid modifying the original data
                var a = cloneDeep(sorting1);
                var b = cloneDeep(sorting2);

                // Compare the modified arrays for equality
                return !isEqual(a, b);
            },
            openSortDrawer() {
                if(this.localSorting == null) this.getSorting();
                this.sortDrawer.showDrawer();
            },
            closeSortDrawer() {
                this.sortDrawer.hideDrawer();
            },
            toggleSortingVisibility(index) {
                if (!this.localSorting[index].active) {
                    this.localSorting.forEach((sort, i) => {
                        this.localSorting[i].active = false;
                    });
                }

                this.localSorting[index].active = !this.localSorting[index].active;
            },
            showMoreOrLess() {
                this.showMore = !this.showMore;
            },
            removeAppliedSort(sort) {
                this.localSorting[sort.sortIndex].options.forEach((option) => {
                    option.active = false;
                });
            },
            clearSorting() {
                this.localSorting = cloneDeep(this.originalSorting);
            },
            async getSorting() {

                try {

                    if(this.isLoadingSorting) return;

                    //  Start loader
                    this.isLoadingSorting = true;

                    //  Set the query params
                    const config = {
                        params: {
                            'type': this.resource
                        }
                    };

                    const response = await axios.get('/api/sorting', config);

                    this.localSorting = response.data.map(sorting => {
                        return {
                            ...sorting,
                            active: false,
                            options: sorting.options.map((option) => {

                                let _option = {
                                    ...option,
                                    active: false
                                };

                                return _option;

                            })
                        };
                    });

                    this.originalSorting = cloneDeep(this.localSorting);

                    this.applySortingExpressions();

                } catch (error) {

                    const message = error?.response?.data?.message || error?.message || 'Something went wrong while fetching the sorting options';
                    this.notificationState.showWarningNotification(message);

                    console.error('Failed to fetch sorting options:', error);

                } finally {
                    this.isLoadingSorting = false;
                }

            },
        },
        mounted() {
            this.sortDrawer = this.$refs.sortDrawer;
            if(this.hasSortingExpressions) this.getSorting();
        },
    };
</script>
