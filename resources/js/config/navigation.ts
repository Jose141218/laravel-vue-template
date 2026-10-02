import {
    FolderKanban,
    KeyRound,
    LayoutGrid,
    ShieldCheck,
    Users,
} from '@lucide/vue';
import type { NavMenuItem } from '@/interfaces';

export const navigationMenu: NavMenuItem[] = [
    {
        label: 'Dashboard',
        route: 'dashboard',
        icon: LayoutGrid,
    },
    {
        label: 'Seguridad',
        icon: ShieldCheck,
        menu: [
            {
                label: 'Módulos',
                route: 'security.modules.index',
                icon: FolderKanban,
                permission: 'modules.view',
            },
            {
                label: 'Permisos',
                route: 'security.permissions.index',
                icon: KeyRound,
                permission: 'permissions.view',
            },
            {
                label: 'Roles',
                route: 'security.roles.index',
                icon: ShieldCheck,
                permission: 'roles.view',
            },
            {
                label: 'Usuarios',
                route: 'security.users.index',
                icon: Users,
                permission: 'users.view',
            },
        ],
    },
];
