<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Equipment;

class EquipmentController extends Controller
{
    public function index()
    {
        $equipments = Equipment::all();
        return view('equipment', compact('equipments'));
    }

    public function create()
    {
        return view('create-equipment');
    }

    public function store(Request $request)
    {
        // Validate the incoming request data
        $request->validate([
            'ownership' => 'required',
            'type' => 'required',
            'brand' => 'required',
            'price' => 'required|numeric',
            'serial_no' => 'required|unique:equipment',
            'code' => 'nullable',
            'distributor' => 'nullable',
            'date_delivered' => 'nullable|date',
            'date_purchased' => 'nullable|date',
        ]);

        // Create a new equipment instance
        $equipment = new Equipment();
        $equipment->ownership = $request->ownership;
        $equipment->type = $request->type;
        $equipment->brand = $request->brand;
        $equipment->price = $request->price;
        $equipment->serial_no = $request->serial_no;
        $equipment->code = $request->code;
        $equipment->distributor = $request->distributor;
        $equipment->date_delivered = $request->date_delivered;
        $equipment->date_purchased = $request->date_purchased;

        // Save the equipment
        $equipment->save();

        // Redirect to the index page with success message
        return redirect()->route('equipment.index')->with('success', 'Equipment created successfully.');

    }

    public function edit($id)
    {
        $equipment = Equipment::findOrFail($id);
        return response()->json($equipment);
    }
    
    public function update(Request $request)
    {
        $equipment = Equipment::findOrFail($request->id);
    
        $request->validate([
            'ownership' => 'required',
            'type' => 'required',
            'brand' => 'required',
            'price' => 'required|numeric',
            'serial_no' => 'required',
            'code' => 'required',
            'distributor' => 'required',
            'date_delivered' => 'required|date',
            'date_purchased' => 'required|date',
            // Add more validation rules as needed
        ]);
    
        $equipment->update([
            'ownership' => $request->ownership,
            'type' => $request->type,
            'brand' => $request->brand,
            'price' => $request->price,
            'serial_no' => $request->serial_no,
            'code' => $request->code,
            'distributor' => $request->distributor,
            'date_delivered' => $request->date_delivered,
            'date_purchased' => $request->date_purchased,
            // Add more fields as needed
        ]);
    
        return redirect('/equipment')->with('success', 'Equipment updated successfully!');
    }

    public function destroy($id)
    {
        // Find the equipment by ID and delete it
        $equipment = Equipment::findOrFail($id);
        $equipment->delete();

        // Redirect to the index page with success message
        return redirect()->route('equipment.index')->with('success', 'Equipment deleted successfully.');

    }
}
