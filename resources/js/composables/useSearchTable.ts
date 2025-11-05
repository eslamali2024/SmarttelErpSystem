import { reactive, watch } from 'vue';
import { router } from '@inertiajs/vue3';

export function useSearchTable(route: string, defaultParams: Record<string, string> = {}) {

    const urlParams = new URLSearchParams(window.location.search);
    const urlEntries = Object.fromEntries(urlParams.entries());

    const search = reactive({
        ...defaultParams,
        ...urlEntries,
    });

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
