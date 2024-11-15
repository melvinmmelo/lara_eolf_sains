<?php

namespace App\Http\Controllers;

use App\Models\NewInboundProduct;
use App\Models\Customers;
use App\Models\DeliveryPurchaseReceipt;
use App\Models\DeliveryReceipt;
use App\Models\Inbound;
use App\Models\Drivers;
use App\Models\Equipment;
use App\Models\EquipmentStore;
use App\Models\ItemMasterData;
use App\Models\NewBadOrder;
use App\Models\pricelevels;
use App\Models\prices;
use App\Models\Product;
use App\Models\ProductType;
use App\Models\Vehicles;
use App\Services\DPRService;
use App\Services\InboundProductsService;
use App\Services\InboundService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

// this is actually the outboundcontroller, nagkamali lang ng naming
class InboundController extends Controller
{

    public function resetInventory()
    {
        $branchCode = session('branch_code');

        // delete first and reset the data
        ItemMasterData::branch($branchCode)->delete();

        $dPurchaseReceipts = DeliveryPurchaseReceipt::branch($branchCode)->where('status', 'Completed')->first();

        foreach ($dPurchaseReceipts as $dpr) {
            $dprService = new DPRService($dpr->products);
            $dprService->saveAndInventoryProducts();
        }

        // ? update item_master_data to reset reserved
        // ? uncomment this if you want to reset the reserved stocks
        $products = InboundService::getTotalOfAllInboundProducts($branchCode);
        foreach ($products as $product) {
            $itemData = ItemMasterData::branch($branchCode)->productCode($product['code'])->first();
            if ($itemData) {
                $itemData->reserved += $product['quantity'];
                $itemData->save();
            }
        }

        $inbounds = InboundService::getInboundsWithDeliveryReceipt($branchCode);

        // inventory products of all inbounds with delivery receipt
        if ($inbounds) {
            foreach ($inbounds as $inbound) {
                $products = json_decode($inbound->products, true);
                if ($products) {
                    foreach ($products as $product) {
                        $itemData = ItemMasterData::branch($branchCode)->productCode($product['code'])->first();
                        if ($itemData) {
                            $itemData->reserved -= $product['quantity'];
                            $itemData->stocks -= $product['quantity'];
                            $itemData->save();
                        }
                    }
                }
            }
        }

        dd("Inventory has been reset.");
    }

    public function addPayment(Request $request)
    {
        $request->validate([
            'ob_id' => 'required|exists:inbounds,id',
            'payment_type' => 'required',
            'ref_no' => 'required|max:30',
            'delivered_amount' => 'numeric|required',
            'status' => 'nullable',
        ]);

        $inbound = Inbound::findOrFail($request->ob_id);

        $total = $inbound->grandTotal;

        $totalDelivered = $inbound->delivered_amount + $request->delivered_amount;

        if ($totalDelivered == $total) {
            $inbound->status = "Paid";
        }

        if ($totalDelivered > $total) {
            return redirect()->route('order.index')->withErrors('Delivered amount is greater than the total amount.');
        }


        $inbound->payment_type = $request->payment_type;
        $inbound->ref_no = $request->ref_no;
        $inbound->delivered_amount = $totalDelivered;

        $inbound->save();

        $changes = ['payment_type' => $request->payment_type, 'ref_no' => $request->ref_no, 'delivered_amount' => $request->delivered_amount, 'status' => $request->status];

        activity('outbound')
            ->performedOn($inbound)
            ->withProperties($changes)
            ->log("Payment added to outbound $inbound->id amounting $request->delivered_amount by " . auth()->user()->fullName);

        return redirect()->route('order.index')->with('success', 'Payment has been added.');
    }

