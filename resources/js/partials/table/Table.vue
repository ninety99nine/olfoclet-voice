<template>

    <!-- Table -->
    <div class="bg-white py-4 px-4 shadow-sm rounded-xl overflow-y-auto relative">

        <slot name="header"></slot>

        <div class="flex justify-between items-center mb-4">

            <div class="w-1/3 flex justify-center items-center space-x-4">

                <!-- Search Input -->
                <SearchInput v-model="localSearchTerm" :isSearching="isSearching" class="w-full"></SearchInput>

            </div>

            <div v-if="pagination" class="flex justify-center items-center space-x-2">

                <!-- Filter Drawer Button -->
                <FilterDrawer ref="filterDrawer" :filterExpressions="filterExpressions" @updatedFilters="updatedFilters"></FilterDrawer>

                <!-- Sorting Drawer Button -->
                <SortingDrawer ref="sortingDrawer" :sortingExpressions="sortingExpressions" @updatedSorting="updatedSorting"></SortingDrawer>

                <!-- Columns Drawer Button -->
                <ColumnsDrawer ref="columnsDrawer" :columns="columns" @updatedColumns="updatedColumns"></ColumnsDrawer>

                <!-- Refresh Button -->
                <Button :action="refresh" type="outline" size="sm" icon="refresh"></Button>

                <!-- Slot After Refresh Button -->
                <slot name="afterRefreshButton"></slot>

            </div>

        </div>

        <transition name="fade-1" mode="out-in">
            <div v-if="hasFilterExpressions && !hasFilters" class="flex flex-col items-center mb-4">
                <div class="flex items-center space-x-2">
                    <SpinningLoader></SpinningLoader>
                    <span class="text-sm text-gray-500">Preparing filters</span>
                </div>
            </div>
            <div v-else-if="hasSortingExpressions && !hasSorting" class="flex flex-col items-center mb-4">
                <div class="flex items-center space-x-2">
                    <SpinningLoader></SpinningLoader>
                    <span class="text-sm text-gray-500">Preparing sorting</span>
                </div>
            </div>
            <div v-else-if="hasFilters || hasSorting" class="flex flex-wrap gap-2 mb-4">
                <Pill :key="index" v-for="(filter, index) in filters" type="primary" size="xs" :closableAction="() => removeAppliedFilter(filter)">{{ filter.label }}</Pill>
                <Pill :key="index" v-for="(sort, index) in sorting" type="info" size="xs" :closableAction="() => removeAppliedSort(sort)">{{ sort.label }}</Pill>
            </div>
        </transition>

        <!-- Table Loader -->
        <div v-if="pagination && isLoading && !isSearching" class="absolute top-0 bottom-0 left-0 right-0 bg-white/50 flex justify-center items-center">
            <SpinningLoader v-if="!isSearching"></SpinningLoader>
        </div>

        <!-- Below Toolbar -->
        <slot name="belowToolbar"></slot>

        <div class="w-full overflow-x-auto rounded-lg">
            <table class="w-full text-sm text-left rtl:text-right text-gray-500">

                <!-- Table Head -->
                <thead class="text-xs text-gray-700 uppercase bg-gray-50">
                    <slot name="head"></slot>
                </thead>

                <!-- Pulsing Placeholder Rows -->
                <tbody v-if="!pagination && isLoading && totalActiveColumns > 0">

                    <tr v-for="(row, index) in [1,2,3]" :key="index" class="animate-pulse">
                        <td v-for="(column, index) in totalActiveColumns" :key="index">
                            <div class="h-2 bg-gray-200 rounded-full dark:bg-gray-700 mx-4 my-4"></div>
                        </td>
                    </tr>

                </tbody>

                <!--
                    Table Body Without <tbody> wrapper tag.
                    This slot is mainly used by the Vue Draggable Component which will define the <tbody> wrapper tag itself e.g:

                    The component would define this as follows:

                    <BasicTable>

                        <template #thead>
                            <tr>
                                ...
                            </tr>
                        </template>

                        <template #tbody>
                            <draggable tag="tbody">
                                <tr v-for="item in items">
                                    ...
                                </td>
                            </draggable>
                        </template>

                    </BasicTable>
                -->
                <slot v-else-if="$slots.tbody" name="tbody"></slot>

                <!--
                    Table Body With <tbody> wrapper tag.
                    This slot is maily used by most Components to simply set the <tr> tags only e.g:

                    The component would define this as follows:

                    <BasicTable>

                        <template #thead>
                            <tr>
                                ...
                            </tr>
                        </template>

                        <template #tbody>
                            <tr v-for="item in items">
                                ...
                            </td>
                        </template>

                    </BasicTable>
                -->
                <tbody v-else>
                    <slot name="body"></slot>

                </tbody>

            </table>
        </div>

        <!-- No Results Desclaimer -->
        <div v-if="pagination && pagination.total == 0" class="text-xs text-gray-700 text-center py-16 bg-gray-50 border-t">
            No results found
        </div>

        <div v-if="pagination && pagination.total > 0" class="flex justify-between items-center mt-4">

            <!-- Results -->
            <span class="text-sm font-normal text-gray-500 dark:text-gray-400">Showing <span class="font-semibold text-gray-900 dark:text-white">{{ pagination.from }}-{{ pagination.to }}</span> of <span class="font-semibold text-gray-900 dark:text-white">{{ pagination.total }}</span></span>

            <div class="flex items-center space-x-4">

                <SelectInput
                    width="w-fit"
                    v-model="localPerPage">
                    <option v-for="(perPage, index) in perPageOptions" :value="perPage" :key="index">
                        {{ perPage }} per page
                    </option>
                </SelectInput>

                <!-- Bottom Pagination -->
                <Pagination v-if="pagination" :pagination="pagination" @paginate="paginate"></Pagination>

            </div>

        </div>

    </div>

