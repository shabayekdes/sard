/**
 * Wrap the global route() so it always returns a same-origin URL.
 * Fixes CORS when Ziggy has a different base (e.g. central domain) but the user is on a tenant (e.g. dev.sard.local).
 *
 * Ziggy shape (from @routes or ziggy:generate) is: { url, port, routes: { name: { uri, methods, domain } } }.
 * The `url` (e.g. 'https://ziggy.test') is used to build absolute URLs. We don't rely on patching Ziggy.url
 * because it can be wrong or cached; instead we rewrite any cross-origin route() result to current origin.
 */
export function installSameOriginRoute(): void {
    if (typeof window === 'undefined') return;
    const original = (globalThis as any).route;
    if (typeof original !== 'function') return;

    (globalThis as any).route = function (...args: unknown[]) {
        const url = original.apply(this, args);
        if (typeof url !== 'string' || !url.startsWith('http')) return url;
        try {
            const parsed = new URL(url);
            if (parsed.origin !== window.location.origin) {
                return window.location.origin + parsed.pathname + parsed.search;
            }
        } catch {
            // ignore
        }
        return url;
    };
}
