import type { BaseFilters, FormattedDate } from '@/interfaces';

export interface Role {
    id: number;
    name: string;
    description?: string | null;
    guard_name: string;
    permissions_count: number;
    users_count: number;
    permissions?: string[];
    created_at: FormattedDate;
    updated_at?: FormattedDate | null;
}

export interface RoleFormData {
    name: string;
    description: string;
    permissions: string[];
}

export interface PermissionItem {
    id: number;
    name: string;
    description: string | null;
    module_key: string | null;
}

export interface ModuleItem {
    id: string;
    name: string;
    key: string;
    description: string | null;
}

export type RoleFilters = BaseFilters;
