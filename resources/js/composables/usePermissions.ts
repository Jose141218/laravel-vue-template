import { usePage } from '@inertiajs/vue3';
import type { Auth } from '@/interfaces';

interface SharedProps {
    auth: Auth;
    [key: string]: unknown;
}

/**
 * Checks if the authenticated user has a specific permission.
 */
export function useCan(permission: string): boolean {
    const page = usePage<SharedProps>();
    const can = page.props.auth?.can ?? {};

    // Wildcard matching: e.g. 'users.*' covers 'users.index'
    if (can[permission]) {
        return true;
    }

    const parts = permission.split('.');

    if (parts.length > 1) {
        const wildcard = `${parts[0]}.*`;

        if (can[wildcard]) {
            return true;
        }
    }

    return false;
}

/**
 * Checks if the authenticated user has ANY of the given permissions.
 */
export function useCanAny(permissions: string[]): boolean {
    return permissions.some((permission) => useCan(permission));
}

/**
 * Checks if the authenticated user has ALL of the given permissions.
 */
export function useCanAll(permissions: string[]): boolean {
    return permissions.every((permission) => useCan(permission));
}

/**
 * Checks if the authenticated user has a specific role.
 */
export function useRole(role: string): boolean {
    const page = usePage<SharedProps>();
    const roles = page.props.auth?.roles ?? {};

    return !!roles[role];
}

/**
 * Checks if the authenticated user has ANY of the given roles.
 */
export function useRoles(roles: string[]): boolean {
    return roles.some((role) => useRole(role));
}

/**
 * Verifies a permission condition; returns true if no permission is required.
 */
export function verifyPermission(permission?: string | null): boolean {
    if (!permission) {
        return true;
    }

    return useCan(permission);
}
