<?php

namespace App\Http\Controllers;

use App\Models\pricelevels;
use Illuminate\View\View;
use Illuminate\Http\Request;

class PricelevelsController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $pricelevels = pricelevels::branch(session('branch_code'))->get();
        // Pass the vehicles data to the view
        return view('pricing-level', compact('pricelevels'));
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
        // dd($request->all());
        $request->validate([
            'branch_code' => 'required',
            'name' => 'required',
            'Description' => 'required',
            'branch_code' => 'required',
            'priceType' => 'required',
            // Add more validation rules as needed
        ]);

        $status = 'ACTIVE';

        // Check if the request data is 'on'
        //  if ($request->status === 'on') {
        //      $status = 'ACTIVE';
        //  }

        switch($request->priceType){
            case 'BAD PRICING':
                $plName = 'BAD PRICING';
                break;
            case 'FACTORY PRICE':
                $plName = 'FACTORY PRICE';
                break;
            default:
                $plName = $request->name;
        }

        pricelevels::create([
            'branch_code' => $request->branch_code,
            'pl_name' => $plName,
            'pl_desc' => $request->Description,
            'pl_status' => $status,
            'pl_type' => $request->priceType,
            // Add more fields as needed
        ]);

        return redirect('/pricing-level/')->with('success', 'Pricing Level added successfully!');

    }

    /**
     * Display the specified resource.
     */
    public function show(pricelevels $pricelevels)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(pricelevels $pricelevels)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, pricelevels $pricelevels)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(pricelevels $pricelevels)
    {
        //
    }
}
