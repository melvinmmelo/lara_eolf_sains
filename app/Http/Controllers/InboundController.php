<?php

namespace App\Http\Controllers;

use App\Models\BadOrder;
use App\Models\Customers;
use App\Models\Inbound;
use App\Models\Drivers;
use App\Models\Equipment;
use App\Models\EquipmentStore;
use App\Models\ItemMasterData;
use App\Models\pricelevels;
use App\Models\prices;
use App\Models\Product;
use App\Models\ProductType;
use App\Models\Vehicles;
use App\Services\InboundProductsService;
use App\Services\InboundService;
use Illuminate\Http\Request;

class InboundController extends Controller
{

    public function addPayment(Request $request)
    {
        $request->validate([
            'ob_id' => 'required|exists:inbounds,id',
            'payment_type' => 'required',
            'ref_no' => 'required|max:30',
            'delivered_amount' => 'numeric|required',
        ]);

        $inbound = Inbound::findOrFail($request->ob_id);

        $total = InboundService::getTotalOfInboundProducts($inbound->id);

        if ($request->delivered_amount > $total) {
            return redirect()->route('order.index')->withErrors('Delivered amount is greater than the total amount.');
        }

        // if($inbound->status == 'Completed'){
        //     return redirect()->route('order.index')->withErrors('Your order has been completed.');
        // }

        $inbound->payment_type = $request->payment_type;
        $inbound->ref_no = $request->ref_no;
        $inbound->delivered_amount = $request->delivered_amount;
        $inbound->status = $request->status;

        $inbound->save();

        $changes = ['payment_type' => $request->payment_type, 'ref_no' => $request->ref_no, 'delivered_amount' => $request->delivered_amount, 'status' => $request->status];

        activity('outbound')
            ->performedOn($inbound)
            ->withProperties($changes)
            ->log('Payment added.');

        return redirect()->route('order.index')->with('success', 'Payment has been added.');
    }

    // delete product from the list
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

        session()->forget('products');

        session()->put('products', $products);

