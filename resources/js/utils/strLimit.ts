/**
 * Truncates a string to a specified limit and adds ellipsis.
 * Returns a fallback string ('-') if the input is null, undefined, or empty.
 *
 * @param {string | null | undefined} input The string to limit.
 * @param {number} limit The character limit.
 * @param {string} fallback The fallback string for empty/nullish values.
 * @returns {string} The limited string or fallback.
 */
export function strLimit(
    input: string | null | undefined,
    limit: number = 15,
    fallback: string = '-'
): string {

    if (!input) {
        return fallback;
    }

    if (input.length > limit) {
        return input.slice(0, limit) + '...';
    }

    return input;
}
