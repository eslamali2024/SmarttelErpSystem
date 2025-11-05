export function useStrLimit(string: string, limit: number = 15): string {
    if (string && string.length > limit) {
        return string.slice(0, limit) + '...';
    }

    return string ?? '-';
}
