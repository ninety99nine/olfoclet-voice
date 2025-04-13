import currencies from '@Json/currencies.json'

export function generateRandomNumber(length) {
    return Array.from({ length }, () => Math.floor(Math.random() * 10)).join('');
}

/**
 * Convert a value to a valid monetary format based on the currency's decimal digits.
 *
 * @param {number|string} value - The numeric value to format.
 * @param {string} currency - The currency code (e.g., 'USD', 'CAD').
 * @param {boolean} allowNegativeAmounts - Whether to allow negative values.
 * @returns {string} - The formatted money string.
 */
export function convertToValidMoney(value, currency, allowNegativeAmounts = false) {

    let numericValue = value.toString().replace(/[^0-9.-]/g, '');

    numericValue = parseFloat(numericValue) || 0;
    const decimalPlaces = currencies[currency]?.decimal_digits ?? 2;

    if (!allowNegativeAmounts) {
        numericValue = Math.max(0, numericValue);
    }

    return numericValue.toFixed(decimalPlaces);
}

