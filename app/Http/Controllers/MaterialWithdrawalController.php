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

    public function list()
    {
        $withdrawals = MaterialItemsWithdrawals::with('materials')
            ->orderBy('withdrawal_date', 'desc')
            ->orderBy('created_at', 'desc')
            ->get();

        return view('material-withdrawals.list', compact('withdrawals'));
    }

    public function review(Request $request)
    {
        $request->validate([
            'items' => 'required|array',
            'items.*.id' => 'required|exists:materials_inventories,id',
            'items.*.quantity' => 'required|numeric|min:1',
            'items.*.name' => 'required|string',
            'items.*.unit' => 'nullable|string',
            'items.*.available' => 'required|numeric',
            'requested_by' => 'required',
            'issued_by' => 'required',
            'withdrawal_date' => 'required|date',
        ]);

        $items = [];
        foreach ($request->items as $id => $item) {
            $items[$id] = [
                'name' => $item['name'],
                'quantity' => $item['quantity'],
                'unit' => $item['unit'] ?? '',
                'available' => $item['available'],
            ];
        }

        return view('material-withdrawals.review', [
            'items' => $items,
            'requested_by' => $request->requested_by,
            'issued_by' => $request->issued_by,
            'withdrawal_date' => $request->withdrawal_date,
        ]);
    }

    public function print($id)
    {
        $withdrawal = MaterialItemsWithdrawals::with('materials')->findOrFail($id);
        return view('material-withdrawals.print', compact('withdrawal'));
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

            // Log withdrawal activity with user information
            activity()
                ->causedBy(auth()->user())
                ->performedOn($material)
                ->withProperties([
                    'withdrawal_code' => $withdrawal_code,
                    'withdrawn_quantity' => $item['quantity'],
                    'remaining_quantity' => $material->quantity,
                ])
                ->log("Withdrawn {$item['quantity']} {$material->unit} from inventory");
        }

        return redirect()->route('material-withdrawals.list')->with('success', 'Items have been withdrawn successfully. Withdrawal Code: ' . $withdrawal_code);
    }
}
