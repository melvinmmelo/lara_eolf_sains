<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Vehicles as Vehicle;

class VehiclesController extends Controller
{

    public function index()
    {
        $vehicles = Vehicle::all();
        return view('vehicles', compact('vehicles'));
    }

    public function create()
    {
        $vehicles = Vehicle::all();
        return view('create-vehicle', compact('vehicles'));

    }

    public function store(Request $request)
    {

        // dd($request->all());
        $request->validate([
            'plateno' => 'required',
            'brand' => 'required',
            'description' => 'required',

            // Add more validation rules as needed
        ]);
        $status = 'INACTIVE';

        // Check if the request data is 'on'
        if ($request->status === 'on') {
            $status = 'ACTIVE';
        }

        Vehicle::create([
            'plateno' => $request->plateno,
            'brand' => $request->brand,
            'description' => $request->description,
            'type' => $request->type,
            'size' => $request->size,
            'capacity' => $request->capacity,
            'remarks' => $request->remarks,
            'status' => $status,

            // Add more fields as needed
        ]);

        return redirect('/vehicles/')->with('success', 'Vehicle added successfully!');
    }

    // public function edit($id)
    // {
    //     $vehicle = Vehicle::findOrFail($id);
    //     return view('edit_vehicle', compact('vehicle'));
    // }
    public function edit($id)
    {
        $customer = Customer::findOrFail($id);
        return response()->json($vehicle);
    }
    public function update(Request $request)
    {
        $vehicle = Vehicle::findOrFail($request->id);

        $request->validate([
            'plateno' => 'required',
            'brand' => 'required',

            // Add more validation rules as needed
        ]);
    // Set default status
    $status = 'NOT ACTIVE';

    // Check if the request data is 'on'
    if ($request->status === 'on') {
        $status = 'ACTIVE';
    }
        $vehicle->update([
            'plateno' => $request->plateno,
            'brand' => $request->brand,
            'description' => $request->description,
            'type' => $request->type,
            'size' => $request->size,
            'capacity' => $request->capacity,
            'remarks' => $request->remarks,
            'status' => $status,
        ]);

        return redirect('/vehicles/')->with('success', 'Vehicle updated successfully!');
    }

    public function destroy($id)
    {
        $vehicle = Vehicle::findOrFail($id);
        $vehicle->delete();
        return redirect('/vehicles/')->with('success', 'Vehicle deleted successfully!');
    }

}
