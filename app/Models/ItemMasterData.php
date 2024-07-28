<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ItemMasterData extends Model
{
    use HasFactory;

    protected $guarded = [];

    // create a scope that gets the product based on the branch code
    public function scopeBranch($query, $branchCode)
    {
        return $query->where('branch_code', $branchCode);
    }

    // create a scope that gets the product based on the product code
    public function scopeProductCode($query, $productCode)
    {
        return $query->where('product_code', $productCode);
    }

    public function product()
    {
        return $this->belongsTo(Product::class, 'product_code', 'code');
    }

    // get attribute that returns the product name
    public function getProductNameAttribute()
    {
        return $this->product->productType->name . ' ' . $this->product->productVariant->name;
    }

    // create a function that returns the available stocks
    // the available stocks is the difference between the stocks and the reserved stocks
    public function getAvailableStocksAttribute()
    {
        return $this->stocks - $this->reserved;
    }

}
