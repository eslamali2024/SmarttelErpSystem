import qs from 'qs'
import { reactive, computed } from 'vue'
import { router } from '@inertiajs/vue3'
import { watchDebounced } from '@vueuse/core'
import { merge, cloneDeep, isPlainObject } from 'lodash-es'

/**
 * Converts strings like "value,=" → { value: "value", operator: "=" }
 */
function normalizeUrlEntries(obj: any): any {
    if (Array.isArray(obj)) {
        return obj.map(normalizeUrlEntries)
    }

    if (isPlainObject(obj)) {
        return Object.fromEntries(
            Object.entries(obj).map(([k, v]) => [k, normalizeUrlEntries(v)])
        )
    }

    if (typeof obj === 'string' && obj.includes(',')) {
        const [value, operator] = obj.split(',', 2)
        return { value, operator }
    }

    return obj
}

/**
 * Deeply cleans and transforms object without mutating original one
 */
function deepCleanAndTransform(obj: any): any {
    if (Array.isArray(obj)) {
        return obj
            .map(deepCleanAndTransform)
            .filter(v => v !== '' && v != null && (typeof v !== 'object' || Object.keys(v).length > 0))
    }

    if (typeof obj === 'object' && obj !== null) {
        // Special case: has value + operator
        if (Object.prototype.hasOwnProperty.call(obj, 'value') && Object.prototype.hasOwnProperty.call(obj, 'operator')) {
            const value = obj.value ?? ''
            const operator = obj.operator ?? ''

            // Remove If Value Empty
            if (value === '') return undefined
            if (value === '' && operator === '') return undefined
            return `${value},${operator}`
        }

        const cleaned = Object.fromEntries(
            Object.entries(obj)
                .map(([k, v]) => [k, deepCleanAndTransform(v)])
                .filter(([, v]) => v !== '' && v != null && (typeof v !== 'object' || Object.keys(v).length > 0))
        )

        return Object.keys(cleaned).length > 0 ? cleaned : undefined
    }

    return obj
}

/**
 * A composition function that will help you to manage search parameters in url.
 * It will parse the current url search string and convert it into a reactive object.
 * You can then use this object to watch for changes and update the url accordingly.
 * The function will also merge the parsed search string with the default parameters.
 * It will also clean the search object by removing any empty or null values.
 * @param {string} route - The route to be used when updating the url.
 * @param {Record<string, any>} defaultParams - The default parameters to be used when merging with the parsed search string.
 * @returns {{ search: Reactive<Record<string, any>>, cleanedSearch: ComputedRef<Record<string, any>> }}
 */
export function useSearchTable(
    route: string,
    defaultParams: Record<string, any> = {}
) {
    const searchString = window.location.search.substring(1)
    const urlEntries = qs.parse(searchString, {
        ignoreQueryPrefix: true,
        arrayLimit: 100,
    })

    // normalize any string like "1,=" to object { value: "1", operator: "=" }
    const normalizedEntries = normalizeUrlEntries(urlEntries)

    // merge with defaults
    const initialState = merge({}, defaultParams, normalizedEntries)
    const search = reactive(initialState)

    const cleanedSearch = computed(() => {
        const cloned = cloneDeep(search)
        return deepCleanAndTransform(cloned)
    })

    watchDebounced(
        search,
        () => {
            const queryString = qs.stringify(cleanedSearch.value, { encode: false })
            router.visit(`${route}?${queryString}`, {
                preserveState: true,
                replace: true,
            })
        },
        { deep: true, debounce: 500 }
    )

    return { search }
}
