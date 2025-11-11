/**
 * Format a number with the given number of decimal pointers and separators.
 * @param {number|string} value - The number to format.
 * @param {number} [pointers=2] - The number of decimal pointers to use.
 * @param {string} [decimalSeparator='.'] - The separator to use for decimal points.
 * @param {string} [thousandSeparator=','] - The separator to use for thousand points.
 * @returns {string} The formatted number.
 */
export function numberFormat(
    value: number | string,
    pointers: number = 2,
    currency: boolean = false,
    decimalSeparator: string = '.',
    thousandSeparator: string = ',',
) {
    const numberValue = Number(value);

    if (isNaN(numberValue)) return '-';

    // Format the number
    let formatted = numberValue.toLocaleString('en-US', {
        minimumFractionDigits: pointers,
        maximumFractionDigits: pointers,
    });

    // Replace decimal and thousand separators
    if (decimalSeparator !== '.' || thousandSeparator !== ',') {
        formatted = formatted.replace(/\./g, 'DECIMAL_TEMP')
            .replace(/,/g, thousandSeparator)
            .replace(/DECIMAL_TEMP/g, decimalSeparator);
    }

    return formatted + (currency ? ' EGP' : '');
}
