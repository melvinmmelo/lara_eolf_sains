<?php

namespace App\Http\Controllers;

use App\Models\Inbound;
use App\Http\Requests\StoreInboundRequest;
use App\Http\Requests\UpdateInboundRequest;
use App\Models\Drivers;
use App\Models\Equipment;
use App\Models\pricelevels;
use App\Models\prices;
use App\Models\Product;
use App\Models\ProductType;
use App\Models\TempInbound;
use App\Models\Vehicles;
use App\Services\InboundProductsService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Session;

class InboundController extends Controller
{

    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        //

        // session()->put('inboundId', 2);

        $equipment = Equipment::all();
        $drivers = Drivers::all();
        $vehicles = Vehicles::all();

        $inbounds = Inbound::with('driver','vehicle')->get();

        return view('order', compact('equipment', 'drivers', 'vehicles', 'inbounds'));
    }

    public function submitProcessOne(Request $request)
    {

        $request->validate([
            'equipment' => 'required',
            'deliveryPerson' => 'required',
            'vehicle' => 'required',
        ]);


        $tempInbound = new Inbound();
        $tempInbound->user_id = auth()->user()->id;
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

        if($inbound->status == 'Completed') {
            session()->forget('inboundId');
            return redirect()->route('order.index')->withErrors('Your order has been completed.');
        }

        $equipment = Equipment::select('name')->find($inbound->equipment_id);
        $deliveryPerson = Drivers::select('name')->find($inbound->driver_id);
        $vehicle = Vehicles::select('plateno')->find($inbound->vehicle_id);

        $defaultPriceLevel = pricelevels::where('pl_status', 'Active')->select('pl_name')->first();

        $productTypes = ProductType::where('is_active', 'on')->get();

        return view('ordering', compact('inboundId','productTypes', 'equipment', 'deliveryPerson', 'vehicle', 'defaultPriceLevel'));

    }

    // ajax products list
    public function ajaxProductList($code)
    {

        $allProducts = Product::where('product_type_code', $code)->get();

        $products = [];

        // get the latest price
        foreach ($allProducts as $product) {
            $price = prices::where('p_code', $product->code)->orderBy('created_at', 'desc')->first();

            $t = ['code' => $product->code, 'price' => $product->price, 'unit' => $price->p_unit, 'qty' => $price->p_quant];

            array_push($products, $t);
        }

        return view('productsList', compact('products'));
    }

    // ajax inbound products
    // per product na ito ha, ito na yung table ng product, yung may details
    public function ajaxInboundList($code)
    {
        if(session()->has('inboundId')) {
            $inboundId = session()->get('inboundId');
        }

        $inbound = Inbound::find($inboundId);
        $product = Product::where('code', $code)->first();
        $price = prices::where('p_code', $code)->first();

        $data = ['ptype_code' => $product->product_type_code, 'code' => $product->code, 'quantity' => 1, 'price' => $price->p_price, 'unit' => $price->unit];

        $products = json_decode($inbound->products, true);

        $isExist = false;

        $inProdService = new InboundProductsService($products);

        $products = $inProdService->addQty($code);

        $isExist = $inProdService->isExist();

        $summary = array_values($inProdService->summary());

        if($isExist == false and $products) {
            array_push($products, $data);
        }else if($products == null) {
            $products = [];
            array_push($products, $data);
        }

        $uiProducts = $products;

        $inbound->products = json_encode($products);
        $inbound->save();

        return view('inboundList', compact('inbound', 'uiProducts', 'summary'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'inboundId' => 'required|exists:inbounds,id',
        ]);

        $inbound = Inbound::find($request->inboundId);

        $inbound->status = 'Completed';

        $inbound->save();

        $updatingData = [];

        $inboundProducts = json_decode($inbound->products, true);
        foreach ($inboundProducts as $product) {
            $message = 'Failed';

            $price = prices::where('p_code', $product['code'])->orderBy('created_at', 'desc')->first();

            $price->p_quant += $product['quantity'];
            $price->save();

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
        if(session()->has('inboundId')) {
            $inboundId = session()->get('inboundId');
        } else {
            return redirect()->route('order.index');
        }

        $inbound = Inbound::find($inboundId);

        $inboundService = new InboundProductsService(json_decode($inbound->products, true));

        if($action == 'add'){
            $products = $inboundService->addQty($code);
        }else{
            $products = $inboundService->minQty($code);
        }

        $inbound->products = json_encode($products);

        $inbound->save();

    }

    // create delete inbound function
    public function destroy($inbound)
    {
        $inbound = Inbound::destroy($inbound);
        return redirect()->route('order.index')->with('success', 'An inbound has been deleted.');

    }
}
