import type { PermissionItem } from '../interfaces';

export function useRolePermissions(
    form: { permissions: string[] },
    groupedPermissions?: Record<string, PermissionItem[]>,
) {
    const togglePermission = (name: string) => {
        const index = form.permissions.indexOf(name);

        if (index > -1) {
            form.permissions.splice(index, 1);
        } else {
            form.permissions.push(name);
        }
    };

    const toggleModulePermissions = (moduleKey: string) => {
        const perms = groupedPermissions?.[moduleKey] || [];

        if (perms.length === 0) {
            return;
        }

        const allSelected = perms.every((p) =>
            form.permissions.includes(p.name),
        );

        if (allSelected) {
            perms.forEach((p) => {
                const index = form.permissions.indexOf(p.name);

                if (index > -1) {
                    form.permissions.splice(index, 1);
                }
            });
        } else {
            perms.forEach((p) => {
                if (!form.permissions.includes(p.name)) {
                    form.permissions.push(p.name);
                }
            });
        }
    };

    return {
        togglePermission,
        toggleModulePermissions,
    };
}
