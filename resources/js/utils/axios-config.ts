import axios from 'axios';

// Reload on 419 for native fetch() calls (used by many components)
const originalFetch = typeof window !== 'undefined' ? window.fetch : undefined;
if (originalFetch) {
    (window as Window & { fetch: typeof fetch }).fetch = function (...args: Parameters<typeof fetch>) {
        return originalFetch.apply(this, args).then((response) => {
            if (response.status === 419) {
                window.location.reload();
            }
            return response;
        });
    };
}

// Set CSRF token for all requests
axios.defaults.headers.common['X-Requested-With'] = 'XMLHttpRequest';

// Intercept requests to add fresh CSRF token
axios.interceptors.request.use((config) => {
  let csrfToken = null;
  
  // Try to get CSRF token from meta tag first (most reliable)
  const metaToken = document.head.querySelector('meta[name="csrf-token"]');
  if (metaToken) {
    csrfToken = (metaToken as HTMLMetaElement).content;
  }
  
  // Override with Inertia token if available (fresher after login)
  try {
    if (typeof window !== 'undefined' && (window as any).page?.props?.csrf_token) {
      csrfToken = (window as any).page.props.csrf_token;
    }
  } catch (e) {
    // Ignore errors accessing window.page
  }
  
  if (csrfToken) {
    config.headers['X-CSRF-TOKEN'] = csrfToken;
  }
  
  return config;
});

// Reload the page when we get 419 Page Expired (CSRF token expired)
axios.interceptors.response.use(
  (response) => response,
  (error) => {
    if (error.response?.status === 419) {
      window.location.reload();
      return Promise.reject(error);
    }
    return Promise.reject(error);
  }
);

export default axios;