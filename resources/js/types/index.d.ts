import { InertiaLinkProps } from '@inertiajs/vue3';
import type { LucideIcon } from 'lucide-vue-next';

export interface Auth {
    user: User;
    auth_permissions: AuthPermissions[]
}

export interface BreadcrumbItem {
    title: string;
    href: string | null;
}

export interface NavItem {
    title: string;
    href: NonNullable<InertiaLinkProps['href']>;
    icon?: LucideIcon;
    isActive?: boolean;
}

export type AppPageProps<
    T extends Record<string, unknown> = Record<string, unknown>,
> = T & {
    name: string;
    quote: { message: string; author: string };
    auth: Auth;
    sidebarOpen: boolean;
};

export interface User {
    id: number;
    name: string;
    email: string;
    avatar?: string;
    email_verified_at: string | null;
    created_at: string;
    updated_at: string;
}

export type AuthPermissions = string;

export type Module = {
    id: string | number
    name: string
    path?: string
    icon?: string
    permission_title?: string
    children?: Module[]
}

export type BreadcrumbItemType = BreadcrumbItem;
