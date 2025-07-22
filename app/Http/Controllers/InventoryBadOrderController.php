<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\InventoryBadOrder;
use App\Models\ItemMasterData;
use Illuminate\Support\Facades\DB;

class InventoryBadOrderController extends Controller
{
    //
    public function index(Request $request)
    {
        $query = InventoryBadOrder::query()
            ->with('user')
            ->orderBy('created_at', 'desc');

        if ($request->has('search') && !empty($request->search)) {
            $searchTerm = '%' . strtolower($request->search) . '%';
            $query->where(function($q) use ($searchTerm) {
                $q->where('reference_name', 'LIKE', $searchTerm)
                  ->orWhereRaw('LOWER(JSON_EXTRACT(products, "$[*].name")) LIKE ?', [$searchTerm])
                  ->orWhereRaw('LOWER(JSON_EXTRACT(products, "$[*].code")) LIKE ?', [$searchTerm]);
            });
        }

        $badOrders = $query->paginate(15);
        
        if ($request->ajax()) {
            return response()->json($badOrders);
        }

        return view('inventory-bad-order.index', compact('badOrders'));
    }

    public function create()
    {
        $itemMasterData = ItemMasterData::where('branch_code', session('branch_code'))->get();
        $reference_name = InventoryBadOrder::generateReferenceName();
        return view('inventory-bad-order.create', compact('itemMasterData', 'reference_name'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'reference_name' => 'required|string|max:255',
            'remarks' => 'nullable|string|max:1000',
            'products' => 'required|array|min:1',
            'products.*.id' => 'required|exists:item_master_data,id',
            'products.*.quantity' => 'required|integer|min:1',
            'products.*.reason' => 'nullable|string|max:255',
        ]);

        try {
            return DB::transaction(function () use ($request) {

                $products = collect($request->products)->map(function ($item) {
                    $product = ItemMasterData::where('id', $item['id'])
                        ->where('branch_code', session('branch_code'))
                        ->lockForUpdate()
                        ->firstOrFail();
                    return [
                        'id' => $item['id'],
                        'code' => $product->product_code,
                        'name' => $product->product_description,
                        'quantity' => $item['quantity'],
                        'reason' => $item['reason'] ?? null,
                        'unit' => $product->unit,
                    ];
                })->toArray();

                $badOrder = InventoryBadOrder::create([
                    'branch_code' => session('branch_code'),
                    'reference_name' => $request->reference_name,
                    'products' => $products,
                    'user_id' => auth()->user()->id,
                    'status' => 'saved',
                    'remarks' => $request->remarks,
                    'date_created' => now(),
                ]);

                

                // add to item master data
                foreach ($products as $product) {
                    $itemMasterData = ItemMasterData::where('id', $product['id'])
                        ->where('branch_code', session('branch_code'))
                        ->lockForUpdate()
                        ->firstOrFail();
                    $oldValues = [
                        'stocks' => $itemMasterData->stocks,
                        'reserved' => $itemMasterData->reserved
                    ];
                    $itemMasterData->stocks = $itemMasterData->stocks - $product['quantity'];
                    $itemMasterData->save();

                    // Log the manual adjustment
                    activity()
                        ->performedOn($itemMasterData)
                        ->withProperties([
                            'old_values' => $oldValues,
                            'new_values' => [
                                'stocks' => $itemMasterData->stocks,
                                'reserved' => $itemMasterData->reserved
                            ],
                            'notes' => $request->remarks
                        ])
                        ->log("Bad order {$badOrder->reference_name} by " . auth()->user()->fullName);
                }

                return redirect()->route('inventory.bad-orders')->with('success', 'Bad order created successfully.');
            });
        } catch (\Exception $e) {
            return redirect()->back()
                ->withInput()
                ->withErrors('Failed to create bad order. ' . $e->getMessage());
        }

        return redirect()->route('inventory.bad-orders')->with('success', 'Bad order created successfully.');
    }

    public function rollback(Request $request, InventoryBadOrder $badOrder)
    {

        if (!auth()->user()->can('admin')) {
            return redirect()->back()->withErrors('You do not have permission to roll back this bad order.');
        }
        $request->validate([
            'rollback_reason' => 'required|string|max:500',
        ]);

        if (!$badOrder->canRollback()) {
            return redirect()->back()->withErrors('This bad order cannot be rolled back. It may have already been rolled back or is in an invalid status.');
        }

        try {
            return DB::transaction(function () use ($request, $badOrder) {
                // Restore stock quantities for each product
                foreach ($badOrder->products as $product) {
                    $itemMasterData = ItemMasterData::where('id', $product['id'])
                        ->where('branch_code', $badOrder->branch_code)
                        ->lockForUpdate()
                        ->first();

                    if (!$itemMasterData) {
                        throw new \Exception("Product {$product['name']} (ID: {$product['id']}) not found in branch {$badOrder->branch_code}");
                    }

                    $oldValues = [
                        'stocks' => $itemMasterData->stocks,
                        'reserved' => $itemMasterData->reserved
                    ];

                    // Restore the stock by adding back the quantity
                    $itemMasterData->stocks = $itemMasterData->stocks + $product['quantity'];
                    $itemMasterData->save();

                    // Log the rollback adjustment
                    activity()
                        ->performedOn($itemMasterData)
                        ->withProperties([
                            'old_values' => $oldValues,
                            'new_values' => [
                                'stocks' => $itemMasterData->stocks,
                                'reserved' => $itemMasterData->reserved
                            ],
                            'notes' => "Rollback of bad order: {$request->rollback_reason}",
                            'original_bad_order' => $badOrder->reference_name
                        ])
                        ->log("Bad order rollback {$badOrder->reference_name} by " . auth()->user()->fullName);
                }

                // Mark the bad order as rolled back
                $badOrder->rollback($request->rollback_reason, auth()->id());

                return redirect()->route('inventory.bad-orders')
                    ->with('success', "Bad order {$badOrder->reference_name} has been successfully rolled back and stock quantities have been restored.");
            });
        } catch (\Exception $e) {
            return redirect()->back()
                ->withErrors('Failed to rollback bad order: ' . $e->getMessage());
        }
    }
}
