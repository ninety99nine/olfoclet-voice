<template>

    <div>

        <!-- Filter Button -->
        <Button
            size="xs"
            type="outline"
            :leftIcon="Funnel"
            :action="openFilterDrawer">
            <span>Filter</span>
        </Button>

        <Drawer
            ref="filterDrawer"
            placement="right"
            :showFooter="false"
            :scrollOnContent="false">

            <template #content>

                <!-- Header -->
                <div class="flex justify-between items-center space-x-2 bg-gray-100 border-b shadow-sm p-4">

                    <div class="flex items-center space-x-2 text-gray-700">

                        <!-- Filter Icon -->
                        <Funnel size="20"></Funnel>

                        <!-- Heading -->
                        <h2>Filters</h2>

                    </div>

                    <!-- Close Icon -->
                    <svg
                        @click="closeFilterDrawer"
                        class="w-6 h-6 text-gray-400 cursor-pointer hover:opacity-90 active:opacity-80 active:scale-90 transition-all"
                        xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M6 18 18 6M6 6l12 12" />
                    </svg>

                </div>

                <p class="p-4 text-sm bg-blue-100">
                    Find exactly what you need by applying filters to your data
                </p>

                <div v-if="isLoadingFilters" class="flex justify-center my-8">

                    <!-- Loader -->
                    <Loader></Loader>

                </div>

                <div v-else class="divide-y mb-4">
                    <template
                        :key="index"
                        v-for="(filter, index) in localFilters">

                        <div v-if="showMore ? true : filter.priority">

                            <div
                                @click="toggleFilterVisibility(index)"
                                class="text-sm p-4 bg-gray-50 hover:bg-gray-100 cursor-pointer flex items-center justify-between">

                                <span>{{ filter.label }}</span>

                                <div class="flex items-center space-x-2">
                                    <Pill v-if="countActiveOptions(filter)" type="primary" size="xs" class="flex-shrink-0">{{ countActiveOptions(filter) }}</Pill>
                                    <svg v-if="filter.active" class="w-4 h-4" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="m4.5 15.75 7.5-7.5 7.5 7.5" />
                                    </svg>
                                    <svg v-else class="w-4 h-4" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="m19.5 8.25-7.5 7.5-7.5-7.5" />
                                    </svg>
                                </div>
                            </div>

                            <div v-if="filter.active" class="text-sm p-4">

                                <!-- If it's an options type filter (Checkboxes) -->
                                <div v-for="(option, index2) in filter.options" :key="index2">

                                    <Input
                                        @click.stop
                                        type="checkbox"
                                        v-model="option.active"
                                        :inputLabel="option.label">
                                    </Input>

                                    <div v-if="option.active && ['date', 'money'].includes(filter.type)" class="my-4">

                                        <!-- If it's a date type filter (Date Input) -->
                                        <div v-if="filter.type === 'date'" class="space-y-4">

                                            <Datepicker @click.stop v-model="option.value1"></Datepicker>
                                            <Datepicker v-if="['bt', 'bt_ex'].includes(option.value)" @click.stop v-model="option.value2"></Datepicker>

                                        </div>

                                        <!-- If it's a money type filter (Numeric Input) -->
                                        <div v-else-if="filter.type === 'money'" class="space-y-4">

                                            <Input type="money" @click.stop v-model="option.value1" />
                                            <Input type="money" v-if="['bt', 'bt_ex'].includes(option.value)" @click.stop v-model="option.value2" />

                                        </div>

                                    </div>

                                </div>

                            </div>

                        </div>

                    </template>

                </div>

                <!-- Clear Filters Button -->
                <div v-if="!isLoadingFilters" class="flex flex-col items-center px-4 space-y-8 mb-60">

                    <Button
                        size="xs"
                        type="outline"
                        :action="showMoreOrLess"
                        :leftIcon="showMore ? ArrowUp : ArrowDown"
                        v-if="hasPriorityFilters && hasNonPriorityFilters">
                        <span>{{ showMore ? 'show less options' : 'show more options' }}</span>
                    </Button>

                    <Button
                        size="sm"
                        type="light"
                        class="w-full"
                        :action="clearFilters"
                        v-if="totalActiveFilters">
                        <span>Clear Filters</span>
                    </Button>

                </div>

            </template>

        </Drawer>

    </div>

</template>

