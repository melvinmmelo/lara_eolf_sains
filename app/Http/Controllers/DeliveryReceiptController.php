<?php
// app/Http/Controllers/DeliveryReceiptController.php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\DeliveryReceipt;
use App\Models\Inbound;
use App\Models\ItemMasterData;
use App\Services\InboundService;

class DeliveryReceiptController extends Controller
{
    public function index(Request $request)
    {
        $query = DeliveryReceipt::query();

        $outbounds = Inbound::branch(session('branch_code'))->withProducts()->get();

        if ($request->filled('from_date') && $request->filled('to_date')) {
            $fromDate = $request->from_date;
            $toDate = $request->to_date;

            $query->whereBetween('date', [$fromDate, $toDate]);
        }

        $deliveryReceipts = $query->get();

        return view('deliveryreceipt', compact('deliveryReceipts', 'outbounds'));
    }
    public function store(Request $request)
    {
        $validatedData = $request->validate([
            'date' => 'required|date',
            'dr_no' => 'required|string',
            'generated_by' => 'required|string',
        ]);

        $inbound = Inbound::findOrFail($request->dr_no);

        if ($inbound->products == null) {
            return redirect()->route('deliveryreceipt.index')->withErrors('No products found.');
        }

        $deliveryReceipt = DeliveryReceipt::create($validatedData);

        $totalOfOutbound = InboundService::getTotalOfInboundProducts($deliveryReceipt->dr_no);

        $amountDueOrBalance = $totalOfOutbound - $inbound->delivered_amount;

        $deliveryReceipt->total_amount = $totalOfOutbound;

        $deliveryReceipt->bad_orders = $inbound->bo_amount;

        $deliveryReceipt->amount_due = $amountDueOrBalance;

        $deliveryReceipt->amount_paid = $inbound->delivered_amount;

        $deliveryReceipt->balance = $amountDueOrBalance;

        $deliveryReceipt->save();

        $products = json_decode($inbound->products, true);

        if ($products == null) {
            $products = [];

        } else {

            foreach ($products as $product) {

                $mainItem = ItemMasterData::productCode($product['code'])->first();
                $mainItem->stocks = $mainItem->stocks - $product['quantity'];
                $mainItem->reserved = $mainItem->reserved - $product['quantity'];
                $mainItem->save();
            }
        }

        return redirect()->route('drprint', ['id' => $deliveryReceipt->id]);
    }

    public function show($id)
    {
        // Fetch delivery receipt by ID
        $deliveryReceipt = DeliveryReceipt::findOrFail($id);
        $inbound = Inbound::findOrFail($deliveryReceipt->dr_no);
        return view('DRprint', compact('deliveryReceipt', 'inbound'));
    }
}