    public function deleteAInbound($pcode, $inboundId = 0)
    {

        $products = NewInboundProduct::where("inbound_id", $inboundId)->where('branch_code', session('branch_code'))->whereNull('status')->get();
        if($inboundId != 0){
            $newInboundProduct = NewInboundProduct::where("inbound_id", $inboundId)->where("code", $pcode)->where('branch_code', session('branch_code'))->whereNull('status')->first();
            $newInboundProduct->status = "Deleted";
            $newInboundProduct->save();
        }else{
            $newInboundProduct = NewInboundProduct::where("inbound_id", 0)->where("code", $pcode)->where('branch_code', session('branch_code'))->whereNull('status')->delete();
        }

        $summary = [];

        $inboundService = new InboundProductsService($products);

        $products = $inboundService->deleteProduct($pcode);

        $uiProducts = $products;

        $summary = [];

        if ($products) {

            $summary = $inboundService->summary();

            $summary = $inboundService->addSppbinSummary(); // ! you need to call summary() first before addSppbinSummary()
        }

        activity('outbound')
            ->log("Product $pcode deleted by " . auth()->user()->fullName);

        return view('inboundList', compact('uiProducts', 'summary'));
    }

    /**
     * Display a listing of the resource.
     */
    public function index()
    {

        $drivers = Drivers::active()->get();

        $vehicles = Vehicles::active()->get();

        $inbounds = Inbound::with('driver', 'vehicle')->branch(session('branch_code'))->activeOrders()->get();

        $equipment = EquipmentStore::all();

        $pricing = pricelevels::getPriceLevels(session('branch_code'));

        return view('order', compact('equipment', 'drivers', 'vehicles', 'inbounds', 'pricing'));
    }

    // ajax products list
    public function ajaxProductList($code)
    {
        $pricelevelId = session('pricelevelId');
        $branchCode = session('branch_code');

        $products = Product::where('product_type_code', $code)
            ->join('item_master_data', function ($join) use ($branchCode) {
                $join->on('products.code', '=', 'item_master_data.product_code')
                    ->where('item_master_data.branch_code', '=', $branchCode);
            })
            ->whereRaw('GREATEST(CAST(item_master_data.stocks AS SIGNED) - CAST(item_master_data.reserved AS SIGNED), 0) > 0')
            ->select(
                'products.code',
                DB::raw('GREATEST(CAST(item_master_data.stocks AS SIGNED) - CAST(item_master_data.reserved AS SIGNED), 0) as available_stocks')
            )
            ->orderByDesc(DB::raw('GREATEST(CAST(item_master_data.stocks AS SIGNED) - CAST(item_master_data.reserved AS SIGNED), 0)'))
            ->get()
            ->map(function ($item) use ($pricelevelId) {
                $price = prices::getPricePerPriceLevelAndPCode($pricelevelId, $item->code);
                return [
                    'code' => $item->code,
                    'price' => $price ?? $item->price,
                    'unit' => "0",
                    'qty' => $item->available_stocks
                ];
            });

        return view(
            'productsList',
            compact('products', 'pricelevelId', 'branchCode')
        );
    }

    // ajax inbound products
    // per product na ito ha, ito na yung table ng product, yung may details
    // adding, editing tayo ng order dito
    public function ajaxInboundList($code, $qty = 1, $pid, $inboundId = 0)
    {
        if ($inboundId == 0) {
            // dump('here');
            $products = NewInboundProduct::where("inbound_id", 0)->where('branch_code', session('branch_code'))->get();
        } else {
            // dump('there');
            $inbound = Inbound::find($inboundId);
        }

        $summary = [];

        $product = Product::where('code', $code)->first();
        $price = prices::where('p_code', $code)->where('pricelevel_id', $pid)->first();
        if ($price == null) {
            return response()->json(['error' => 'Price not found.']);
        }

        $item = ItemMasterData::branch(session('branch_code'))->productCode($code)->first();

        $sequence_no = ProductType::code($product->product_type_code)->pluck('sequence_no')->first();


        $data = ['order' => $sequence_no, 'ptype_code' => $product->product_type_code, 'code' => $product->code, 'quantity' => $qty, 'price' => $price->p_price, 'unit' => $price->p_unit, 'sppb' => $product->spoon_pcs_per_bag, 'description' => $product->productName, 'created_at' => now()];

        $newInboundProduct = NewInboundProduct::where("inbound_id", $inboundId)->where('code', $code)->where('branch_code', session('branch_code'))->first();

        if($newInboundProduct){
            $newInboundProduct->quantity += $qty;

        }else{

            $newInboundProduct = new NewInboundProduct();
            $newInboundProduct->inbound_id = $inboundId;
            $newInboundProduct->branch_code = session('branch_code');
            $newInboundProduct->order = $sequence_no;
            $newInboundProduct->ptype_code = $product->product_type_code;
            $newInboundProduct->code = $product->code;
            $newInboundProduct->old_quantity = 0;
            $newInboundProduct->quantity = $qty;
            $newInboundProduct->price = $price->p_price;
            $newInboundProduct->unit = $price->p_unit;
            $newInboundProduct->description = $product->productName;
            $newInboundProduct->user_id = auth()->user()->id;

        }

        if ($newInboundProduct->quantity > $item->availableStocks) {
            return response()->json(['error' => 'Insufficient stocks.', 'current' => $newInboundProduct, 'available' => $item->availableStocks]);
        }

        $newInboundProduct->save();
        $products = NewInboundProduct::where("inbound_id", $inboundId)->whereNull("status")->where('branch_code', session('branch_code'))->get();

        $uiProducts = $products;

        if (!$uiProducts === null) {
            usort($uiProducts, function ($a, $b) {
                return $a['order'] <=> $b['order'];
            });
        }

        $newProdService = new InboundProductsService(json_encode($products));

        $summary = $newProdService->summary();
        $summary = $newProdService->addSppbinSummary();

        return view('inboundList', compact('uiProducts', 'summary'));
    }

