<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Equipment;

class EquipmentController extends Controller
{
    public function index()
    {
        $equipments = Equipment::branchCode(session('branch_code'))->get();

        // ! update status

        // foreach ($equipments as $equipment) {
        //     $update = $equipment->equipmentStore->customer->fullName ?? 'update';
        //     if($update != 'update' and $equipment->status === 'available'){
        //         $equipment->status = 'added';
        //         $equipment->save();
        //     }
        // }

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
            'branch_code' => 'required',
            'ownership' => 'required',
            'type' => 'required',
            'brand' => 'required',
            'price' => 'nullable|numeric',
            'serial_no' => 'nullable',
            'model' => 'required',
            'code' => 'required',
            'distributor' => 'nullable',
            'date_delivered' => 'nullable|date',
            'date_purchased' => 'nullable|date',
        ]);

        // Create a new equipment instance
        $equipment = new Equipment();
        $equipment->branch_code = $request->branch_code;
        $equipment->ownership = $request->ownership;
        $equipment->type = $request->type;
        $equipment->brand = $request->brand;
        $equipment->price = $request->price;
        $equipment->serial_no = $request->serial_no;
        $equipment->model = $request->model;
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
            'e_model' => 'required',
            'code' => 'required',
            'date_delivered' => 'nullable|date',
            'date_purchased' => 'nullable|date',
        ]);

        $equipment->update([
            'ownership' => $request->ownership,
            'type' => $request->type,
            'brand' => $request->brand,
            'price' => $request->price,
            'serial_no' => $request->serial_no,
            'model' => $request->e_model,
            'code' => $request->code,
            'distributor' => $request->distributor,
            'date_delivered' => $request->date_delivered,
            'date_purchased' => $request->date_purchased,
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

    public function bulkDelete(Request $request)
    {
        $ids = $request->input('equipment_ids');

        if ($ids && is_array($ids)) {
            Equipment::whereIn('id', $ids)->where('status', 'available')->delete();
            return redirect()->route('equipment.index')->with('success', 'Selected equipment deleted successfully.');
        }

        return redirect()->route('equipment.index')->with('error', 'No equipment selected for deletion.');
    }

}
