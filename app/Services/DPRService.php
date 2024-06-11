<?php

namespace App\Services;

use App\Models\ItemMasterData;
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

            if (!$exist) {
                $this->products[] = $newProduct;
            }
        } else {
            $this->products[] = $newProduct;
        }

        return $this->products;
    }

    public function holdProduct($productCode, $qty)
    {
        // check if product is already in the list
        if ($this->products) {
            foreach ($this->products as $key => $value) {
                if ($value['code'] == $productCode) {

                    if ($value['quantity'] < $qty) {
                    } else {
                        $this->products[$key]['hold'] = $qty;
                        $this->isExist = true;
                    }
                }
            }
        }

        return $this->products;
    }

    public function saveAndInventoryProducts()
    {
        foreach ($this->products as $value) {

            $product = ItemMasterData::branch(session('branch_code'))->productCode($value['code'])->first();

            if (!$product) {
                $product = new ItemMasterData();
                $product->branch_code = session('branch_code');
                $product->product_code = $value['code'];
                $product->product_description = $value['description'];
                $product->stocks = ($value['quantity'] - $value['hold']);
                $product->hold_quantity = $value['hold'];
                $product->unit = $value['unit'];
                $product->save();
            } else {
                $product->stocks += ($value['quantity'] - $value['hold']);
                $product->hold_quantity += $value['hold'];
                $product->save();
            }
        }
    }

    // add a function to save and inventory products with hold
    // ! hindi pa ginagamit as of JUNE 11, 2024
    public function saveAndInventoryProductsWithHold()
    {
        foreach ($this->products as $value) {

            if ($value['hold'] > 0) {

                $product = ItemMasterData::branch(session('branch_code'))->productCode($value['code'])->first();

                if (!$product) {
                    $product = new ItemMasterData();
                    $product->branch_code = session('branch_code');
                    $product->product_code = $value['code'];
                    $product->product_description = $value['description'];
                    $product->stocks = $value['hold'];
                    $product->unit = $value['unit'];
                    $product->save();
                } else {
                    $product->stocks += $value['hold'];
                    $product->save();
                }

                // update the product hold to 0
                $this->holdProduct($value['code'], 0);
            }
        }
    }
}
