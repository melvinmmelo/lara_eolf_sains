<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class BadOrder extends Model
{
    use HasFactory;

    protected $table = 'bad_orders';

    protected $fillable = [
        'bo_id', // Add this line to the fillable array
        'customer_id',
        'store_id',
        're_dr',
        'bo_percentage',
        'remarks',
        'ptype_code',
        'code',
        'description',
        'quantity',
        'price',
        'unit',
        'amount',
    ];

    public function customer()
    {
        return $this->belongsTo(Customers::class, 'customer_id');
    }

    public function storeinfo()
    {
        return $this->belongsTo(StoreInfo::class, 'store_id');
    }

    // scope to get all bad orders of inbound id
    public function scopeOfInboundId($query, $inboundId)
    {
        return $query->where('inbound_id', $inboundId);
    }

}
