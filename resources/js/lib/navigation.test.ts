import { describe, expect, it, vi } from 'vitest';
import type { NavMenuItem } from '@/interfaces';
import { filterNavByPermissions, resolveHref } from './navigation';

vi.mock('@inertiajs/vue3', () => ({
    usePage: () => ({
        props: {
            auth: {
                can: {
                    'users.view': true,
                    'roles.*': true,
                },
            },
        },
    }),
}));

describe('navigation utilities', () => {
    describe('resolveHref', () => {
        it('returns direct href when provided', () => {
            const item: NavMenuItem = { label: 'Home', href: '/custom-url' };
            expect(resolveHref(item)).toBe('/custom-url');
        });

        it('returns path or fallback when route is provided', () => {
            const item: NavMenuItem = { label: 'Direct', route: '/dashboard' };
            expect(resolveHref(item)).toBe('/dashboard');
        });

        it('returns fallback hash when no route or href is provided', () => {
            const item: NavMenuItem = { label: 'Empty' };
            expect(resolveHref(item)).toBe('#');
        });
    });

    describe('filterNavByPermissions', () => {
        it('keeps items without permission', () => {
            const items: NavMenuItem[] = [
                { label: 'Public 1' },
                { label: 'Public 2' },
            ];

            const result = filterNavByPermissions(items);
            expect(result).toHaveLength(2);
        });

        it('filters leaf items based on permission', () => {
            const items: NavMenuItem[] = [
                { label: 'Users', permission: 'users.view' },
                { label: 'Roles', permission: 'roles.view' },
                { label: 'Modules', permission: 'modules.view' },
            ];

            const result = filterNavByPermissions(items);
            expect(result).toHaveLength(2);
            expect(result.map((item) => item.label)).toEqual([
                'Users',
                'Roles',
            ]);
        });

        it('preserves parent menu if at least one child is allowed', () => {
            const items: NavMenuItem[] = [
                {
                    label: 'Security',
                    menu: [
                        { label: 'Users', permission: 'users.view' },
                        { label: 'Modules', permission: 'modules.view' },
                    ],
                },
            ];

            const result = filterNavByPermissions(items);
            expect(result).toHaveLength(1);
            expect(result[0].menu).toHaveLength(1);
            expect(result[0].menu?.[0].label).toBe('Users');
        });

        it('discards parent menu completely if all children are denied', () => {
            const items: NavMenuItem[] = [
                {
                    label: 'Security',
                    menu: [
                        { label: 'Modules', permission: 'modules.view' },
                        {
                            label: 'Permissions',
                            permission: 'permissions.view',
                        },
                    ],
                },
            ];

            const result = filterNavByPermissions(items);
            expect(result).toHaveLength(0);
        });
    });
});
