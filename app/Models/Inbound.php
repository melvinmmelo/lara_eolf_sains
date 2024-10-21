<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Inbound extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
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
        'order_slip_code',
        'order_slip_sno',
        'order_date'
    ];

    protected $grandTotal = 0, $netAmount = 0, $balance = 0;

    protected $appends = ['f_created_at', 'f_updated_at', 'code'];

    protected $casts = [
        'order_date' => 'datetime'
    ];

    public function priceLevel() : BelongsTo {
        return $this->belongsTo(pricelevels::class, 'pricelevel_id');
    }

    public function customer() : BelongsTo {
        return $this->belongsTo(Customers::class);
    }

    public function store() : BelongsTo {
        return $this->belongsTo(StoreInfo::class);
    }

    public function equipment() : BelongsTo {
        return $this->belongsTo(Equipment::class);
    }

    public function driver() : BelongsTo {
        return $this->belongsTo(Drivers::class);
    }

    public function vehicle() : BelongsTo {
        return $this->belongsTo(Vehicles::class);
    }

    public function deliveryReceipt() : BelongsTo {
        return $this->belongsTo(DeliveryReceipt::class);
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
        return $this->created_at->format('y') . "-" . str_pad($this->id, 5, "0", STR_PAD_LEFT);
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
        return $query->where('status', 'Completed')->where('ticket_sequence_no' , 0);
    }

    public function scopeForOrderSlip($query)
    {
        return $query->whereNull('order_slip_code');
    }

    public function scopeWithProducts($query)
    {
        return $query->whereNotNull('products');
    }

    // scope that is not free and status is not deleted
    public function scopeActiveOrders($query)
    {
        return $query->where('status', 'Completed')
            ->whereNotIn('status', ['Deleted', 'Cancelled', 'Wrong entry']);
    }

    public function scopeFreeOrders($query){
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

        if($products == null) {
            return 0;
        }

        foreach ($products as $product) {
            $total += $product['quantity'] * $product['price'];
        }

        $this->grandTotal = $total;
        return $this->netAmount = $total - ($this->bo_amount + $this->discount);
    }

    public function getGrandTotalAttribute() // always call this first before getting the netAmount
    {
        if($this->grandTotal === 0) {
            $this->getTotalAmountAttribute();
        }
        return $this->grandTotal;
    }

    public function getTotalBalanceAttribute()
    {
        return $this->balance =  $this->netAmount - $this->delivered_amount;
    }
}
