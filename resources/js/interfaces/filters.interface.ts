export interface SortOption {
    label: string;
    value: string;
}

export interface BaseFilters {
    search?: string;
    rows?: number;
    order?: string;
    direction?: 'asc' | 'desc';
    [key: string]: string | number | boolean | null | undefined;
    // [key: string]: unknown;
}