        return view('inboundList', compact('uiProducts', 'summary'));
    }

    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $drivers = Drivers::active()->get();

        $vehicles = Vehicles::active()->get();

        $inbounds = Inbound::with('driver', 'vehicle')->branch(session('branch_code'))->get();

        $equipment = EquipmentStore::all();

        $pricing = pricelevels::getPriceLevels(session('branch_code'));

        $inbounds->map(function ($inbound) {
            $inbound->is_with_badOrder = InboundService::isWithBadOrder($inbound->id);
            return $inbound;
        });

        return view('order', compact('equipment', 'drivers', 'vehicles', 'inbounds', 'pricing'));
    }

    public function submitProcessOne(Request $request)
    {

        $request->validate([
            'branch_code' => 'required',
            'equipment' => 'required',
            'deliveryPerson' => 'required',
            'vehicle' => 'required',
            'pricelevel_id' => 'required',
            'customer_id' => 'required',
        ]);

        $equipStore = EquipmentStore::find($request->equipment);

        $tempInbound = new Inbound();
        $tempInbound->user_id = auth()->user()->id;
        $tempInbound->branch_code = $request->branch_code;
        $tempInbound->equipment_id = $equipStore->equipment->id;
        $tempInbound->driver_id = $request->deliveryPerson;
        $tempInbound->vehicle_id = $request->vehicle;
        $tempInbound->products = null;
        $tempInbound->status = 'Encoding';
        $tempInbound->pricelevel_id = $request->pricelevel_id;
        $tempInbound->customer_id = $request->customer_id;
        $tempInbound->store_id = $equipStore->store_id;
        $tempInbound->save();

        session()->put('pricelevelId', $request->pricelevel_id);

        $inboundId = $tempInbound->id;

        session()->put('inboundId', $inboundId);

        $data = json_encode($request->except('_token'));

        session()->put('orderDetails', $data);

        activity('outbound')
            ->performedOn($tempInbound)
            ->log('Order created.');


        return redirect()->route('order.processTwo', ['inbound' => $inboundId]);
    }

    public function orderProcessTwoUI($inbound) // adding products in order
    {

        $inboundId = $inbound;

        session()->put('inboundId', $inboundId);

        $inbound = Inbound::find($inboundId);

        if ($inbound->status == 'Completed') {
            session()->forget('inboundId');
            return redirect()->route('order.index')->withErrors('Your order has been completed.');
        }

        $equipment = Equipment::find($inbound->equipment_id);
        $customerName = Customers::find($inbound->customer_id)->fullName;
        $branchCode = session('branch_code');

        $equipmentSerial = $equipment->serial_no;
        $deliveryPerson = Drivers::select('name')->find($inbound->driver_id);

        $vehicle = Vehicles::select('plateno')->find($inbound->vehicle_id);

        $defaultPriceLevel = pricelevels::find($inbound->pricelevel_id);
        session()->put('pricelevelId', $inbound->pricelevel_id);

        $productTypes = ProductType::where('is_active', 1)->orderBy('sequence_no', 'asc')->get();

        // check if inbound has products
        $inboundList = [];
        $summary = [];

        if ($inbound->products) {
            $inboundList = json_decode($inbound->products, true);

            $inboundService = new InboundProductsService($inbound->products);

            $summary = $inboundService->summary();

            $summary = $inboundService->addSppbinSummary(); // ! you need to call summary() first before addSppbinSummary()
        }


        return view('ordering', compact('inboundId', 'productTypes', 'deliveryPerson', 'vehicle', 'defaultPriceLevel', 'inboundList', 'summary', 'customerName', 'equipmentSerial', 'inbound'));
    }

    // ajax products list
    public function ajaxProductList($code)
    {

        $pricelevelId = session('pricelevelId');
        $branchCode = session('branch_code');

        $allProducts = Product::where('product_type_code', $code)->get();

        $products = [];

        // get the latest price
        foreach ($allProducts as $product) {

            $price = prices::getPricePerPriceLevelAndPCode(session('pricelevelId'), $product->code);

            $item = ItemMasterData::branch(session('branch_code'))->productCode($product->code)->first();

            $stocks = $item->stocks ?? 0;

            if ($stocks != 0) {

                $t = ['code' => $product->code, 'price' => $product->price, 'unit' => "0", 'qty' => $stocks];

                array_push($products, $t);
            }
        }

        return view('productsList', compact('products', 'pricelevelId', 'branchCode'));
    }

    // ajax inbound products
    // per product na ito ha, ito na yung table ng product, yung may details
    public function ajaxInboundList($code, $qty = 1, $pid)
    {
        if(!session()->has('pricelevelId')){
            session()->put('pricelevelId', $pid);
        }

        $products = InboundProductsService::getInboundProducts();
        $summary = [];

        $product = Product::where('code', $code)->first();
        $price = prices::where('p_code', $code)->where('pricelevel_id', $pid)->first();

        // get the first two characters of the product type code
        $sequence_no = ProductType::code($product->product_type_code)->pluck('sequence_no')->first();


        $data = ['order' => $sequence_no, 'ptype_code' => $product->product_type_code, 'code' => $product->code, 'quantity' => $qty, 'price' => $price->p_price, 'unit' => $price->p_unit, 'sppb' => $product->spoon_pcs_per_bag, 'description' => $product->productName, 'created_at' => now()];


        $isExist = false;

        $inProdService = new InboundProductsService($products);

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

        $request->validate([
            'pricelevel_id' => 'required',
            'customer_id' => 'required',
            'equipment_id' => 'required',
            'driver_id' => 'required',
            'vehicle_id' => 'required',
            'bad_order_id' => 'nullable',
            'bo_amount' => 'nullable',
        ]);

        $products = session()->get('products');

        // check if there are products
        if ($products == null) {
            return back()->withErrors('Please add products.');
        }

        $inbound = new Inbound();
        $inbound->user_id = auth()->user()->id;
        $inbound->branch_code = session('branch_code');
        $inbound->equipment_id = $request->equipment_id;
        $inbound->driver_id = $request->driver_id;
        $inbound->vehicle_id = $request->vehicle_id;
        $inbound->products = json_encode($products);
        $inbound->status = 'Completed';
        $inbound->pricelevel_id = $request->pricelevel_id;
        $inbound->customer_id = $request->customer_id;
        $inbound->store_id = EquipmentStore::find($request->equipment_id)->store_id;
        $inbound->status = 'Completed';

        $bad_order = $request->bad_order == 'on' ? 1 : 0;

        if ($bad_order == 1) {

            BadOrder::where('bo_id', $request->bad_order_id)->update(['is_active' => 0]);

            $inbound->bad_order_id = $request->bad_order_id;

            $inbound->bo_amount = $request->bo_amount;
        }

        $inbound->save();

        $updatingData = [];

        foreach ($products as $product) {

            $message = 'Failed';

            $itemData = ItemMasterData::branch(session('branch_code'))->productCode($product['code'])->first();

            $itemData->reserved += $product['quantity'];
            $itemData->save();

            $message = 'Success';

            $updatingData[] = ['code' => $product['code'], 'message' => $message];
        }

        session()->put('updatingDataResults', $updatingData);

        session()->forget('products');

        activity('outbound')
            ->performedOn($inbound)
            ->log('Order completed.');


        return redirect()->route('order.index')->with('success', 'Your order has been completed.');
    }

    public function update(Request $request, $code, $action)
    {

        $products = InboundProductsService::getInboundProducts();

        if ($products == null) {
            return back()->withErrors('Please add products.');
        }

        $inboundService = new InboundProductsService($products);

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

        session()->forget('products');
        session()->put('products', $products);

        $summary = $inboundService->summary();
        $summary = $inboundService->addSppbinSummary();

        return view('orderProductSum', compact('summary'));
    }

    // create delete inbound function
    public function destroy($inbound)
    {
        Inbound::destroy($inbound);

        activity('outbound')
            ->performedOn($inbound)
            ->log('Inbound deleted.');

        return redirect()->route('order.index')->with('success', 'An inbound has been deleted.');
    }

    public function create()
    {

        session()->forget('products');
        session()->forget('inboundId');


        $productTypes = ProductType::where('is_active', 1)->orderBy('sequence_no', 'asc')->get();

        $drivers = Drivers::active()->get();

        $vehicles = Vehicles::active()->get();

        $inbounds = Inbound::with('driver', 'vehicle')->branch(session('branch_code'))->get();

        $equipment = EquipmentStore::all();

        $pricing = pricelevels::getPriceLevels(session('branch_code'));

        $inbounds->map(function ($inbound) {
            $inbound->is_with_badOrder = InboundService::isWithBadOrder($inbound->id);
            return $inbound;
        });

        return view('ordering', compact('equipment', 'drivers', 'vehicles', 'inbounds', 'pricing', 'productTypes'));
    }

    public function edit($inboundId)
    {

        session()->put('inboundId', $inboundId);

        $inbound = Inbound::find($inboundId);

        $products = $inbound->products;

        // $products = InboundProductsService::getInboundProducts($inboundId);

        $drivers = Drivers::active()->get();

        $vehicles = Vehicles::active()->get();

        $equipment = EquipmentStore::all();

        $pricing = pricelevels::getPriceLevels(session('branch_code'));

        $productTypes = ProductType::where('is_active', 1)->orderBy('sequence_no', 'asc')->get();

        $productTypes = ProductType::where('is_active', 1)->orderBy('sequence_no', 'asc')->get();

        // check if inbound has products
        $inboundList = [];
        $summary = [];

        if ($products) {
            $inboundList = json_decode($products, true);
            if($inboundList == null){
                $inboundList = [];
            }

            $inboundService = new InboundProductsService($products);

            $summary = $inboundService->summary();

            $summary = $inboundService->addSppbinSummary(); // ! you need to call summary() first before addSppbinSummary()
        }

        return view('ordering-edit', compact('inbound', 'inboundId', 'equipment', 'drivers', 'vehicles', 'pricing', 'productTypes', 'inboundList', 'summary'));
    }

    public function updateInbound(Request $request){

        $request->validate([
            'inbound_id' => 'required|exists:inbounds,id',
            'pricelevel_id' => 'required',
            'customer_id' => 'required',
            'equipment_id' => 'required',
            'driver_id' => 'required',
            'vehicle_id' => 'required',
            'bad_order_id' => 'nullable',
            'bo_amount' => 'nullable',
        ]);

        $inbound = Inbound::find($request->inbound_id);
        $inbound->equipment_id = $request->equipment_id;
        $inbound->driver_id = $request->driver_id;
        $inbound->vehicle_id = $request->vehicle_id;
        $inbound->pricelevel_id = $request->pricelevel_id;
        $inbound->customer_id = $request->customer_id;
        $inbound->store_id = EquipmentStore::find($request->equipment_id)->store_id;
        $inbound->products = json_encode(session()->get('products'));

        if($request->bad_order == 'on'){
            $inbound->bad_order_id = $request->bad_order_id;
            $inbound->bo_amount = $request->bo_amount;
        }

        $inbound->save();

        session()->forget('inboundId');
        session()->forget('products');

        return redirect()->route('order.index')->with('success', 'Inbound has been updated.');

    }
}
