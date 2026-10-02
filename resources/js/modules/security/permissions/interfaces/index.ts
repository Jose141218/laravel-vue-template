import type { BaseFilters, FormattedDate } from '@/interfaces';

export interface Permission {
    id: number;
    name: string;
    description?: string | null;
    module_key?: string | null;
    guard_name: string;
    created_at: FormattedDate;
    updated_at?: FormattedDate | null;
}

export interface PermissionFormData {
    name: string;
    description: string;
    module_key: string;
}

export interface ModuleOption {
    key: string;
    name: string;
}

export interface PermissionFilters extends BaseFilters {
    module_key?: string;
}
