<?php

namespace App\Services;

use App\Models\prices;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class InboundProductsService extends Model
{
    use HasFactory;

    protected $products;
    protected $isExist = false;

    public function __construct($products)
    {
        $this->products = json_decode($products, true);
    }

    public function isExist()
    {
        return $this->isExist;
    }

    public function addQty($newProductCode){
        // check if product is already in the list
        if ($this->products) {
            foreach ($this->products as $key => $value) {

                $price = prices::where('product_code', $newProductCode)->first();

                if ($value['code'] == $newProductCode) {
                    $this->products[$key]['quantity'] += 1;
                    $this->isExist = true;
                }
            }
        }

        return $this->products;
    }

    public function minQty($newProductCode)
    {
        // check if product is already in the list
        if ($this->products) {
            foreach ($this->products as $key => $value) {
                if ($value['code'] == $newProductCode) {
                    $this->products[$key]['quantity'] -= 1;
                    $this->isExist = true;
                }
            }
        }

        return $this->products;
    }

    public function summary(){

        $summary = [];

        if($this->products == null) return $summary;

        foreach ($this->products as $product) {
            $ptypeCode = $product['ptype_code'];
            if (isset($summary[$ptypeCode])) {
                $summary[$ptypeCode]['total']++;
            } else {
                $summary[$ptypeCode] = ['ptype_code' => $ptypeCode, 'total' => 1];
            }
        }


        return $summary;
    }
}
