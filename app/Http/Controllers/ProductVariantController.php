<?php

namespace App\Http\Controllers;

use App\Models\ProductVariant;
use Illuminate\Http\Request;

class ProductVariantController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $productVariants = ProductVariant::all();
        return view('product-variants', compact('productVariants'));
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
            'code' => 'string|unique:product_types|max:190',
            'name' => 'required|string|max:190'
        ]);

        $productType = new ProductVariant();
        $productType->code = $request->code;
        $productType->name = $request->name;
        $productType->save();

        return redirect()->back()->with('sucess', 'Data saved!');
    }

    /**
     * Display the specified resource.
     */
    public function show(ProductVariant $productVariant)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(ProductVariant $productVariant)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, ProductVariant $productVariant)
    {
        //
        $request->validate([
            'e_code' => 'string|max:190',
            'e_name' => 'required|string|max:190',
        ]);

        $pvar = ProductVariant::find($request->e_code)->first();
        $pvar->name = $request->e_name;
        $pvar->save();

        return redirect()->back()->with('sucess', 'Data saved!');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(ProductVariant $productVariant)
    {
        //
    }
}
