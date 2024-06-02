<?php

namespace App\Http\Controllers;

use App\Models\Drivers;
use App\Models\pricelevels;
use Illuminate\View\View;
use Illuminate\Http\Request;

class DriversController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $drivers = Drivers::with('priceLevel')->get();
        // Pass the vehicles data to the view
        $priceLevels = pricelevels::all();
        return view('delivery-persons', compact('drivers', 'priceLevels'));

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
            'name' => 'required',
            'address' => 'required',
            'contact' => 'required',
            'price_level' => 'required',
        ]);

        $status = 'NOT AVAILABLE';

         // Check if the request data is 'on'
         if ($request->status === 'on') {
             $status = 'AVAILABLE';
         }

        Drivers::create([
            'name' => $request->name,
            'address' => $request->address,
            'contact' => $request->contact,
            'status' => $status,
            'default_price_level' => $request->price_level,
        ]);

        return redirect('/delivery-persons/')->with('success', 'Delivery person added successfully!');
    }

    /**
     * Display the specified resource.
     */
    public function show(Drivers $drivers)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Drivers $drivers)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request)
    {
        $request->validate([
            'e_id' => 'required',
            'e_address' => 'required',
            'e_contact' => 'required',
            'e_status' => 'required',
            'e_price_level' => 'required',
        ]);

        $dp = Drivers::find($request->e_id);
        $dp->address = $request->e_address;
        $dp->contact = $request->e_contact;
        $dp->status = $request->e_status ? 'Active' : 'Inactive';
        $dp->default_price_level = $request->e_price_level;
        $dp->save();

        return redirect('/delivery-persons/')->with('success', 'Delivery person updated successfully!');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Drivers $drivers)
    {
        //
    }
}
