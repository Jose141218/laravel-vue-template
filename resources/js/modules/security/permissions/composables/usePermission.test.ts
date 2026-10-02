import { beforeEach, describe, expect, it, vi } from 'vitest';
import { usePermission } from './usePermission';

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

describe('usePermission', () => {
    beforeEach(() => {
        vi.clearAllMocks();
    });

    it('initializes with default values in create mode', () => {
        const { form, isEditing } = usePermission({
            routeName: 'security.permissions.',
        });

        expect(isEditing.value).toBe(false);
        expect(form.name).toBe('');
        expect(form.description).toBe('');
        expect(form.module_key).toBe('');
    });

    it('initializes with permission data in edit mode', () => {
        const { form, isEditing } = usePermission({
            routeName: 'security.permissions.',
            permission: {
                id: 10,
                name: 'users.create',
                description: 'Create users permission',
                module_key: 'users',
                guard_name: 'web',
                created_at: {
                    raw: '2026-01-01',
                    formatted: '2026-01-01',
                    human: '1 month ago',
                    diff: '1 month ago',
                },
            },
        });

        expect(isEditing.value).toBe(true);
        expect(form.name).toBe('users.create');
        expect(form.description).toBe('Create users permission');
        expect(form.module_key).toBe('users');
    });

    it('calls storeForm on form.post with route and preserveScroll', () => {
        const { storeForm } = usePermission({
            routeName: 'security.permissions.',
        });

        storeForm();

        expect((globalThis as any).route).toHaveBeenCalledWith(
            'security.permissions.store',
        );
        expect(mockPost).toHaveBeenCalledWith(
            '/mocked-route/security.permissions.store',
            {
                preserveScroll: true,
            },
        );
    });

    it('calls updateForm on form.put when permission id exists', () => {
        const { updateForm } = usePermission({
            routeName: 'security.permissions.',
            permission: {
                id: 10,
                name: 'users.create',
                description: 'Create users',
                module_key: 'users',
                guard_name: 'web',
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
            'security.permissions.update',
            10,
        );
        expect(mockPut).toHaveBeenCalledWith(
            '/mocked-route/security.permissions.update/10',
            {
                preserveScroll: true,
            },
        );
    });

    it('does not call updateForm when permission id does not exist', () => {
        const { updateForm } = usePermission({
            routeName: 'security.permissions.',
        });

        updateForm();

        expect(mockPut).not.toHaveBeenCalled();
    });

    it('submits using storeForm in create mode', () => {
        const { submit } = usePermission({
            routeName: 'security.permissions.',
        });

        submit();

        expect(mockPost).toHaveBeenCalledTimes(1);
        expect(mockPut).not.toHaveBeenCalled();
    });

    it('submits using updateForm in edit mode', () => {
        const { submit } = usePermission({
            routeName: 'security.permissions.',
            permission: {
                id: 25,
                name: 'roles.delete',
                description: 'Delete roles',
                module_key: 'roles',
                guard_name: 'web',
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
