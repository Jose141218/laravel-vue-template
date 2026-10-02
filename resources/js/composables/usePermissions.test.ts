import { describe, expect, it, vi } from 'vitest';
import {
    useCan,
    useCanAll,
    useCanAny,
    useRole,
    useRoles,
    verifyPermission,
} from './usePermissions';

vi.mock('@inertiajs/vue3', () => ({
    usePage: () => ({
        props: {
            auth: {
                user: { id: 'uuid-123', name: 'Admin', email: 'admin@sw.com' },
                roles: {
                    admin: true,
                    coordinador: true,
                },
                can: {
                    'users.view': true,
                    'users.create': true,
                    'roles.*': true,
                },
            },
        },
    }),
}));

describe('usePermissions composable', () => {
    it('checks direct permission correctly', () => {
        expect(useCan('users.view')).toBe(true);
        expect(useCan('users.create')).toBe(true);
        expect(useCan('users.delete')).toBe(false);
    });

    it('resolves wildcard permissions', () => {
        expect(useCan('roles.view')).toBe(true);
        expect(useCan('roles.create')).toBe(true);
        expect(useCan('roles.delete')).toBe(true);
    });

    it('checks any permissions', () => {
        expect(useCanAny(['users.view', 'nonexistent.perm'])).toBe(true);
        expect(useCanAny(['nonexistent.1', 'nonexistent.2'])).toBe(false);
    });

    it('checks all permissions', () => {
        expect(useCanAll(['users.view', 'users.create'])).toBe(true);
        expect(useCanAll(['users.view', 'users.delete'])).toBe(false);
    });

    it('checks role existence', () => {
        expect(useRole('admin')).toBe(true);
        expect(useRole('coordinador')).toBe(true);
        expect(useRole('invitado')).toBe(false);
    });

    it('checks any roles', () => {
        expect(useRoles(['admin', 'invitado'])).toBe(true);
        expect(useRoles(['invitado', 'otro'])).toBe(false);
    });

    it('verifies optional permission', () => {
        expect(verifyPermission(null)).toBe(true);
        expect(verifyPermission('users.view')).toBe(true);
        expect(verifyPermission('nonexistent')).toBe(false);
    });
});
