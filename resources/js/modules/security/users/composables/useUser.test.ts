import { describe, expect, it, vi } from 'vitest';
import { useUser } from './useUser';

// Mock inertia useForm
vi.mock('@inertiajs/vue3', () => ({
    useForm: vi.fn((data: any) => ({
        ...data,
        post: vi.fn(),
        put: vi.fn(),
        reset: vi.fn(),
    })),
}));

describe('useUser composable', () => {
    it('initializes with empty values and roles in create mode', () => {
        const { form, isEditing, isRoleSelected } = useUser({
            routeName: 'security.users.',
        });

        expect(isEditing.value).toBe(false);
        expect(form.name).toBe('');
        expect(form.email).toBe('');
        expect(form.send_invitation).toBe(true);
        expect(form.roles).toEqual([]);
        expect(isRoleSelected('admin')).toBe(false);
    });

    it('initializes with existing user data and maps role objects in edit mode', () => {
        const { form, isEditing, isRoleSelected } = useUser({
            routeName: 'security.users.',
            user: {
                id: '1',
                name: 'Carlos Ruiz',
                email: 'carlos@example.com',
                roles: [
                    { id: 1, name: 'admin' },
                    { id: 2, name: 'editor' },
                ],
                email_verified_at: null,
                status: 'active',
                created_at: {
                    raw: '2026-01-01',
                    formatted: '2026-01-01',
                    human: 'hace 1 mes',
                    diff: '1 month ago',
                },
            },
        });

        expect(isEditing.value).toBe(true);
        expect(form.name).toBe('Carlos Ruiz');
        expect(form.email).toBe('carlos@example.com');
        expect(form.send_invitation).toBe(false);
        expect(form.roles).toEqual(['admin', 'editor']);
        expect(isRoleSelected('admin')).toBe(true);
        expect(isRoleSelected('editor')).toBe(true);
        expect(isRoleSelected('viewer')).toBe(false);
    });

    it('toggles roles correctly', () => {
        const { form, toggleRole, isRoleSelected } = useUser({
            routeName: 'security.users.',
            user: {
                id: '3',
                name: 'Pedro',
                email: 'pedro@example.com',
                roles: [{ id: 1, name: 'admin' }],
                email_verified_at: null,
                status: 'active',
                created_at: {
                    raw: '2026-01-01',
                    formatted: '2026-01-01',
                    human: 'hace 1 mes',
                    diff: '1 month ago',
                },
            },
        });

        expect(isRoleSelected('admin')).toBe(true);

        // Toggle admin off
        toggleRole('admin');
        expect(form.roles).not.toContain('admin');
        expect(isRoleSelected('admin')).toBe(false);

        // Toggle editor on
        toggleRole('editor');
        expect(form.roles).toContain('editor');
        expect(isRoleSelected('editor')).toBe(true);
    });
});
