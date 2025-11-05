import { reactive, watch } from 'vue';
import { router } from '@inertiajs/vue3';

export function useSearchTable(route: string, defaultParams: Record<string, string> = {}) {
    const urlParams = new URLSearchParams(window.location.search);

    const search = reactive(
        Object.fromEntries(
            Object.keys(defaultParams).map(key => [
                key,
                urlParams.get(key) ?? defaultParams[key] ?? ''
            ])
        )
    );

    // watch search changes
    watch(
        search,
        () => {
            router.get(route, search, {
                preserveState: true,
                replace: true,
            });
        },
        { deep: true }
    );

    return { search };
}
