import { useCallback, useMemo, useState } from 'react';
import { router } from '@inertiajs/react';
import { useTranslation } from 'react-i18next';
import { Button } from '@/components/ui/button';
import { toast } from '@/components/custom-toast';
import { hasPermission } from '@/utils/authorization';
import MediaLibraryModal from '@/components/MediaLibraryModal';
import { Download, File, FileText, Image as ImageIcon, Paperclip, Plus, Trash2 } from 'lucide-react';

export type HearingAttachmentRow = {
  id: number;
  name: string;
  file_name: string;
  url: string;
  thumb_url: string;
  size: number;
  mime_type: string | null;
};

type Props = {
  hearingId: number;
  /** Current ordered Spatie `media.id` list from the server */
  attachmentIds: number[];
  mediaRows: HearingAttachmentRow[];
  canEdit: boolean;
  onAfterChange?: () => void;
  permissions: string[];
};

function formatFileSize(bytes: number): string {
  if (bytes < 1024) {
    return `${bytes} B`;
  }
  if (bytes < 1024 * 1024) {
    return `${(bytes / 1024).toFixed(1)} KB`;
  }
  return `${(bytes / (1024 * 1024)).toFixed(1)} MB`;
}

function fileIcon(mime: string | null) {
  if (mime?.startsWith('image/')) {
    return <ImageIcon className="h-4 w-4 shrink-0 text-sky-600" aria-hidden />;
  }
  if (mime === 'application/pdf' || (mime && mime.includes('pdf'))) {
    return <FileText className="h-4 w-4 shrink-0 text-red-600" aria-hidden />;
  }
  return <File className="h-4 w-4 shrink-0 text-gray-500" aria-hidden />;
}

