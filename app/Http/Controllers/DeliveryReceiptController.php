<?php
// app/Http/Controllers/DeliveryReceiptController.php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\DeliveryReceipt;
use App\Models\Inbound;

class DeliveryReceiptController extends Controller
{
    public function index(Request $request)
    {
        $query = DeliveryReceipt::query();

        // Filter by date range
        if ($request->filled('from_date') && $request->filled('to_date')) {
            $fromDate = $request->from_date;
            $toDate = $request->to_date;

            $query->whereBetween('date', [$fromDate, $toDate]);
        }

        $deliveryReceipts = $query->get();

        return view('deliveryreceipt', compact('deliveryReceipts'));
    }
    public function store(Request $request)
    {
        // Validate incoming request
        $validatedData = $request->validate([
            'date' => 'required|date',
            'dr_no' => 'required|string',
            'generated_by' => 'nullable|string',
            'total_amount' => 'nullable|numeric',
            'bad_orders' => 'nullable|numeric',
            'discount' => 'nullable|numeric',
            'amount_due' => 'nullable|numeric',
            'amount_paid' => 'nullable|numeric',
            'balance' => 'nullable|numeric',
        ]);

        // Create new delivery receipt
        $deliveryReceipt = DeliveryReceipt::create($validatedData);

        // Redirect to DRprint page with the ID of the newly created delivery receipt
        return redirect()->route('drprint', ['id' => $deliveryReceipt->id]);
    }

    public function show($id)
    {
        // Fetch delivery receipt by ID
        $deliveryReceipt = DeliveryReceipt::findOrFail($id);
        $inbound = Inbound::findOrFail($id);
        return view('DRprint', compact('deliveryReceipt'));
    }
    
}
