<?php

namespace App\Http\Controllers;

use App\Models\prices;
use App\Models\pricelevels;
use App\Models\Product;
use App\Models\ProductType;
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

        $productTypes = ProductType::all()->sortBy('sequence_no');

        return view('pricing', compact('pricing', 'pricelevels', 'products', 'productTypes'));
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
        ]);

        $pricing = pricelevels::find($request->pricing_id);

        if (!$pricing) {
            return redirect('/pricing/')->withErrors('Pricing level not found!');
        }

        if ($pricing->pl_name == 'BAD PRICING') {

            $request->validate([
                'product_type' => 'required',
                'price' => 'required',
            ]);

            // check if pricelevel id and product type already exists
            $price = prices::where('pricelevel_id', $request->pricing_id)
                ->where('p_code', $request->product_type)
                ->first();

            if ($price) {
                return redirect('/pricing/')->withErrors('Price already exists!');
            }

            prices::create([
                'pricelevel_id' => $request->pricing_id,
                'p_code' => $request->product_type,
                'p_price' => $request->price,
            ]);
        } else {

            $request->validate([
                'price_code' => 'required',
                'price_unit' => 'required',
                'quant' => 'required',
                'price' => 'required',
            ]);

            // check if pricelevel id and product type already exists
            $price = prices::where('pricelevel_id', $request->pricing_id)
                ->where('p_code', $request->product_type)
                ->first();

            if ($price) {
                return redirect('/pricing/')->withErrors('Price already exists!');
            }

            prices::create([
                'pricelevel_id' => $request->pricing_id,
                'p_code' => $request->price_code,
                'p_unit' => $request->price_unit,
                'p_quant' => $request->quant,
                'p_price' => $request->price,
            ]);
        }


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
    public function update(Request $request)
    {
        $request->validate([
            'price_id' => 'required',
        ]);

        $pricing = prices::find($request->price_id);

        if ($pricing->priceLevel->pl_name == 'BAD PRICING') {

            $request->validate([
                'e_price' => 'required|numeric',
            ]);

            $pricing->p_price = $request->e_price;
            $pricing->save();
        } else {

            $request->validate([
                'price_id' => 'required',
                'e_quant' => 'required|numeric',
                'e_price_unit' => 'required',
                'e_price' => 'required|numeric',
            ]);

            if ($request->e_price_unit == '0') {
                return redirect('/pricing/')->withErrors('Please select a price unit!');
            }

            $pricing->update([
                'p_quant' => $request->e_quant,
                'p_unit' => $request->e_price_unit,
                'p_price' => $request->e_price,
            ]);
        }

        return redirect('/pricing/')->with('success', 'Price updated successfully!');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(prices $prices)
    {
        //
    }
}
