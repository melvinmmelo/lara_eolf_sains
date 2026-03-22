<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class InboundPayment extends Model
{
    use HasFactory;

    protected $fillable = [
        'inbound_id',
        'branch_code',
        'customer_id',
        'store_id',
        'payment_date',
        'amount',
        'payment_method',
        'reference_no',
        'remarks',
        'created_by',
        'updated_by',
    ];

    protected $casts = [
        'payment_date' => 'date',
        'amount' => 'decimal:2',
    ];

    public function inbound(): BelongsTo
    {
        return $this->belongsTo(Inbound::class);
    }
}
