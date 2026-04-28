// components/CrudDeleteModal.tsx
import { Alert, AlertDescription } from '@/components/ui/alert';
import { Dialog, DialogContent, DialogDescription, DialogFooter, DialogHeader, DialogTitle } from '@/components/ui/dialog';
import { Button } from '@/components/ui/button';
import { AlertTriangle } from 'lucide-react';
import type { ReactNode } from 'react';
import { useTranslation } from 'react-i18next';

interface CrudDeleteModalProps {
  isOpen: boolean;
  onClose: () => void;
  onConfirm: () => void;
  itemName: string;
  entityName: string;
  /** Optional warning shown above the standard delete confirmation (e.g. linked records). */
  warningMessage?: ReactNode;
}

export function CrudDeleteModal({
  isOpen,
  onClose,
  onConfirm,
  itemName,
  entityName,
  warningMessage,
}: CrudDeleteModalProps) {
  const { t } = useTranslation();
  return (
    <Dialog open={isOpen} onOpenChange={onClose}>
      <DialogContent className="sm:max-w-md">
        <DialogHeader>
          <DialogTitle>{t('Delete')} {t(entityName)}</DialogTitle>
          {warningMessage ? (
            <Alert className="mt-2 border-amber-200 bg-amber-50 text-amber-950 dark:border-amber-900 dark:bg-amber-950/40 dark:text-amber-100">
              <AlertTriangle className="text-amber-600 dark:text-amber-400" />
              <AlertDescription className="text-amber-950 dark:text-amber-100">{warningMessage}</AlertDescription>
            </Alert>
          ) : null}
          <DialogDescription>
            {t('Are you sure you want to delete "{{name}}"? This action cannot be undone.', {
              name: itemName || t('this {{entity}}', { entity: t(entityName) })
            })}
          </DialogDescription>
        </DialogHeader>
        <DialogFooter className="sm:justify-end">
          <Button type="button" variant="outline" onClick={onClose}>
            {t("Cancel")}
          </Button>
          <Button type="button" variant="destructive" onClick={onConfirm}>
            {t("Delete")}
          </Button>
        </DialogFooter>
      </DialogContent>
    </Dialog>
  );
}
