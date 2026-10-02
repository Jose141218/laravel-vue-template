export interface PaginationLink {
    url: string | null;
    label: string;
    active: boolean;
}

export interface Links {
    first?: string | null;
    last?: string | null;
    prev?: string | null;
    next?: string | null;
}

export interface Meta {
    current_page: number;
    from?: number | null;
    last_page: number;
    path: string;
    per_page: number;
    to?: number | null;
    total: number;
    links?: PaginationLink[];
}

export interface ResourceCollection<T = any> {
    data: T[];
    links: Links;
    meta: Meta;
}

export type Collection<T = any> = ResourceCollection<T>;
export type PaginatedResource<T = any> = ResourceCollection<T>;
export type PaginationMeta = Meta;

export interface LengthAwarePaginator<T = any> {
    data: T[];
    current_page: number;
    first_page_url: string;
    from: number | null;
    last_page: number;
    last_page_url: string;
    links: PaginationLink[];
    next_page_url: string | null;
    path: string;
    per_page: number;
    prev_page_url: string | null;
    to: number | null;
    total: number;
}

export interface PaginationProps {
    links?: PaginationLink[];
    from?: number | null;
    to?: number | null;
    total?: number;
}
