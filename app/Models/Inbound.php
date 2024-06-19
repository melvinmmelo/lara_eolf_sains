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
        'vehicle_id',
        'products',
        'with_invoice',
        'bad_order',
        'status',
        'pricelevel_id',
        'payment_type',
        'ref_no',
        'delivered_amount',
    ];

    protected $appends = ['f_created_at', 'f_updated_at'];

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

    public function getfCreatedAtAttribute()
    {
        return $this->created_at ? $this->created_at->format('Y-m-d h:s A') : null;
    }

    public function getfUpdatedAtAttribute()
    {
        return $this->created_at ? $this->created_at->format('Y-m-d h:s A') : null;
    }

    public function scopeBranch($query, $branch_code)
    {
        return $query->where('branch_code', $branch_code);
    }

    // get the total amount of the products
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
        return $total - $this->bo_amount;
    }

}
