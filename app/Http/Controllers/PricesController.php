<?php

namespace App\Http\Controllers;

use App\Models\prices;
use App\Models\pricelevels;
use App\Models\Product;
use App\Models\ProductType;
use App\Models\BadOrderPrice;
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

        if ($pricing->pl_type == 'BAD PRICING') {

            $request->validate([
                'product_type' => 'required',
                'price' => 'required',
            ]);

            // Check if bad order price already exists for this product type and price level
            $existingBadPrice = BadOrderPrice::where('price_level_id', $request->pricing_id)
                ->where('ptype_code', $request->product_type)
                ->first();

            if ($existingBadPrice) {
                return redirect('/pricing/')->withErrors('Bad order price already exists!');
            }

            // Get product type name
            $productType = ProductType::where('code', $request->product_type)->first();

            if (!$productType) {
                return redirect('/pricing/')->withErrors('Product type not found!');
            }

            // Save to bad_order_prices table
            BadOrderPrice::create([
                'ptype_code' => $request->product_type,
                'ptype_name' => $productType->name,
                'price_level_id' => $request->pricing_id,
                'price' => $request->price,
            ]);

            // Also save to prices table for backward compatibility
            prices::create([
                'pricelevel_id' => $request->pricing_id,
                'p_code' => $request->product_type,
                'p_unit' => 'Pc/s',
                'p_price' => $request->price,
            ]);
        } else {

            $request->validate([
                'product_type' => 'required',
                'price_unit' => 'required',
                'quant' => 'required',
                'price' => 'required',
            ]);

            $price = prices::where('pricelevel_id', $request->pricing_id)
                ->where('p_code', $request->product_type)
                ->first();

            if ($price) {
                return redirect('/pricing/')->withErrors('Price already exists for this product type!');
            }

            prices::create([
                'pricelevel_id' => $request->pricing_id,
                'p_code' => $request->product_type,
                'p_unit' => $request->price_unit,
                'p_quant' => $request->quant,
                'p_price' => $request->price,
            ]);
        }

        activity()
            ->causedBy(auth()->user())
            ->performedOn($pricing)
            ->withProperties($request->all())
            ->log('Price added.');


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

        if ($pricing->priceLevel->pl_type == 'BAD PRICING') {

            $request->validate([
                'e_price' => 'required|numeric',
            ]);

            $pricing->p_unit = 'Pc/s';
            $pricing->p_price = $request->e_price;
            $pricing->save();

            // Keep bad_order_prices in sync
            BadOrderPrice::where('price_level_id', $pricing->pricelevel_id)
                ->where('ptype_code', $pricing->p_code)
                ->update(['price' => $request->e_price]);
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

        activity()
            ->causedBy(auth()->user())
            ->performedOn($pricing)
            ->withProperties($request->all())
            ->log('Price updated.');

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
