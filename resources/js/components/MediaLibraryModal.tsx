import { useLayout } from '@/contexts/LayoutContext';
import { hasPermission } from '@/utils/authorization';
import { getCsrfToken } from '@/utils/csrf';
import { usePage } from '@inertiajs/react';
import { Check, Copy, File, FileText, Image as ImageIcon, Plus, Search, Upload } from 'lucide-react';
import React, { useCallback, useEffect, useState } from 'react';
import { useTranslation } from 'react-i18next';
import { toast } from 'sonner';
import { Badge } from './ui/badge';
import { Button } from './ui/button';
import { Dialog, DialogContent, DialogHeader, DialogTitle } from './ui/dialog';
import { Input } from './ui/input';

interface MediaItem {
    id: number;
    name: string;
    file_name: string;
    url: string;
    thumb_url: string;
    size: number;
    mime_type: string;
    created_at: string;
}

interface MediaLibraryModalProps {
    isOpen: boolean;
    onClose: () => void;
    onSelect: (url: string) => void;
    multiple?: boolean;
}

interface StorageSettings {
    allowed_file_types: string;
    max_file_size_mb: number;
}

export default function MediaLibraryModal({ isOpen, onClose, onSelect, multiple = false }: MediaLibraryModalProps) {
    const { t } = useTranslation();
    const { isRtl } = useLayout();
    const { auth, storageSettings, csrf_token } = usePage().props as any;
    const permissions = auth?.permissions || [];
    const canCreateMedia = hasPermission(permissions, 'create-media');
    const canManageMedia = hasPermission(permissions, 'manage-media');

    const allowedTypes = storageSettings?.allowed_file_types || 'jpg,png,webp,gif';
    const acceptAttribute = allowedTypes
        .split(',')
        .map((type: string) => `.${type.trim()}`)
        .join(',');

    const [media, setMedia] = useState<MediaItem[]>([]);
    const [filteredMedia, setFilteredMedia] = useState<MediaItem[]>([]);
    const [loading, setLoading] = useState(false);
    const [uploading, setUploading] = useState(false);
    const [selectedItems, setSelectedItems] = useState<string[]>([]);
    const [dragActive, setDragActive] = useState(false);
    const [searchTerm, setSearchTerm] = useState('');
    const [currentPage, setCurrentPage] = useState(1);
    const itemsPerPage = 18;

    const fetchMedia = useCallback(async () => {
        setLoading(true);
        try {
            const response = await fetch(route('api.media.index'), {
                credentials: 'same-origin',
                headers: {
                    Accept: 'application/json',
                    'X-Requested-With': 'XMLHttpRequest',
                },
            });

            if (!response.ok) {
                throw new Error(`HTTP error! status: ${response.status}`);
            }

            const data = await response.json();
            setMedia(data);
            setFilteredMedia(data);
        } catch (error) {
            toast.error(t('Failed to load media'));
        } finally {
            setLoading(false);
        }
    }, []);

    useEffect(() => {
        if (isOpen) {
            fetchMedia();
            setSearchTerm('');
        }
    }, [isOpen, fetchMedia]);

    // Filter media based on search term
    useEffect(() => {
        if (!searchTerm.trim()) {
            setFilteredMedia(media);
        } else {
            const filtered = media.filter(
                (item) =>
                    item.name.toLowerCase().includes(searchTerm.toLowerCase()) || item.file_name.toLowerCase().includes(searchTerm.toLowerCase()),
            );
            setFilteredMedia(filtered);
        }
        setCurrentPage(1);
    }, [searchTerm, media]);

    // Pagination calculations
    const totalPages = Math.ceil(filteredMedia.length / itemsPerPage);
    const startIndex = (currentPage - 1) * itemsPerPage;
    const currentMedia = filteredMedia.slice(startIndex, startIndex + itemsPerPage);

    const handleFileUpload = async (files: FileList) => {
        setUploading(true);

        const allowedExtensions = allowedTypes.split(',').map((type: string) => type.trim().toLowerCase());

        const validFiles = Array.from(files).filter((file) => {
            const fileExtension = file.name.split('.').pop()?.toLowerCase();
            if (!fileExtension || !allowedExtensions.includes(fileExtension)) {
                toast.error(`${file.name} - ${t('File type not allowed. Allowed types: {{types}}', { types: allowedTypes })}`);
                return false;
            }
            return true;
        });

        if (validFiles.length === 0) {
            setUploading(false);
            return;
        }

        const formData = new FormData();
        validFiles.forEach((file) => {
            formData.append('files[]', file);
        });

        try {
            // Get fresh CSRF token (prioritizes Inertia token over meta tag)
            const token = csrf_token || getCsrfToken() || '';

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

            if (response.ok) {
                if (result.data && result.data.length > 0) {
                    setMedia((prev) => [...result.data, ...prev]);
                }

                // Show appropriate success/warning messages
                if (result.errors && result.errors.length > 0) {
                    toast.warning(result.message || `${result.data?.length || 0} uploaded, ${result.errors.length} failed`);
                    result.errors.forEach((error: string) => {
                        toast.error(error, { duration: 5000 });
                    });
                } else {
                    toast.success(result.message || `${result.data?.length || 0} file(s) uploaded successfully`);
                }
            } else {
                toast.error(result.message || t('Failed to upload files'));
                if (result.errors) {
                    result.errors.forEach((error: string) => {
                        toast.error(error, { duration: 5000 });
                    });
                }
            }
        } catch (error) {
            toast.error(t('Error uploading files'));
        }

        setUploading(false);
    };

    const handleDrag = (e: React.DragEvent) => {
        e.preventDefault();
        e.stopPropagation();
        if (e.type === 'dragenter' || e.type === 'dragover') {
            setDragActive(true);
        } else if (e.type === 'dragleave') {
            setDragActive(false);
        }
    };

    const handleDrop = (e: React.DragEvent) => {
        e.preventDefault();
        e.stopPropagation();
        setDragActive(false);

        if (e.dataTransfer.files && e.dataTransfer.files[0]) {
            handleFileUpload(e.dataTransfer.files);
        }
    };

    const handleSelect = (url: string) => {
        if (multiple) {
            setSelectedItems((prev) => (prev.includes(url) ? prev.filter((item) => item !== url) : [...prev, url]));
        } else {
            onSelect(url);
            onClose();
        }
    };

    const handleConfirmSelection = () => {
        if (multiple && selectedItems.length > 0) {
            onSelect(selectedItems.join(','));
            onClose();
        }
    };

    return (
        <Dialog open={isOpen} onOpenChange={onClose}>
            <DialogContent className="max-h-[85vh] max-w-5xl overflow-hidden" dir={isRtl ? 'rtl' : 'ltr'}>
                <DialogHeader className="pb-4">
                    <DialogTitle className="flex items-center justify-center gap-2">
                        <ImageIcon className="h-5 w-5" />
                        {t('Media Library')}
                        {filteredMedia.length > 0 && (
                            <Badge variant="secondary" className={isRtl ? 'mr-2 ml-0' : 'ml-2'}>
                                {filteredMedia.length}
                            </Badge>
                        )}
                    </DialogTitle>
                </DialogHeader>

                <div className="space-y-4">
                    {/* Header: Search, Upload, and Stats in one row */}
                    <div className={`flex flex-wrap items-center gap-3 ${isRtl ? 'flex-row-reverse' : ''}`}>
                        <div className="relative min-w-0 flex-1">
                            <Search
                                className={`text-muted-foreground absolute top-1/2 h-4 w-4 -translate-y-1/2 transform ${isRtl ? 'right-3 left-auto' : 'left-3'}`}
                            />
                            <Input
                                placeholder={t('Search media files...')}
                                value={searchTerm}
                                onChange={(e) => setSearchTerm(e.target.value)}
                                className={isRtl ? 'pr-10 pl-4' : 'pl-10'}
                            />
                        </div>

                        {canCreateMedia && (
                            <div className={`flex shrink-0 gap-2 ${isRtl ? 'flex-row-reverse' : ''}`}>
                                <Input
                                    type="file"
                                    multiple
                                    accept={acceptAttribute}
                                    onChange={(e) => e.target.files && handleFileUpload(e.target.files)}
                                    className="hidden"
                                    id="file-upload"
                                />
                                <Button
                                    type="button"
                                    variant="outline"
                                    onClick={() => document.getElementById('file-upload')?.click()}
                                    disabled={uploading}
                                    size="sm"
                                >
                                    <Plus className={`h-4 w-4 ${isRtl ? 'mr-0 ml-2' : 'mr-2'}`} />
                                    {uploading ? t('Uploading...') : t('Upload')}
                                </Button>
                            </div>
                        )}
                    </div>

                    {/* Media Grid */}
                    <div className="bg-muted/10 flex flex-col rounded-lg border">
                        {loading ? (
                            <div className="flex flex-1 items-center justify-center">
                                <div className="text-center">
                                    <div className="border-primary mx-auto mb-4 h-8 w-8 animate-spin rounded-full border-b-2"></div>
                                    <p className="text-muted-foreground">{t('Loading media...')}</p>
                                </div>
                            </div>
                        ) : filteredMedia.length === 0 ? (
                            <div className="flex flex-1 items-center justify-center py-16">
                                <div className="max-w-sm text-center">
                                    <div
                                        className={`mx-auto mb-6 flex h-24 w-24 items-center justify-center rounded-xl border-2 border-dashed transition-colors ${
                                            dragActive ? 'border-primary bg-primary/5' : 'border-muted-foreground/25'
                                        }`}
                                        onDragEnter={handleDrag}
                                        onDragLeave={handleDrag}
                                        onDragOver={handleDrag}
                                        onDrop={handleDrop}
                                    >
                                        <Upload className="text-muted-foreground h-10 w-10" />
                                    </div>

                                    <div className="mb-6 space-y-3">
                                        <h3 className="text-lg font-semibold">{t('No media files found')}</h3>
                                        {searchTerm && (
                                            <p className="text-muted-foreground text-sm">
                                                {t('No results for')} <span className="text-foreground font-medium">"{searchTerm}"</span>
                                            </p>
                                        )}
                                        <p className="text-muted-foreground text-sm">
                                            {searchTerm ? t('Try a different search term or upload new images') : t('Upload images to get started')}
                                        </p>
                                    </div>

                                    {canCreateMedia && (
                                        <Button type="button" onClick={() => document.getElementById('file-upload')?.click()} disabled={uploading}>
                                            <Plus className={`h-4 w-4 ${isRtl ? 'mr-0 ml-2' : 'mr-2'}`} />
                                            {t('Upload Images')}
                                        </Button>
                                    )}
                                </div>
                            </div>
                        ) : (
                            <div className="p-4">
                                <div className="grid grid-cols-6 gap-3">
                                    {currentMedia.map((item) => (
                                        <div
                                            key={item.id}
                                            className={`group relative cursor-pointer overflow-hidden rounded-lg transition-all hover:scale-105 ${
                                                selectedItems.includes(item.url)
                                                    ? 'ring-primary shadow-lg ring-2'
                                                    : 'border-border hover:border-primary/50 border hover:shadow-md'
                                            }`}
                                            onClick={() => handleSelect(item.url)}
                                        >
                                            <div className="bg-muted relative aspect-square">
                                                {item.mime_type.startsWith('image/') ? (
                                                    <img
                                                        src={item.thumb_url}
                                                        alt={item.name}
                                                        className="h-full w-full object-cover"
                                                        onError={(e) => {
                                                            e.currentTarget.src = item.url;
                                                        }}
                                                    />
                                                ) : item.mime_type === 'application/pdf' ? (
                                                    <div className="flex h-full w-full flex-col items-center justify-center">
                                                        <div className="mb-1 flex h-8 w-8 items-center justify-center rounded-lg bg-red-100">
                                                            <FileText className="h-5 w-5 text-red-600" />
                                                        </div>
                                                        <span className="text-xs font-medium text-red-600">PDF</span>
                                                    </div>
                                                ) : item.mime_type.includes('word') || item.mime_type.includes('document') ? (
                                                    <div className="flex h-full w-full flex-col items-center justify-center">
                                                        <div className="mb-1 flex h-8 w-8 items-center justify-center rounded-lg bg-blue-100">
                                                            <FileText className="h-5 w-5 text-blue-600" />
                                                        </div>
                                                        <span className="text-xs font-medium text-blue-600">DOC</span>
                                                    </div>
                                                ) : (
                                                    <div className="flex h-full w-full flex-col items-center justify-center">
                                                        <div className="mb-1 flex h-8 w-8 items-center justify-center rounded-lg bg-gray-100">
                                                            <File className="h-5 w-5 text-gray-600" />
                                                        </div>
                                                        <span className="text-xs font-medium text-gray-600">FILE</span>
                                                    </div>
                                                )}

                                                {/* Selection Indicator */}
                                                {selectedItems.includes(item.url) && (
                                                    <div className="bg-primary/30 absolute inset-0 flex items-center justify-center">
                                                        <div className="bg-primary text-primary-foreground rounded-full p-1.5">
                                                            <Check className="h-4 w-4" />
                                                        </div>
                                                    </div>
                                                )}

                                                {/* Copy Link Button */}
                                                <button
                                                    className={`absolute top-1 rounded bg-white/90 p-1 opacity-0 transition-opacity group-hover:opacity-100 hover:bg-white ${isRtl ? 'right-auto left-1' : 'right-1'}`}
                                                    onClick={(e) => {
                                                        e.stopPropagation();
                                                        navigator.clipboard.writeText(item.url);
                                                        toast.success(t('Link copied to clipboard'));
                                                    }}
                                                    title={t('Copy link')}
                                                >
                                                    <Copy className="h-3 w-3 text-gray-600" />
                                                </button>

                                                {/* Hover Overlay */}
                                                <div className="absolute inset-0 bg-black/0 transition-colors group-hover:bg-black/20" />

                                                {/* File Name Tooltip */}
                                                <div className="absolute right-0 bottom-0 left-0 bg-gradient-to-t from-black/70 to-transparent p-2 opacity-0 transition-opacity group-hover:opacity-100">
                                                    <p className="truncate text-xs text-white" title={item.name}>
                                                        {item.name}
                                                    </p>
                                                </div>
                                            </div>
                                        </div>
                                    ))}
                                </div>
                                {/* Stats and Selection Info - under Media Grid */}
                                {filteredMedia.length > 0 && (
                                    <div
                                        className={`text-muted-foreground bg-muted/30 flex items-center justify-center rounded-md px-3 py-2 text-sm`}
                                    >
                                        <span className="whitespace-nowrap">
                                            {filteredMedia.length} {t('files')} • {t('Page')} {currentPage} {t('of')} {totalPages || 1}
                                        </span>
                                        {multiple && selectedItems.length > 0 && (
                                            <Badge variant="default" className="text-xs">
                                                {selectedItems.length} {t('selected')}
                                            </Badge>
                                        )}
                                    </div>
                                )}
                            </div>
                        )}
                    </div>

                    {/* Pagination */}
                    {totalPages > 1 && (
                        <div className={`flex items-center justify-between border-t pt-3 ${isRtl ? 'flex-row-reverse' : ''}`}>
                            <div className="text-muted-foreground text-sm">
                                {t('Showing {{start}} to {{end}} of {{total}} files', {
                                    start: startIndex + 1,
                                    end: Math.min(startIndex + itemsPerPage, filteredMedia.length),
                                    total: filteredMedia.length,
                                })}
                            </div>
                            <div className={`flex gap-1 ${isRtl ? 'flex-row-reverse' : ''}`}>
                                <Button
                                    variant="outline"
                                    size="sm"
                                    disabled={currentPage === 1}
                                    onClick={() => setCurrentPage((prev) => Math.max(prev - 1, 1))}
                                >
                                    {t('Previous')}
                                </Button>
                                {Array.from({ length: Math.min(totalPages, 5) }, (_, i) => {
                                    let page;
                                    if (totalPages <= 5) {
                                        page = i + 1;
                                    } else if (currentPage <= 3) {
                                        page = i + 1;
                                    } else if (currentPage >= totalPages - 2) {
                                        page = totalPages - 4 + i;
                                    } else {
                                        page = currentPage - 2 + i;
                                    }

                                    return (
                                        <Button
                                            key={page}
                                            variant={currentPage === page ? 'default' : 'outline'}
                                            size="sm"
                                            className="h-8 w-8 p-0"
                                            onClick={() => setCurrentPage(page)}
                                        >
                                            {page}
                                        </Button>
                                    );
                                })}
                                <Button
                                    variant="outline"
                                    size="sm"
                                    disabled={currentPage === totalPages}
                                    onClick={() => setCurrentPage((prev) => Math.min(prev + 1, totalPages))}
                                >
                                    {t('Next')}
                                </Button>
                            </div>
                        </div>
                    )}

                    {/* Actions */}
                    <div className={`flex items-center justify-between border-t pt-4 ${isRtl ? 'flex-row-reverse' : ''}`}>
                        <Button variant="outline" onClick={onClose}>
                            {t('Cancel')}
                        </Button>
                        <div className={`flex gap-2 ${isRtl ? 'flex-row-reverse' : ''}`}>
                            {multiple && selectedItems.length > 0 && (
                                <Button variant="outline" onClick={() => setSelectedItems([])} size="sm">
                                    {t('Clear')}
                                </Button>
                            )}
                            {multiple && selectedItems.length > 0 && (
                                <Button onClick={handleConfirmSelection}>
                                    {selectedItems.length === 1
                                        ? t('Select {{count}} item', { count: 1 })
                                        : t('Select {{count}} items', { count: selectedItems.length })}
                                </Button>
                            )}
                        </div>
                    </div>
                </div>
            </DialogContent>
        </Dialog>
    );
}
