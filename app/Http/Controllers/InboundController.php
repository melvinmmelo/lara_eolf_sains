<?php

namespace App\Http\Controllers;

use App\Models\BadOrder;
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

        $total = InboundService::getTotalOfInboundProducts($inbound->id);

        if ($request->delivered_amount == $inbound->totalAmount) {
            $inbound->status = "Paid";
        }

        if ($request->delivered_amount > $total) {
            return redirect()->route('order.index')->withErrors('Delivered amount is greater than the total amount.');
        }


        $inbound->payment_type = $request->payment_type;
        $inbound->ref_no = $request->ref_no;
        $inbound->delivered_amount = $request->delivered_amount;

        $inbound->save();

        $changes = ['payment_type' => $request->payment_type, 'ref_no' => $request->ref_no, 'delivered_amount' => $request->delivered_amount, 'status' => $request->status];

        activity('outbound')
            ->performedOn($inbound)
            ->withProperties($changes)
            ->log("Payment added to outbound $inbound->id amounting $request->delivered_amount by " . auth()->user()->fullName);

        return redirect()->route('order.index')->with('success', 'Payment has been added.');
    }

    public function deleteAInbound($pcode)
    {


        $products = json_encode(session()->get('products'));

        $summary = [];

        $inboundService = new InboundProductsService($products);

        $products = $inboundService->deleteProduct($pcode);

        $uiProducts = $products;

        $summary = [];

        if ($products) {

            $summary = $inboundService->summary();

            $summary = $inboundService->addSppbinSummary(); // ! you need to call summary() first before addSppbinSummary()
        }

        session()->put('products', $products);

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
    public function ajaxInboundList($code, $qty = 1, $pid)
    {
        if (!session()->has('pricelevelId')) {
            session()->put('pricelevelId', $pid);
        }


        $products = InboundProductsService::getInboundProducts();
        $summary = [];

        $product = Product::where('code', $code)->first();
        $price = prices::where('p_code', $code)->where('pricelevel_id', $pid)->first();
        if ($price == null) {
            return response()->json(['error' => 'Price not found.']);
        }

        $item = ItemMasterData::branch(session('branch_code'))->productCode($code)->first();

        $sequence_no = ProductType::code($product->product_type_code)->pluck('sequence_no')->first();


        $data = ['order' => $sequence_no, 'ptype_code' => $product->product_type_code, 'code' => $product->code, 'quantity' => $qty, 'price' => $price->p_price, 'unit' => $price->p_unit, 'sppb' => $product->spoon_pcs_per_bag, 'description' => $product->productName, 'created_at' => now()];


        $isExist = false;

        $inProdService = new InboundProductsService($products);
        $currentProduct = $inProdService->getCurrentQty($code);
        $newQty = $currentProduct + $qty;

        if ($newQty > $item->availableStocks) {
            return response()->json(['error' => 'Insufficient stocks.', 'current' => $currentProduct, 'available' => $item->availableStocks]);
        }


        try {
            $products = $inProdService->addQty($code, $qty);
        } catch (\Exception $e) {
            return response()->json(['error' => $e->getMessage()]);
        }

        $isExist = $inProdService->isExist();


        if ($isExist == false and $products) {
            array_push($products, $data);
        } else if ($products == null) {
            $products = [];
            array_push($products, $data);
        }

        session()->forget('products');
        session()->put('products', $products);

        $uiProducts = $products;

        usort($uiProducts, function ($a, $b) {
            return $a['order'] <=> $b['order'];
        });

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

        $products = session()->get('products');

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
        $inbound->branch_code = session('branch_code');
        $inbound->equipment_id = $equipStore->equipment->id;
        $inbound->store_id = $equipStore->store_id;
        $inbound->driver_id = $request->driver_id;
        $inbound->delivery_person_id = $request->delivery_person_id;
        $inbound->vehicle_id = $request->vehicle_id;
        $inbound->products = json_encode($products);
        $inbound->status = 'Completed';
        $inbound->pricelevel_id = $request->pricelevel_id;
        $inbound->customer_id = $request->customer_id;
        $inbound->status = 'Completed';

        $inbound->degic_no = $equipStore->equipment->code;
        $inbound->customer_name = $customer->fullName;
        $inbound->store_name = $equipStore->store->storename;
        $inbound->driver_name = $driver->name;
        $inbound->delivery_person = $deliveryPerson->name;
        $inbound->vehicle_no = $vehicles->plateno;
        $inbound->with_invoice = $request->with_invoice == 'on' ? 1 : NULL;

        $bad_order = $request->bad_order === 'on' ? 1 : 0;
        $is_foc = $request->is_foc === 'on' ? 1 : NULL;
        $inbound->is_foc = $is_foc;


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

        session()->put('updatingDataResults', $updatingData);

        session()->forget('products');

        activity('outbound')
            ->performedOn($inbound)
            ->log("Outbound $inbound->id created by " . auth()->user()->fullName);

        session('errors', $errors);

        return redirect()->route('order.index')->with('success', 'Your order has been completed.');
    }

    public function update(Request $request, $code, $action)
    {

        $products = InboundProductsService::getInboundProducts();

        if ($products == null) {
            return back()->withErrors('Please add products.');
        }

        $inboundService = new InboundProductsService($products);
        $currentProduct = $inboundService->getCurrentQty($code);

        $item = ItemMasterData::branch(session('branch_code'))->productCode($code)->first();
        if ($action == 'add' && ($currentProduct + 1) > $item->availableStocks) {
            return response()->json(['error' => 'Insufficient stocks.']);
        }


        if ($action == 'add') {
            try {
                $products = $inboundService->addQty($code, 1);
            } catch (\Exception $e) {
                return response()->json(['error' => $e->getMessage()]);
            }
        } else {
            try {
                $products = $inboundService->minQty($code);
            } catch (\Exception $e) {
                return response()->json(['error' => $e->getMessage()]);
            }
        }

        usort($products, function ($a, $b) {
            return $a['order'] <=> $b['order'];
        });

        session()->forget('products');
        session()->put('products', $products);

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

                if ($inbound->delivery_receipt_id == NULL) {
                    $newReserved = $itemData->reserved - $product['quantity'];
                }

                if ($newReserved < 0) {
                    $errors[] = "Product $product[code] has negative reserved stocks.";
                    $newReserved = 0;
                }

                $newStocks = $itemData->stocks + $product['quantity'];
                if ($newStocks < 0) {
                    $errors[] = "Product $product[code] has negative stocks.";
                    $newStocks = 0;
                }

                if ($inbound->delivery_receipt_id == NULL) {
                    $itemData->reserved = $newReserved;
                }

                $itemData->stocks = $newStocks;
                $itemData->save();
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

        activity('outbound')
            ->performedOn($inbound)
            ->log("Inbound $inbound deleted by " . auth()->user()->fullName);

        return redirect()->route('order.index')->with('success', 'The order has been deleted.');
    }

    public function create()
    {

        session()->forget('products');
        session()->forget('inboundId');
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

        session()->put('inboundId', $inboundId);

        $inbound = Inbound::find($inboundId);

        if ($inbound->delivery_receipt_id !== NULL and !auth()->user()->hasRole(['admin'])) {
            return back()->withErrors('This order is already delivered.');
        }

        if ($inbound->balance === 0) {
            return back()->withErrors('This order is already paid.');
        }

        $products = $inbound->products;

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

        session()->put('products', $inboundList);
        session()->put('old_products', $inboundList);


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

        return view('ordering-view', compact('inbound', 'inboundId', 'drivers', 'inboundList', 'summary', 'priceLevel'));
    }

    public function updateInbound(Request $request)
    {
        $branchCode = session('branch_code');
        $products = session('products');
        $oldProducts = session('old_products');

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
        ]);

        $equipStore = EquipmentStore::findOrFail($request->equipment_id);
        $inbound = Inbound::findOrFail($request->inbound_id);

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
            'products' => json_encode($products),
        ]);

        if ($request->boolean('bad_order')) {
            $inbound->bad_order_id = $request->bad_order_id;
            $inbound->bo_amount = $request->bo_amount;
        }

        $differentProducts = $this->getDifferentProducts($products, $oldProducts);
        $errors = $this->updateItemMasterData($differentProducts, $branchCode, $inbound->id);

        $inbound->save();

        session()->forget(['inboundId', 'products']);

        activity('outbound')
            ->performedOn($inbound)
            ->log("Outbound $inbound->id updated by " . auth()->user()->fullName);

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

        foreach ($differentProducts as $product) {
            $itemData = ItemMasterData::branch($branchCode)->productCode($product['code'])->first();
            if (!$itemData) {
                $errMsg = "Product {$product['code']} not found in ItemMasterData. Order $inboundId failed.";
                activity('outbound-update')->log($errMsg);
                $errors[] = $errMsg;
            } else {
                if (isset($product['old_quantity'])) {

                    if($inbound->delivery_receipt_id == NULL){
                        $itemData->reserved -= $product['old_quantity'];
                    }

                }
                $itemData->reserved += $product['quantity'];
                $itemData->save();
            }
        }

        $deletedProducts = session()->get('deleted_products');
        if ($deletedProducts) {
            foreach ($deletedProducts as $product) {
                $itemData = ItemMasterData::branch($branchCode)->productCode($product["code"])->first();
                if ($itemData) {

                    if($inbound->delivery_receipt_id == NULL){
                        $itemData->reserved -= $product['quantity'];
                    }

                    $itemData->save();
                }
            }
        }
        return $errors;
    }
}
