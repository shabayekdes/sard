import MediaPicker from '@/components/MediaPicker';
import { Button } from '@/components/ui/button';
import { Input } from '@/components/ui/input';
import { Label } from '@/components/ui/label';
import { Select, SelectContent, SelectItem, SelectTrigger, SelectValue } from '@/components/ui/select';
import { getCsrfToken } from '@/utils/csrf';
import { hasPermission } from '@/utils/authorization';
import { usePage } from '@inertiajs/react';
import { FileText, Loader2, Trash2, Upload } from 'lucide-react';
import { useId, useState } from 'react';
import { useTranslation } from 'react-i18next';
import { toast } from '@/components/custom-toast';
import { cn } from '@/lib/utils';

const CASE_DOC_EXTENSIONS = new Set(['pdf', 'doc', 'docx', 'jpg', 'jpeg', 'png']);
const MAX_BYTES = 10 * 1024 * 1024;
const ACCEPT = '.pdf,.doc,.docx,.jpg,.jpeg,.png';

export type CaseFormDocumentRow = {
    document_name: string;
    document_type_id: string;
    confidentiality: string;
    file: string;
};

type Props = {
    documents: CaseFormDocumentRow[];
    onChange: (rows: CaseFormDocumentRow[]) => void;
    documentTypeOptions: { value: string; label: string }[];
    /** Optional: document errors from server (e.g. documents.0.file) */
    serverDocumentError?: string;
};

function emptyRow(): CaseFormDocumentRow {
    return {
        document_name: '',
        document_type_id: '',
        confidentiality: 'public',
        file: '',
    };
}

function basenameFromUrlOrPath(s: string): string {
    if (!s) return '';
    try {
        const u = s.startsWith('http') ? new URL(s) : null;
        const path = u ? u.pathname : s;
        const last = path.split('/').filter(Boolean).pop() || s;
        return last.split('?')[0] || s;
    } catch {
        return s;
    }
}

