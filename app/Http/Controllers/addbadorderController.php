<?php

namespace App\Http\Controllers;

use App\Models\Customers;
use App\Models\Inbound;
use App\Models\TempBadOrder;
use App\Models\BadOrder; // Ensure this is correct
use App\Models\pricelevels as PriceLevels;
use App\Models\prices as Prices;
use Illuminate\Http\Request;

use Illuminate\Support\Facades\Redirect;
use Illuminate\Support\Facades\Session;

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
        $customers = Customers::with('storeinfo')->branch(session('branch_code'))->get();

        $badPricing = PriceLevels::where('pl_name', 'BAD PRICING')->first();

        $items = Prices::where('pricelevel_id', $badPricing->id)
            ->join('product_types', 'prices.p_code', '=', 'product_types.code')
            ->select('prices.*', 'product_types.name as description', 'product_types.code as ptype_code')
            ->orderBy('product_types.sequence_no')
            ->get();

        return view('addbadorder', compact('customers', 'badPricing', 'items'));
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
            $session_id = $request->session()->getId(); // Get session ID

            $customer_id = $request->input('customer_id');
            $store_id = $request->input('store_id');
            $re_dr = $request->input('re_dr');
            $bo_percentage = $request->input('bo_percentage');
            $remarks = $request->input('remarks');

            if (!$customer_id) {
                return response()->json(['error' => 'Required fields are missing'], 400);
            }

            $tempBadOrders = TempBadOrder::where('session_id', $session_id)
                                         ->where('customer_id', $customer_id)
                                         ->get();

            if ($tempBadOrders->isEmpty()) {
                return response()->json(['error' => 'No temp bad orders found for the given session and customer'], 404);
            }

            $bo_id = $this->generateUniqueBoId();

            foreach ($tempBadOrders as $tempOrder) {
                BadOrder::create([
                    'bo_id' => $bo_id,
                    'customer_id' => $customer_id,
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

            // Clear previous temp data for this session
            TempBadOrder::where('session_id', $session_id)
                        ->where('customer_id', $customer_id)
                        ->delete();

            // Redirect to the appropriate route after successful storage
            return redirect()->route('badOrders.index')->with('success', 'BO added successfully!');
        } catch (\Exception $e) {
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }

}
