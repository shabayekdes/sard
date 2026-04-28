import { LucideIcon } from 'lucide-react';

/** Tenant-defined task status (API / Inertia). */
export interface TaskStatusOption {
    id: number;
    name: string | Record<string, string>;
    color?: string;
}

/** File linked to a task (media library path, same contract as client documents). */
export interface TaskAttachmentItem {
    id: number;
    task_id: number;
    name?: string;
    file_path?: string;
    uploaded_by?: number;
    created_at?: string;
    updated_at?: string;
    media_item?: {
        id: number;
        name: string;
        url: string;
        thumb_url: string;
        mime_type: string;
    };
}

export interface Task {
    id: number;
    title: string;
    description?: string | null;
    priority: string;
    task_status_id?: number | null;
    task_status?: TaskStatusOption | null;
    /** FK id when not expanded; prefer `assigned_user` for display from API. */
    assigned_to?: number | { id: number; name: string; avatar?: string } | null;
    assigned_user?: { id: number; name: string; avatar?: string } | null;
    progress?: number;
    end_date?: string | null;
    /** Planned start (datetime). */
    start_date?: string | null;
    due_date?: string | null;
    attachments?: TaskAttachmentItem[];
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