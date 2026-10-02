import { describe, expect, it } from 'vitest';
import { reactive } from 'vue';
import type { PermissionItem } from '../interfaces';
import { useRolePermissions } from './useRolePermissions';

describe('useRolePermissions', () => {
    const mockGroupedPermissions: Record<string, PermissionItem[]> = {
        users: [
            {
                id: 1,
                name: 'users.view',
                description: 'View users',
                module_key: 'users',
            },
            {
                id: 2,
                name: 'users.create',
                description: 'Create users',
                module_key: 'users',
            },
        ],
        roles: [
            {
                id: 3,
                name: 'roles.view',
                description: 'View roles',
                module_key: 'roles',
            },
            {
                id: 4,
                name: 'roles.edit',
                description: 'Edit roles',
                module_key: 'roles',
            },
        ],
    };

    it('toggles permission selection on and off', () => {
        const form = reactive({ permissions: ['users.view'] });
        const { togglePermission } = useRolePermissions(
            form,
            mockGroupedPermissions,
        );

        togglePermission('users.create');
        togglePermission('users.view');

        expect(form.permissions).toContain('users.create');
        expect(form.permissions).not.toContain('users.view');
    });

    it('toggles all module permissions on and off', () => {
        const form = reactive({ permissions: ['users.view'] });
        const { toggleModulePermissions } = useRolePermissions(
            form,
            mockGroupedPermissions,
        );

        toggleModulePermissions('users');
        expect(form.permissions).toContain('users.view');
        expect(form.permissions).toContain('users.create');

        toggleModulePermissions('users');
        expect(form.permissions).not.toContain('users.view');
        expect(form.permissions).not.toContain('users.create');
    });

    it('safely handles non-existent or empty module in toggleModulePermissions', () => {
        const form = reactive({ permissions: ['users.view'] });
        const { toggleModulePermissions } = useRolePermissions(
            form,
            mockGroupedPermissions,
        );

        toggleModulePermissions('nonexistent');
        expect(form.permissions).toEqual(['users.view']);
    });
});
