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

    public function productType()
    {
        return $this->belongsTo(ProductType::class, 'p_code', 'code');
    }

    /**
     * Extract product type code from full product code
     * Examples: "SC_RR" → "SC", "N3.6L_VNL" → "N3.6L", "SC" → "SC"
     *
     * @param string|null $code Full product code or product type code
     * @return string|null Product type code
     */
    public static function extractProductTypeCode($code)
    {
        if (empty($code)) {
            return null;
        }

        // If code contains underscore, extract everything before last underscore
        // This handles cases like N3.6L_VNL correctly
        $lastUnderscorePos = strrpos($code, '_');
        if ($lastUnderscorePos !== false) {
            return substr($code, 0, $lastUnderscorePos);
        }

        // Already a product type code
        return $code;
    }

    public static function getPrice($productCode, $branchCode, $priceType)
    {
        $productTypeCode = self::extractProductTypeCode($productCode);

        $price = prices::where('p_code', $productTypeCode)->whereHas('pricelevel', function ($query) use ($branchCode, $priceType) {
            $query->where('branch_code', $branchCode)->where('pl_type', $priceType);
        })->first();

        return ($price) ? $price->p_price : null;
    }

    public static function getPricePerPriceLevelAndPCode($pricelevelId, $productCode)
    {
        $productTypeCode = self::extractProductTypeCode($productCode);

        $price = prices::where('p_code', $productTypeCode)->where('pricelevel_id', $pricelevelId)->first();
        return ($price) ? $price : null;
    }

    public static function getFactoryPrice($productCode, $priceType = 'FACTORY PRICE')
    {
        $productTypeCode = self::extractProductTypeCode($productCode);

        $price = prices::where('p_code', $productTypeCode)->whereHas('pricelevel', function ($query) use ($priceType) {
            $query->where('pl_type', $priceType);
        })->first();

        return ($price) ?? null;
    }
    protected $appends = ['date_created'];

    public function getDateCreatedAttribute()
    {
        return $this->created_at?->format('m-d-Y h:i A');
    }


}
