import { useState, useRef } from 'react';
import { Dialog, DialogContent, DialogHeader, DialogTitle } from '@/components/ui/dialog';
import { Button } from '@/components/ui/button';
import { Input } from '@/components/ui/input';
import { Label } from '@/components/ui/label';
import { Textarea } from '@/components/ui/textarea';
import { useTranslation } from 'react-i18next';
import { router } from '@inertiajs/react';
import { toast } from '@/components/custom-toast';
import { CurrencyAmount } from '@/components/currency-amount';
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
  const [attachmentError, setAttachmentError] = useState('');
  const [notes, setNotes] = useState('');
  const fileInputRef = useRef<HTMLInputElement>(null);

  const handleAttachmentChange = (e: React.ChangeEvent<HTMLInputElement>) => {
    const file = e.target.files?.[0];
    setAttachment(file || null);
    setAttachmentError('');
  };

  const handleSubmit = () => {
    if (!attachment) {
      setAttachmentError(t('Attachment is required'));
      toast.error(t('Please attach a file to submit your payment request'));
      return;
    }
    setAttachmentError('');
    setProcessing(true);

    const payload: Record<string, unknown> = {
      payment_method: 'bank_transfer',
      invoice_token: invoice.payment_token,
      amount: amount,
      attachment,
    };
    if (notes.trim()) {
      payload.note = notes.trim();
    }

    router.post(route('invoice.payment.process', invoice.payment_token), payload, {
      forceFormData: true,
      onSuccess: () => {
        toast.success(t('Payment request submitted successfully'));
        setAttachment(null);
        setNotes('');
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
      setAttachmentError('');
      setNotes('');
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
          <div className="bg-primary/10 p-4 rounded-lg">
            <h4 className="font-semibold text-primary mb-2">{t('Payment Instructions')}</h4>
            <div className="text-sm text-primary/80 space-y-1">
              <p><strong>{t('Amount')}:</strong> <CurrencyAmount amount={amount} /></p>
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
              {t('Attachment')} <span className="text-destructive">*</span>
            </Label>
            <div className="flex flex-col gap-1">
              <span
                role="button"
                tabIndex={0}
                onClick={() => fileInputRef.current?.click()}
                onKeyDown={(e) => e.key === 'Enter' && fileInputRef.current?.click()}
                className={`inline-flex items-center justify-center rounded-md border px-4 py-2 text-sm font-medium ring-offset-background focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-ring focus-visible:ring-offset-2 cursor-pointer w-fit ${attachmentError
                    ? 'border-destructive bg-destructive/5 hover:bg-destructive/10'
                    : 'border-input bg-background hover:bg-accent hover:text-accent-foreground'
                  }`}
              >
                {t('Choose file')}
              </span>
              <Input
                id="bank-transfer-attachment"
                ref={fileInputRef}
                type="file"
                accept=".pdf,.jpg,.jpeg,.png,.gif,.doc,.docx"
                onChange={handleAttachmentChange}
                className="sr-only"
                aria-label={t('Choose file')}
                aria-required="true"
              />
              <p className={`text-xs ${attachmentError ? 'text-destructive' : 'text-muted-foreground'}`}>
                {attachmentError || (attachment ? attachment.name : t('No file chosen'))}
              </p>
            </div>
          </div>

          <div className="space-y-2">
            <Label htmlFor="bank-transfer-notes" className="text-sm font-medium">
              {t('Notes')} ({t('optional')})
            </Label>
            <Textarea
              id="bank-transfer-notes"
              placeholder={t('Add any notes for your payment request...')}
              value={notes}
              onChange={(e) => setNotes(e.target.value)}
              rows={3}
              className="resize-none text-sm"
            />
          </div>

          <div className="flex gap-3 pt-4">
            <Button type="button" variant="outline" onClick={() => handleClose(false)} className="flex-1">
              {t('Cancel')}
            </Button>
            <Button onClick={handleSubmit} disabled={processing || !attachment} className="flex-1">
              {processing ? t('Submitting...') : t('Submit Request')}
            </Button>
          </div>
        </div>
      </DialogContent>
    </Dialog>
  );
}