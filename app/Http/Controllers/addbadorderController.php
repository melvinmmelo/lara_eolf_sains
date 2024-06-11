<?php

namespace App\Http\Controllers;

use App\Models\Customers;
use App\Models\Inbound;
use Illuminate\Http\Request;

class addbadorderController extends Controller
{
    public function create()
    {
        // Fetch all inbounds and customers
        $inbounds = Inbound::with('customer.storeinfo')->get();
        $customers = Customers::with('storeinfo')->get();

        return view('addbadorder', compact('inbounds', 'customers'));
    }


    public function getCustomerItems($customerId)
    {
        // Fetch the items for the given customer
        $inbounds = Inbound::where('customer_id', $customerId)->get();
        $items = [];
        foreach ($inbounds as $inbound) {
            foreach (json_decode($inbound->products) as $product) {
                $items[] = $product;
            }
        }
        return response()->json(['items' => $items]);
    }


    public function getProducts($inboundId, $customerId)
    {
        $inbounds = Inbound::where('id', $inboundId)
                           ->where('customer_id', $customerId)
                           ->get();
        
        $products = [];
        
        foreach ($inbounds as $inbound) {
            $inboundProducts = $inbound->products;
    
            // Check if inboundProducts is a string and needs to be decoded
            if (is_string($inboundProducts)) {
                $decodedProducts = json_decode($inboundProducts, true);
            } else {
                $decodedProducts = $inboundProducts;
            }
        
            // Ensure the decodedProducts is an array before merging
            if (is_array($decodedProducts)) {
                $products = array_merge($products, $decodedProducts);
            }
        }
        
        return response()->json($products);
    }
    
    

}

