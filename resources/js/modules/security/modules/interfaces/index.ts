import type { BaseFilters, FormattedDate } from '@/interfaces';

export interface Module {
    id: string;
    name: string;
    description?: string | null;
    key: string;
    user_id?: string | null;
    created_at: FormattedDate;
    updated_at?: FormattedDate | null;
}

export interface ModuleFormData {
    name: string;
    key: string;
    description: string;
}

export type ModuleFilters = BaseFilters;
