<?php
// app/Http/Controllers/DeliveryReceiptController.php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\DeliveryReceipt;
use Illuminate\Support\Facades\DB;
use App\Models\Inbound;
use App\Models\ItemMasterData;
use App\Services\InboundProductsService;
use App\Services\InboundService;

class DeliveryReceiptController extends Controller
{

    public function updateDRPrintedDate($id)
    {
        $deliveryReceipt = DeliveryReceipt::findOrFail($id);

        $inbound = Inbound::find($deliveryReceipt->inbound_id);


        if ($inbound->delivery_receipt_id == null or $inbound->delivery_receipt_id == 0) {

            $inbound->delivery_receipt_id = $id;

            $inbound->save();

            $deliveryReceipt->printed_date = now();

            $deliveryReceipt->save();

            activity('delivery-receipt')
                ->performedOn($deliveryReceipt)
                ->log("DR $deliveryReceipt->id printed by " . auth()->user()->fullName);

            return response()->json(['message' => 'Printed date updated.']);
        } else {

            if ($inbound->delivery_receipt_id !== null && $deliveryReceipt->printed_date === null) {
                $deliveryReceipt->printed_date = now();
                $deliveryReceipt->save();
            }

            return response()->json(['message' => 'Printed date already updated.']);
        }
    }

    public function index(Request $request)
    {

        $nextDay = date('Y-m-d', strtotime('+1 day', strtotime(date('Y-m-d'))));

        $outbounds = Inbound::branch(session('branch_code'))->whereNull('delivery_receipt_id')->withProducts()->notDRYet()->get();

        $query = DeliveryReceipt::query();

        if ($request->filled('from_date') && $request->filled('to_date')) {
            $fromDate = $request->from_date;
            $toDate = $request->to_date;
            $query->whereBetween('date', [$fromDate, $toDate])
                ->whereNull('printed_date')
                ->whereHas('inbound', function ($query) {
                    $query->where('branch_code', session('branch_code'));
                });
        } else {

            $query->whereNull('printed_date')->whereHas('inbound', function ($query) {
                $query->where('branch_code', session('branch_code'));
            });
        }

        $deliveryReceipts = $query->get();


        return view('deliveryreceipt', compact('deliveryReceipts', 'outbounds', 'nextDay'));
    }

    public function indexDone(Request $request)
    {
        $outbounds = Inbound::branch(session('branch_code'))->withProducts()->get();

        $query = DeliveryReceipt::query();

        if ($request->filled('from_date') && $request->filled('to_date')) {
            $fromDate = $request->from_date;
            $toDate = $request->to_date;
            $query->whereBetween('date', [$fromDate, $toDate])
                ->whereNotNull('printed_date')
                ->whereHas('inbound', function ($query) {
                    $query->where('branch_code', session('branch_code'));
                });
        } else {
            $query->whereNotNull('printed_date')->whereHas('inbound', function ($query) {
                $query->where('branch_code', session('branch_code'));
            });
        }

        $deliveryReceipts = $query->get();


        return view('deliveryreceipt_done', compact('deliveryReceipts', 'outbounds'));
    }


    public function store(Request $request)
    {
        $validatedData = $request->validate([
            'date' => 'required|date',
            'inbound_id' => 'required|string',
            'generated_by' => 'required|string',
            'discount' => 'required|numeric',
            'is_fixed_amount' => 'nullable'
        ]);


        $inbound = Inbound::findOrFail($request->inbound_id);

        $isExisting = DeliveryReceipt::where('inbound_id', $validatedData['inbound_id'])->first();
        if ($isExisting) {
            return redirect()->route('deliveryreceipt.index')->withErrors('Delivery receipt already exists.');
        }

        $validatedData['branch_code'] = session('branch_code');

        if (empty(session('branch_code'))) {
            return redirect()->route('deliveryreceipt.index')->withErrors('Branch code not found.');
        }

        if ($inbound->products == null) {
            return redirect()->route('deliveryreceipt.index')->withErrors('No products found.');
        }

        $validatedData['customer_name'] = $inbound->customer_name;

        $validatedData['code'] = DeliveryReceipt::generateCode(session('branch_code'));

        $deliveryReceipt = DeliveryReceipt::create($validatedData);

        $totalOfOutbound = InboundService::getTotalOfInboundProducts($deliveryReceipt->inbound_id);


        $deliveryReceipt->save();

        $products = json_decode($inbound->products, true);

        if ($products == null) {
            $products = [];
        } else {

            foreach ($products as $product) {

                $mainItem = ItemMasterData::branch(session('branch_code'))->productCode($product['code'])->first();
                $mainItem->stocks = max(0, $mainItem->stocks - $product['quantity']);
                $mainItem->reserved = max(0, $mainItem->reserved - $product['quantity']);
                $mainItem->save();
            }
        }

        $inbound->delivery_receipt_id = $deliveryReceipt->id;

        if ($request->is_fixed_amount && $request->is_fixed_amount == "1") {
            $discount_type = 'Php ' . $request->discount;
            $totalDiscount = $request->discount;
        } else {
            $discount_type = $request->discount . '%';
            $totalDiscount = $totalOfOutbound * ($request->discount / 100);
        }

        if ($request->discount != 0) {
            $inbound->discount_details = $discount_type;
            $inbound->discount = $totalDiscount;
        }

        $inbound->save();

        activity('delivery-receipt')
            ->performedOn($deliveryReceipt)
            ->log("DR $deliveryReceipt->id created by $deliveryReceipt->generated_by.");

        return redirect()->route('drprint', ['id' => $deliveryReceipt->id]);
    }

    public function update(Request $request){

        $request->validate([
            'dr_id' => 'required',
            'discount' => 'required|numeric',
        ]);

        $deliveryReceipt = DeliveryReceipt::findOrFail($request->dr_id);

        $inbound = Inbound::findOrFail($deliveryReceipt->inbound_id);

        $discount_type = $request->discount . '%';
        $totalDiscount = $inbound->grandTotal * ($request->discount / 100);

        $inbound->discount_details = $discount_type;
        $inbound->discount = $totalDiscount;

        $inbound->save();

        return redirect()->route('deliveryreceipt.indexDone');
    }

    public function show($id)
    {
        $deliveryReceipt = DeliveryReceipt::findOrFail($id);

        $inbound = Inbound::findOrFail($deliveryReceipt->inbound_id);

        $inboundService = new InboundProductsService($inbound->products);
        $summary = $inboundService->summary();
        $products = $inboundService->addSppbinSummary();
        return view('drprint', compact('deliveryReceipt', 'products', 'inbound'));
    }
}
