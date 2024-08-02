<?php

namespace App\Http\Controllers;

use App\Models\DeliveryPurchaseReceipt;
use App\Http\Requests\StoreDeliveryPurchaseReceiptRequest;
use App\Http\Requests\UpdateDeliveryPurchaseReceiptRequest;
use App\Models\ItemMasterData;
use App\Models\prices;
use App\Models\Product;
use App\Models\ProductType;
use App\Services\DPRService;
use Illuminate\Http\Request;

class DeliveryPurchaseReceiptController extends Controller
{

    public function saveAndInventoryProduct(int $dprId)
    {
        $dpr = DeliveryPurchaseReceipt::findOrFail($dprId);

        $dpr->status = 'Completed';

        if ($dpr->save()) {
            $dprService = new DPRService($dpr->products);
            $dprService->saveAndInventoryProducts();
        }

        $dpr->products = $dprService->getNewProducts();
        $dpr->save();

        activity()
            ->performedOn($dpr)
            ->log('Inbound completed.');


        return redirect()->route('delivery-purchase-receipts.index')->with('success', 'Delivery Receipt saved successfully.');
    }

    public function storeProduct(Request $request)
    {

        $request->validate([
            'dpr_id' => 'required|exists:delivery_purchase_receipts,id',
            'product_code' => 'required',
            'qty' => 'required',
        ]);

        // dd($request->product_code);

        $dpr = DeliveryPurchaseReceipt::findOrFail($request->dpr_id);

        $productPrice = prices::getFactoryPrice($request->product_code);

        if (!$productPrice) {
            return redirect()->back()->withErrors('Price not found.');
        }

        $product = Product::productCode($request->product_code)->first();

        $sequence_no = ProductType::code($product->product_type_code)->pluck('sequence_no')->first();


        $newProduct = ['order' => $sequence_no, 'code' => $request->product_code, 'description' => $product->productName, 'quantity' => $request->qty, 'unit' => $productPrice->p_unit, 'price' => $productPrice->p_price, 'hold' => '0', 'created_at' => now(), 'updated_at' => ''];

        $dprService = new DPRService($dpr->products);

        $dprService->addProduct($newProduct);

        $dpr->products = $dprService->getNewProducts();

        $dpr->save();

        activity()
            ->performedOn($dpr)
            ->log('Product added to inbound.');

        return redirect()->back()->with('success', 'Product added to Delivery Receipt successfully.');
    }

    /**
     * Display a listing of the resource.
     */
    public function products(int $dprId)
    {
        $deliveryPurchaseReceipt = DeliveryPurchaseReceipt::findOrFail($dprId);
        // dd($deliveryPurchaseReceipt);

        if (strtolower($deliveryPurchaseReceipt->status) == 'completed') {
            // return redirect()->back()->with('error', 'Delivery receipt already saved.');
        }

        $originalProducts = Product::all();
        $originalProducts = $originalProducts->sortBy(function ($product) {
            return ProductType::code($product->product_type_code)->pluck('sequence_no')->first();
        });

        return view('delivery-purchase-receipts.products', compact('deliveryPurchaseReceipt', 'originalProducts'));
    }

    public function productsEdit(int $dprId)
    {
        $deliveryPurchaseReceipt = DeliveryPurchaseReceipt::findOrFail($dprId);
        // dd($deliveryPurchaseReceipt);

        if (strtolower($deliveryPurchaseReceipt->status) == 'completed') {
            // return redirect()->back()->with('error', 'Delivery receipt already saved.');
        }

        $originalProducts = Product::all();
        $originalProducts = $originalProducts->sortBy(function ($product) {
            return ProductType::code($product->product_type_code)->pluck('sequence_no')->first();
        });

        return view('delivery-purchase-receipts.products-edit', compact('deliveryPurchaseReceipt', 'originalProducts'));
    }


    /**
     * Display a listing of the resource.
     */
    public function index()
    {
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
        $dpr = DeliveryPurchaseReceipt::create($request->validated());

        activity()
            ->performedOn($dpr)
            ->log('Inbound created.');

        return redirect()->route('drp.products', ['dprId' => $dpr->id])->with('success', 'Delivery Receipt created successfully.');
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
     * Realtime update in item master data
     */
    public function update(Request $request)
    {

        $dpr = DeliveryPurchaseReceipt::findOrFail($request->dprId);

        if (!$dpr) {
            return redirect()->back()->withErrors('Item not found.');
        }

        $dprService = new DPRService($dpr->products);

        $product = $dprService->getProduct($request->code);

        $item = ItemMasterData::branch(session('branch_code'))->productCode($request->code)->first();

        if (!$item) {
            return back()->withErrors('Error processing your request: No item found in master data.');
        }

        if ($request->action == 'delete') {
            $newQuantity = $item->stocks - $product['quantity'];
            if ($newQuantity < 0) {
                return back()->withErrors('Error processing your request.');
            }

            $dprService->deleteProduct($request->code);
            $dpr->products = $dprService->getNewProducts();
        }

        if ($request->action == 'add') {

            $newQuantity = $item->stocks + ($request->quantity);

            if (!$product) { // if not existing in products

                $productPrice = prices::getFactoryPrice($request->code);

                if (!$productPrice) {
                    return redirect()->back()->withErrors('Price not found.');
                }

                $product = Product::productCode($request->code)->first();

                $sequence_no = ProductType::code($product->product_type_code)->pluck('sequence_no')->first();

                $newProduct = ['order' => $sequence_no, 'code' => $request->code, 'description' => $product->productName, 'quantity' => $request->quantity, 'unit' => $productPrice->p_unit, 'price' => $productPrice->p_price, 'hold' => '0', 'created_at' => now(), 'updated_at' => ''];

                $dprService->addProduct($newProduct);


            }else{ // update only the quantity and updated at

                $dprService->addProduct($product);

            }

            $dpr->products = $dprService->getNewProducts();


        }

        $dpr->save();


        $item->stocks = $newQuantity;

        $item->save();

        if (!$item) {
            return redirect()->back()->withErrors('Item not found.');
        }

        return redirect()->back()->with('success', 'Item updated successfully.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function delete(Request $request)
    {
        $dpr = DeliveryPurchaseReceipt::findOrFail($request->drid);

        $dprService = new DPRService($dpr->products);
        // dd($dpr->products);

        $dprService->deleteProduct($request->pcode);

        $dpr->products = $dprService->getNewProducts();

        $dpr->save();

        activity()
            ->performedOn($dpr)
            ->log('Product deleted from inbound.');

        return redirect()->back()->with('success', 'Item deleted successfully.');
    }

    public function holdProduct(Request $request)
    {

        $request->validate([
            'hold_dpr_id' => 'required|exists:delivery_purchase_receipts,id',
            'hold_pcode' => 'required',
            'hold_qty' => 'required',
        ]);

        $dpr = DeliveryPurchaseReceipt::findOrFail($request->hold_dpr_id);

        $dprService = new DPRService($dpr->products);

        $products = $dprService->holdProduct($request->hold_pcode, $request->hold_qty);

        if (!$products) {
            return redirect()->back()->withErrors('There is no enough quantity to hold.');
        }

        $dpr->products = $dprService->getNewProducts();

        $dpr->save();

        activity()
            ->performedOn($dpr)
            ->log('Product hold from inbound.');

        return redirect()->back()->with('success', 'Item hold successfully.');
    }
}
