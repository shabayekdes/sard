import { useEffect, useMemo, useState } from 'react';
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
import { Mail } from 'lucide-react';
import { gregorianYmdToHijriYmd, normalizeGregorianYmd } from '@/utils/hijriGregorian';

type HearingForClientSummary = {
  id: number;
  title?: string | null;
  hearing_date?: string | null;
  case?: {
    client?: {
      email?: string | null;
    } | null;
  } | null;
};

type Props = {
  open: boolean;
  onOpenChange: (open: boolean) => void;
  hearing: HearingForClientSummary | null;
  onAfterSend?: () => void;
};

export function HearingSendClientSummaryModal({ open, onOpenChange, hearing, onAfterSend }: Props) {
  const { t } = useTranslation();
  const [clientEmail, setClientEmail] = useState('');
  const [summaryHtml, setSummaryHtml] = useState('');
  const [sending, setSending] = useState(false);
  const [fieldErrors, setFieldErrors] = useState<Record<string, string>>({});

  useEffect(() => {
    if (!open || !hearing) {
      return;
    }
    setClientEmail((hearing.case?.client?.email as string) || '');
    setSummaryHtml('');
    setFieldErrors({});
  }, [open, hearing]);

  const dateReadOnly = useMemo(() => {
    const gYmd = normalizeGregorianYmd(hearing?.hearing_date ?? '');
    const hijri = gYmd ? gregorianYmdToHijriYmd(gYmd) : '';
    const gregorian =
      gYmd && typeof window !== 'undefined' && window.appSettings?.formatDate
        ? window.appSettings.formatDate(gYmd)
        : gYmd || '—';
    if (!hijri) {
      return gregorian;
    }
    return `${hijri} — ${gregorian}`;
  }, [hearing?.hearing_date]);

  const handleSubmit = (e: React.FormEvent) => {
    e.preventDefault();
    if (!hearing?.id) {
      return;
    }
    setSending(true);
    setFieldErrors({});
    router.post(
      route('hearings.send-client-summary', hearing.id),
      {
        client_email: clientEmail.trim(),
        summary_html: summaryHtml.trim() ? summaryHtml : null,
      },
      {
        preserveScroll: true,
        onFinish: () => setSending(false),
        onSuccess: (page) => {
          onOpenChange(false);
          toast.dismiss();
          const flash = (page.props as { flash?: { success?: string; error?: string } }).flash;
          if (flash?.success) {
            toast.success(flash.success);
          }
          onAfterSend?.();
        },
        onError: (errors) => {
          toast.dismiss();
          const flat: Record<string, string> = {};
          Object.entries(errors).forEach(([k, v]) => {
            flat[k] = Array.isArray(v) ? v[0] : String(v);
          });
          setFieldErrors(flat);
          const first = Object.values(flat)[0];
          if (first) {
            toast.error(first);
          } else {
            toast.error(t('Could not send email. Please check your mail settings.'));
          }
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
              <Mail className="h-5 w-5 shrink-0 text-green-600" aria-hidden />
              {t('Send to client')}
            </DialogTitle>
            {hearing?.title ? (
              <DialogDescription className="text-start text-base font-medium text-foreground">
                {hearing.title}
              </DialogDescription>
            ) : null}
          </DialogHeader>

          <div className="mt-4 space-y-4">
            <div className="space-y-2">
              <Label className="text-start">{t('Client email')}</Label>
              <Input
                type="email"
                className="h-10"
                value={clientEmail}
                onChange={(e) => setClientEmail(e.target.value)}
                autoComplete="email"
                required
              />
              {fieldErrors.client_email ? (
                <p className="text-sm text-destructive">{fieldErrors.client_email}</p>
              ) : null}
            </div>

            <div className="space-y-2">
              <Label className="text-start">{t('Title')}</Label>
              <Input className="h-10 bg-muted" value={hearing?.title || ''} readOnly tabIndex={-1} />
            </div>

            <div className="space-y-2">
              <Label className="text-start">{t('Date')}</Label>
              <Input className="h-10 bg-muted" value={dateReadOnly} readOnly tabIndex={-1} />
            </div>

            <RichTextField
              label={t('Session details for client email')}
              name="summary_html"
              value={summaryHtml}
              onChange={setSummaryHtml}
              placeholder={t('Session details for client email')}
            />
            {fieldErrors.summary_html ? (
              <p className="text-sm text-destructive">{fieldErrors.summary_html}</p>
            ) : null}
          </div>

          <DialogFooter className="mt-6 gap-2 sm:justify-end">
            <Button type="button" variant="outline" onClick={() => onOpenChange(false)} disabled={sending}>
              {t('Cancel')}
            </Button>
            <Button type="submit" className="bg-emerald-600 hover:bg-emerald-700" disabled={sending || !hearing?.id}>
              {t('Send')}
            </Button>
          </DialogFooter>
        </form>
      </DialogContent>
    </Dialog>
  );
}
