<?php

namespace App\Http\Controllers;

use App\Models\MaterialItemsWithdrawals;
use App\Models\MaterialsInventory;
use Illuminate\Http\Request;
use Spatie\Activitylog\Contracts\Activity;
use Spatie\Activitylog\Models\Activity as ModelsActivity;

class MaterialsInventoryController extends Controller
{
    //
    public function index()
    {
        $materials = MaterialsInventory::branchCode(session('branch_code'))->activeItems()->get();
        return view('materials-inventory.index', compact('materials'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'branch_code' => 'required',
            'name' => 'required',
            'unit' => 'nullable',
            'quantity' => 'required|numeric',
            'amount' => 'required|numeric',
            'location' => 'required',
            'remarks' => 'nullable',
        ]);

        $material = new MaterialsInventory();
        $material->branch_code = $request->branch_code;
        $material->name = $request->name;
        $material->unit = $request->unit;
        $material->quantity = $request->quantity;
        $material->amount = $request->amount;
        $material->location = $request->location;
        $material->remarks = $request->remarks;
        $material->modified_by = auth()->user()->fullName;
        $material->save();

        activity('general-inventory')
            ->log("Added $material->name to inventory");

        return back()->with('success', 'Data saved.');
    }

    public function update(Request $request)
    {

        $request->validate([
            'inv_id' => 'required|exists:materials_inventories,id',
            'e_name' => 'required',
            'e_unit' => 'nullable',
            'e_quantity' => 'required|numeric',
            'e_amount' => 'required|numeric',
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
        $material->amount = $request->e_amount;
        $material->location = $request->e_location;
        $material->remarks = $request->e_remarks;
        $material->modified_by = auth()->user()->fullName;
        $material->save();

        activity('general-inventory')
            ->log("Updated $material->name details");

        return back()->with('success', 'Data has been saved.');
    }

    public function history($id)
    {
        $material = MaterialsInventory::find($id);

        $activityLogs = ModelsActivity::where('subject_id', $id)
            ->where('subject_type', 'App\Models\MaterialsInventory')
            ->get();

        return view('materials-inventory.history', compact('material', 'activityLogs'));
    }

    public function deleteOrWithdraw(Request $request)
    {

        $request->validate([
            'items' => 'required|array',
            'submit_form' => 'required'
        ]);

        $ids = request('items');
        $materials = MaterialsInventory::whereIn('id', $ids)->get();

        if ($request->submit_form === 'delete') {

            foreach ($materials as $material) {
                $material->delete();
            }

            activity('general-inventory')
                ->log("Deleted $materials->name from inventory");

            $returnMessage = 'Data has been deleted.';
        } else {

            // generate withdrawal code
            $withdrawal_code = 'W' . now()->format('Ymd'). str_pad(MaterialItemsWithdrawals::max('id'), 5, '0', STR_PAD_LEFT);

            $request->validate([
                'requested_by' => 'required',
                'issued_by' => 'required',
                'withdrawal_date' => 'required',
            ]);


            $materialItemsWithdrawal = new MaterialItemsWithdrawals();
            $materialItemsWithdrawal->code = $withdrawal_code;
            $materialItemsWithdrawal->requested_by = $request->requested_by;
            $materialItemsWithdrawal->issued_by = $request->issued_by;
            $materialItemsWithdrawal->save();


            foreach ($materials as $material) {
                $material->withdrawal_id = $materialItemsWithdrawal->id;
                $material->save();
            }

            activity('general-inventory')
                ->log("Withdrawn from inventory");

            $returnMessage = 'Data has been withdrawn.';
        }

        return back()->with('success', $returnMessage);
    }
}
