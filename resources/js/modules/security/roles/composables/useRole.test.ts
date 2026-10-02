import { beforeEach, describe, expect, it, vi } from 'vitest';
import { useRole } from './useRole';

const mockPost = vi.fn();
const mockPut = vi.fn();

vi.mock('@inertiajs/vue3', () => ({
    useForm: vi.fn((data: any) => ({
        ...data,
        post: mockPost,
        put: mockPut,
    })),
}));

(globalThis as any).route = vi.fn(
    (name: string, id?: number | string) =>
        `/mocked-route/${name}${id !== undefined ? `/${id}` : ''}`,
);

describe('useRole composable', () => {
    beforeEach(() => {
        vi.clearAllMocks();
    });

    it('initializes with default values in create mode', () => {
        const { form, isEditing } = useRole({
            routeName: 'security.roles.',
        });

        expect(isEditing.value).toBe(false);
        expect(form.name).toBe('');
        expect(form.description).toBe('');
        expect(form.permissions).toEqual([]);
    });

    it('initializes with role data in edit mode and copies permissions array', () => {
        const permissions = ['users.view', 'users.create'];
        const { form, isEditing } = useRole({
            routeName: 'security.roles.',
            role: {
                id: 3,
                name: 'Manager',
                description: 'Manager role',
                guard_name: 'web',
                permissions_count: 2,
                users_count: 5,
                permissions,
                created_at: {
                    raw: '2026-01-01',
                    formatted: '2026-01-01',
                    human: '1 month ago',
                    diff: '1 month ago',
                },
            },
        });

        expect(isEditing.value).toBe(true);
        expect(form.name).toBe('Manager');
        expect(form.description).toBe('Manager role');
        expect(form.permissions).toEqual(['users.view', 'users.create']);
        // Verify it's a clone and not the exact reference
        expect(form.permissions).not.toBe(permissions);
    });

    it('calls storeForm on form.post with route and preserveScroll', () => {
        const { storeForm } = useRole({
            routeName: 'security.roles.',
        });

        storeForm();

        expect((globalThis as any).route).toHaveBeenCalledWith(
            'security.roles.store',
        );
        expect(mockPost).toHaveBeenCalledWith(
            '/mocked-route/security.roles.store',
            {
                preserveScroll: true,
            },
        );
    });

    it('calls updateForm on form.put when role id exists', () => {
        const { updateForm } = useRole({
            routeName: 'security.roles.',
            role: {
                id: 7,
                name: 'Supervisor',
                guard_name: 'web',
                permissions_count: 0,
                users_count: 1,
                created_at: {
                    raw: '2026-01-01',
                    formatted: '2026-01-01',
                    human: '1 month ago',
                    diff: '1 month ago',
                },
            },
        });

        updateForm();

        expect((globalThis as any).route).toHaveBeenCalledWith(
            'security.roles.update',
            7,
        );
        expect(mockPut).toHaveBeenCalledWith(
            '/mocked-route/security.roles.update/7',
            {
                preserveScroll: true,
            },
        );
    });

    it('does not call updateForm when role id does not exist', () => {
        const { updateForm } = useRole({
            routeName: 'security.roles.',
        });

        updateForm();

        expect(mockPut).not.toHaveBeenCalled();
    });

    it('submits using storeForm in create mode', () => {
        const { submit } = useRole({
            routeName: 'security.roles.',
        });

        submit();

        expect(mockPost).toHaveBeenCalledTimes(1);
        expect(mockPut).not.toHaveBeenCalled();
    });

    it('submits using updateForm in edit mode', () => {
        const { submit } = useRole({
            routeName: 'security.roles.',
            role: {
                id: 12,
                name: 'Editor',
                guard_name: 'web',
                permissions_count: 0,
                users_count: 0,
                created_at: {
                    raw: '2026-01-01',
                    formatted: '2026-01-01',
                    human: '1 month ago',
                    diff: '1 month ago',
                },
            },
        });

        submit();

        expect(mockPut).toHaveBeenCalledTimes(1);
        expect(mockPost).not.toHaveBeenCalled();
    });
});