    public function store(Request $request)
    {

        $branchCode = session('branch_code');
        $errors = [];

        $request->validate([
            'pricelevel_id' => 'required',
            'customer_id' => 'required',
            'equipment_id' => 'required',
            'driver_id' => 'required',
            'delivery_person_id' => 'required',
            'vehicle_id' => 'required',
            'bad_order_id' => 'nullable',
            'bo_amount' => 'nullable',
            'order_date' => 'required|date',
        ]);

        $products = NewInboundProduct::where("inbound_id", 0)->where('branch_code', session('branch_code'))->get();
        $orderNo = Inbound::getNextOrderNo($branchCode);

        $equipStore = EquipmentStore::find($request->equipment_id);
        $customer = Customers::find($request->customer_id);
        $driver = Drivers::find($request->driver_id);
        $deliveryPerson = Drivers::find($request->delivery_person_id);

        $vehicles = Vehicles::find($request->vehicle_id);

        if ($equipStore == null || $customer == null || $driver == null || $vehicles == null) {
            return back()->withErrors('All fields are requred.');
        }


        if ($products == null) {
            return back()->withErrors('Please add products.');
        }

        $inbound = new Inbound();
        $inbound->user_id = auth()->user()->id;
        $inbound->order_no = $orderNo;
        $inbound->branch_code = session('branch_code');
        $inbound->equipment_id = $equipStore->equipment->id;
        $inbound->store_id = $equipStore->store_id;
        $inbound->driver_id = $request->driver_id;
        $inbound->delivery_person_id = $request->delivery_person_id;
        $inbound->vehicle_id = $request->vehicle_id;
        $inbound->products = json_encode($products);
        $inbound->pricelevel_id = $request->pricelevel_id;
        $inbound->customer_id = $request->customer_id;

        $inbound->degic_no = $equipStore->equipment->code;
        $inbound->customer_name = $customer->fullName;
        $inbound->store_name = $equipStore->store->storename;
        $inbound->driver_name = $driver->name;
        $inbound->delivery_person = $deliveryPerson->name;
        $inbound->vehicle_no = $vehicles->plateno;
        $inbound->with_invoice = $request->with_invoice == 'on' ? 1 : NULL;

        $bad_order = $request->bad_order === 'on' ? 1 : 0;
        $is_foc = $request->foc === 'on' ? 1 : NULL;
        $is_with_sf = $request->with_sf === 'on' ? 1 : NULL;

        $inbound->is_foc = $is_foc;
        $inbound->is_with_sf = $is_with_sf;

        $inbound->status = 'Completed';
        if ($is_foc == 1) {
            $inbound->delivered_amount = 0;
            $inbound->remarks = 'Free of charge';
        }

        if ($bad_order == 1) {

            $badOrder = NewBadOrder::find($request->bad_order_id);
            $badOrder->is_active = 0;
            $badOrder->save();

            $inbound->bad_order_id = $request->bad_order_id;
            $inbound->bo_amount = $request->bo_amount;
        }

        $inbound->order_date = $request->order_date;

        $inbound->save();

        $updatingData = [];

        foreach ($products as $product) {

            $message = 'Failed';

            $itemData = ItemMasterData::branch($branchCode)->productCode($product['code'])->first();
            if (!$itemData) {
                $errMsg = "Product $product[code] not found in ItemMasterData. Order $inbound->id failed.";
                activity('outbound')
                    ->log($errMsg);
                $errors[] = $errMsg;
                continue;
            } else {
                $itemData->reserved += $product['quantity'];
                $itemData->save();

                $message = 'Success';

                $updatingData[] = ['code' => $product['code'], 'message' => $message];
            }
        }

        // delete all new inbound products
        NewInboundProduct::where("inbound_id", 0)->where('branch_code', session('branch_code'))->delete();


        activity('outbound')
            ->performedOn($inbound)
            ->log("Outbound $inbound->id created by " . auth()->user()->fullName);

        session('errors', $errors);

        return redirect()->route('order.index')->with('success', 'Your order has been completed.');
    }

