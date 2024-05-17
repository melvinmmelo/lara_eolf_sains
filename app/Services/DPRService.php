<?php

namespace App\Services;

use App\Models\prices;
use App\Models\ProductType;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class DPRService extends Model
{
    use HasFactory;

    protected $products;

    public function __construct($products)
    {
        $this->products = json_decode($products, true);
    }

    public function getNewProducts()
    {
        return json_encode($this->products);
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

    public function addProduct($newProduct)
    {

        $exist = false;
        // check if product is already in the list
        if ($this->products) {
            foreach ($this->products as $key => $value) {
                if ($value['code'] == $newProduct['code']) {
                    $this->products[$key]['quantity'] += $newProduct['quantity'];
                    $exist = true;
                }
            }

            if(!$exist){
                $this->products[] = $newProduct;
            }

        }else{
            $this->products[] = $newProduct;
        }

        return $this->products;
    }

    public function saveAndInventoryProducts()
    {
        foreach ($this->products as $value) {
            $product = prices::where('p_code', $value['code'])->first();

            if($product){
                $product->p_quant += $value['quantity'];
                $product->save();
            }
        }
    }
}
