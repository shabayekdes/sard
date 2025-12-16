export type AuthProps = {
    user?: any; // you can replace with a real User type later
    roles?: string[] | (() => string[]); // inertia can serialize closures as arrays; keep flexible
    permissions?: string[] | (() => string[]);
};

export type FlashProps = {
    success?: string | null;
    error?: string | null;
};

export type PageProps = {
    name: string;
    base_url: string;
    image_url: string;
    csrf_token: string;

    auth: AuthProps;
    flash: FlashProps;

    globalSettings: Record<string, any>;
    storageSettings: Record<string, any>;
    is_demo: boolean;

    ziggy: any;
    quote?: { message: string; author: string };

    // Inertia adds these sometimes depending on setup
    errors?: Record<string, string>;
};
