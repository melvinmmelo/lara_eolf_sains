<?php

namespace App\Services;

use App\Models\ItemMasterData;
use App\Models\ProductType;
use App\Models\Inbound;
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
        if ($products == null) {
            $this->products = null;
            return;
        }
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

                    // check stocks
                    $item = ItemMasterData::branch(session('branch_code'))->productCode($newProductCode)->first();

                    if($item == null){
                        session()->put('error', 'Product not found in IMD.');
                        throw new \Exception('Product not found in IMD.');
                        return $this->products;
                    }

                    if($item->stocks < $value['quantity'] + $plusQty){
                        session()->put('error', 'Not enough stocks in IMD.');
                        throw new \Exception('Not enough stocks in IMD.');
                        return $this->products;
                    }

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
                $summary[$ptypeCode] = ['ptype_code' => $ptypeCode,'total' => $product['quantity'], 'price' => $product['price'], 'order' => $product['order']];
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

    public static function getInboundProducts()
    {

        if (session()->get('products')) {
            $products = json_encode(session()->get('products'));
        }else if (session()->get('inboundId') AND session()->get('inboundId') != null AND session()->get('inboundId') != '' AND session()->get('inboundId') != 0) {
            $inboundId = session()->get('inboundId');
            $inbound = Inbound::find($inboundId);
            $products = json_decode($inbound->products, true);
            session()->put('products', $products);

            return $inbound->products;
        } else{
            $products = [];
        }

        return $products;
    }

    // create a function that will return the current total quantity of the product code
    public function getCurrentQty($productCode)
    {
        $totalQty = 0;

        if ($this->products) {
            foreach ($this->products as $product) {
                if ($product['code'] == $productCode) {
                    $totalQty = $product['quantity'];
                }
            }
        }

        return $totalQty;

    }



}
