import type { BaseFilters, FormattedDate } from '@/interfaces';

export interface RoleData {
    id: number;
    name: string;
    description?: string | null;
}

export interface RoleOption {
    id: number;
    name: string;
    description: string | null;
}

export type UserStatus = 'active' | 'pending' | 'expired';

export interface User {
    id: string;
    name: string;
    email: string;
    roles: RoleData[];
    email_verified_at: FormattedDate | null;
    status: UserStatus;
    is_active?: boolean;
    created_at: FormattedDate;
    updated_at?: FormattedDate | null;
}

export interface UserFormData {
    name: string;
    email: string;
    password: string;
    send_invitation?: boolean;
    roles: string[];
}

export interface UserFilters extends BaseFilters {
    role?: string;
}
