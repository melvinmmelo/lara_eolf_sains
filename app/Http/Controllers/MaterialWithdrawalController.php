<?php

namespace App\Http\Controllers;

use App\Models\MaterialsInventory;
use App\Models\MaterialItemsWithdrawals;
use Illuminate\Http\Request;

class MaterialWithdrawalController extends Controller
{
    public function index()
    {
        return view('material-withdrawals.index');
    }

    public function search(Request $request)
    {
        $query = $request->get('query');
        $materials = MaterialsInventory::branch(session('branch_code'))
            ->activeItems()
            ->where('name', 'like', "%{$query}%")
            ->get();
        
        return response()->json($materials);
    }

    public function store(Request $request)
    {
        $request->validate([
            'items' => 'required|array',
            'items.*.id' => 'required|exists:materials_inventories,id',
            'items.*.quantity' => 'required|numeric|min:1',
            'requested_by' => 'required',
            'issued_by' => 'required',
            'withdrawal_date' => 'required|date',
        ]);

        // Generate withdrawal code
        $withdrawal_code = 'W' . now()->format('Ymd') . str_pad(MaterialItemsWithdrawals::max('id'), 5, '0', STR_PAD_LEFT);

        $materialItemsWithdrawal = new MaterialItemsWithdrawals();
        $materialItemsWithdrawal->code = $withdrawal_code;
        $materialItemsWithdrawal->requested_by = $request->requested_by;
        $materialItemsWithdrawal->issued_by = $request->issued_by;
        $materialItemsWithdrawal->withdrawal_date = $request->withdrawal_date;
        $materialItemsWithdrawal->save();

        foreach ($request->items as $item) {
            $material = MaterialsInventory::find($item['id']);
            
            // Create a copy of the material for withdrawal
            $withdrawnMaterial = $material->replicate();
            $withdrawnMaterial->quantity = $item['quantity'];
            $withdrawnMaterial->withdrawal_id = $materialItemsWithdrawal->id;
            $withdrawnMaterial->save();

            // Update original material quantity
            $material->quantity -= $item['quantity'];
            if ($material->quantity <= 0) {
                $material->withdrawal_id = $materialItemsWithdrawal->id;
            }
            $material->save();

            activity('general-inventory')
                ->performedOn($material)
                ->log("Withdrawn {$item['quantity']} units of $material->name from inventory");
        }

        return back()->with('success', 'Items have been withdrawn successfully.');
    }
}
