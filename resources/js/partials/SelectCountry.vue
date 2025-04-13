<template>

    <Select
        v-bind="$props"
        :options="countryOptions"
        noResultsText="No countries found"
        :searchableFields="['label', 'value']">

        <!-- Custom Styled Selected Option -->
        <template #selectedOption="{ selectedOption }">
            <div v-if="selectedOption" class="flex items-center space-x-2">
                <img :src="`/svgs/country-flags/${selectedOption.value}.svg`" class="w-4 h-4" />
                <span class="text-gray-700 text-sm leading-3">{{ selectedOption.label }}</span>
            </div>
            <span v-else class="text-gray-700 text-sm">Select a country</span>
        </template>

        <!-- Custom Styled Dropdown Select Option -->
        <template #option="{ option }">
            <div class="flex items-center space-x-2">
                <img :src="`/svgs/country-flags/${option.value}.svg`" class="w-4 h-4" />
                <span class="text-gray-700 text-sm">{{ option.label }}</span>
            </div>
        </template>

    </Select>

</template>

<script>

    import Select from '@Partials/Select.vue';
    import countries from '@Json/countries.json';

    export default {
        components: { Select },
        data() {
            return {
                countryOptions: []
            }
        },
        created() {
            this.countryOptions = countries.map((country) => {
                return {
                    'label': country.name,
                    'value': country.iso
                };
            });
        }
    }

</script>
