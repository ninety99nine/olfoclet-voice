<template>

    <div>

        <!-- Filter Button -->
        <Button :action="openFilterDrawer" type="outline" size="sm" icon="filter"></Button>

        <Drawer ref="filterDrawer" placement="right" :bodyScrolling="true" :backdrop="true" :hideCloseIcon="true">

            <template #content>

                <!-- Header -->
                <div class="flex justify-between items-center space-x-2 bg-gray-100 border-b shadow-sm p-4">

                    <div class="flex items-center space-x-2">

                        <!-- Filter Icon -->
                        <svg class="w-5 h-5 text-gray-700" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M12 3c2.755 0 5.455.232 8.083.678.533.09.917.556.917 1.096v1.044a2.25 2.25 0 0 1-.659 1.591l-5.432 5.432a2.25 2.25 0 0 0-.659 1.591v2.927a2.25 2.25 0 0 1-1.244 2.013L9.75 21v-6.568a2.25 2.25 0 0 0-.659-1.591L3.659 7.409A2.25 2.25 0 0 1 3 5.818V4.774c0-.54.384-1.006.917-1.096A48.32 48.32 0 0 1 12 3Z" />
                        </svg>

                        <!-- Heading -->
                        <h2 class="text-gray-700">
                            Filters
                        </h2>

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
                    <SpinningLoader></SpinningLoader>

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

                                    <Checkbox
                                        @click.stop
                                        v-model="option.active">
                                        {{ option.label }}
                                    </Checkbox>

                                    <div v-if="option.active && ['date', 'money'].includes(filter.type)" class="my-4">

                                        <!-- If it's a date type filter (Date Input) -->
                                        <div v-if="filter.type === 'date'" class="space-y-4">

                                            <Datepicker @click.stop v-model="option.value1"></Datepicker>
                                            <Datepicker v-if="['bt', 'bt_ex'].includes(option.value)" @click.stop v-model="option.value2"></Datepicker>
                                        </div>

                                        <!-- If it's a money type filter (Numeric Input) -->
                                        <div v-else-if="filter.type === 'money'" class="space-y-4">

                                            <MoneyInput @click.stop v-model="option.value1" />
                                            <MoneyInput v-if="['bt', 'bt_ex'].includes(option.value)" @click.stop v-model="option.value2" />

                                        </div>

                                    </div>

                                </div>

                            </div>

                        </div>

                    </template>

                </div>

                <!-- Clear Filters Button -->
                <div v-if="!isLoadingFilters" class="flex flex-col items-center px-4 space-y-8 mb-60">

                    <Button :action="showMoreOrLess" type="outline" size="xs" :icon="showMore ? 'short-up-arrow' : 'short-down-arrow'">
                        <span>{{ showMore ? 'show less options' : 'show more options' }}</span>
                    </Button>

                    <Button :action="clearFilters" type="light" size="sm" class="w-full">
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
    import cloneDeep from 'lodash/cloneDeep';
    import Pill from '@Partials/pills/Pill.vue';
    import Button from '@Partials/buttons/Button.vue';
    import Drawer from '@Partials/drawers/Drawer.vue';
    import Checkbox from '@Partials/checkboxes/Checkbox.vue';
    import MoneyInput from '@Partials/inputs/MoneyInput.vue';
    import Datepicker from '@Partials/datepicker/Datepicker.vue';
    import SpinningLoader from '@Partials/loaders/SpinningLoader.vue';

    export default {
        inject: ['apiState' , 'formState', 'storeState'],
        components: { Pill, Button, Drawer, Checkbox, MoneyInput, Datepicker, SpinningLoader },
        props: {
            filterExpressions: {
                type: Array,
                default: () => []
            },
        },
        emits: ['updatedFilters'],
        data() {
            return {
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

                if(this.isLoadingFilters) return;

                this.isLoadingFilters = true;

                const url = this.apiState.apiHome['_links']['showFilters'];

                let config = {
                    params: {
                        'type': 'orders'
                    }
                }

                if(this.store) {
                    config.params['store_id'] = this.store.id
                }

                await axios.get(url, config).then(response => {

                    if(response.status == 200) {

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

                    }else{

                        this.formState.setFormError('general', response.data.message);
                        this.notificationState.showWarningNotification(response.data.message);

                    }

                }).catch(errorException => {
                    this.formState.setServerFormErrors(errorException);
                });

                this.isLoadingFilters = false;

            },
        },
        mounted() {
            this.filterDrawer = this.$refs.filterDrawer;
            if(this.hasFilterExpressions) this.getFilters();
        },
    };
</script>
