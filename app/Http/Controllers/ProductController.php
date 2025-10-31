<?php

namespace App\Http\Controllers;

use App\Models\Product;
use App\Models\ProductType;
use App\Models\ProductVariant;
use Illuminate\Http\Request;

class ProductController extends Controller
{

    public function order()
    {
        return view('order');
    }

    public function ordering()
    {
        return view('ordering');
    }


    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        //
        $products = Product::with(['productType', 'productVariant'])->where('is_active', true)->get()->sortBy('productType.sequence_no');
        $archivedProducts = Product::with(['productType', 'productVariant'])->where('is_active', false)->get()->sortBy('productType.sequence_no');
        $types = ProductType::orderBy('sequence_no', 'asc')->get();
        $variants = ProductVariant::active()->get();

        return view('products', compact('products', 'archivedProducts', 'types', 'variants'));
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
            'product_type_code' => 'required|exists:product_types,code',
            'product_variant_code' => 'required|exists:product_variants,code',
        ]);

        $Product = new Product();
        $Product->product_type_code = $request->product_type_code;
        $Product->product_variant_code = $request->product_variant_code;
        $Product->save();

        return redirect()->back()->with('success', 'Data saved!');
    }

    public function toggleStatus(int $id)
    {
        $Product = Product::where('id',$id)->first();
        $Product->is_active = !$Product->is_active;
        $Product->save();

        return redirect()->back()->with('success', 'Data saved!');
    }

    /**
     * Display the specified resource.
     */
    public function show(Product $product)
    {
        //

    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Product $product)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request)
    {
        $request->validate([
            'product_code' => 'required|exists:products,code',
            'product_type_code' => 'required|exists:product_types,code',
            'product_variant_code' => 'required|exists:product_variants,code',
        ]);

        $Product = Product::where('code', $request->product_code)->first();
        $Product->code = $request->product_type_code . "_" . $request->product_variant_code;
        $Product->product_type_code = $request->product_type_code;
        $Product->product_variant_code = $request->product_variant_code;
        $Product->save();

        return redirect()->back()->with('success', 'Data saved!');

    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Product $product)
    {
        //
    }

    /**
     * Archive a product (set is_active to false)
     */
    public function archive(Request $request)
    {
        $request->validate([
            'product_code' => 'required|exists:products,code',
        ]);

        $product = Product::where('code', $request->product_code)->firstOrFail();
        $product->is_active = false;
        $product->save();

        activity()
            ->performedOn($product)
            ->withProperties([
                'product_code' => $product->code,
                'product_name' => $product->product_name,
                'action' => 'archived'
            ])
            ->log('Product archived: ' . $product->code);

        return redirect()->back()->with('success', 'Product archived successfully!');
    }

    /**
     * Restore an archived product (set is_active to true)
     */
    public function restore(Request $request)
    {
        $request->validate([
            'product_code' => 'required|exists:products,code',
        ]);

        $product = Product::where('code', $request->product_code)->firstOrFail();
        $product->is_active = true;
        $product->save();

        activity()
            ->performedOn($product)
            ->withProperties([
                'product_code' => $product->code,
                'product_name' => $product->product_name,
                'action' => 'restored'
            ])
            ->log('Product restored: ' . $product->code);

        return redirect()->back()->with('success', 'Product restored successfully!');
    }
}
