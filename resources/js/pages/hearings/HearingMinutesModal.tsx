import { useEffect, useState } from 'react';
import { router } from '@inertiajs/react';
import { useTranslation } from 'react-i18next';
import {
  Dialog,
  DialogContent,
  DialogDescription,
  DialogFooter,
  DialogHeader,
  DialogTitle,
} from '@/components/ui/dialog';
import { Button } from '@/components/ui/button';
import { Input } from '@/components/ui/input';
import { Label } from '@/components/ui/label';
import { RichTextField } from '@/components/ui/rich-text-field';
import { toast } from '@/components/custom-toast';
import { FileSpreadsheet } from 'lucide-react';

type HearingRef = {
  id: number;
  /** Main session/hearing title (للعرض تحت عنوان المودال) */
  session_title?: string | null;
  minutes_title?: string | null;
  minutes_date?: string | null;
  minutes_content?: string | null;
};

type Props = {
  open: boolean;
  onOpenChange: (open: boolean) => void;
  hearing: HearingRef | null;
  /** e.g. reload show page so minutes display updates */
  onAfterSave?: () => void;
};

export function HearingMinutesModal({ open, onOpenChange, hearing, onAfterSave }: Props) {
  const { t } = useTranslation();
  const [title, setTitle] = useState('');
  const [date, setDate] = useState('');
  const [content, setContent] = useState('');
  const [saving, setSaving] = useState(false);

  useEffect(() => {
    if (!open || !hearing) {
      return;
    }
    setTitle((hearing.minutes_title as string) || '');
    setDate(
      hearing.minutes_date
        ? String(hearing.minutes_date).split('T')[0] || ''
        : '',
    );
    setContent((hearing.minutes_content as string) || '');
  }, [open, hearing]);

  const handleSubmit = (e: React.FormEvent) => {
    e.preventDefault();
    if (!hearing?.id) {
      return;
    }
    setSaving(true);
    router.put(
      route('hearings.minutes.update', hearing.id),
      {
        minutes_title: title.trim() || null,
        minutes_date: date.trim() || null,
        minutes_content: content.trim() ? content : null,
      },
      {
        preserveScroll: true,
        onFinish: () => setSaving(false),
        onSuccess: (page) => {
          onOpenChange(false);
          toast.dismiss();
          const flash = (page.props as { flash?: { success?: string; error?: string } }).flash;
          if (flash?.success) {
            toast.success(flash.success);
          }
          onAfterSave?.();
        },
        onError: () => {
          toast.dismiss();
          toast.error(t('Failed to save'));
        },
      },
    );
  };

  return (
    <Dialog open={open} onOpenChange={onOpenChange}>
      <DialogContent
        className="max-h-[90vh] max-w-3xl overflow-y-auto sm:max-w-3xl"
        closeButtonClassName="right-auto left-4"
      >
        <form onSubmit={handleSubmit}>
          <DialogHeader>
            <DialogTitle className="flex items-center gap-2 text-start">
              <FileSpreadsheet className="h-5 w-5 shrink-0 text-green-600" aria-hidden />
              {t('Hearing minutes')}
            </DialogTitle>
            {hearing?.session_title ? (
              <DialogDescription className="text-start text-base font-medium text-foreground">
                {hearing.session_title}
              </DialogDescription>
            ) : null}
          </DialogHeader>

          <div className="mt-4 space-y-4">
            <div className="space-y-2">
              <Label className="text-start">{t('Minutes title')}</Label>
              <Input
                className="h-10"
                value={title}
                onChange={(e) => setTitle(e.target.value)}
                maxLength={500}
                placeholder={t('Minutes title')}
              />
            </div>

            <div className="space-y-2">
              <Label className="text-start">{t('Minutes date')}</Label>
              <Input
                type="date"
                className="h-10"
                value={date}
                onChange={(e) => setDate(e.target.value)}
              />
            </div>

            <RichTextField
              label={t('Minutes content')}
              name="minutes_content"
              value={content}
              onChange={setContent}
              placeholder={t('Minutes content')}
            />
          </div>

          <DialogFooter className="mt-6 gap-2 sm:justify-end">
            <Button type="button" variant="outline" onClick={() => onOpenChange(false)} disabled={saving}>
              {t('Cancel')}
            </Button>
            <Button type="submit" disabled={saving || !hearing?.id}>
              {t('Save')}
            </Button>
          </DialogFooter>
        </form>
      </DialogContent>
    </Dialog>
  );
}
