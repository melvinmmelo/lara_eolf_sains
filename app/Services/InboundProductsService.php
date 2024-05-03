<?php

namespace App\Services;

use App\Models\ProductType;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class InboundProductsService extends Model
{
    use HasFactory;

    protected $products;
    protected $isExist = false;
    protected $summary = null;


    public function __construct($products)
    {
        $this->products = json_decode($products, true);
    }

    public function isExist()
    {
        return $this->isExist;
    }

    // delete product from the list
    public function deleteProduct($productCode)
    {
        // check if product is already in the list
        if ($this->products) {
            foreach ($this->products as $key => $value) {
                if ($value['code'] == $productCode) {
                    unset($this->products[$key]);
                    $this->isExist = true;
                }
            }
        }

        return $this->products;
    }

    public function addQty($newProductCode, $plusQty){
        // check if product is already in the list
        if ($this->products) {
            foreach ($this->products as $key => $value) {

                if ($value['code'] == $newProductCode) {
                    $this->products[$key]['quantity'] += $plusQty;
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
                $summary[$ptypeCode]['total'] += $product['quantity'];
            } else {
                $summary[$ptypeCode] = ['ptype_code' => $ptypeCode,'total' => $product['quantity']];
            }
        }

        $this->summary = $summary;

        return $this->summary;
    }

    public function addSppbinSummary()
    {

        if ($this->summary == null) return [];
        $summary = array_values($this->summary);

        foreach ($summary as $key => $value) {
            $product = ProductType::where('code', $value['ptype_code'])->first();
            $summary[$key]['sppb'] = $product->spoon_pcs_per_bag;
        }

        return $summary;
    }
}
