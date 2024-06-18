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

        activity()
            ->performedOn($inbound)
            ->withProperties($changes)
            ->log('Payment added.');

        return redirect()->route('order.index')->with('success', 'Payment has been added.');
    }

    // delete product from the list
    public function deleteAInbound($inboundId, $pcode)
    {
        $inbound = Inbound::find($inboundId);

        $inboundService = new InboundProductsService($inbound->products);

        $products = $inboundService->deleteProduct($pcode);


        $uiProducts = $products;

        $summary = [];

        if ($inbound->products) {

            $summary = $inboundService->summary();

            $summary = $inboundService->addSppbinSummary(); // ! you need to call summary() first before addSppbinSummary()
        }

        $inbound->products = json_encode($products);

        $inbound->save();

        activity()
            ->performedOn($inbound)
            ->log('Order deleted.');

        return view('inboundList', compact('inboundId', 'inbound', 'uiProducts', 'summary'));
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

        activity()
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

            // return response()->json([$price, $item]);

            // if($item == null){
            //     break;
            //     return response()->json(['Not stock available.']);
            // }

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
    public function ajaxInboundList($code, $qty = 1)
    {
        if (session()->has('inboundId')) {
            $inboundId = session()->get('inboundId');
        }

        $inbound = Inbound::find($inboundId);
        $product = Product::where('code', $code)->first();
        $price = prices::where('p_code', $code)->first();

        // get the first two characters of the product type code
        $sequence_no = ProductType::code($product->product_type_code)->pluck('sequence_no')->first();


        $data = ['order' => $sequence_no, 'ptype_code' => $product->product_type_code, 'code' => $product->code, 'quantity' => $qty, 'price' => $price->p_price, 'unit' => $price->p_unit, 'sppb' => $product->spoon_pcs_per_bag, 'description' => $product->productName, 'created_at' => now()];

        $products = $inbound->products;

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

        $uiProducts = $products;

        usort($uiProducts, function ($a, $b) {
            return $a['order'] <=> $b['order'];
        });

        $inbound->products = json_encode($products);

        $summary = [];

        if ($inbound->save()) {
            $inProdService = new InboundProductsService($inbound->products);
            $summary = $inProdService->summary();
            $summary = $inProdService->addSppbinSummary();
        }

        return view('inboundList', compact('inboundId', 'inbound', 'uiProducts', 'summary'));
    }

    public function store(Request $request)
    {

        $request->validate([
            'inboundId' => 'required|exists:inbounds,id',
            'bad_order_id' => 'nullable',
            'bo_amount' => 'nullable',
        ]);
        // dd($request->all());


        $inbound = Inbound::find($request->inboundId);

        $inbound->status = 'Completed';

        $inbound->with_invoice = $request->with_invoice == 'on' ? 1 : 0;

        $inbound->bad_order = $request->bad_order == 'on' ? 1 : 0;

        if ($request->bad_order_id) {

            BadOrder::where('bo_id', $request->bad_order_id)->update(['is_active' => 0]);

            $inbound->bad_order_id = $request->bad_order_id;

            $inbound->bo_amount = $request->bo_amount;

        }

        $inbound->save();

        $updatingData = [];

        $inboundProducts = json_decode($inbound->products, true);



        foreach ($inboundProducts as $product) {

            $message = 'Failed';

            $itemData = ItemMasterData::branch(session('branch_code'))->productCode($product['code'])->first();

            $itemData->reserved += $product['quantity'];
            $itemData->save();

            $message = 'Success';

            $updatingData[] = ['code' => $product['code'], 'message' => $message];
        }

        session()->forget('inboundId');

        // create a session for variable updatingData
        session()->put('updatingDataResults', $updatingData);

        activity()
            ->performedOn($inbound)
            ->log('Order completed.');


        return redirect()->route('order.index')->with('success', 'Your order has been completed.');
    }

    public function update(Request $request, $inbound, $code, $action)
    {
        if (session()->has('inboundId')) {
            $inboundId = session()->get('inboundId');
        } else {
            return redirect()->route('order.index');
        }

        $inbound = Inbound::find($inboundId);

        $inboundService = new InboundProductsService($inbound->products);

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

        $inbound->products = json_encode($products);

        $summary = $inboundService->summary();

        $summary = $inboundService->addSppbinSummary();

        $inbound->save();

        activity()
            ->performedOn($inbound)
            ->log('Ordered products has been updated.');

        return view('orderProductSum', compact('summary'));
    }

    // create delete inbound function
    public function destroy($inbound)
    {
        Inbound::destroy($inbound);

        activity()
            ->performedOn($inbound)
            ->log('Inbound deleted.');

        return redirect()->route('order.index')->with('success', 'An inbound has been deleted.');
    }
}
