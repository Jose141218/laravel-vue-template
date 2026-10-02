import type { InertiaLinkProps } from '@inertiajs/vue3';
import type { Component } from 'vue';

export interface BreadcrumbItem {
    title: string;
    href: NonNullable<InertiaLinkProps['href']>;
}

export interface NavMenuItem {
    title?: string;
    label?: string;
    href?: NonNullable<InertiaLinkProps['href']>;
    route?: string;
    icon?: Component;
    permission?: string;
    isActive?: boolean;
    menu?: NavMenuItem[];
}

export interface NavItem extends NavMenuItem {
    title: string;
    href: NonNullable<InertiaLinkProps['href']>;
}
