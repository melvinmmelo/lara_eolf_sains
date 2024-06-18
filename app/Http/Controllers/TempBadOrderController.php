<?php

namespace App\Http\Controllers;

use App\Models\TempBadOrder;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Session;

class TempBadOrderController extends Controller
{
    public function store(Request $request)
    {
        $validated = $request->validate([
            'customer_id' => 'required|exists:customers,id',
            'store_id' => 'required|exists:storeinfo,id',
            'ptype_code' => 'required|string',
            'code' => 'required|string',
            'description' => 'required|string',
            'quantity' => 'required|integer',
            'price' => 'required|numeric',
            'unit' => 'required|string',
            'amount' => 'required|numeric',
        ]);

        // Add session_id to validated data
        $validated['session_id'] = session()->getId();

        TempBadOrder::create($validated);

        return response()->json(['message' => 'Item saved to temporary table'], 200);
    }

    public function clear()
    {
        TempBadOrder::where('session_id', session()->getId())->delete();

        return response()->json(['message' => 'Temporary table cleared'], 200);
    }
}