</template>

<script>
    /**
     * Component Reference: https://flowbite.com/docs/components/tables/
     */
    import Pill from '@Partials/pills/Pill.vue';
    import { Modal, initFlowbite } from "flowbite";
    import Button from '@Partials/buttons/Button.vue';
    import { generateUniqueId } from '@Utils/generalUtils.js';
    import SearchInput from '@Partials/inputs/SearchInput.vue';
    import SelectInput from '@Partials/inputs/SelectInput.vue';
    import Pagination from '@Partials/paginations/Pagination.vue';
    import SpinningLoader from '@Partials/loaders/SpinningLoader.vue';
    import FilterDrawer from '@Partials/tables/components/FilterDrawer.vue';
    import ColumnsDrawer from '@Partials/tables/components/ColumnsDrawer.vue';
    import SortingDrawer from '@Partials/tables/components/SortingDrawer.vue';

    export default {
        components: { Pill, Button, SearchInput, SelectInput, Pagination, SpinningLoader, FilterDrawer, ColumnsDrawer, SortingDrawer },
        props: {
            isLoading: {
                type: Boolean,
                default: false
            },
            searchTerm: {
                type: String,
                default: null
            },
            pagination: {
                type: Object
            },
            perPage: {
                type: String,
                default: '15'
            },
            columns: {
                type: Array,
                default: () => []
            },
            filterExpressions: {
                type: Array,
                default: () => []
            },
            sortingExpressions: {
                type: Array,
                default: () => []
            }
        },
        emits: ['search', 'refresh', 'paginate', 'updatedColumns', 'updatedFilters', 'updatedSorting', 'updatedPerPage'],
        data() {
            return {
                modal: null,
                filters: [],
                sorting: [],
                filterDrawer: null,
                sortingDrawer: null,
                localPerPage: this.perPage,
                localSearchTerm: this.searchTerm,
                perPageOptions: ['15', '50', '100', '200'],
                uniqueModalId: generateUniqueId('modal'),
            }
        },
        watch: {
            localSearchTerm(newValue) {
                this.$emit('search', newValue);
            },
            localPerPage(newValue) {
                this.$emit('updatedPerPage', newValue);
            },
            pagination(newValue) {
                if(newValue) {
                    this.$nextTick(() => {
                        this.filterDrawer = this.$refs.filterDrawer;
                        this.sortingDrawer = this.$refs.sortingDrawer;
                    });
                }
            }
        },
        computed: {
            isSearching() {
                return this.hasSearchTerm && this.isLoading;
            },
            hasSearchTerm() {
                return this.localSearchTerm != null && this.localSearchTerm.trim() != '';
            },
            hasFilters() {
                return this.filters.length > 0;
            },
            hasSorting() {
                return this.sorting.length > 0;
            },
            hasFilterExpressions() {
                return this.filterExpressions.length > 0;
            },
            hasSortingExpressions() {
                return this.sortingExpressions.length > 0;
            },
            totalActiveColumns() {
                return this.columns.filter((tableHeader) => tableHeader.active).length;
            }
        },
        methods: {
            refresh() {
                if(!this.isLoading) this.$emit('refresh');
            },
            paginate(url) {
                this.$emit('paginate', url);
            },
            updatedColumns(columns) {
                this.$emit('updatedColumns', columns);
            },
            updatedFilters(filters) {
                this.filters = filters;
                this.$emit('updatedFilters', filters);
            },
            updatedSorting(sorting) {
                this.sorting = sorting;
                this.$emit('updatedSorting', sorting);
            },
            removeAppliedFilter(filter) {
                this.filterDrawer.removeAppliedFilter(filter);
            },
            removeAppliedSort(sort) {
                this.sortingDrawer.removeAppliedSort(sort);
            }
        },
        mounted() {

            /**
             *  Flowbite javascript will not work unless we deliberately
             *  Initialize Flowbite on mount of the Vue component.
             *
             *  Reference: https://stackoverflow.com/questions/76043935/flowbite-is-not-working-with-inertia-laravel-app
             */
            initFlowbite();

            const $targetEl = document.getElementById(this.uniqueModalId);

            if($targetEl != null) {
                this.modal = new Modal($targetEl);
            }

        }
    };
</script>
