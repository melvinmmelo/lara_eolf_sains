<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class NewBadOrder extends Model
{
    use HasFactory;

    protected $fillable = [
        'branch_code',
        'customer_id',
        'session_bo_id',
        'degic_code',
        'bo_percentage',
        'remarks',
        'is_active',
    ];

    public function customer()
    {
        return $this->belongsTo(Customers::class);
    }

    public function equipment()
    {
        return $this->belongsTo(Equipment::class, 'code', 'degic_code');
    }

    public function products(){
        return $this->hasMany(NewTempBadOrder::class);
    }

    // scope to filter bad orders by branch code
    public function scopeBranch($query, $branchCode)
    {
        return $query->where('branch_code', $branchCode);
    }

    // accessor sum of all bad order products amount
    // formula is price * quantity
    public function getAmountAttribute()
    {
        return $this->products->sum(function($product){
            return $product->price * $product->quantity;
        });
    }
}
