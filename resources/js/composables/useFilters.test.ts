import { useFilters } from './useFilters';

const mockGet = vi.fn();
vi.mock('@inertiajs/vue3', () => ({
    router: {
        get: (...args: unknown[]) => mockGet(...args),
    },
    useRemember: (data: unknown) => data,
}));

describe('useFilters composable', () => {
    it('initializes filters properly', () => {
        const { filters, isLoading } = useFilters(
            { search: 'test', rows: 10, order: 'name', direction: 'asc' },
            '/users',
        );

        expect(filters.search).toBe('test');
        expect(filters.rows).toBe(10);
        expect(isLoading.value).toBe(false);
    });

    it('applies filters using router.get', () => {
        const { applyFilters } = useFilters(
            { search: 'hello', rows: 15 },
            '/users',
        );

        applyFilters(true);
        expect(mockGet).toHaveBeenCalledWith(
            '/users',
            { search: 'hello', rows: 15 },
            expect.objectContaining({
                preserveScroll: true,
                preserveState: true,
                replace: true,
            }),
        );
    });

    it('clears filters and resets to default values with router.get', () => {
        const { filters, clearFilters, isLoading } = useFilters(
            { search: 'hello', rows: 15, order: 'name', direction: 'desc' },
            '/users',
        );

        clearFilters();

        expect(isLoading.value).toBe(true);
        expect(filters.search).toBe('');
        expect(filters.order).toBe('name');
        expect(filters.direction).toBe('asc');
        expect(filters.rows).toBe(15);
        expect(mockGet).toHaveBeenCalledWith(
            '/users',
            {},
            expect.objectContaining({
                preserveScroll: true,
                preserveState: true,
                replace: true,
            }),
        );
    });

    it('clears filters and defaults order to created_at when not provided', () => {
        const { filters, clearFilters } = useFilters(
            { search: 'hello', rows: 15, order: undefined },
            '/users',
        );

        clearFilters();

        expect(filters.order).toBe('created_at');
    });
});
