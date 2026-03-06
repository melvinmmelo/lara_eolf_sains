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
        $materials = MaterialsInventory::branch(session('branch_code'))->activeItems()->get();
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
            'remarks' => 'nullable',
        ]);

        $material = new MaterialsInventory();
        $material->branch_code = $request->branch_code;
        $material->name = $request->name;
        $material->unit = $request->unit;
        $material->quantity = $request->quantity;
        $material->amount = $request->amount;
        $material->remarks = $request->remarks;
        $material->modified_by = auth()->user()->fullName;

        // Save - LogsActivity trait will automatically log the creation
        $material->save();

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

        // Update the material fields
        $material->name = $request->e_name;
        $material->unit = $request->e_unit;
        $material->quantity = $request->e_quantity;
        $material->amount = $request->e_amount;
        $material->location = $request->e_location;
        $material->remarks = $request->e_remarks;
        $material->modified_by = auth()->user()->fullName;

        // Save - LogsActivity trait will automatically log the changes
        $material->save();

        return back()->with('success', 'Data has been saved.');
    }

    public function history($id)
    {
        $material = MaterialsInventory::find($id);

        $activityLogs = ModelsActivity::where('subject_id', $id)
            ->where('subject_type', 'App\Models\MaterialsInventory')
            ->orderBy('created_at', 'desc')
            ->get();

        return view('materials-inventory.history', compact('material', 'activityLogs'));
    }

    public function destroy(Request $request)
    {
        $request->validate([
            'items' => 'required|array',
            'items.*' => 'exists:materials_inventories,id'
        ]);

        $ids = $request->items;
        $materials = MaterialsInventory::whereIn('id', $ids)->get();

        foreach ($materials as $material) {
            // Delete - LogsActivity trait will automatically log the deletion
            $material->delete();
        }

        return back()->with('success', 'Selected items have been deleted.');
    }

    public function receive()
    {
        $materials = MaterialsInventory::branch(session('branch_code'))->activeItems()->get();
        return view('materials-inventory.receive', compact('materials'));
    }

    public function bulkReceive(Request $request)
    {
        $request->validate([
            'existing'             => 'nullable|array',
            'existing.*.id'        => 'required|exists:materials_inventories,id',
            'existing.*.add_qty'   => 'required|numeric|min:0',
            'new'                  => 'nullable|array',
            'new.*.name'           => 'required|string',
            'new.*.unit'           => 'nullable|string',
            'new.*.quantity'       => 'required|numeric|min:1',
            'new.*.amount'         => 'required|numeric|min:0',
        ]);

        $modifiedBy = auth()->user()->fullName;

        // Update existing materials
        foreach ($request->input('existing', []) as $item) {
            if ($item['add_qty'] > 0) {
                $material = MaterialsInventory::find($item['id']);
                $material->quantity += $item['add_qty'];
                $material->modified_by = $modifiedBy;
                $material->save();
            }
        }

        // Create new materials
        foreach ($request->input('new', []) as $item) {
            MaterialsInventory::create([
                'branch_code' => session('branch_code'),
                'name'        => $item['name'],
                'unit'        => $item['unit'] ?? null,
                'quantity'    => $item['quantity'],
                'amount'      => $item['amount'],
                'modified_by' => $modifiedBy,
            ]);
        }

        return redirect()->route('materialsInventory.index')->with('success', 'Delivery received and inventory updated.');
    }

    public function withdraw(Request $request)
    {
        $request->validate([
            'items' => 'required|array',
            'items.*' => 'exists:materials_inventories,id',
            'requested_by' => 'required',
            'issued_by' => 'required',
            'withdrawal_date' => 'required|date',
        ]);

        $ids = $request->items;
        $materials = MaterialsInventory::whereIn('id', $ids)->get();

        // Generate withdrawal code
        $withdrawal_code = 'W' . now()->format('Ymd') . str_pad(MaterialItemsWithdrawals::max('id'), 5, '0', STR_PAD_LEFT);

        $materialItemsWithdrawal = new MaterialItemsWithdrawals();
        $materialItemsWithdrawal->code = $withdrawal_code;
        $materialItemsWithdrawal->requested_by = $request->requested_by;
        $materialItemsWithdrawal->issued_by = $request->issued_by;
        $materialItemsWithdrawal->withdrawal_date = $request->withdrawal_date;
        $materialItemsWithdrawal->save();

        foreach ($materials as $material) {
            $material->withdrawal_id = $materialItemsWithdrawal->id;

            // Save - LogsActivity trait will automatically log the change
            $material->save();
        }

        return back()->with('success', 'Selected items have been withdrawn.');
    }
}
