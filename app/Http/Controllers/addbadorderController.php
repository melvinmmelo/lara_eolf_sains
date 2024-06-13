<?php

namespace App\Http\Controllers;

use App\Models\Customers;
use App\Models\Inbound;
use App\Models\TempBadOrder;
use App\Models\BadOrder; // Ensure this is correct
use App\Models\PriceLevels;
use App\Models\Prices;
use Illuminate\Http\Request;

class addbadorderController extends Controller
{
    // Function to generate a unique bo_id
    private function generateUniqueBoId()
    {
        // Get the latest bo_id from the database
        $latestBoOrder = BadOrder::latest()->first();
    
        // If there are no existing orders, start with 1
        if (!$latestBoOrder) {
            return 1;
        }
    
        // Increment the latest bo_id by 1
        $nextBoId = $latestBoOrder->bo_id + 1;
    
        return $nextBoId;
    }

    public function create()
    {
        $inbounds = Inbound::with('customer.storeinfo')->get();
        $customers = Customers::with('storeinfo')->get();
        $badPricing = PriceLevels::where('pl_name', 'BAD PRICING')->first();

        // return view('addbadorder', compact('inbounds', 'customers'));
        return view('addbadorder', compact('inbounds', 'customers', 'badPricing'));
    }

    public function getCustomerItems($customerId)
    {
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
    
            if (is_string($inboundProducts)) {
                $decodedProducts = json_decode($inboundProducts, true);
            } else {
                $decodedProducts = $inboundProducts;
            }
        
            if (is_array($decodedProducts)) {
                $products = array_merge($products, $decodedProducts);
            }
        }
        
        return response()->json($products);
    }

    public function store(Request $request)
    {
        try {
            $customer_id = $request->input('customer_id');
            $inbound_id = $request->input('inbound_id');
            $store_id = $request->input('store_id');
            $re_dr = $request->input('re_dr');
            $bo_percentage = $request->input('bo_percentage');
            $remarks = $request->input('remarks');

            // Validate required fields
            if (!$customer_id || !$inbound_id || !$re_dr) {
                return response()->json(['error' => 'Required fields are missing'], 400);
            }

            $tempBadOrders = TempBadOrder::where('customer_id', $customer_id)
                                         ->where('inbound_id', $inbound_id)
                                         ->get();

            if ($tempBadOrders->isEmpty()) {
                return response()->json(['error' => 'No temp bad orders found for the given customer and inbound ID'], 404);
            }
            
            // Generate a unique bo_id
            $bo_id = $this->generateUniqueBoId();

            foreach ($tempBadOrders as $tempOrder) {
                BadOrder::create([
                    'bo_id' => $bo_id, // Assign the bo_id
                    'customer_id' => $customer_id,
                    'inbound_id' => $inbound_id,
                    'store_id' => $store_id,
                    're_dr' => $re_dr,
                    'bo_percentage' => $bo_percentage,
                    'remarks' => $remarks,
                    'ptype_code' => $tempOrder->ptype_code,
                    'code' => $tempOrder->code,
                    'description' => $tempOrder->description,
                    'quantity' => $tempOrder->quantity,
                    'price' => $tempOrder->price,
                    'unit' => $tempOrder->unit,
                    'amount' => $tempOrder->amount,
                ]);
            }
            
            TempBadOrder::where('customer_id', $customer_id)
                        ->where('inbound_id', $inbound_id)
                        ->delete();

            // return response()->json(['success' => true]);
            return redirect('/bad-orders-list/')->with('success', 'Vehicle added successfully!');
        } catch (\Exception $e) {
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }

    public function getPrice($pricelevel_id, $p_code)
    {
        $price = Prices::where('pricelevel_id', $pricelevel_id)
                    ->where('p_code', $p_code)
                    ->first();

        return response()->json($price);
    }


}