<script>

    import dayjs from 'dayjs';
    import isEqual from 'lodash/isEqual';
    import Pill from '@Partials/Pill.vue';
    import Input from '@Partials/Input.vue';
    import cloneDeep from 'lodash/cloneDeep';
    import Button from '@Partials/Button.vue';
    import Drawer from '@Partials/Drawer.vue';
    import Loader from '@Partials/Loader.vue';
    import Datepicker from '@Partials/Datepicker.vue';
    import { Funnel, ArrowUp, ArrowDown } from 'lucide-vue-next';

    export default {
        inject: ['notificationState'],
        components: { Pill, Input, Button, Drawer, Datepicker, Loader, Funnel },
        props: {
            filterExpressions: {
                type: Array,
                default: () => []
            },
            resource: {
                type: String
            },
        },
        emits: ['updatedFilters'],
        data() {
            return {
                Funnel,
                ArrowUp,
                ArrowDown,
                showMore: false,
                localFilters: null,
                filterDrawer: null,
                originalFilters: null,
                isLoadingFilters: false,
                lastEmittedFilters: null,
            }
        },
        watch: {
            localFilters: {
                handler(newVal) {
                    this.createFilterExpressions();
                },
                deep: true
            },
        },
        computed: {
            store() {
                return this.storeState.store;
            },
            hasFilterExpressions() {
                return this.filterExpressions.length > 0;
            },
            totalActiveFilters() {
                if(this.localFilters == null) return 0;

                return this.localFilters.filter((filter) => {
                    return filter.options.some((option) => option.active);
                }).length;
            },
            hasPriorityFilters() {
                if(this.localFilters == null) return false;

                return this.localFilters.filter((filter) => {
                    return filter.priority;
                }).length > 0;
            },
            hasNonPriorityFilters() {
                if(this.localFilters == null) return false;

                return this.localFilters.filter((filter) => {
                    return !filter.priority;
                }).length > 0;
            }
        },
        methods: {
            countActiveOptions(filter) {
                return filter.options.filter((option) => option.active).length;
            },
            createFilterExpressions() {

                let filters = [];

                for (let filterIndex = 0; filterIndex < this.localFilters.length; filterIndex++) {

                    const localFilter = this.localFilters[filterIndex];
                    const hasActiveOptions = localFilter.options.some((option) => option.active);

                    if(hasActiveOptions) {

                        if (localFilter.type === 'checkboxes') {

                            const activeFilterOptions = localFilter.options.filter((option) => option.active);
                            const activeFilterOptionValues = activeFilterOptions.map((option) => option.value);
                            const valuesCommaSeparated = activeFilterOptionValues.join(',');

                            const label = `${localFilter.label} = ${valuesCommaSeparated}`;
                            const expression = `${localFilter.target}:in:${valuesCommaSeparated}`;

                            filters.push({
                                label: label,
                                expression: expression,
                                filterIndex: filterIndex
                            });

                        }else{

                            for (let optionIndex = 0; optionIndex < localFilter.options.length; optionIndex++) {

                                const filterOption = localFilter.options[optionIndex];
                                const isActiveFilterOption = filterOption.active;

                                if(isActiveFilterOption) {

                                    if (['money', 'date'].includes(localFilter.type)) {

                                        let label = null;
                                        let expression = null;

                                        let value1 = filterOption.value1;
                                        let value2 = filterOption.value2;
                                        const operator = filterOption.value;

                                        if (localFilter.type === 'date') {
                                            value1 = dayjs(filterOption.value1, 'DD MMM YYYY').format('YYYY-MM-DD');
                                            value2 = dayjs(filterOption.value2, 'DD MMM YYYY').format('YYYY-MM-DD');
                                        }

                                        if (['bt', 'bt_ex'].includes(operator)) {
                                            label = `${localFilter.label} between ${filterOption.value1} - ${filterOption.value2} ${
                                                operator == 'bt' ? '(including)' : '(excluding)'
                                            }`;
                                            expression = `${localFilter.target}:${operator}:${value1}:${value2}`;
                                        } else {
                                            label = `${localFilter.label} ${operator} ${filterOption.value1}`;
                                            expression = `${localFilter.target}:${operator}:${value1}`;
                                        }

                                        filters.push({
                                            label: label,
                                            expression: expression,
                                            filterIndex: filterIndex,
                                            optionIndex: optionIndex
                                        });

                                    }

                                }

                            }

                        }

                    }

                }

                if(this.filtersHaveChanged(filters, this.lastEmittedFilters)) {
                    this.$emit('updatedFilters', filters);
                    this.lastEmittedFilters = cloneDeep(filters);
                }
            },
            applyFilterExpressions() {
                this.filterExpressions.forEach((filterExpression) => {

                    // Example: ['status', 'in', 'waiting,cancelled']
                    const parts = filterExpression.split(':');

                    const target = parts[0];           // 'status'
                    const operator = parts[1];         // 'in'
                    const value1 = parts[2];           // 'waiting,cancelled' or '2025-02-20'
                    const value2 = parts[3] ?? null;   // '2025-03-06'

                    // Find the localFilter that matches the target
                    const matchingFilter = this.localFilters.find(filter => filter.target === target);

                    if (matchingFilter) {

                        // If the filter is of type 'checkboxes'
                        if (operator === 'in') {

                            const values = value1.split(',');

                            matchingFilter.options.forEach((option) => {
                                if (values.includes(option.value)) {
                                    option.active = true;
                                }
                            });

                        } else {

                            matchingFilter.options.forEach((option) => {
                                if (option.value === operator) {
                                    option.active = true;

                                    if (dayjs(value1).isValid()) {
                                        option.value1 = dayjs(value1).format('DD MMM YYYY');    //  format for dates
                                    }else{
                                        option.value1 = value1;                                 //  Leave as is e.g money
                                    }

                                    if (value2 && dayjs(value2).isValid()) {
                                        option.value2 = dayjs(value2).format('DD MMM YYYY');    //  format for dates
                                    }else{
                                        option.value2 = value2;                                 //  Leave as is e.g money
                                    }

                                }
                            });

                        }
                    }
                });
            },
            filtersHaveChanged(filter1, filter2) {
                // Clone the objects to avoid modifying the original data
                var a = cloneDeep(filter1);
                var b = cloneDeep(filter2);

                // Compare the modified arrays for equality
                return !isEqual(a, b);
            },
            openFilterDrawer() {
                if(this.localFilters == null) this.getFilters();
                this.filterDrawer.showDrawer();
            },
            closeFilterDrawer() {
                this.filterDrawer.hideDrawer();
            },
            toggleFilterVisibility(index) {
                if (!this.localFilters[index].active) {
                    this.localFilters.forEach((filter, i) => {
                        this.localFilters[i].active = false;
                    });
                }

                this.localFilters[index].active = !this.localFilters[index].active;
            },
            showMoreOrLess() {
                this.showMore = !this.showMore;
            },
            removeAppliedFilter(filter) {
                if (filter.hasOwnProperty('filterIndex') && filter.hasOwnProperty('optionIndex')) {
                    this.localFilters[filter.filterIndex].options[filter.optionIndex].active = false;
                } else {
                    this.localFilters[filter.filterIndex].options.forEach((option) => {
                        option.active = false;
                    });
                }
            },
            clearFilters() {
                this.localFilters = cloneDeep(this.originalFilters);
            },
            async getFilters() {

                try {

                    if(this.isLoadingFilters) return;

                    //  Start loader
                    this.isLoadingFilters = true;

                    //  Set the query params
                    const config = {
                        params: {
                            'type': this.resource
                        }
                    };

                    const response = await axios.get('/api/filters', config);

                    this.localFilters = response.data.map(filter => {
                        return {
                            ...filter,
                            active: false,
                            options: filter.options.map((option) => {

                                let _option = {
                                    ...option,
                                    active: false
                                };

                                if(filter.type == 'money') {

                                    _option.value1 = '0.00';
                                    _option.value2 = '0.00';

                                }else if(filter.type == 'date') {

                                    _option.value1 = dayjs().subtract(7, 'day').format('DD MMM YYYY'); // 7 days ago
                                    _option.value2 = dayjs().add(7, 'day').format('DD MMM YYYY');      // 7 days ahead

                                }

                                return _option;

                            })
                        };
                    });

                    this.originalFilters = cloneDeep(this.localFilters);
                    this.applyFilterExpressions();

                } catch (error) {

                    const message = error?.response?.data?.message || error?.message || 'Something went wrong while fetching the filter options';
                    this.notificationState.showWarningNotification(message);

                    console.error('Failed to fetch filter options:', error);

                } finally {
                    this.isLoadingFilters = false;
                }
            }
        },
        mounted() {
            this.filterDrawer = this.$refs.filterDrawer;
            if(this.hasFilterExpressions) this.getFilters();
        },
    };
</script>
