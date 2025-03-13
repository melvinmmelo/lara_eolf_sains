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
        $productTypes = ProductType::orderBy('sequence_no', 'asc')->get();
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
            'spoon_pcs_per_bag' => 'integer',
            'is_active' => 'nullable',
        ]);

        $productType = new ProductType();
        $productType->code = $request->code;
        $productType->name = $request->name;
        $productType->volume = $request->volume;
        $productType->spoon_pcs_per_bag = $request->spoon_pcs_per_bag ?? 0;
        $productType->is_active = $request->is_active == 'on' ? 1 : 0;
        $productType->save();

        activity()
            ->performedOn($productType)
            ->log('Product type created.');

        return redirect()->back()->with('success', 'Data saved!');
    }

    public function toggleStatus(string $id)
    {
        $productType = ProductType::find($id)->first();
        $productType->is_active = !$productType->is_active;
        $productType->save();

        return redirect()->back()->with('success', 'Data saved!');
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
    public function update(Request $request)
    {
        //
        $request->validate([
            'e_code' => 'string|max:190',
            'e_name' => 'required|string|max:190',
            'e_volume' => 'required|string|max:190',
            'e_spoon_pcs_per_bag' => 'integer',
            'e_is_active' => 'nullable',
        ]);

        // dd($request->all());

        $productType = ProductType::code($request->e_code)->first();
        $productType->name = $request->e_name;
        $productType->volume = $request->e_volume;
        $productType->spoon_pcs_per_bag = $request->e_spoon_pcs_per_bag;
        $productType->is_active = !$request->e_is_active;
        $productType->save();

        $changes = $productType->getChanges();

        activity()
            ->performedOn($productType)
            ->withProperties($changes)
            ->log('Product type updated.');

        return redirect()->back()->with('success', 'Data saved!');

    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        //
    }
}
