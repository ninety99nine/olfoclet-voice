<template>

    <Select
        v-bind="$props"
        :options="currencyOptions"
        placeholder="Select a currency"
        noResultsText="No currencies found"
        :searchableFields="['label', 'value']">
    </Select>

</template>

<script>

    import Select from '@Partials/Select.vue';
    import currencies from '@Json/currencies.json';

    export default {
        components: { Select },
        props: {
            allowedCurrencies: {
                type: [Array, null],
                default: null
            }
        },
        data() {
            return {
                currencyOptions: []
            }
        },
        created() {
            const allCurrencies = Object.values(currencies).map(currency => ({
                label: `${currency.name} (${currency.code})`,
                value: currency.code
            }));

            if (this.allowedCurrencies && Array.isArray(this.allowedCurrencies)) {
                this.currencyOptions = allCurrencies.filter(option =>
                    this.allowedCurrencies.includes(option.value)
                );
            } else {
                this.currencyOptions = allCurrencies;
            }
        }
    }

</script>
