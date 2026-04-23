import React, { useCallback, useEffect, useMemo, useRef, useState } from 'react';
import { useTranslation } from 'react-i18next';
import { Button } from './ui/button';
import { Input } from './ui/input';
import { Label } from './ui/label';
import MediaLibraryModal from './MediaLibraryModal';
import { Image as ImageIcon, X, FileText, File } from 'lucide-react';
import { usePage } from '@inertiajs/react';

interface MediaPickerProps {
  label?: string;
  value?: string;
  onChange: (value: string) => void;
  multiple?: boolean;
  placeholder?: string;
  showPreview?: boolean;
  /** When `media_id`, value is comma-separated Spatie `media.id` values (library files only). */
  valueMode?: 'url' | 'media_id';
  /** e.g. files attached to a record (Hearing) so labels/previews work when not in the library list */
  supplementalMedia?: MediaIndexItem[];
}

export interface MediaIndexItem {
  id: number;
  name: string;
  file_name: string;
  url: string;
  thumb_url: string;
  mime_type: string;
}

export default function MediaPicker({
  label,
  value = '',
  onChange,
  multiple = false,
  placeholder = 'Select image...',
  showPreview = true,
  valueMode = 'url',
  supplementalMedia = [],
}: MediaPickerProps) {
  const { t } = useTranslation();
  const [isModalOpen, setIsModalOpen] = useState(false);
  const [mediaIndex, setMediaIndex] = useState<MediaIndexItem[]>([]);
  const wasModalOpen = useRef(false);
  const { storageSettings } = usePage().props as any;

  const fetchMediaIndex = useCallback(async () => {
    try {
      const response = await fetch(route('api.media.index'), {
        credentials: 'same-origin',
        headers: {
          Accept: 'application/json',
          'X-Requested-With': 'XMLHttpRequest',
        },
      });
      if (!response.ok) {
        return;
      }
      const data = await response.json();
      if (Array.isArray(data)) {
        setMediaIndex(data);
      }
    } catch {
      /* ignore */
    }
  }, []);

  useEffect(() => {
    if (valueMode !== 'media_id') {
      return;
    }
    fetchMediaIndex();
  }, [valueMode, fetchMediaIndex]);

  useEffect(() => {
    if (valueMode !== 'media_id') {
      return;
    }
    if (wasModalOpen.current && !isModalOpen) {
      fetchMediaIndex();
    }
    wasModalOpen.current = isModalOpen;
  }, [isModalOpen, valueMode, fetchMediaIndex]);

  const mergedForLookup = useMemo(() => {
    const m = new Map<string, MediaIndexItem>();
    for (const it of mediaIndex) {
      m.set(String(it.id), it);
    }
    for (const it of supplementalMedia) {
      m.set(String(it.id), it);
    }
    return Array.from(m.values());
  }, [mediaIndex, supplementalMedia]);

  const handleSelect = (selectedUrl: string) => {
    onChange(selectedUrl);
  };

  const handleClear = () => {
    onChange('');
  };

  const mediaIdList = value ? value.split(',').map((s) => s.trim()).filter(Boolean) : [];

  const imageUrls =
    valueMode === 'media_id'
      ? mediaIdList
          .map((id) => mergedForLookup.find((m) => String(m.id) === id)?.url)
          .filter((u): u is string => Boolean(u))
      : value
        ? value.split(',').filter(Boolean)
        : [];

  // Extract filename from URL for display
  const getDisplayValue = (raw: string) => {
    if (!raw) return '';
    if (valueMode === 'media_id') {
      const ids = raw.split(',').map((s) => s.trim()).filter(Boolean);
      return ids
        .map((id) => {
          const row = mergedForLookup.find((m) => String(m.id) === id);
          return row?.name || row?.file_name || id;
        })
        .join(', ');
    }
    const urls = raw.split(',').filter(Boolean);
    return urls
      .map((u) => {
        const filename = u.split('/').pop() || u;
        return filename.split('?')[0];
      })
      .join(', ');
  };

  // Get display URL for images
  const getDisplayUrl = (url: string) => {
    if (!url) return '';
    if (url.startsWith('http')) return url;
    if (url.startsWith('/')) {
      return `${window.appSettings?.imageUrl || ''}${url}`;
    }
    return `${window.appSettings?.imageUrl || ''}/${url}`;
  };

  return (
    <div className="space-y-2" dir="auto">
      {label && <Label className="block text-start">{label}</Label>}

      <div className="flex flex-row gap-2 rtl:flex-row-reverse">
        <Input
          value={getDisplayValue(value)}
          onChange={(e) => onChange(e.target.value)}
          placeholder={placeholder}
          readOnly
          className="text-start"
        />
        <Button
          type="button"
          variant="outline"
          onClick={() => setIsModalOpen(true)}
          className="rtl:flex-row-reverse"
        >
          <ImageIcon className="h-4 w-4 mr-2 rtl:ml-2 rtl:mr-0" />
          {t('Browse')}
        </Button>
        {value && (
          <Button
            type="button"
            variant="outline"
            size="icon"
            onClick={handleClear}
          >
            <X className="h-4 w-4" />
          </Button>
        )}
      </div>

      {/* Preview */}
      {showPreview && imageUrls.length > 0 && (
        <div className="grid grid-cols-4 gap-2 mt-2">
          {imageUrls.map((url, index) => {
            const isPdf = url.toLowerCase().includes('.pdf') || url.includes('application/pdf');
            const isDoc = url.toLowerCase().includes('.doc') || url.toLowerCase().includes('.docx') || url.includes('document');

            return (
              <div key={`${valueMode}-${index}-${url.slice(0, 32)}`} className="relative">
                {isPdf ? (
                  <div className="w-full h-20 bg-red-50 border border-red-200 rounded flex flex-col items-center justify-center">
                    <div className="w-8 h-8 bg-red-100 rounded flex items-center justify-center mb-1">
                      <FileText className="h-5 w-5 text-red-600" />
                    </div>
                    <span className="text-xs text-red-600 font-medium">PDF</span>
                  </div>
                ) : isDoc ? (
                  <div className="w-full h-20 bg-blue-50 border border-blue-200 rounded flex flex-col items-center justify-center">
                    <div className="w-8 h-8 bg-blue-100 rounded flex items-center justify-center mb-1">
                      <FileText className="h-5 w-5 text-blue-600" />
                    </div>
                    <span className="text-xs text-blue-600 font-medium">DOC</span>
                  </div>
                ) : (
                  <img
                    src={getDisplayUrl(url)}
                    alt={`Preview ${index + 1}`}
                    className="w-full h-20 object-cover rounded border"
                    onError={(e) => {
                      // Fallback to generic file icon if image fails to load
                      const target = e.target as HTMLImageElement;
                      target.style.display = 'none';
                      const fallback = target.nextElementSibling as HTMLElement;
                      if (fallback) fallback.style.display = 'flex';
                    }}
                  />
                )}
                {/* Fallback for failed image loads */}
                {!isPdf && !isDoc && (
                  <div className="w-full h-20 bg-gray-50 border border-gray-200 rounded flex-col items-center justify-center" style={{display: 'none'}}>
                    <div className="w-8 h-8 bg-gray-100 rounded flex items-center justify-center mb-1">
                      <File className="h-5 w-5 text-gray-600" />
                    </div>
                    <span className="text-xs text-gray-600 font-medium">FILE</span>
                  </div>
                )}
              </div>
            );
          })}
        </div>
      )}

      <MediaLibraryModal
        isOpen={isModalOpen}
        onClose={() => setIsModalOpen(false)}
        onSelect={handleSelect}
        multiple={multiple}
        valueMode={valueMode}
      />
    </div>
  );
}
