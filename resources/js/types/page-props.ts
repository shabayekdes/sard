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

    /** True when on central domain (e.g. sard.com); false when on tenant subdomain (e.g. acme.sard.com) */
    isCentralDomain: boolean;
    /** Current host when on tenant subdomain; null on central */
    tenantDomain: string | null;
    /** Current tenant id when in tenant context; null on central */
    tenantId: string | null;

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
