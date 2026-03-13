import { useRef, useState } from 'react';
import { useTranslation } from 'react-i18next';
import { Button } from '@/components/ui/button';
import { Card, CardContent } from '@/components/ui/card';
import { CurrencyAmount } from '@/components/currency-amount';
import { Input } from '@/components/ui/input';
import { Label } from '@/components/ui/label';
import { Textarea } from '@/components/ui/textarea';
import { router } from '@inertiajs/react';
import { toast } from '@/components/custom-toast';
import { Copy, CheckCircle, Upload } from 'lucide-react';

interface BankTransferFormProps {
  planId: number;
  planPrice: number;
  couponCode: string;
  billingCycle: string;
  bankDetails: string;
  onSuccess: () => void;
  onCancel: () => void;
}

export function BankTransferForm({ 
  planId, 
  planPrice,
  couponCode, 
  billingCycle, 
  bankDetails,
  onSuccess, 
  onCancel 
}: BankTransferFormProps) {
  const { t } = useTranslation();
  const fileInputRef = useRef<HTMLInputElement>(null);
  const [processing, setProcessing] = useState(false);
  const [note, setNote] = useState('');
  const [attachment, setAttachment] = useState<File | null>(null);

  const copyToClipboard = (text: string) => {
    navigator.clipboard.writeText(text);
    toast.success(t('Copied to clipboard'));
  };

  const handleConfirmPayment = () => {
    if (!attachment) {
      toast.error(t('Please upload proof of payment'));
      return;
    }
    setProcessing(true);

    const payload: Record<string, string | number> = {
      plan_id: planId,
      billing_cycle: billingCycle,
      coupon_code: couponCode,
      amount: planPrice,
      note: note.trim(),
    };

    const formData = new FormData();
    Object.entries(payload).forEach(([key, value]) => {
      formData.append(key, String(value));
    });
    formData.append('attachment', attachment);

    router.post(route('bank-transfer.payment'), formData, {
      onSuccess: () => {
        toast.success(t('Payment request submitted successfully'));
        onSuccess();
      },
      onError: () => {
        toast.error(t('Failed to submit payment request'));
      },
      onFinish: () => {
        setProcessing(false);
      },
    });
  };

  return (
    <div className="space-y-4">
      <Card>
        <CardContent className="p-4">
          <h3 className="font-medium mb-3">{t('Bank Transfer Details')}</h3>
          <div className="space-y-3 text-sm">
            <div className="whitespace-pre-line">{bankDetails}</div>
            <div className="flex items-center justify-between p-2 bg-gray-50 rounded">
              <span className="font-medium inline-flex items-center gap-1.5">
                {t('Amount')}: <CurrencyAmount amount={planPrice} variant="superadmin" />
              </span>
              <Button
                variant="outline"
                size="sm"
                onClick={() => copyToClipboard(planPrice.toString())}
              >
                <Copy className="h-3 w-3 mr-1" />
                {t('Copy')}
              </Button>
            </div>
          </div>
        </CardContent>
      </Card>

      <Card className="border-orange-200 bg-orange-50">
        <CardContent className="p-4">
          <div className="flex items-start gap-2">
            <CheckCircle className="h-5 w-5 text-orange-600 mt-0.5" />
            <div className="text-sm text-orange-800">
              <p className="font-medium mb-1">{t('Important Instructions')}</p>
              <ul className="space-y-1 text-xs">
                <li>• {t('Transfer the exact amount shown above')}</li>
                <li>• {t('Include your order reference in the transfer description')}</li>
                <li>• {t('Your plan will be activated after payment verification')}</li>
                <li>• {t('Verification may take 1-3 business days')}</li>
              </ul>
            </div>
          </div>
        </CardContent>
      </Card>

      <Card>
        <CardContent className="p-4 space-y-4">
          <div className="space-y-2">
            <Label htmlFor="bank-transfer-note">{t('Note')}</Label>
            <Textarea
              id="bank-transfer-note"
              value={note}
              onChange={(e) => setNote(e.target.value)}
              placeholder={t('Optional note or reference for this transfer')}
              rows={3}
              className="resize-none"
            />
          </div>
          <div className="space-y-2">
            <Label htmlFor="bank-transfer-attachment">
              {t('Attachment')} <span className="text-destructive">*</span>
            </Label>
            <div className="flex items-center gap-2">
              <Input
                ref={fileInputRef}
                id="bank-transfer-attachment"
                type="file"
                accept=".pdf,.jpg,.jpeg,.png,.gif"
                onChange={(e) => setAttachment(e.target.files?.[0] ?? null)}
                className="hidden"
              />
              <Button
                type="button"
                variant="outline"
                size="sm"
                onClick={() => fileInputRef.current?.click()}
                className="shrink-0"
              >
                <Upload className="h-4 w-4 mr-2" />
                {t('Choose file')}
              </Button>
              {attachment && (
                <span className="text-sm text-muted-foreground truncate max-w-[180px]" title={attachment.name}>
                  {attachment.name}
                </span>
              )}
            </div>
            <p className="text-xs text-muted-foreground">
              {t('Upload proof of payment (PDF or image)')}
            </p>
          </div>
        </CardContent>
      </Card>

      <div className="flex gap-3">
        <Button variant="outline" onClick={onCancel} className="flex-1">
          {t('Cancel')}
        </Button>
        <Button 
          onClick={handleConfirmPayment} 
          disabled={processing}
          className="flex-1"
        >
          {processing ? t('Processing...') : t('I have made the payment')}
        </Button>
      </div>
    </div>
  );
}