    public function update(Request $request, $code, $action, $inboundId = 0)
    {

        if($inboundId == 0){

            $newInboundProduct = NewInboundProduct::where("inbound_id", 0)->where("code", $code)->where('branch_code', session('branch_code'))->first();
            if ($action === 'add') {
                $newInboundProduct->quantity += 1;
            } else {
                $newInboundProduct->quantity -= 1;
            }
            $newInboundProduct->save();

        }else{

            $newInboundProduct = NewInboundProduct::where("inbound_id", $inboundId)->where('code', $code)->where('branch_code', session('branch_code'))->first();
            if($action === 'add'){
                $newInboundProduct->quantity += 1;
            }else{
                $newInboundProduct->quantity -= 1;
            }
            $newInboundProduct->save();

        }

        if (NewInboundProduct::where('inbound_id', $inboundId)->where('branch_code', session('branch_code'))->exists()) {
            $products = NewInboundProduct::where('inbound_id', $inboundId)->where('branch_code', session('branch_code'))->get();
        } else {
            $products = NewInboundProduct::where("inbound_id", 0)->where('branch_code', session('branch_code'))->get();
        }


        $inboundService = new InboundProductsService($products);
        $currentProduct = $inboundService->getProductDetails($code);
        $currentProductQty = $currentProduct['quantity'];

        $item = ItemMasterData::branch(session('branch_code'))->productCode($code)->first();
        if ($action == 'add' && ($currentProductQty + 1) > $item->availableStocks) {
            return response()->json(['error' => 'Insufficient stocks.']);
        }

        // if ($action == 'add') {
        //     $newQty = $currentProductQty + 1;
        //     try {
        //         $products = $inboundService->addQty($code, 1);
        //     } catch (\Exception $e) {
        //         return response()->json(['error' => $e->getMessage()]);
        //     }
        // } else {
        //     $newQty = $currentProductQty - 1;
        //     try {
        //         $products = $inboundService->minQty($code);
        //     } catch (\Exception $e) {
        //         return response()->json(['error' => $e->getMessage()]);
        //     }
        // }

        // if (UpdateOrder::where('inbound_id', $inboundId)->where('code', $code)->where('action', $action)->exists()) {
        //     $updateProduct = OrderUpdate::where('inbound_id', $inboundId)->where('product_code', $code)->where('action', $action)->first();
        // } else {
        //     $updateProduct = new UpdateOrder();
        //     $updateProduct->inbound_id = $inboundId;
        //     $updateProduct->ptype_code = substr($code, 0, 2);
        //     $updateProduct->code = $code;
        //     $updateProduct->description = $currentProduct['description'];
        //     $updateProduct->price = $item->price;
        //     $updateProduct->unit = $currentProduct['unit'];
        // }

        // $updateProduct->old_quantity = $currentProductQty;
        // $updateProduct->quantity = $newQty;
        // $updateProduct->action = $action;
        // $updateProduct->user_id = auth()->user()->id;
        // $updateProduct->save();

        if (!$products === null) {
            usort($products, function ($a, $b) {
                return $a['order'] <=> $b['order'];
            });
        }

        $summary = $inboundService->summary();
        $summary = $inboundService->addSppbinSummary();

        return view('orderProductSum', compact('summary'));
    }

