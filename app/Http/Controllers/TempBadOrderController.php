<?php

namespace App\Http\Controllers;

use App\Models\TempBadOrder;
use Illuminate\Http\Request;

class TempBadOrderController extends Controller
{
    public function store(Request $request)
    {
        $validated = $request->validate([
            'customer_id' => 'required|exists:customers,id',
            'inbound_id' => 'required|exists:inbounds,id',
            'store_id' => 'required|exists:storeinfo,id', // Change to store_id
            'ptype_code' => 'required|string',
            'code' => 'required|string',
            'description' => 'required|string',
            'quantity' => 'required|integer',
            'price' => 'required|numeric',
            'unit' => 'required|string',
            'amount' => 'required|numeric',
        ]);
    

        TempBadOrder::create($validated);

        return response()->json(['message' => 'Item saved to temporary table'], 200);
    }

    public function clear()
    {
        TempBadOrder::truncate();

        return response()->json(['message' => 'Temporary table cleared'], 200);
    }
}
