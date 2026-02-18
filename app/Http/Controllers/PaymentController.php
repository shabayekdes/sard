<?php

namespace App\Http\Controllers;

use App\Models\Payment;
use App\Models\Invoice;
use Illuminate\Http\Request;
use Inertia\Inertia;

class PaymentController extends Controller
{
    public function index(Request $request)
    {
        $query = Payment::withPermissionCheck()->with(['invoice.client', 'creator']);

        if ($request->search) {
            $query->where(function ($q) use ($request) {
                $q->whereHas('invoice', function ($invoiceQuery) use ($request) {
                      $invoiceQuery->where('invoice_number', 'like', '%' . $request->search . '%')
                          ->orWhereHas('client', function ($clientQuery) use ($request) {
                              $clientQuery->where('name', 'like', '%' . $request->search . '%');
                          });
                  });
            });
        }

        if ($request->payment_method) {
            $query->where('payment_method', $request->payment_method);
        }

        if ($request->approval_status) {
            $query->where('approval_status', $request->approval_status);
        }

        if ($request->invoice_id) {
            $query->where('invoice_id', $request->invoice_id);
        }

        $payments = $query->orderBy('payment_date', 'desc')->paginate(10);
        
        // Transform payments to convert attachment array to comma-separated string for frontend MediaPicker
        $payments->getCollection()->transform(function ($payment) {
            $paymentData = $payment->toArray();
            
            // Convert attachment array to comma-separated string for frontend MediaPicker
            if (isset($paymentData['attachment']) && is_array($paymentData['attachment'])) {
                $paymentData['attachment'] = implode(',', array_filter($paymentData['attachment']));
            }
            
            return $paymentData;
        });
        
        $invoices = Invoice::withPermissionCheck()->with('client')->select('id', 'invoice_number', 'client_id')->get();

        return Inertia::render('billing/payments/index', [
            'payments' => $payments,
            'invoices' => $invoices,
            'filters' => $request->only(['search', 'payment_method', 'invoice_id', 'approval_status']),
        ]);
    }

    public function store(Request $request)
    {
        $invoice = Invoice::findOrFail($request->invoice_id);
        $maxAmount = $invoice->remaining_amount ?: $invoice->total_amount;
        
        $request->validate([
            'invoice_id' => 'required|exists:invoices,id',
            'payment_method' => 'required|string',
            'amount' => 'required|numeric|min:0.01|max:' . $maxAmount,
            'payment_date' => 'required|date',
            'notes' => 'nullable|string|max:1000',
            'attachment' => 'nullable',
        ]);

        // Convert attachment from comma-separated string to array and extract storage paths
        $attachmentFiles = null;
        if ($request->has('attachment') && $request->attachment) {
            if (is_string($request->attachment)) {
                $attachmentFiles = array_filter(array_map('trim', explode(',', $request->attachment)));
            } elseif (is_array($request->attachment)) {
                $attachmentFiles = array_filter($request->attachment);
            }
            
            // Extract storage path from full URLs (store only path from /storage/ onwards)
            if ($attachmentFiles) {
                $attachmentFiles = array_map(function ($file) {
                    return $this->extractStoragePath($file);
                }, $attachmentFiles);
            }
            
            // Convert empty array to null
            if (empty($attachmentFiles)) {
                $attachmentFiles = null;
            }
        }

        $isBankTransfer = $request->payment_method === 'bank_transfer';

        Payment::create([
            'created_by' => createdBy(),
            'invoice_id' => $request->invoice_id,
            'payment_method' => $request->payment_method,
            'amount' => $request->amount,
            'payment_date' => $request->payment_date,
            'notes' => $request->notes,
            'attachment' => $attachmentFiles,
            'approval_status' => $isBankTransfer ? 'pending' : 'approved',
            'approved_at' => $isBankTransfer ? null : now(),
            'approved_by' => $isBankTransfer ? null : auth()->id(),
        ]);

        return redirect()->back()->with('success', __('Payment recorded successfully.'));
    }