    // create delete inbound function
    public function destroy(Request $request)
    {
        $errors = [];

        $request->validate([
            'inbound_id' => 'required|exists:inbounds,id',
            'confirm_delete' => 'required',
            'remarks' => 'required',
            'remarks_details' => 'nullable',
        ]);

        if ($request->confirm_delete !== 'Delete') {
            return back()->withErrors('Please confirm deletion.');
        }

        $inbound = Inbound::findOrFail($request->inbound_id);

        $products = json_decode($inbound->products, true);
        foreach ($products as $product) {
            $itemData = ItemMasterData::branch(session('branch_code'))->productCode($product['code'])->first();
            if ($itemData) {
                if ($inbound->delivery_receipt_id !== NULL) {
                    // If inbound has delivery receipt and is being deleted
                    $newStocks = $itemData->stocks + $product['quantity'];
                    if ($newStocks < 0) {
                        $errors[] = "Product {$product['code']} has negative stocks.";
                        $newStocks = 0;
                    }

                    $itemData->stocks = $newStocks;
                    $itemData->save();
                } else {
                    $itemData->reserved -= $product['quantity'];
                    $itemData->save();
                }
            }
        }

        $inbound->status = $request->remarks;
        $inbound->remarks = $request->remarks . " - " . $request->remarks_details;
        $inbound->save();

        $deliveryReceipt = DeliveryReceipt::where('inbound_id', $inbound->id)->first();
        if ($deliveryReceipt) {
            $deliveryReceipt->status = $request->remarks;
            $deliveryReceipt->save();
        }

        NewInboundProduct::where('inbound_id', $inbound->id)->delete();

        activity('outbound')
            ->performedOn($inbound)
            ->log("Inbound {$inbound->id} deleted by " . auth()->user()->fullName);

        if (!empty($errors)) {
            return back()->withErrors($errors);
        }

        return redirect()->route('order.index')->with('success', 'The order has been deleted.');
    }

    public function create()
    {


        $nextDay = date('Y-m-d', strtotime('+1 day'));


        $productTypes = ProductType::where('is_active', 1)->orderBy('sequence_no', 'asc')->get();

        $drivers = Drivers::active()->perDesignation('Driver')->get();

        $deliveryPersons = Drivers::active()->perDesignation('Salesman')->get();

        $vehicles = Vehicles::active()->get();

        $inbounds = Inbound::with('driver', 'vehicle')->branch(session('branch_code'))->get();

        $equipment = Equipment::has('equipmentStore')->branch(session('branch_code'))->get();

        $pricing = pricelevels::getPriceLevels(session('branch_code'));

        return view('ordering', compact('equipment', 'drivers', 'vehicles', 'inbounds', 'pricing', 'productTypes', 'nextDay', 'deliveryPersons'));
    }

