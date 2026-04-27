/**
 * Length of visible text in HTML (for rich-text character limits). Ignores tags.
 */
export function htmlPlainTextLength(html: string): number {
    if (typeof document === 'undefined') {
        return (html || '')
            .replace(/<[^>]*>/g, ' ')
            .replace(/\s+/g, ' ')
            .trim().length;
    }
    const d = document.createElement('div');
    d.innerHTML = html;
    return (d.textContent || d.innerText || '').length;
}
