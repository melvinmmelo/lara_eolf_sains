<?php

namespace App\Http\Controllers;

use App\Models\ProductType;
use Illuminate\Http\Request;

class ProductTypeController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        //
        $productTypes = ProductType::all();
        return view('product-types', compact('productTypes'));
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
        //
        $request->validate([
            'code' => 'string|unique:product_types|max:190',
            'name' => 'required|string|max:190',
            'volume' => 'required|string|max:190',
            'is_active' => 'required',
        ]);

        $productType = new ProductTypes();
        $productType->code = $request->code;
        $productType->name = $request->name;
        $productType->volume = $request->volume;
        $productType->is_active = $request->is_active;
        $productType->save();

        return redirect()->back()->with('sucess', 'Data saved!');
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        //
    }
}
