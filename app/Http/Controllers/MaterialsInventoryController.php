<?php

namespace App\Http\Controllers;

use App\Models\MaterialsInventory;
use Illuminate\Http\Request;
use Spatie\Activitylog\Contracts\Activity;
use Spatie\Activitylog\Models\Activity as ModelsActivity;

class MaterialsInventoryController extends Controller
{
    //
    public function index()
    {
        $materials = MaterialsInventory::all();
        return view('materials-inventory.index', compact('materials'));
    }

    public function store(Request $request){
        $request->validate([
            'branch_code' => 'required',
            'name' => 'required',
            'unit' => 'nullable',
            'quantity' => 'required|numeric',
            'location' => 'required',
            'remarks' => 'nullable',
        ]);

        $material = new MaterialsInventory();
        $material->branch_code = $request->branch_code;
        $material->name = $request->name;
        $material->unit = $request->unit;
        $material->quantity = $request->quantity;
        $material->location = $request->location;
        $material->remarks = $request->remarks;
        $material->modified_by = auth()->user()->fullName;
        $material->save();

        activity('general-inventory')
            ->log("Added $material->name to inventory");

        return back()->with('success', 'Data saved.');

    }

    public function update(Request $request){

        $request->validate([
            'inv_id' => 'required|exists:materials_inventories,id',
            'e_name' => 'required',
            'e_unit' => 'nullable',
            'e_quantity' => 'required|numeric',
            'e_location' => 'required',
            'e_remarks' => 'nullable',
        ]);

        $material = MaterialsInventory::find($request->inv_id);

        $old = $material->getOriginal();

        activity('general-inventory')
            ->performedOn($material)
            ->withProperties($old)
            ->log("Updated $material->name details");

        $material->name = $request->e_name;
        $material->unit = $request->e_unit;
        $material->quantity = $request->e_quantity;
        $material->location = $request->e_location;
        $material->remarks = $request->e_remarks;
        $material->modified_by = auth()->user()->fullName;
        $material->save();

        return back()->with('success', 'Data saved.');


    }

    public function history($id){
        $material = MaterialsInventory::find($id);

        $activityLogs = ModelsActivity::where('subject_id', $id)
            ->where('subject_type', 'App\Models\MaterialsInventory')
            ->get();

        return view('materials-inventory.history', compact('material','activityLogs'));
    }
}
