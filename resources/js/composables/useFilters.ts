import { router } from '@inertiajs/vue3';
import { useDebounceFn } from '@vueuse/core';
import { reactive, ref, watch } from 'vue';
import type { BaseFilters } from '@/interfaces';

export function useFilters<T extends BaseFilters>(
    initialFilters: T,
    routeName: string,
    options: {
        debounceTime?: number;
        preserveScroll?: boolean;
    } = {},
) {
    const { debounceTime = 350, preserveScroll = true } = options;

    const isLoading = ref(false);
    const filters = reactive<T>({ ...initialFilters });
    let isResetting = false;

    const applyFilters = (preserveState = true) => {
        if (isResetting) {
            return;
        }

        isLoading.value = true;
        const cleanFilters: Record<string, any> = {};

        Object.entries(filters as Record<string, any>).forEach(
            ([key, value]) => {
                if (value !== null && value !== undefined && value !== '') {
                    cleanFilters[key] = value;
                }
            },
        );

        router.get(routeName, cleanFilters, {
            preserveScroll,
            preserveState,
            replace: true,
            onFinish: () => {
                isLoading.value = false;
            },
        });
    };

    const clearFilters = () => {
        isResetting = true;
        isLoading.value = true;

        Object.keys(filters).forEach((key) => {
            if (key === 'rows') {
                (filters as Record<string, any>)[key] =
                    initialFilters.rows ?? 5;
            } else if (key === 'direction') {
                (filters as Record<string, any>)[key] = 'asc';
            } else if (key === 'order') {
                (filters as Record<string, any>)[key] =
                    initialFilters.order ?? 'created_at';
            } else {
                (filters as Record<string, any>)[key] = '';
            }
        });

        router.get(
            routeName,
            {},
            {
                preserveScroll,
                preserveState: true,
                replace: true,
                onFinish: () => {
                    isLoading.value = false;
                    isResetting = false;
                },
            },
        );
    };

    const debouncedSearch = useDebounceFn(() => {
        if (isResetting) {
            return;
        }

        applyFilters(true);
    }, debounceTime);

    watch(
        () => initialFilters,
        (newDefaults) => {
            if (newDefaults) {
                Object.assign(filters, newDefaults);
            }
        },
        { deep: true },
    );

    watch(
        () => filters.search,
        (newVal, oldVal) => {
            if (isResetting) {
                return;
            }

            if (newVal !== oldVal) {
                debouncedSearch();
            }
        },
    );

    watch(
        () => {
            const rest = { ...(filters as Record<string, any>) };
            delete rest.search;

            return JSON.stringify(rest);
        },
        (newVal, oldVal) => {
            if (isResetting) {
                return;
            }

            if (newVal !== oldVal) {
                applyFilters(true);
            }
        },
    );

    return {
        filters,
        isLoading,
        applyFilters,
        clearFilters,
    };
}
