<?php

namespace App\Http\Controllers;

use App\Models\Drivers;
use Illuminate\View\View;
use Illuminate\Http\Request;

class DriversController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $drivers = Drivers::all();
        // Pass the vehicles data to the view
        return view('delivery-persons', compact('drivers'));

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

            // Add more validation rules as needed
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

            // Add more fields as needed
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
            // Add more validation rules as needed
        ]);

        $dp = Drivers::find($request->e_id);
        $dp->address = $request->e_address;
        $dp->contact = $request->e_contact;
        $dp->status = $request->e_status ? 'Active' : 'Inactive';
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
