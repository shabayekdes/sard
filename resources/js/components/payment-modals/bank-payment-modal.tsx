import { useState, useRef } from 'react';
import { Dialog, DialogContent, DialogHeader, DialogTitle } from '@/components/ui/dialog';
import { Button } from '@/components/ui/button';
import { Input } from '@/components/ui/input';
import { Label } from '@/components/ui/label';
import { useTranslation } from 'react-i18next';
import { router } from '@inertiajs/react';
import { toast } from '@/components/custom-toast';
import { formatCurrencyForCompany } from '@/utils/helpers';
import { Paperclip } from 'lucide-react';

interface BankPaymentModalProps {
  isOpen: boolean;
  onClose: () => void;
  invoice: any;
  amount: number;
}

export function BankPaymentModal({ isOpen, onClose, invoice, amount }: BankPaymentModalProps) {
  const { t } = useTranslation();
  const [processing, setProcessing] = useState(false);
  const [attachment, setAttachment] = useState<File | null>(null);
  const fileInputRef = useRef<HTMLInputElement>(null);

  const handleAttachmentChange = (e: React.ChangeEvent<HTMLInputElement>) => {
    const file = e.target.files?.[0];
    setAttachment(file || null);
  };

  const handleSubmit = () => {
    setProcessing(true);

    const payload: Record<string, unknown> = {
      payment_method: 'bank_transfer',
      invoice_token: invoice.payment_token,
      amount: amount,
    };
    if (attachment) {
      payload.attachment = attachment;
    }

    router.post(route('invoice.payment.process', invoice.payment_token), payload, {
      forceFormData: true,
      onSuccess: () => {
        toast.success(t('Payment request submitted successfully'));
        setAttachment(null);
        if (fileInputRef.current) fileInputRef.current.value = '';
        onClose();
      },
      onError: (errors) => {
        toast.error(Object.values(errors).join(', '));
        setProcessing(false);
      },
    });
  };

  const handleClose = (open: boolean) => {
    if (!open) {
      setAttachment(null);
      if (fileInputRef.current) fileInputRef.current.value = '';
    }
    onClose();
  };

  return (
    <Dialog open={isOpen} onOpenChange={handleClose}>
      <DialogContent className="max-w-md">
        <DialogHeader>
          <DialogTitle>{t('Bank Transfer Payment')}</DialogTitle>
        </DialogHeader>
        
        <div className="space-y-4">
          <div className="bg-blue-50 p-4 rounded-lg">
            <h4 className="font-semibold text-blue-900 mb-2">{t('Payment Instructions')}</h4>
            <div className="text-sm text-blue-800 space-y-1">
              <p><strong>{t('Amount')}:</strong> {formatCurrencyForCompany(amount.toFixed(2))}</p>
              <p><strong>{t('Invoice')}:</strong> #{invoice.invoice_number}</p>
              <p><strong>{t('Reference')}:</strong> {invoice.payment_token}</p>
            </div>
          </div>
          
          <div className="bg-yellow-50 p-4 rounded-lg">
            <p className="text-sm text-yellow-800">
              {t('Your payment request will be submitted for manual verification. Please contact support for bank transfer details.')}
            </p>
          </div>

          <div className="space-y-2">
            <Label htmlFor="bank-transfer-attachment" className="text-sm font-medium flex items-center gap-2">
              <Paperclip className="h-4 w-4" />
              {t('Attachment')} ({t('optional')})
            </Label>
            <Input
              id="bank-transfer-attachment"
              ref={fileInputRef}
              type="file"
              accept=".pdf,.jpg,.jpeg,.png,.gif,.doc,.docx"
              onChange={handleAttachmentChange}
              className="text-sm"
            />
            {attachment && (
              <p className="text-xs text-muted-foreground">{attachment.name}</p>
            )}
          </div>
          
          <div className="flex gap-3 pt-4">
            <Button type="button" variant="outline" onClick={() => handleClose(false)} className="flex-1">
              {t('Cancel')}
            </Button>
            <Button onClick={handleSubmit} disabled={processing} className="flex-1">
              {processing ? t('Submitting...') : t('Submit Request')}
            </Button>
          </div>
        </div>
      </DialogContent>
    </Dialog>
  );
}