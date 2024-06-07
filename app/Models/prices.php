<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class prices extends Model
{
    use HasFactory;
    protected $fillable = [
        'pricelevel_id',
        'p_code',
        'p_unit',
        'p_quant',
        'p_price'
        // Add other fillable attributes here if any
    ];

    public function product()
    {
        return $this->belongsTo(Product::class, 'p_code', 'code');
    }

    public function pricelevel()
    {
        return $this->belongsTo(pricelevels::class);
    }

    public static function getPrice($productCode, $branchCode, $priceType)
    {
        $price = prices::where('p_code', $productCode)->whereHas('pricelevel', function ($query) use ($branchCode, $priceType) {
            $query->where('branch_code', $branchCode)->where('pl_type', $priceType);
        })->first();

        return ($price) ? $price->p_price : null;
    }

    public static function getPricePerPriceLevelAndPCode($pricelevelId, $productCode)
    {
        $price = prices::where('p_code', $productCode)->where('pricelevel_id', $pricelevelId)->first();
        return ($price) ? $price : null;
    }

    public static function getFactoryPrice($productCode, $priceType = 'FACTORY PRICE')
    {
        $price = prices::where('p_code', $productCode)->whereHas('pricelevel', function ($query) use ($priceType) {
            $query->where('pl_type', $priceType);
        })->first();

        return ($price) ?? null;
    }
    protected $appends = ['date_created'];

    public function getDateCreatedAttribute()
    {
        return $this->created_at->format('m-d-Y h:i A');
    }


}
