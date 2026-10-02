import { verifyPermission } from '@/composables/usePermissions';
import type { NavMenuItem } from '@/interfaces';
import { toUrl } from '@/lib/utils';

export const resolveHref = (item: NavMenuItem): string => {
    if (item.href) {
        return toUrl(item.href);
    }

    if (item.route) {
        try {
            return typeof route === 'function' && route().has(item.route)
                ? route(item.route)
                : item.route.startsWith('/')
                  ? item.route
                  : '#';
        } catch {
            return item.route.startsWith('/') ? item.route : '#';
        }
    }

    return '#';
};

export const filterNavByPermissions = (items: NavMenuItem[]): NavMenuItem[] => {
    return items
        .map((item) => {
            if (item.menu && item.menu.length > 0) {
                const filteredMenu = filterNavByPermissions(item.menu);

                if (filteredMenu.length === 0) {
                    return null;
                }

                return {
                    ...item,
                    menu: filteredMenu,
                };
            }

            return verifyPermission(item.permission) ? item : null;
        })
        .filter((item): item is NavMenuItem => item !== null);
};
