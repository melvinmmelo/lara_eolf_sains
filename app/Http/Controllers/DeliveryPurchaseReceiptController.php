<?php

namespace App\Http\Controllers;

use App\Models\DeliveryPurchaseReceipt;
use App\Http\Requests\StoreDeliveryPurchaseReceiptRequest;
use App\Http\Requests\UpdateDeliveryPurchaseReceiptRequest;
use App\Models\prices;
use App\Models\Product;
use App\Services\DPRService;
use Illuminate\Http\Request;

class DeliveryPurchaseReceiptController extends Controller
{

    public function saveAndInventoryProduct(int $dprId){
        $dpr = DeliveryPurchaseReceipt::findOrFail($dprId);

        $dpr->status = 'Saved';

        if($dpr->save()){
            $dprService = new DPRService($dpr->products);
            $dprService->saveAndInventoryProducts();
        }

        return redirect()->route('delivery-purchase-receipts.index')->with('success', 'Delivery Receipt saved successfully.');

    }

    public function storeProduct(Request $request)
    {

        $request->validate([
            'dpr_id' => 'required|exists:delivery_purchase_receipts,id',
            'product_code' => 'required',
            'qty' => 'required',
        ]);

        $dpr = DeliveryPurchaseReceipt::findOrFail($request->dpr_id);

        $productPrice = prices::where('p_code', $request->product_code)->first()->p_price;

        if(!$productPrice){
            return redirect()->back()->with('error', 'Product price not found.');
        }


        $newProduct = ['code' => $request->product_code, 'quantity' => $request->qty, 'price' => $productPrice];


        $dprService = new DPRService($dpr->products);

        $dprService->addProduct($newProduct);

        $dpr->products = $dprService->getNewProducts();

        $dpr->save();

        return redirect()->back()->with('success', 'Product added to Delivery Receipt successfully.');
    }

    /**
     * Display a listing of the resource.
     */
    public function products(int $dprId)
    {
        $deliveryPurchaseReceipt = DeliveryPurchaseReceipt::findOrFail($dprId);

        if( strtolower($deliveryPurchaseReceipt->status) == 'saved'){
            return redirect()->back()->with('error', 'Delivery receipt already saved.');
        }

        $originalProducts = Product::all();

        return view('delivery-purchase-receipts.products', compact('deliveryPurchaseReceipt', 'originalProducts'));
    }


    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        //
        $deliveryPurchaseReceipts = DeliveryPurchaseReceipt::branch(session('branch_code'))->get();
        return view('delivery-purchase-receipts.index', compact('deliveryPurchaseReceipts'));
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
    public function store(StoreDeliveryPurchaseReceiptRequest $request)
    {
        DeliveryPurchaseReceipt::create($request->validated());
        return redirect()->back()->with('success', 'Delivery Receipt created successfully.');
    }

    /**
     * Display the specified resource.
     */
    public function show(DeliveryPurchaseReceipt $deliveryPurchaseReceipt)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(DeliveryPurchaseReceipt $deliveryPurchaseReceipt)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateDeliveryPurchaseReceiptRequest $request, DeliveryPurchaseReceipt $deliveryPurchaseReceipt)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(DeliveryPurchaseReceipt $deliveryPurchaseReceipt)
    {
        //
    }
}
