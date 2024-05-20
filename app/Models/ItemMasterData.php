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

}
