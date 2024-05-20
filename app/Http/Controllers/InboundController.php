<?php

namespace App\Http\Controllers;

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
use Illuminate\Http\Request;

class InboundController extends Controller
{

    // delete product from the list
    public function deleteAInbound($inboundId, $pcode)
    {
        $inbound = Inbound::find($inboundId);

        $inboundService = new InboundProductsService($inbound->products);

        $products = $inboundService->deleteProduct($pcode);


        $uiProducts = $products;

        $summary = [];

        if($inbound->products){

            $summary = $inboundService->summary();

            $summary = $inboundService->addSppbinSummary(); // ! you need to call summary() first before addSppbinSummary()
        }

        $inbound->products = json_encode($products);

        $inbound->save();

        return view('inboundList', compact('inboundId', 'inbound', 'uiProducts', 'summary'));

    }

    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $drivers = Drivers::all();

        $vehicles = Vehicles::all();

        $inbounds = Inbound::with('driver', 'vehicle')->branch(session('branch_code'))->get();

        $equipment = EquipmentStore::all();

        return view('order', compact('equipment', 'drivers', 'vehicles', 'inbounds'));
    }

    public function submitProcessOne(Request $request)
    {

        $request->validate([
            'branch_code' => 'required',
            'equipment' => 'required',
            'deliveryPerson' => 'required',
            'vehicle' => 'required',
        ]);

        $tempInbound = new Inbound();
        $tempInbound->user_id = auth()->user()->id;
        $tempInbound->branch_code = $request->branch_code;
        $tempInbound->equipment_id = $request->equipment;
        $tempInbound->driver_id = $request->deliveryPerson;
        $tempInbound->vehicle_id = $request->vehicle;
        $tempInbound->products = null;
        $tempInbound->status = 'Pending';
        $tempInbound->save();

        $inboundId = $tempInbound->id;

        session()->put('inboundId', $inboundId);

        $data = json_encode($request->except('_token'));

        session()->put('orderDetails', $data);


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

        $equipmentStore = EquipmentStore::find($inbound->equipment_id);
        $customerName = $equipmentStore->store->customer->fullName ?? 'N/A';
        $branchCode = session('branch_code');

        $equipmentSerial = $equipmentStore->equipment->serial_no;
        $deliveryPerson = Drivers::select('name')->find($inbound->driver_id);

        $vehicle = Vehicles::select('plateno')->find($inbound->vehicle_id);

        $defaultPriceLevel = pricelevels::where('branch_code', $branchCode)->select('pl_name')->orderBy('id', 'desc')->first();

        $productTypes = ProductType::where('is_active', 1)->get();

        // check if inbound has products
        $inboundList = [];
        $summary = [];

        if ($inbound->products) {
            $inboundList = json_decode($inbound->products, true);

            $inboundService = new InboundProductsService($inbound->products);

            $summary = $inboundService->summary();

            $summary = $inboundService->addSppbinSummary(); // ! you need to call summary() first before addSppbinSummary()
        }

        return view('ordering', compact('inboundId', 'productTypes', 'deliveryPerson', 'vehicle', 'defaultPriceLevel', 'inboundList', 'summary', 'customerName', 'equipmentSerial'));
    }

    // ajax products list
    public function ajaxProductList($code)
    {

        $allProducts = Product::where('product_type_code', $code)->get();

        $products = [];


        // get the latest price
        foreach ($allProducts as $product) {

            $price = prices::where('p_code', $product->code)->orderBy('created_at', 'desc')->first();

            if ($price == null) {
                break;
            } else {

                $t = ['code' => $product->code, 'price' => $product->price, 'unit' => "0", 'qty' => $price->p_quant];

                array_push($products, $t);
            }
        }

        // dd($products);

        return view('productsList', compact('products'));
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

        $data = ['ptype_code' => $product->product_type_code, 'code' => $product->code, 'quantity' => 1, 'price' => $price->p_price, 'unit' => $price->p_unit, 'sppb' => $product->spoon_pcs_per_bag];

        $products = $inbound->products;

        $isExist = false;

        $inProdService = new InboundProductsService($products);

        $products = $inProdService->addQty($code, $qty);

        $isExist = $inProdService->isExist();


        if ($isExist == false and $products) {
            array_push($products, $data);
        } else if ($products == null) {
            $products = [];
            array_push($products, $data);
        }

        $uiProducts = $products;

        $inbound->products = json_encode($products);

        $summary = [];

        if ($inbound->save()) {
            $inProdService = new InboundProductsService($inbound->products);
            $summary = $inProdService->summary();
            $summary = $inProdService->addSppbinSummary();
        }


        return view('inboundList', compact('inboundId','inbound', 'uiProducts', 'summary'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'inboundId' => 'required|exists:inbounds,id',

        ]);

        $inbound = Inbound::find($request->inboundId);

        $inbound->status = 'Completed';

        $inbound->with_invoice = $request->with_invoice == 'on' ? 1 : 0;

        $inbound->bad_order = $request->bad_order == 'on' ? 1 : 0;

        $inbound->save();

        $updatingData = [];

        $inboundProducts = json_decode($inbound->products, true);

        foreach ($inboundProducts as $product) {

            $message = 'Failed';

            $itemData = ItemMasterData::branch(session('branch_code'))->productCode($product['code'])->first();

            $itemData->stocks -= $product['quantity'];
            $itemData->save();

            $message = 'Success';

            $updatingData[] = ['code' => $product['code'], 'message' => $message];

        }

        session()->forget('inboundId');

        // create a session for variable updatingData
        session()->put('updatingDataResults', $updatingData);


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
            $products = $inboundService->addQty($code, 1);
        } else {
            $products = $inboundService->minQty($code);
        }

        $inbound->products = json_encode($products);

        $summary = $inboundService->summary();

        $summary = $inboundService->addSppbinSummary();

        $inbound->save();

        return view('orderProductSum', compact('summary'));
    }

    // create delete inbound function
    public function destroy($inbound)
    {
        $inbound = Inbound::destroy($inbound);
        return redirect()->route('order.index')->with('success', 'An inbound has been deleted.');
    }
}
