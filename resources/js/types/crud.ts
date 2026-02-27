// types/crud.ts
export interface EntityConfig {
    name: string;
    endpoint: string;
    permissions: {
        view: string;
        create: string;
        edit: string;
        delete: string;
    };
    breadcrumbs: {
        title: string;
        href?: string;
    }[];
}

export interface TableColumn {
    key: string;
    label: string;
    sortable?: boolean;
    type?: 'text' | 'badge' | 'image' | 'date' | 'currency' | 'boolean' | 'link' | 'custom' | 'switch';
    className?: string;
    linkClassName?: string;
    href?: string | ((row: any) => string);
    openInNewTab?: boolean;
    /** For type 'switch': action name to pass to onAction when toggled (default 'toggle-status'). */
    switchAction?: string;
    /** For type 'switch': permission required to enable the switch (defaults to entityPermissions.edit when present). */
    switchPermission?: string;
    render?: (value: any, row: any) => React.ReactNode;
}

export interface TableAction {
    label: string | ((row: any) => string);
    icon: string | ((row: any) => string);
    action?: string;
    href?: string | ((row: any) => string);
    openInNewTab?: boolean;
    permission?: string;
    className?: string;
    requiredPermission?: string;
    condition?: (row: any) => boolean;
}

export interface TableConfig {
    columns: TableColumn[];
    actions: TableAction[];
    statusColors?: Record<string, string>;
}

export interface FilterOption {
    value: string;
    label: string;
}

export interface FilterField {
    key: string;
    label: string;
    type: 'select' | 'date' | 'daterange' | 'text' | 'number' | 'boolean';
    options?: FilterOption[];
    relation?: {
        endpoint: string;
        valueField: string;
        labelField: string;
    };
}

export interface FileValidation {
    accept?: string;
    maxSize?: number; // in bytes
    mimeTypes?: string[]; // e.g. ['image/jpeg', 'image/png']
    extensions?: string[]; // e.g. ['.jpg', '.png']
}

export interface FormField {
    name: string;
    label: string;
    type:
    | 'text'
    | 'time'
    | 'email'
    | 'password'
    | 'select'
    | 'textarea'
    | 'radio'
    | 'checkbox'
    | 'switch'
    | 'file'
    | 'date'
    | 'number'
    | 'multi-select'
    | 'media-picker'
    | 'custom'
    | 'dependent-dropdown';
    placeholder?: string;
    required?: boolean;
    multiple?: boolean; // For media-picker and multi-select fields
    disabled?: boolean; // For disabling fields
    step?: string | number; // For number input step
    min?: string | number; // For number input min
    max?: string | number; // For number input max
    options?: FilterOption[];
    relation?: {
        endpoint: string;
        valueField: string;
        labelField: string;
    };
    validation?: {
        pattern?: string;
        min?: number;
        max?: number;
        minLength?: number;
        maxLength?: number;
    };
    fileValidation?: FileValidation;
    column?: number;
    description?: string;
    colSpan?: number; // Number of columns this field should span (1-12)
    width?: string; // CSS width value (e.g., '50%', '200px')
    row?: number; // Optional row number for grouping fields
    render?: (field: FormField, formData: any, onChange: (name: string, value: any) => void) => React.ReactNode;
    conditional?: (mode: string, formData: any) => boolean;
    dependentConfig?: Array<{
        name: string;
        label: string;
        options?: { value: string | number; label: string }[];
        dependencies?: Record<string, { value: string | number; label: string }[]>;
        apiEndpoint?: string;
    }>;
    onDependentChange?: (fieldName: string, value: string, formData: Record<string, any>) => void;
}

export interface FormConfig {
    fields: FormField[];
    modalSize?: string;
    columns?: number; // Number of columns in the form grid (default: 1)
    layout?: 'grid' | 'flex' | 'default'; // Layout type
    column?: number;
    transformData?: (data: any) => any;
}

export interface CrudHooks {
    afterCreate?: (data: any, response: any) => void;
    afterUpdate?: (data: any, response: any) => void;
    afterDelete?: (id: any) => void;
}

export interface CrudConfig {
    entity: EntityConfig;
    table: TableConfig;
    filters: FilterField[];
    form: FormConfig;
    hooks?: CrudHooks;
    modalSize?: 'sm' | 'md' | 'lg' | 'xl' | '2xl' | '3xl' | '4xl' | '5xl' | 'full';
    description?: string; // Description for accessibility in dialogs
}
