<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Inbound extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'order_no',
        'branch_code',
        'equipment_id',
        'customer_id',
        'store_id',
        'driver_id',
        'delivery_person_id',
        'vehicle_id',
        'products',
        'with_invoice',
        'bad_order',
        'is_foc',
        'is_with_sf', // is with delivery charge
        'status',
        'pricelevel_id',
        'payment_type',
        'ref_no',
        'delivered_amount',
        'grp_print_ticket_no',
        'ticket_sequence_no',
        'degic_no',
        'customer_name',
        'store_name',
        'driver_name',
        'vehicle_no',
        // if order slip has been generated this will have a value
        'order_slip_code',
        'order_slip_sno',
        // end
        'order_date',
        'bo_amount',
        'discount',
        'discount_details',
        'sales_invoice_no'
    ];

    protected $grandTotal = 0, $netAmount = 0, $balance = 0;

    protected $appends = ['f_created_at', 'f_updated_at', 'code'];

    protected ?float $ledgerDeliveredAmount = null;

    protected $casts = [
        'order_date' => 'datetime',
        'is_with_sf' => 'boolean',
    ];

    /**
     * Get the next order number for the given branch code.
     *
     * @param string $branchCode
     * @return int
     */
    public static function getNextOrderNo($branchCode)
    {
        $lastOrder = self::where('branch_code', $branchCode)
            ->max('order_no');

        return $lastOrder ? $lastOrder + 1 : 1;
    }

    public function priceLevel(): BelongsTo
    {
        return $this->belongsTo(pricelevels::class, 'pricelevel_id');
    }

    public function customer(): BelongsTo
    {
        return $this->belongsTo(Customers::class);
    }

    public function store(): BelongsTo
    {
        return $this->belongsTo(StoreInfo::class);
    }

    public function equipment(): BelongsTo
    {
        return $this->belongsTo(Equipment::class);
    }

    public function driver(): BelongsTo
    {
        return $this->belongsTo(Drivers::class);
    }

    public function vehicle(): BelongsTo
    {
        return $this->belongsTo(Vehicles::class);
    }

    public function deliveryReceipt(): BelongsTo
    {
        return $this->belongsTo(DeliveryReceipt::class);
    }

    public function payments(): HasMany
    {
        return $this->hasMany(InboundPayment::class)->orderBy('payment_date')->orderBy('id');
    }

    public function latestPayment(): HasMany
    {
        return $this->hasMany(InboundPayment::class)->orderByDesc('payment_date')->orderByDesc('id');
    }

    public function getfCreatedAtAttribute()
    {
        return $this->order_date ? $this->order_date->format('Y-m-d') : null;
    }

    public function getfUpdatedAtAttribute()
    {
        return $this->created_at ? $this->created_at->format('Y-m-d h:s A') : null;
    }

    public function getCodeAttribute()
    {
        if (!$this->created_at) {
            return null;
        }

        if ($this->branch_code === 'EFTO-CAG') {
            $prefix = 'C';
        } elseif ($this->branch_code === 'EFTO-TAR') {
            $prefix = 'T';
        } else {
            $prefix = 'N';
        }

        return $this->created_at->format('y') . "-" . $prefix  . str_pad($this->order_no, 5, "0", STR_PAD_LEFT);
    }

    public function scopeBranch($query, $branch_code)
    {
        return $query->where('branch_code', $branch_code);
    }

    public function scopeCompleted($query)
    {
        return $query->where('status', 'Completed');
    }

    public function scopeForLoading($query)
    {
        return $query->where('status', 'Completed')->where('ticket_sequence_no', 0);
    }

    public function scopeForOrderSlip($query)
    {
        return $query->whereNull('order_slip_code')->whereNotIn('status', ['Cancelled', 'Deleted']);
    }

    public function scopeWithProducts($query)
    {
        return $query->whereNotNull('products');
    }

    public function scopeActiveOrders($query)
    {
        return $query->where('status', 'Completed')->whereNull('is_foc');
    }

    public function scopeNotDRYet($query)
    {
        return $query->whereNull('delivery_receipt_id');
    }

    public function scopeFreeOrders($query)
    {
        return $query->where('is_foc', 1);
    }

    public function scopePaidOrders($query)
    {
        return $query->where('status', 'Paid');
    }

    public function getTotalAmountAttribute()
    {
        $total = 0;
        $products = json_decode($this->products, true);

        if ($products == null) {
            return 0;
        }

        foreach ($products as $product) {
            $total += $product['quantity'] * $product['price'];
        }

        $this->grandTotal = $total + ($this->is_with_sf ? 1000 : 0);

        // Use bo_amount and discount from database, defaulting to 0 if null
        $boAmount = $this->bo_amount ?? 0;
        $discount = $this->discount ?? 0;

        return $this->netAmount = $this->grandTotal - ($boAmount + $discount);
    }

    public function getGrandTotalAttribute() // always call this first before getting the netAmount
    {
        if ($this->grandTotal === 0) {
            $this->getTotalAmountAttribute();
        }
        return $this->grandTotal;
    }

    public function getLedgerDeliveredAmountAttribute(): float
    {
        if ($this->ledgerDeliveredAmount !== null) {
            return $this->ledgerDeliveredAmount;
        }

        if ($this->relationLoaded('payments')) {
            return $this->ledgerDeliveredAmount = (float) $this->payments->sum('amount');
        }

        return $this->ledgerDeliveredAmount = (float) ($this->payments()->sum('amount') ?? ($this->delivered_amount ?? 0));
    }

    public function syncPaymentAggregates(): void
    {
        $totalPaid = round($this->payments()->sum('amount'), 2);
        $latestPayment = $this->payments()->latest('payment_date')->latest('id')->first();
        $netAmount = round($this->totalAmount, 2);

        $this->delivered_amount = $totalPaid;
        $this->payment_type = $latestPayment?->payment_method;
        $this->ref_no = $latestPayment?->reference_no;

        if ($this->is_foc) {
            $this->status = 'Paid';
        } elseif ($totalPaid > 0 && $totalPaid >= $netAmount) {
            $this->status = 'Paid';
        } elseif (in_array($this->status, ['Paid', 'Unpaid'])) {
            $this->status = 'Completed';
        }

        $this->save();
        $this->ledgerDeliveredAmount = $totalPaid;
    }

    public function getTotalBalanceAttribute()
    {
        $this->getGrandTotalAttribute();
        $deliveredAmount = $this->ledger_delivered_amount;

        return $this->balance = $this->netAmount - $deliveredAmount;
    }

    public function scopeActiveOrdersv2($query)
    {
        return $query->whereIn('status', ['Completed', 'Paid', 'Free']);
    }
}
