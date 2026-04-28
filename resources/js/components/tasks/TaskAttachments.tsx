import React, { useState } from 'react';
import { router } from '@inertiajs/react';
import { Button } from '@/components/ui/button';
import { DropdownMenu, DropdownMenuContent, DropdownMenuItem, DropdownMenuTrigger } from '@/components/ui/dropdown-menu';
import { Download, MoreHorizontal, Trash2, File, Image, FileText } from 'lucide-react';
import { Task, TaskAttachmentItem } from '@/types';
import MediaPicker from '@/components/MediaPicker';
import { CrudDeleteModal } from '@/components/CrudDeleteModal';
import { toast } from 'sonner';
import { useTranslation } from 'react-i18next';

interface Props {
    task: Task;
    attachments: TaskAttachmentItem[];
    onUpdate?: () => void;
}

export default function TaskAttachments({ task, attachments, onUpdate }: Props) {
    const { t } = useTranslation();
    const [selectedMedia, setSelectedMedia] = useState('');
    const [isDeleteModalOpen, setIsDeleteModalOpen] = useState(false);
    const [attachmentToDelete, setAttachmentToDelete] = useState<TaskAttachmentItem | null>(null);

    const handleMediaSelect = (url: string) => {
        setSelectedMedia(url);
        const parts = url
            .split(',')
            .map((u) => u.trim())
            .filter(Boolean);
        if (parts.length === 0) {
            return;
        }
        router.post(
            route('task-attachments.store', task.id),
            { files: parts },
            {
                preserveScroll: true,
                onSuccess: () => {
                    onUpdate?.();
                    setSelectedMedia('');
                },
                onError: (errors) => {
                    const msg =
                        typeof errors === 'string'
                            ? errors
                            : Object.values(errors as Record<string, string>)
                                  .filter(Boolean)
                                  .join(', ');
                    toast.error(msg || t("Failed to add files"));
                },
            },
        );
    };

    const handleDownload = (attachmentId: number) => {
        window.open(route('task-attachments.download', attachmentId), '_blank');
    };

    const handleDelete = (attachment: TaskAttachmentItem) => {
        setAttachmentToDelete(attachment);
        setIsDeleteModalOpen(true);
    };

    const handleDeleteConfirm = () => {
        if (attachmentToDelete) {
            router.delete(route('task-attachments.destroy', attachmentToDelete.id), {
                onSuccess: () => {
                    setIsDeleteModalOpen(false);
                    setAttachmentToDelete(null);
                    onUpdate?.();
                },
                onError: () => {
                    setIsDeleteModalOpen(false);
                    setAttachmentToDelete(null);
                }
            });
        }
    };

    const getFileIcon = (mimeType: string) => {
        if (mimeType.startsWith('image/')) return Image;
        if (mimeType.includes('pdf') || mimeType.includes('document')) return FileText;
        return File;
    };

    return (
        <div className="space-y-4">
            {/* Media Display Grid */}
            {attachments.length > 0 && (
                <div className="grid grid-cols-2 md:grid-cols-3 lg:grid-cols-4 gap-4">
                    {attachments.map((attachment) => {
                        const FileIcon = getFileIcon(attachment.media_item?.mime_type || '');
                        const isImage = attachment.media_item?.mime_type?.startsWith('image/');
                        
                        return (
                            <div key={attachment.id} className="relative group border rounded-lg overflow-hidden hover:shadow-md transition-shadow">
                                {/* Media Preview */}
                                <div className="aspect-square bg-gray-50 flex items-center justify-center">
                                    {isImage && attachment.media_item?.thumb_url ? (
                                        <img
                                            src={attachment.media_item.thumb_url}
                                            alt={attachment.media_item?.name || t('Attachment')}
                                            className="w-full h-full object-cover cursor-pointer"
                                            onClick={() => window.open(attachment.media_item?.url || attachment.media_item.thumb_url, '_blank')}
                                            onError={(e) => {
                                                if (attachment.media_item?.url) {
                                                    e.currentTarget.src = attachment.media_item.url;
                                                }
                                            }}
                                        />
                                    ) : (
                                        <FileIcon className="h-12 w-12 text-gray-400" />
                                    )}
                                </div>
                                
                                {/* Actions */}
                                <div className="absolute top-2 right-2">
                                    <DropdownMenu>
                                        <DropdownMenuTrigger asChild>
                                            <Button variant="secondary" size="sm" className="h-8 w-8 p-0">
                                                <MoreHorizontal className="h-4 w-4" />
                                            </Button>
                                        </DropdownMenuTrigger>
                                        <DropdownMenuContent align="end" className="z-[9999]">
                                            <DropdownMenuItem onClick={() => handleDownload(attachment.id)}>
                                                <Download className="h-4 w-4 mr-2" />
                                                {t('Download')}
                                            </DropdownMenuItem>

                                            <DropdownMenuItem
                                                onClick={() => handleDelete(attachment)}
                                                className="text-red-600"
                                            >
                                                <Trash2 className="h-4 w-4 mr-2" />
                                                {t('Remove')}
                                            </DropdownMenuItem>
                                        </DropdownMenuContent>
                                    </DropdownMenu>
                                </div>
                            </div>
                        );
                    })}
                </div>
            )}



            {/* Media Picker with Portal for Modal */}
            <div>
                <MediaPicker
                    label={t('Add Media')}
                    value={selectedMedia}
                    onChange={handleMediaSelect}
                    placeholder={t('Select media...')}
                    showPreview={true}
                    multiple
                />
            </div>

            {/* Delete Modal */}
            <CrudDeleteModal
                isOpen={isDeleteModalOpen}
                onClose={() => {
                    setIsDeleteModalOpen(false);
                    setAttachmentToDelete(null);
                }}
                onConfirm={handleDeleteConfirm}
                itemName={attachmentToDelete?.name || attachmentToDelete?.media_item?.name || t('Attachment')}
                entityName="Attachment"
            />
        </div>
    );
}