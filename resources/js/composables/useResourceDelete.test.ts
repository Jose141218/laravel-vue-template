import { beforeEach, describe, expect, test, vi } from 'vitest';
import { useResourceDelete } from './useResourceDelete';

const mockDelete = vi.fn();
vi.mock('@inertiajs/vue3', () => ({
    router: {
        delete: (...args: unknown[]) => mockDelete(...args),
    },
}));

(globalThis as any).route = vi.fn(
    (name: string, id: number | string) => `/${name.replace('.', '/')}/${id}`,
);

describe('useResourceDelete composable', () => {
    beforeEach(() => {
        vi.clearAllMocks();
    });

    test('initializes with default state', () => {
        const { itemToDelete, isDeleteDialogOpen, isDeleting } =
            useResourceDelete('security.modules.');

        expect(itemToDelete.value).toBeNull();
        expect(isDeleteDialogOpen.value).toBe(false);
        expect(isDeleting.value).toBe(false);
    });

    test('opens dialog on confirmDelete and sets item', () => {
        const { itemToDelete, isDeleteDialogOpen, confirmDelete } =
            useResourceDelete('security.modules.');

        confirmDelete({ id: 5, name: 'Test Module' });

        expect(itemToDelete.value).toEqual({ id: 5, name: 'Test Module' });
        expect(isDeleteDialogOpen.value).toBe(true);
    });

    test('closes dialog on cancelDelete and clears item', () => {
        const {
            itemToDelete,
            isDeleteDialogOpen,
            confirmDelete,
            cancelDelete,
        } = useResourceDelete('security.modules.');

        confirmDelete({ id: 5, name: 'Test Module' });
        cancelDelete();

        expect(itemToDelete.value).toBeNull();
        expect(isDeleteDialogOpen.value).toBe(false);
    });

    test('deletes item with Ziggy route name', () => {
        const { confirmDelete, handleDelete, isDeleting } =
            useResourceDelete('security.modules.');

        confirmDelete({ id: 10, name: 'Module 10' });
        handleDelete();

        expect(isDeleting.value).toBe(true);
        expect((globalThis as any).route).toHaveBeenCalledWith(
            'security.modules.destroy',
            10,
        );
        expect(mockDelete).toHaveBeenCalledWith(
            expect.any(String),
            expect.objectContaining({ preserveScroll: true }),
        );
    });
});