export function CaseDocumentsDropzone({ documents, onChange, documentTypeOptions, serverDocumentError }: Props) {
    const { t } = useTranslation();
    const inputId = useId();
    const { auth, csrf_token } = usePage().props as { auth?: { permissions?: string[] }; csrf_token?: string };
    const permissions = auth?.permissions || [];
    const canUpload = hasPermission(permissions, 'create-media');
    const [uploading, setUploading] = useState(false);
    const [dragOver, setDragOver] = useState(false);

    const validateFile = (file: File): string | null => {
        const ext = file.name.split('.').pop()?.toLowerCase() || '';
        if (!CASE_DOC_EXTENSIONS.has(ext)) {
            return t('{{name}}: file type not allowed. Use PDF, DOC, DOCX, JPG, or PNG.', { name: file.name });
        }
        if (file.size > MAX_BYTES) {
            return t('{{name}}: file too large. Maximum size is 10 MB.', { name: file.name });
        }
        return null;
    };

    const uploadFiles = async (files: FileList | File[]) => {
        const list = Array.from(files);
        const errors: string[] = [];
        const toUpload: File[] = [];
        for (const f of list) {
            const err = validateFile(f);
            if (err) errors.push(err);
            else toUpload.push(f);
        }
        errors.forEach((e) => toast.error(e));
        if (toUpload.length === 0) return;

        setUploading(true);
        const formData = new FormData();
        toUpload.forEach((file) => formData.append('files[]', file));
        const token = csrf_token || getCsrfToken() || '';

        try {
            const response = await fetch(route('api.media.batch'), {
                method: 'POST',
                body: formData,
                credentials: 'same-origin',
                headers: {
                    'X-CSRF-TOKEN': token,
                    'X-Requested-With': 'XMLHttpRequest',
                },
            });
            const result = await response.json();
            if (!response.ok) {
                toast.error(result.message || t('Failed to upload files'));
                if (Array.isArray(result.errors)) {
                    result.errors.forEach((e: string) => toast.error(e, { duration: 5000 }));
                }
                return;
            }
            const uploaded = result.data as Array<{ url: string; file_name: string; name: string }>;
            if (!uploaded?.length) return;
            const next = [...documents];
            for (const item of uploaded) {
                const name = (item.file_name || item.name || 'document').replace(/\.[^.]+$/, '');
                next.push({
                    document_name: name,
                    document_type_id: '',
                    confidentiality: 'public',
                    file: item.url,
                });
            }
            onChange(next);
            toast.success(t('{{count}} file(s) added.', { count: uploaded.length }));
        } catch {
            toast.error(t('Error uploading files'));
        } finally {
            setUploading(false);
        }
    };

    const onDrop = (e: React.DragEvent) => {
        e.preventDefault();
        e.stopPropagation();
        setDragOver(false);
        if (!canUpload || uploading) return;
        if (e.dataTransfer.files?.length) {
            void uploadFiles(e.dataTransfer.files);
        }
    };

    const updateRow = (index: number, partial: Partial<CaseFormDocumentRow>) => {
        const next = [...documents];
        next[index] = { ...next[index], ...partial };
        onChange(next);
    };

    const removeRow = (index: number) => {
        onChange(documents.filter((_, i) => i !== index));
    };

    return (
        <div className="space-y-4">
            {canUpload && (
                <div
                    className={cn(
                        'relative flex min-h-[140px] flex-col items-center justify-center rounded-lg border-2 border-dashed px-4 py-8 transition-colors',
                        dragOver ? 'border-primary bg-primary/5' : 'border-slate-300 dark:border-slate-600',
                        uploading && 'pointer-events-none opacity-70',
                    )}
                    onDragEnter={(e) => {
                        e.preventDefault();
                        e.stopPropagation();
                        setDragOver(true);
                    }}
                    onDragLeave={(e) => {
                        e.preventDefault();
                        e.stopPropagation();
                        setDragOver(false);
                    }}
                    onDragOver={(e) => {
                        e.preventDefault();
                        e.stopPropagation();
                    }}
                    onDrop={onDrop}
                >
                    {uploading && (
                        <div className="absolute inset-0 z-10 flex items-center justify-center rounded-lg bg-background/80">
                            <Loader2 className="h-8 w-8 animate-spin text-primary" />
                        </div>
                    )}
                    <Upload className="mb-2 h-10 w-10 text-slate-400" />
                    <p className="text-center text-sm font-medium text-slate-700 dark:text-slate-200">
                        {t('Drag and drop files here, or')}{' '}
                        <label htmlFor={inputId} className="cursor-pointer text-primary underline">
                            {t('browse')}
                        </label>
                    </p>
                    <p className="mt-1 text-center text-xs text-slate-500">
                        {t('PDF, DOC, DOCX, JPG, PNG — max. 10 MB per file')}
                    </p>
                    <input
                        id={inputId}
                        type="file"
                        className="sr-only"
                        accept={ACCEPT}
                        multiple
                        disabled={uploading}
                        onChange={(e) => {
                            if (e.target.files?.length) {
                                void uploadFiles(e.target.files);
                            }
                            e.target.value = '';
                        }}
                    />
                </div>
            )}

            {!canUpload && (
                <p className="text-sm text-muted-foreground">{t('You do not have permission to upload new files. Add documents from the library using each row’s file field.')}</p>
            )}

            {documents.length === 0 && (
                <p className="text-sm text-muted-foreground">{t('No documents yet. Add files above or add a row manually.')}</p>
            )}

            <div className="space-y-3">
                {documents.map((row, index) => (
                    <div
                        key={`doc_${index}`}
                        className="space-y-3 rounded-lg border border-slate-200 bg-white p-4 dark:border-gray-800 dark:bg-slate-950/20"
                    >
                        <div className="grid grid-cols-1 gap-3 md:grid-cols-2">
                            <div className="space-y-1.5">
                                <Label htmlFor={`doc_name_${index}`} required>
                                    {t('Document Name')}
                                </Label>
                                <Input
                                    id={`doc_name_${index}`}
                                    value={row.document_name}
                                    onChange={(e) => updateRow(index, { document_name: e.target.value })}
                                    required
                                />
                            </div>
                            <div className="space-y-1.5">
                                <Label required>{t('Document Type')}</Label>
                                <Select
                                    value={row.document_type_id || ''}
                                    onValueChange={(v) => updateRow(index, { document_type_id: v })}
                                >
                                    <SelectTrigger>
                                        <SelectValue placeholder={t('Select Document Type')} />
                                    </SelectTrigger>
                                    <SelectContent>
                                        {documentTypeOptions.map((opt) => (
                                            <SelectItem key={opt.value} value={String(opt.value)}>
                                                {opt.label}
                                            </SelectItem>
                                        ))}
                                    </SelectContent>
                                </Select>
                            </div>
                        </div>
                        <div className="grid grid-cols-1 gap-3 md:grid-cols-2">
                            <div className="space-y-1.5">
                                <Label required>{t('Confidentiality Level')}</Label>
                                <Select
                                    value={row.confidentiality}
                                    onValueChange={(v) => updateRow(index, { confidentiality: v })}
                                >
                                    <SelectTrigger>
                                        <SelectValue />
                                    </SelectTrigger>
                                    <SelectContent>
                                        <SelectItem value="public">{t('Public')}</SelectItem>
                                        <SelectItem value="confidential">{t('Confidential')}</SelectItem>
                                        <SelectItem value="privileged">{t('Privileged')}</SelectItem>
                                    </SelectContent>
                                </Select>
                            </div>
                            <div className="space-y-1.5">
                                <Label required>{t('File')}</Label>
                                <div className="flex flex-col gap-2 sm:flex-row sm:items-end">
                                    <div className="min-w-0 flex-1">
                                        <MediaPicker
                                            value={row.file}
                                            onChange={(url) => updateRow(index, { file: url })}
                                            placeholder={t('Select or upload a file')}
                                            showPreview
                                        />
                                    </div>
                                </div>
                                {row.file && (
                                    <a
                                        href={row.file}
                                        target="_blank"
                                        rel="noreferrer"
                                        className="inline-flex items-center gap-1 text-xs text-primary hover:underline"
                                    >
                                        <FileText className="h-3.5 w-3.5" />
                                        {basenameFromUrlOrPath(row.file)}
                                    </a>
                                )}
                            </div>
                        </div>
                        <div className="flex justify-end">
                            <Button
                                type="button"
                                variant="ghost"
                                size="sm"
                                className="text-destructive"
                                onClick={() => removeRow(index)}
                            >
                                <Trash2 className="h-4 w-4" />
                                {t('Remove')}
                            </Button>
                        </div>
                    </div>
                ))}
            </div>

            <Button type="button" variant="outline" onClick={() => onChange([...documents, emptyRow()])}>
                {t('Add document manually')}
            </Button>

            {serverDocumentError && <p className="text-sm text-destructive">{serverDocumentError}</p>}
        </div>
    );
}