    public function edit($inboundId)
    {

        $inbound = Inbound::find($inboundId);

        // delete all new inbound products
        NewInboundProduct::where('inbound_id', $inboundId)->delete();

        if ($inbound->delivery_receipt_id !== NULL and !auth()->user()->hasRole(['admin'])) {
            return back()->withErrors('This order is already delivered.');
        }

        if ($inbound->balance === 0) {
            return back()->withErrors('This order is already paid.');
        }

        $products = $inbound->products;
        $convertedProducts = json_decode($products, true);

        foreach ($convertedProducts as $product) {


            $newInboundProduct = new NewInboundProduct();
            $newInboundProduct->inbound_id = $inboundId;
            $newInboundProduct->branch_code = session('branch_code');
            $newInboundProduct->order = $product['order'];
            $newInboundProduct->ptype_code = $product['ptype_code'];
            $newInboundProduct->code = $product['code'];
            $newInboundProduct->old_quantity = $product['quantity'];
            $newInboundProduct->quantity = $product['quantity'];
            $newInboundProduct->price = $product['price'];
            $newInboundProduct->unit = $product['unit'];
            $newInboundProduct->description = $product['description'];
            $newInboundProduct->user_id = auth()->user()->id;
            $newInboundProduct->save();

        }

        $deliveryPersons = Drivers::active()->perDesignation('Salesman')->get();

        $drivers = Drivers::active()->perDesignation('Driver')->get();

        $vehicles = Vehicles::active()->get();

        $equipment = Equipment::has('equipmentStore')->branch(session('branch_code'))->get();

        $pricing = pricelevels::getPriceLevels(session('branch_code'));

        $productTypes = ProductType::where('is_active', 1)->orderBy('sequence_no', 'asc')->get();

        $inboundList = [];
        $summary = [];

        if ($products) {
            $inboundList = json_decode($products, true);
            if ($inboundList == null) {
                $inboundList = [];
            }

            $inboundService = new InboundProductsService($products);

            $summary = $inboundService->summary();

            $summary = $inboundService->addSppbinSummary(); // ! you need to call summary() first before addSppbinSummary()
        }

        usort($inboundList, function ($a, $b) {
            return $a['order'] <=> $b['order'];
        });

        $equipmentStore = EquipmentStore::where('customer_id', $inbound->customer_id)->where('equipment_id', $inbound->equipment_id)->where('store_id', $inbound->store_id)->first();

        return view('ordering-edit', compact('inbound', 'inboundId', 'equipment', 'drivers', 'vehicles', 'pricing', 'productTypes', 'inboundList', 'summary', 'deliveryPersons', 'equipmentStore'));
    }

    public function view($inboundId)
    {

        session()->put('inboundId', $inboundId);

        $inbound = Inbound::find($inboundId);

        $priceLevel = pricelevels::find($inbound->pricelevel_id);

        $products = $inbound->products;

        $drivers = Drivers::active()->get();

        $productTypes = ProductType::where('is_active', 1)->orderBy('sequence_no', 'asc')->get();

        $inboundList = [];
        $summary = [];

        if ($products) {
            $inboundList = json_decode($products, true);
            if ($inboundList == null) {
                $inboundList = [];
            }

            $inboundService = new InboundProductsService($products);

            $summary = $inboundService->summary();

            $summary = $inboundService->addSppbinSummary(); // ! you need to call summary() first before addSppbinSummary()
        }

        usort($inboundList, function ($a, $b) {
            return $a['order'] <=> $b['order'];
        });

        return view('ordering-view', compact('inbound', 'inboundId', 'drivers', 'inboundList', 'summary', 'priceLevel'));
    }

    public function updateInbound(Request $request)
    {

        $branchCode = session('branch_code');

        $request->validate([
            'inbound_id' => 'required|exists:inbounds,id',
            'pricelevel_id' => 'required',
            'customer_id' => 'required',
            'equipment_id' => 'required',
            'delivery_person_id' => 'required',
            'driver_id' => 'required',
            'vehicle_id' => 'required',
            'bad_order_id' => 'nullable',
            'bo_amount' => 'nullable',
            'is_foc' => 'nullable',
            'with_sf' => 'nullable',
        ]);

        $equipStore = EquipmentStore::findOrFail($request->equipment_id);
        $inbound = Inbound::findOrFail($request->inbound_id);

        $products = NewInboundProduct::where('inbound_id', $request->inbound_id)->get();
        $customer = Customers::find($request->customer_id);

        $inbound->fill([
            'equipment_id' => $equipStore->equipment->id,
            'driver_id' => $request->driver_id,
            'delivery_person_id' => $request->delivery_person_id,
            'vehicle_id' => $request->vehicle_id,
            'pricelevel_id' => $request->pricelevel_id,
            'customer_id' => $request->customer_id,
            'store_id' => $equipStore->store_id,
            'with_invoice' => $request->with_invoice == 'on' ? 1 : NULL,
            'is_foc' => $request->is_foc == 'on' ? 1 : NULL,
            'driver_name' => Drivers::find($request->driver_id)->name,
            'delivery_person' => Drivers::find($request->delivery_person_id)->name,
            'products' => json_encode($products->toArray()),
            'is_with_sf' => $request->with_sf == 'on' ? 1 : NULL,
        ]);

        if ($request->boolean('bad_order')) {
            $inbound->bad_order_id = $request->bad_order_id;
            $inbound->bo_amount = $request->bo_amount;
        }

        foreach ($products as $product) {
            $itemData = ItemMasterData::branch($branchCode)->productCode($product->code)->first();
            if ($itemData) {

                if($inbound->delivery_receipt_id !== NULL){
                    $newStocks = ($itemData->stocks + $product->old_quantity) - $product->quantity;

                    if($product->status === 'Deleted'){
                        $newStocks = $itemData->stocks + $product->old_quantity;
                    }

                    $itemData->stocks = $newStocks;
                }else{
                    $newReserved = ($itemData->reserved - $product->old_quantity) + $product->quantity;

                    if ($product->status === 'Deleted') {
                        $newReserved = $itemData->reserved - $product->old_quantity;
                    }
                    $itemData->reserved = $newReserved;
                }

                $itemData->save();
            }
        }

        $activeProducts = array_filter($products->toArray(), function ($product) {
            return $product['status'] !== 'Deleted';
        });

        $inbound->products = $activeProducts;
        $inbound->customer_name = $customer->fullName;
        $inbound->save();

        session()->forget(['inboundId', 'products']);

        activity('outbound')
            ->performedOn($inbound)
            ->log("Outbound $inbound->id updated by " . auth()->user()->fullName);

        NewInboundProduct::where('inbound_id', $inbound->id)->delete();

        if (!empty($errors)) {
            return back()->withErrors($errors);
        }

        return redirect()->route('order.index')->with('success', 'The order has been updated.');
    }

