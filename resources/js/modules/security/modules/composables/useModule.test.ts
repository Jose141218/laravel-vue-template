import { beforeEach, describe, expect, it, vi } from 'vitest';
import { useModule } from './useModule';

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

describe('useModule', () => {
    beforeEach(() => {
        vi.clearAllMocks();
    });

    it('initializes with default values in create mode', () => {
        const { form, isEditing } = useModule({
            routeName: 'security.modules.',
        });

        expect(isEditing.value).toBe(false);
        expect(form.name).toBe('');
        expect(form.key).toBe('');
        expect(form.description).toBe('');
    });

    it('initializes with module data in edit mode', () => {
        const { form, isEditing } = useModule({
            routeName: 'security.modules.',
            module: {
                id: 'module-uuid-1',
                name: 'User Management',
                key: 'users',
                description: 'Manage system users',
                created_at: {
                    raw: '2026-01-01',
                    formatted: '2026-01-01',
                    human: '1 month ago',
                    diff: '1 month ago',
                },
            },
        });

        expect(isEditing.value).toBe(true);
        expect(form.name).toBe('User Management');
        expect(form.key).toBe('users');
        expect(form.description).toBe('Manage system users');
    });

    it('calls storeForm on form.post with route', () => {
        const { storeForm } = useModule({
            routeName: 'security.modules.',
        });

        storeForm();

        expect((globalThis as any).route).toHaveBeenCalledWith(
            'security.modules.store',
        );
        expect(mockPost).toHaveBeenCalledWith(
            '/mocked-route/security.modules.store',
        );
    });

    it('calls updateForm on form.put when module id exists', () => {
        const { updateForm } = useModule({
            routeName: 'security.modules.',
            module: {
                id: 'mod-123',
                name: 'Role Management',
                key: 'roles',
                description: '',
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
            'security.modules.update',
            'mod-123',
        );
        expect(mockPut).toHaveBeenCalledWith(
            '/mocked-route/security.modules.update/mod-123',
        );
    });

    it('does not call updateForm when module id does not exist', () => {
        const { updateForm } = useModule({
            routeName: 'security.modules.',
        });

        updateForm();

        expect(mockPut).not.toHaveBeenCalled();
    });

    it('submits using storeForm in create mode', () => {
        const { submit } = useModule({
            routeName: 'security.modules.',
        });

        submit();

        expect(mockPost).toHaveBeenCalledTimes(1);
        expect(mockPut).not.toHaveBeenCalled();
    });

    it('submits using updateForm in edit mode', () => {
        const { submit } = useModule({
            routeName: 'security.modules.',
            module: {
                id: 'mod-456',
                name: 'Permissions',
                key: 'permissions',
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
