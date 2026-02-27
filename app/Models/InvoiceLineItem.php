<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Stancl\Tenancy\Database\Concerns\BelongsToTenant;

class InvoiceLineItem extends Model
{
    use BelongsToTenant;
    protected $fillable = [
        'invoice_id',
        'tenant_id',
        'type',
        'description',
        'quantity',
        'rate',
        'amount',
        'sort_order',
        'vat_rate',
        'vat_amount',
        'expense_date',
        'time_entry_id',
        'expense_id',
    ];

    protected $casts = [
        'quantity' => 'decimal:2',
        'rate' => 'decimal:2',
        'amount' => 'decimal:2',
        'vat_rate' => 'decimal:2',
        'vat_amount' => 'decimal:2',
        'expense_date' => 'date',
    ];

    protected static function booted(): void
    {
        static::creating(function (InvoiceLineItem $item) {
            if (empty($item->tenant_id) && $item->invoice_id) {
                $item->tenant_id = Invoice::find($item->invoice_id)?->tenant_id;
            }
        });
    }

    public function invoice(): BelongsTo
    {
        return $this->belongsTo(Invoice::class);
    }

    public function timeEntry(): BelongsTo
    {
        return $this->belongsTo(TimeEntry::class, 'time_entry_id');
    }

    public function expense(): BelongsTo
    {
        return $this->belongsTo(Expense::class);
    }
}
