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
import { Label } from '@/components/ui/label';
import { Select, SelectContent, SelectItem, SelectTrigger, SelectValue } from '@/components/ui/select';
import { toast } from '@/components/custom-toast';
import { Users } from 'lucide-react';

type Props = {
  open: boolean;
  onOpenChange: (open: boolean) => void;
  hearingId: number;
  caseId: number | null;
  sessionTitle?: string | null;
  /** User ids already assigned to this hearing */
  assignedUserIds: number[];
  onAfterSave?: () => void;
};

export function HearingAssignMemberModal({
  open,
  onOpenChange,
  hearingId,
  caseId,
  sessionTitle,
  assignedUserIds,
  onAfterSave,
}: Props) {
  const { t } = useTranslation();
  const [options, setOptions] = useState<{ value: string; label: string }[]>([]);
  const [loadError, setLoadError] = useState(false);
  const [loading, setLoading] = useState(false);
  const [saving, setSaving] = useState(false);
  const [userId, setUserId] = useState('');

  const assignedIdsKey = useMemo(
    () =>
      [...assignedUserIds]
        .filter((n) => Number.isFinite(n))
        .sort((a, b) => a - b)
        .join(','),
    [assignedUserIds],
  );

  useEffect(() => {
    if (!open) {
      return;
    }
    setUserId('');
    setLoadError(false);
    if (caseId == null || !Number.isFinite(caseId)) {
      setOptions([]);
      return;
    }
    setOptions([]);
    setLoading(true);
    let cancelled = false;
    const skip = new Set(assignedUserIds);
    fetch(route('api.hearings.case-team-users', caseId), {
      headers: {
        Accept: 'application/json',
        'X-Requested-With': 'XMLHttpRequest',
      },
      credentials: 'same-origin',
    })
      .then((res) => (res.ok ? res.json() : Promise.reject(new Error('bad response'))))
      .then((data: { users?: { value: number; label: string }[] }) => {
        if (cancelled) return;
        const next = (data.users ?? [])
          .filter((u) => !skip.has(u.value))
          .map((u) => ({ value: String(u.value), label: u.label }));
        setOptions(next);
        setLoadError(false);
      })
      .catch(() => {
        if (!cancelled) {
          setOptions([]);
          setLoadError(true);
        }
      })
      .finally(() => {
        if (!cancelled) setLoading(false);
      });
    return () => {
      cancelled = true;
    };
  }, [open, caseId, assignedIdsKey]);

  const handleSubmit = (e: React.FormEvent) => {
    e.preventDefault();
    if (!userId) {
      return;
    }
    setSaving(true);
    router.post(
      route('hearings.team-members.store', hearingId),
      { user_id: parseInt(userId, 10) },
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
          onOpenChange(false);
          if (flash?.success) {
            toast.success(flash.success);
          }
          onAfterSave?.();
        },
        onError: (errors) => {
          toast.dismiss();
          const raw = errors.user_id ?? Object.values(errors)[0];
          const msg = Array.isArray(raw) ? raw[0] : raw;
          toast.error(typeof msg === 'string' && msg ? msg : t('Failed to assign team member'));
        },
      },
    );
  };

  const noCase = caseId == null;
  const empty = !loading && !loadError && options.length === 0;

  return (
    <Dialog open={open} onOpenChange={onOpenChange}>
      <DialogContent className="max-w-md sm:max-w-md" closeButtonClassName="right-auto left-4">
        <form onSubmit={handleSubmit}>
          <DialogHeader>
            <DialogTitle className="flex items-center gap-2 text-start">
              <Users className="h-5 w-5 shrink-0 text-green-600 dark:text-green-500" aria-hidden />
              {t('Add assigned member')}
            </DialogTitle>
            {sessionTitle ? (
              <DialogDescription className="text-start text-base font-medium text-foreground">
                {sessionTitle}
              </DialogDescription>
            ) : null}
          </DialogHeader>

          <div className="mt-4 space-y-2">
            <Label className="text-start" required>
              {t('Team Members')}
            </Label>
            {noCase ? (
              <p className="text-start text-sm text-destructive">{t('Hearing has no case.')}</p>
            ) : null}
            {loadError ? <p className="text-sm text-destructive">{t('Could not load case team.')}</p> : null}
            {!noCase && !loadError && empty && !loading ? (
              <p className="text-sm text-muted-foreground">{t('All case team members are already assigned.')}</p>
            ) : null}
            {!noCase && !loadError && options.length > 0 ? (
              <Select value={userId} onValueChange={setUserId} disabled={loading || saving}>
                <SelectTrigger className="h-10 w-full" disabled={loading || saving}>
                  <SelectValue
                    placeholder={loading ? t('Select') : t('Select a team member')}
                    aria-label={t('Select a team member')}
                  />
                </SelectTrigger>
                <SelectContent>
                  {options.map((o) => (
                    <SelectItem key={o.value} value={o.value}>
                      {o.label}
                    </SelectItem>
                  ))}
                </SelectContent>
              </Select>
            ) : null}
          </div>

          <DialogFooter className="mt-6 gap-2 sm:justify-end">
            <Button type="button" variant="outline" onClick={() => onOpenChange(false)} disabled={saving}>
              {t('Cancel')}
            </Button>
            <Button
              type="submit"
              disabled={saving || !userId || noCase || loadError || options.length === 0}
            >
              {t('Add')}
            </Button>
          </DialogFooter>
        </form>
      </DialogContent>
    </Dialog>
  );
}