    public function update(Request $request, Payment $payment)
    {
        $invoice = Invoice::findOrFail($request->invoice_id);
        $otherPayments = $invoice->payments()->where('id', '!=', $payment->id)->sum('amount');
        $maxAmount = $invoice->total_amount - $otherPayments;
        
        $request->validate([
            'invoice_id' => 'required|exists:invoices,id',
            'payment_method' => 'required|string',
            'amount' => 'required|numeric|min:0.01|max:' . $maxAmount,
            'payment_date' => 'required|date',
            'notes' => 'nullable|string|max:1000',
            'attachment' => 'nullable',
        ]);

        // Convert attachment from comma-separated string to array and extract storage paths
        $attachmentFiles = null;
        if ($request->has('attachment') && $request->attachment) {
            if (is_string($request->attachment)) {
                $attachmentFiles = array_filter(array_map('trim', explode(',', $request->attachment)));
            } elseif (is_array($request->attachment)) {
                $attachmentFiles = array_filter($request->attachment);
            }
            
            // Extract storage path from full URLs (store only path from /storage/ onwards)
            if ($attachmentFiles) {
                $attachmentFiles = array_map(function ($file) {
                    return $this->extractStoragePath($file);
                }, $attachmentFiles);
            }
            
            // Convert empty array to null
            if (empty($attachmentFiles)) {
                $attachmentFiles = null;
            }
        }

        $newMethod = $request->payment_method;
        $currentMethod = $payment->payment_method;
        $isBankTransfer = $newMethod === 'bank_transfer';
        $approvalUpdates = [];

        if ($currentMethod !== $newMethod) {
            if ($isBankTransfer) {
                $approvalUpdates = [
                    'approval_status' => 'pending',
                    'approved_at' => null,
                    'approved_by' => null,
                    'rejection_reason' => null,
                ];
            } else {
                $approvalUpdates = [
                    'approval_status' => 'approved',
                    'approved_at' => now(),
                    'approved_by' => auth()->id(),
                    'rejection_reason' => null,
                ];
            }
        } elseif (!$isBankTransfer && $payment->approval_status !== 'approved') {
            $approvalUpdates = [
                'approval_status' => 'approved',
                'approved_at' => now(),
                'approved_by' => auth()->id(),
                'rejection_reason' => null,
            ];
        }

        $payment->update(array_merge([
            'invoice_id' => $request->invoice_id,
            'payment_method' => $request->payment_method,
            'amount' => $request->amount,
            'payment_date' => $request->payment_date,
            'notes' => $request->notes,
            'attachment' => $attachmentFiles,
        ], $approvalUpdates));

        return redirect()->back()->with('success', __('Payment updated successfully.'));
    }

    public function destroy(Payment $payment)
    {
        $payment->delete();
        return redirect()->back()->with('success', __('Payment deleted successfully.'));
    }

    public function approve(Payment $payment)
    {
        if ($payment->payment_method !== 'bank_transfer' || $payment->approval_status !== 'pending') {
            return redirect()->back()->with('error', __('Only pending bank transfer payments can be approved.'));
        }

        $payment->update([
            'approval_status' => 'approved',
            'approved_at' => now(),
            'approved_by' => auth()->id(),
            'rejection_reason' => null,
        ]);

        return redirect()->back()->with('success', __('Payment approved successfully.'));
    }

    public function reject(Request $request, Payment $payment)
    {
        if ($payment->payment_method !== 'bank_transfer' || $payment->approval_status !== 'pending') {
            return redirect()->back()->with('error', __('Only pending bank transfer payments can be rejected.'));
        }

        $request->validate([
            'rejection_reason' => 'nullable|string|max:1000',
        ]);

        $payment->update([
            'approval_status' => 'rejected',
            'approved_at' => null,
            'approved_by' => null,
            'rejection_reason' => $request->rejection_reason,
        ]);

        return redirect()->back()->with('success', __('Payment rejected successfully.'));
    }

    /**
     * Extract storage path from full URL or return path as is
     * Converts full URLs like "https://example.com/storage/app/public/files/image.jpg"
     * to "/storage/app/public/files/image.jpg" or "storage/app/public/files/image.jpg"
     * 
     * @param string $filePath
     * @return string
     */
    private function extractStoragePath($filePath)
    {
        if (empty($filePath)) {
            return $filePath;
        }

        // If it's already a relative path starting with /storage/, return as is
        if (str_starts_with($filePath, '/storage/')) {
            return $filePath;
        }

        // If it's a relative path starting with storage/ (without leading slash), return as is
        if (str_starts_with($filePath, 'storage/')) {
            return '/' . $filePath;
        }

        // If it's a full URL, extract the path from /storage/ onwards
        if (str_starts_with($filePath, 'http://') || str_starts_with($filePath, 'https://')) {
            $storageIndex = strpos($filePath, '/storage/');
            if ($storageIndex !== false) {
                return substr($filePath, $storageIndex);
            }
        }

        // If it doesn't contain /storage/, return as is (might be a different path format)
        return $filePath;
    }
}