    private function getDifferentProducts($products, $oldProducts)
    {
        $newProducts =  array_udiff($products, $oldProducts, function ($a, $b) {
            $codeComparison = $a['code'] <=> $b['code'];
            return $codeComparison === 0 ? ($a['quantity'] <=> $b['quantity']) : $codeComparison;
        });


        foreach ($newProducts as $key => $product) {
            $oldProduct = array_values(array_filter($oldProducts, function ($oldProduct) use ($product) {
                return $oldProduct['code'] === $product['code'];
            }))[0] ?? null;

            if ($oldProduct) {
                $newProducts[$key]['old_quantity'] = $oldProduct['quantity'];
            }
        }

        return $newProducts;
    }

    private function updateItemMasterData($differentProducts, $branchCode, $inboundId)
    {
        $errors = [];
        $inbound = Inbound::find($inboundId);
        $hasDeliveryReceipt = $inbound->delivery_receipt_id !== NULL;

        foreach ($differentProducts as $product) {
            $itemData = ItemMasterData::branch($branchCode)->productCode($product['code'])->first();
            if (!$itemData) {
                $errMsg = "Product {$product['code']} not found in ItemMasterData. Order $inboundId failed.";
                activity('outbound-update')->log($errMsg);
                $errors[] = $errMsg;
                continue;
            }

            if (isset($product['old_quantity'])) {
                // Modified existing product
                $quantityDifference = $product['quantity'] - $product['old_quantity'];
                if (!$hasDeliveryReceipt) {
                    $itemData->reserved += $quantityDifference;
                } else {
                    $itemData->stocks -= $quantityDifference;
                }
            } else {

                if (!$hasDeliveryReceipt) {
                    $itemData->reserved += $product['quantity'];
                } else {
                    $itemData->stocks -= $product['quantity'];
                }
            }

            $itemData->save();
        }

        $deletedProducts = session()->get('deleted_products');
        if ($deletedProducts) {
            foreach ($deletedProducts as $product) {
                $itemData = ItemMasterData::branch($branchCode)->productCode($product["code"])->first();
                if ($itemData) {
                    if (!$hasDeliveryReceipt) {
                        $itemData->reserved -= $product['quantity'];
                    } else {
                        $itemData->stocks += $product['quantity'];
                    }
                    $itemData->save();
                }
            }
        }

        return $errors;
    }

    public function freeOrders()
    {
        $inbounds = Inbound::with('driver', 'vehicle')->branch(session('branch_code'))->freeOrders()->get();
        return view('free', compact('inbounds'));
    }

    public function paidOrders()
    {
        $inbounds = Inbound::with('driver', 'vehicle')->branch(session('branch_code'))->paidOrders()->get();
        return view('paid', compact('inbounds'));
    }
}
