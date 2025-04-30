<?php

namespace App\Http\Controllers;

use App\Models\ItemMasterData;
use App\Http\Requests\StoreItemMasterDataRequest;
use App\Http\Requests\UpdateItemMasterDataRequest;
use App\Models\ProductType;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Gate;
use App\Models\Inbound;
use Illuminate\Support\Facades\Artisan;

class ItemMasterDataController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        // Get unique product type codes first
        $ptypeCodes = ItemMasterData::branch(session('branch_code'))
            ->select('product_code')
            ->get()
            ->map(function ($product) {
                return explode('_', $product->product_code)[0];
            })
            ->unique();

        // Fetch all required ProductType data in one query
        $productTypes = ProductType::whereIn('code', $ptypeCodes)
            ->select('code', 'sequence_no')
            ->get()
            ->keyBy('code');

        $products = ItemMasterData::branch(session('branch_code'))
            ->select('id', 'product_code', 'reserved', 'hold_quantity', 'stocks', 'updated_at') // Select only necessary columns
            ->get()
            ->map(function ($product) use ($productTypes) {
                $ptypeCode = explode('_', $product->product_code)[0];
                $product->sequence_no = $productTypes[$ptypeCode]->sequence_no ?? PHP_INT_MAX;
                return $product;
            })
            ->sortBy('sequence_no');

        $productsSumm = $products->map(function ($product) {

                if (isset($product->product_type_code)) {
                    $ptypeCode = $product->product_type_code;
                } else {
                    $ptypeCode = substr($product->product_code, 0, 2);
                    if ($ptypeCode == 'IC') {
                        $ptypeCode = substr($product->product_code, 0, 3);
                    }
                }

                $productType = ProductType::where("code", "LIKE", "$ptypeCode%")->first();

                return [
                    'code' => $ptypeCode,
                    'quantity' => $product->stocks,
                    'order' => $productType->sequence_no ?? '',
                ];
            })
            ->groupBy('code')
            ->map(function ($group) {
                return [
                    'code' => $group->first()['code'],
                    'quantity' => $group->sum('quantity'),
                    'order' => $group->first()['order'],
                ];
            })
            ->sortBy('order')
            ->values()
            ->toArray();
        
        return view('item-master-data', compact('products', 'productsSumm'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreItemMasterDataRequest $request)
    {
        //
    }

    /**
     * Display the specified resource.
     */
    public function show(ItemMasterData $itemMasterData)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(ItemMasterData $itemMasterData)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateItemMasterDataRequest $request, ItemMasterData $itemMasterData)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(ItemMasterData $itemMasterData)
    {
        //
    }

    public function addQtyFromHold(Request $request)
    {

        $request->validate([
            'imd_id' => 'required',
            'quantity' => 'required|numeric',
        ]);


        $product = ItemMasterData::find($request->imd_id);

        if ($request->quantity > $product->hold_quantity) {
            return redirect()->back()->withErrors('Failed to add quantity.');
        }

        $product->stocks += $request->quantity;
        $product->hold_quantity -= $request->quantity;
        $product->save();

        activity()
            ->performedOn($product)
            ->withProperties(['quantity' => $request->quantity])
            ->log('Quantity added from hold.');

        return redirect()->back()->with('success', 'Quantity added successfully.');
    }

    public function updateStocks(Request $request)
    {
        $request->validate([
            'id' => 'required|exists:item_master_data,id',
            'stocks' => 'required|numeric|min:0',
            'reserved' => 'required|numeric|min:0'
        ]);

        $item = ItemMasterData::findOrFail($request->id);
        $oldStock = $item->stocks;
        $item->update([
            'stocks' => $request->stocks,
            'reserved' => $request->reserved
        ]);

        activity()
                ->performedOn($item)
                ->withProperties(['old_stocks' => $oldStock])
                ->log('stocks-updated');

        return response()->json([
            'success' => true,
            'message' => 'Stocks updated successfully',
            'item' => $item
        ]);
    }

    public function updateStocksPage(Request $request)
    {
        Gate::authorize('admin');

        $query = ItemMasterData::branch(session('branch_code'))
            ->with('product') // Include the product relationship
            ->select('id', 'product_code', 'reserved', 'hold_quantity', 'stocks', 'updated_at');

        if ($request->has('search')) {
            $search = $request->get('search');
            $query->where('product_code', 'like', "%{$search}%");
        }

        $products = $query->paginate(50);

        return view('update-stocks', compact('products'));
    }

    public function bulkUpdateStocksPage(Request $request)
    {
        Gate::authorize('admin');

        $selectedProducts = collect();
        $query = ItemMasterData::branch(session('branch_code'))
            ->select('id', 'product_code', 'reserved', 'hold_quantity', 'stocks', 'updated_at');

        if ($request->has('search')) {
            $search = $request->get('search');
            $query->where('product_code', 'like', "%{$search}%");
        }

        $products = $query->paginate(50);

        return view('bulk-update-stocks', compact('products', 'selectedProducts'));
    }

    public function bulkUpdateStocks(Request $request)
    {
        Gate::authorize('admin');

        $request->validate([
            'products' => 'required|array',
            'products.*.id' => 'required|exists:item_master_data,id',
            'products.*.quantity' => 'required|numeric|min:0'
        ]);

        foreach ($request->products as $product) {
            $item = ItemMasterData::findOrFail($product['id']);
            $oldStock = $item->stocks;
            
            $item->update([
                'stocks' => $item->stocks + $product['quantity']
            ]);

            activity()
                ->performedOn($item)
                ->withProperties(['old_stocks' => $oldStock, 'added_quantity' => $product['quantity']])
                ->log('bulk-stocks-updated');
        }

        return response()->json([
            'success' => true,
            'message' => 'Stocks updated successfully'
        ]);
    }

    /**
     * Revert order items back to inventory stocks
     * 
     * @param \App\Models\Inbound $inbound
     * @return \Illuminate\Http\RedirectResponse
     */
    public function revertOrderItems($inboundId)
    {
        // Find the inbound order
        $inbound = Inbound::findOrFail($inboundId);
        
        // Get the products from the inbound order
        $products = json_decode($inbound->products, true);
        
        if (!$products) {
            return redirect()->back()->withErrors('No products found in this order.');
        }
        
        $branchCode = session('branch_code');
        $revertedProducts = [];
        
        // Begin transaction to ensure all stock updates are atomic
        \DB::beginTransaction();
        
        try {

            // backup database first 
            // run the php artisan command
            Artisan::call('db:backup');
            


            foreach ($products as $product) {
                // Find the corresponding item in the master data
                $itemData = ItemMasterData::branch($branchCode)
                    ->productCode($product['code'])
                    ->first();
                
                if ($itemData) {
                    // Add the quantity back to stocks
                    $itemData->stocks += $product['quantity'];
                    $itemData->save();
                    
                    $revertedProducts[] = [
                        'code' => $product['code'],
                        'description' => $product['description'],
                        'quantity' => $product['quantity']
                    ];
                }
            }
            
            // Log the activity
            activity()
                ->performedOn($inbound)
                ->withProperties(['reverted_products' => $revertedProducts])
                ->log('Order items reverted back to inventory');
            
            \DB::commit();
            
            return redirect()->back()->with('success', 'Order items have been successfully reverted back to inventory.');
        } catch (\Exception $e) {
            \DB::rollBack();
            return redirect()->back()->withErrors('Failed to revert order items: ' . $e->getMessage());
        }
    }
}