export function HearingAttachmentsSection({
  hearingId,
  attachmentIds: initialIds,
  mediaRows,
  canEdit,
  onAfterChange,
  permissions,
}: Props) {
  const { t } = useTranslation();
  const [mediaModalOpen, setMediaModalOpen] = useState(false);
  const [saving, setSaving] = useState(false);
  const canDownload = hasPermission(permissions, 'download-media');

  const orderedIds = useMemo(() => {
    const fromRows = mediaRows.map((m) => m.id);
    if (fromRows.length) {
      return fromRows;
    }
    return initialIds.filter((id) => Number.isFinite(id) && id > 0);
  }, [initialIds, mediaRows]);

  const persist = useCallback(
    (ids: number[]) => {
      const unique = [...new Set(ids.filter((n) => n > 0))];
      setSaving(true);
      router.put(
        route('hearings.attachments.update', hearingId),
        { attachments: unique.length > 0 ? unique : null },
        {
          preserveScroll: true,
          onFinish: () => setSaving(false),
          onSuccess: (page) => {
            toast.dismiss();
            const flash = (page.props as { flash?: { success?: string; error?: string } }).flash;
            if (flash?.error) {
              toast.error(flash.error);
              return;
            }
            if (flash?.success) {
              toast.success(flash.success);
            }
            onAfterChange?.();
          },
          onError: () => {
            toast.dismiss();
            toast.error(t('Failed to save'));
          },
        },
      );
    },
    [hearingId, onAfterChange, t],
  );

  const mergeNewIds = (csv: string) => {
    if (!canEdit) return;
    const incoming = csv
      .split(',')
      .map((s) => s.trim())
      .filter(Boolean)
      .map((s) => parseInt(s, 10))
      .filter((n) => !Number.isNaN(n) && n > 0);
    if (incoming.length === 0) return;
    const seen = new Set(orderedIds);
    const next = [...orderedIds];
    for (const id of incoming) {
      if (!seen.has(id)) {
        seen.add(id);
        next.push(id);
      }
    }
    if (next.length === orderedIds.length) return;
    persist(next);
    setMediaModalOpen(false);
  };

  const remove = (id: number) => {
    if (!canEdit) return;
    const next = orderedIds.filter((x) => x !== id);
    if (next.length === orderedIds.length) return;
    if (!window.confirm(t('Are you sure you want to remove this file?'))) return;
    persist(next);
  };

  return (
    <div className="rounded-xl bg-white p-6 dark:bg-gray-950">
      <div
        className="mb-4 flex min-w-0 flex-col gap-3 pb-4 xl:flex-row xl:items-center xl:justify-between xl:gap-3"
        dir="ltr"
      >
        <div
          className="order-1 flex min-w-0 flex-wrap items-center gap-x-2 gap-y-1 sm:gap-x-3 xl:order-2"
          dir="auto"
        >
          <Paperclip className="h-5 w-5 shrink-0 text-gray-700 dark:text-gray-300" aria-hidden />
          <h2 className="shrink-0 text-lg font-bold text-gray-900 dark:text-white">{t('Attachments')}</h2>
          <span className="inline-flex h-6 min-w-6 items-center justify-center rounded-full bg-gray-200 px-2 text-xs font-semibold text-gray-800 dark:bg-gray-700 dark:text-gray-100">
            {mediaRows.length}
          </span>
        </div>
        {canEdit ? (
          <div className="order-2 w-full shrink-0 sm:w-auto xl:order-1" dir="auto">
            <Button
              type="button"
              variant="outline"
              size="sm"
              disabled={saving}
              className="inline-flex w-full shrink-0 items-center gap-1.5 self-start sm:w-auto"
              onClick={() => setMediaModalOpen(true)}
            >
              <Plus className="h-4 w-4 shrink-0" aria-hidden />
              {t('Add file')}
            </Button>
          </div>
        ) : null}
      </div>

      {canEdit ? (
        <MediaLibraryModal
          isOpen={mediaModalOpen}
          onClose={() => setMediaModalOpen(false)}
          onSelect={mergeNewIds}
          multiple
          valueMode="media_id"
        />
      ) : null}

      <div className="overflow-x-auto" dir="auto">
        <table className="w-full min-w-[480px] table-fixed border-collapse text-sm">
          <colgroup>
            <col className="w-12" />
            <col />
            <col className="w-28" />
            <col className="w-32" />
          </colgroup>
          <thead>
            <tr className="text-start text-xs font-medium text-gray-500 dark:text-gray-400">
              <th className="w-12 px-3 py-3 font-medium">#</th>
              <th className="px-3 py-3 font-medium">{t('File Name')}</th>
              <th className="w-28 px-3 py-3 font-medium">{t('File Size')}</th>
              <th className="w-32 px-3 py-3 font-medium">{t('Actions')}</th>
            </tr>
          </thead>
          <tbody>
            {mediaRows.length === 0 ? (
              <tr>
                <td colSpan={4} className="px-3 py-8 text-center text-gray-500 dark:text-gray-400">
                  {t('No attachments yet')}
                </td>
              </tr>
            ) : (
              mediaRows.map((row, index) => (
                <tr
                  key={row.id}
                >
                  <td className="w-12 px-3 py-3 align-top text-gray-600 dark:text-gray-300">{index + 1}</td>
                  <td className="min-w-0 px-3 py-3 align-top">
                    <div className="flex min-w-0 items-start gap-2">
                      {fileIcon(row.mime_type)}
                      <div className="min-w-0 break-words font-medium text-gray-900 dark:text-white" title={row.name}>
                        {row.name || row.file_name}
                      </div>
                    </div>
                  </td>
                  <td className="w-28 whitespace-nowrap px-3 py-3 align-top text-gray-800 tabular-nums dark:text-gray-200">
                    {formatFileSize(row.size || 0)}
                  </td>
                  <td className="w-32 px-3 py-3 align-top">
                    <div className="flex w-full min-w-0 items-center justify-start gap-1.5">
                      {canDownload ? (
                        <a
                          href={route('api.media.download', row.id)}
                          className="rounded p-1.5 text-gray-500 transition-colors hover:bg-gray-100 hover:text-gray-800 dark:hover:bg-gray-800 dark:hover:text-white"
                          aria-label={t('Download')}
                        >
                          <Download className="h-4 w-4" />
                        </a>
                      ) : null}
                      {canEdit ? (
                        <button
                          type="button"
                          className="rounded p-1.5 text-gray-500 transition-colors hover:bg-red-50 hover:text-red-600 dark:hover:bg-red-950/40 dark:hover:text-red-400"
                          aria-label={t('Delete')}
                          disabled={saving}
                          onClick={() => remove(row.id)}
                        >
                          <Trash2 className="h-4 w-4" />
                        </button>
                      ) : null}
                      {!canDownload && !canEdit ? <span className="text-gray-400">—</span> : null}
                    </div>
                  </td>
                </tr>
              ))
            )}
          </tbody>
        </table>
      </div>
    </div>
  );
}
