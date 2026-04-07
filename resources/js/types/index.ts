import { LucideIcon } from 'lucide-react';

/** Tenant-defined task status (API / Inertia). */
export interface TaskStatusOption {
    id: number;
    name: string | Record<string, string>;
    color?: string;
}

export interface Task {
    id: number;
    title: string;
    description?: string | null;
    priority: string;
    task_status_id?: number | null;
    task_status?: TaskStatusOption | null;
    assigned_to?: { id: number; name: string; avatar?: string } | null;
    progress?: number;
    end_date?: string | null;
    due_date?: string | null;
    [key: string]: unknown;
}

export interface User {
    id: number;
    name: string;
    email?: string;
    avatar?: string;
}

export interface PaginatedData<T> {
    data: T[];
    current_page?: number;
    from?: number;
    to?: number;
    total?: number;
    per_page?: number;
    links?: { url: string | null; label: string; active: boolean }[];
}

export interface Project {
    id: number;
    milestones?: unknown[];
    [key: string]: unknown;
}

export interface ProjectMilestone {
    id: number;
    title: string;
    [key: string]: unknown;
}

export interface SharedData {
    auth: {
        user: {
            id: number;
            name: string;
            email: string;
        } | null;
    };
}

export interface NavItem {
    title: string;
    type?: 'item' | 'label';
    href?: string;
    icon?: LucideIcon;
    permission?: string;
    children?: NavItem[];
    target?: string;
    external?: boolean;
    defaultOpen?: boolean;
    badge?: {
        label: string;
        variant?: 'default' | 'destructive' | 'outline' | 'secondary' | 'ghost';
    };
}

export interface BreadcrumbItem {
    title: string;
    href?: string;
}

export interface PageAction {
    label: string;
    icon: React.ReactNode;
    variant: 'default' | 'destructive' | 'outline' | 'secondary' | 'ghost' | 'link';
    onClick: () => void;
}