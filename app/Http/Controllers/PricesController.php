<?php

namespace App\Http\Controllers;

use App\Models\prices;
use App\Models\pricelevels;
use App\Models\Product;
use Illuminate\View\View;
use Illuminate\Http\Request;

class PricesController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {

        $branchCode = session('branch_code');

        $pricelevels = pricelevels::branch($branchCode)->get();

        $pricing = prices::whereHas('pricelevel', function ($query) use ($branchCode) {
            $query->where('branch_code', $branchCode);
        })->get();

        // Get all products

        $products = Product::all();

        // Pass the vehicles data to the view
        return view('pricing', compact('pricing', 'pricelevels', 'products'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $request->validate([
            'pricing_id' => 'required',
            'price_code' => 'required',
            'price_unit' => 'required',
            'quant' => 'required',
            'price' => 'required',
        ]);

        prices::create([
            'pricelevel_id' => $request->pricing_id,
            'p_code' => $request->price_code,
            'p_unit' => $request->price_unit,
            'p_quant' => $request->quant,
            'p_price' => $request->price,

            // Add more fields as needed
        ]);

        return redirect('/pricing/')->with('success', 'Pricing added successfully!');
    }

    /**
     * Display the specified resource.
     */
    public function show(prices $prices)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(prices $prices)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, prices $prices)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(prices $prices)
    {
        //
    }
}
