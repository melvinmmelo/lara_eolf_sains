<?php

namespace App\Http\Controllers;

use App\Models\DeliveryPurchaseReceipt;
use App\Models\Inbound;
use App\Models\ItemMasterData;
use App\Models\NewInboundProduct;
use App\Models\Product;
use App\Services\DPRService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Spatie\Activitylog\Models\Activity;

class StockReconciliationController extends Controller
{
    /**
     * Display the stock reconciliation dashboard
     */
    public function index(Request $request)
    {
        $branchCode = session('branch_code');
        $searchTerm = $request->search;
        $items = [];
        $items = DB::table('item_master_data')
            ->where('branch_code', $branchCode)
            ->paginate(20);
            
        if ($searchTerm) {
            $items = DB::table('item_master_data')
                        ->where('branch_code', $branchCode)
                        ->where('product_code', 'like', "%$searchTerm%")
                        ->paginate(20);
        }
            
        return view('stock-reconciliation.index', compact('items', 'searchTerm'));
    }
    
    /**
     * Show reconciliation details for a specific product
     */
    public function showProduct($productCode)
    {
        $branchCode = session('branch_code');
        
        // Get the current item data
        $item = ItemMasterData::branch($branchCode)
            ->productCode($productCode)
            ->with('product')
            ->firstOrFail();
            
        // Calculate expected stock from DPRs (incoming stock)
        $dprStock = $this->calculateDPRStock($productCode, $branchCode);
        
        // Get DPR history
        $dprHistory = $this->getDPRHistory($productCode, $branchCode);
        
        // Calculate reserved from orders (outgoing)
        $orderReserved = $this->calculateOrderReserved($productCode, $branchCode);
        
        // Get order history
        $orderHistory = $this->getOrderHistory($productCode, $branchCode);
        
        // Calculate expected values
        $totalInbounds = $dprStock;
        $totalOrders = $orderReserved;
        $remainingStocksBasedOnTransactions = $totalInbounds - $totalOrders;
        
        // Get recent activity logs related to this product
        $logs = Activity::query()
            ->where('causer_id', auth()->id())
            ->where('subject_type', ItemMasterData::class)
            ->where('subject_id', $item->id)
            ->orderBy('created_at', 'desc')
            ->take(20)
            ->get();
            
        return view('stock-reconciliation.product', compact(
            'item', 
            'totalInbounds',
            'totalOrders',
            'remainingStocksBasedOnTransactions',
            'dprHistory',
            'orderHistory',
            'logs'
        ));
    }
    
    /**
     * Fix the stock for a specific product
     */
    public function fixStock(Request $request, $productCode)
    {

        $request->validate([
            'new_stock' => 'required|integer|min:0',
            'new_reserved' => 'required|integer|min:0',
            'notes' => 'required|string'
        ]);


        
        $branchCode = session('branch_code');
        
        try {


            DB::transaction(function() use ($request, $productCode, $branchCode) {
                $item = ItemMasterData::branch($branchCode)
                    ->productCode($productCode)
                    ->lockForUpdate()
                    ->firstOrFail();
                    
                $oldValues = [
                    'stocks' => $item->stocks,
                    'reserved' => $item->reserved
                ];
                
                $item->stocks = $request->new_stock;
                $item->reserved = $request->new_reserved;
                $item->save();
                
                // Log the manual adjustment
                activity()
                    ->performedOn($item)
                    ->withProperties([
                        'old_values' => $oldValues,
                        'new_values' => [
                            'stocks' => $request->new_stock,
                            'reserved' => $request->new_reserved
                        ],
                        'notes' => $request->notes
                    ])
                    ->log("Manual stock adjustment for {$productCode} by " . auth()->user()->fullName);
            });
            
            return redirect()
                ->route('stock-reconciliation.product', $productCode)
                ->with('success', 'Stock has been adjusted successfully');
                
        } catch (\Exception $e) {
            Log::error('Stock adjustment failed: ' . $e->getMessage());
            return back()->withErrors('Failed to adjust stock: ' . $e->getMessage());
        }
    }
    
    /**
     * Run full system reconciliation for all products
     */
    public function reconcileAll()
    {
        $branchCode = session('branch_code');
        
        // This could be a long process, so we'll use a queue job in production
        // For now, we'll implement it directly
        
        try {
            $products = ItemMasterData::branch($branchCode)->get();
            $fixed = 0;
            
            foreach ($products as $item) {
                // Calculate expected values
                $dprStock = $this->calculateDPRStock($item->product_code, $branchCode);
                $orderReserved = $this->calculateOrderReserved($item->product_code, $branchCode);
                
                // Check if values differ
                if ($item->stocks != $dprStock || $item->reserved != $orderReserved) {
                    DB::transaction(function() use ($item, $dprStock, $orderReserved) {
                        $oldValues = [
                            'stocks' => $item->stocks,
                            'reserved' => $item->reserved
                        ];
                        
                        $item->stocks = $dprStock;
                        $item->reserved = $orderReserved;
                        $item->save();
                        
                        // Log the automated adjustment
                        activity()
                            ->performedOn($item)
                            ->withProperties([
                                'old_values' => $oldValues,
                                'new_values' => [
                                    'stocks' => $dprStock,
                                    'reserved' => $orderReserved
                                ],
                                'notes' => 'Automated system reconciliation'
                            ])
                            ->log("Auto stock reconciliation for {$item->product_code} by " . auth()->user()->fullName);
                    });
                    
                    $fixed++;
                }
            }
            
            return redirect()
                ->route('stock-reconciliation.index')
                ->with('success', "Reconciliation completed. Fixed {$fixed} products.");
                
        } catch (\Exception $e) {
            Log::error('Full reconciliation failed: ' . $e->getMessage());
            return back()->withErrors('Reconciliation failed: ' . $e->getMessage());
        }
    }
    
    /**
     * Calculate the total stock from DPRs
     */
    private function calculateDPRStock($productCode, $branchCode)
    {
        // Get all completed DPRs
        $dprs = DeliveryPurchaseReceipt::branch($branchCode)
            ->where('status', 'Completed')
            ->get();
            
        $totalStock = 0;
        
        foreach ($dprs as $dpr) {
            $products = json_decode($dpr->products, true) ?: [];
            
            foreach ($products as $product) {
                if ($product['code'] == $productCode) {
                    $totalStock += $product['quantity'];
                }
            }
        }
        
        return $totalStock;
    }
    
    /**
     * Calculate the total reserved stock from orders
     */
    private function calculateOrderReserved($productCode, $branchCode)
    {
        // Get all active inbound products (orders)
        $orders = Inbound::where('branch_code', $branchCode)
            ->whereIn('status', ['Completed', 'Paid', 'Free'])
            ->get();
            
        $totalReserved = 0;
        
        if ($orders->isNotEmpty()) {
            foreach ($orders as $order) {
                $products = json_decode($order->products, true) ?: [];
                
                foreach ($products as $product) {
                    if ($product['code'] == $productCode) {
                        $totalReserved += $product['quantity'];
                    }
                }
            }
        }
        
        return $totalReserved;
    }
    
    /**
     * Display product stock history
     */
    public function productHistory($productCode)
    {
        $branchCode = session('branch_code');
        
        // Get DPR history
        $dprHistory = $this->getDPRHistory($productCode, $branchCode);
        
        // Get order history
        $orderHistory = $this->getOrderHistory($productCode, $branchCode);
        
        // Get activity logs
        $logs = Activity::query()
            ->where('causer_id', auth()->id())
            ->where('description', 'like', "%{$productCode}%")
            ->orderBy('created_at', 'desc')
            ->paginate(20);
            
        return view('stock-reconciliation.history', compact(
            'productCode',
            'dprHistory',
            'orderHistory',
            'logs'
        ));
    }
    
    /**
     * Get DPR history for a product
     */
    private function getDPRHistory($productCode, $branchCode)
    {
        $dprs = DeliveryPurchaseReceipt::branch($branchCode)
            ->where('products', 'like', "%{$productCode}%")
            ->where('status', 'Completed')
            ->orderBy('created_at', 'desc')
            ->get();
            
        $history = [];
        
        foreach ($dprs as $dpr) {
            $products = json_decode($dpr->products, true) ?: [];
            
            foreach ($products as $product) {
                if ($product['code'] == $productCode) {
                    $history[] = [
                        'dr_no' => $dpr->dr_no,
                        'date' => $dpr->created_at,
                        'quantity' => $product['quantity'],
                        'hold' => $product['hold'] ?? 0,
                        'status' => $dpr->status
                    ];
                }
            }
        }
        
        return $history;
    }
    
    /**
     * Get order history for a product
     */
    private function getOrderHistory($productCode, $branchCode)
    {
        $history = [];
        
        $orders = Inbound::where('branch_code', $branchCode)
            ->whereIn('status', ['Completed', 'Paid', 'Free'])
            ->orderBy('created_at', 'desc')
            ->get();

        foreach ($orders as $order) {
            $products = json_decode($order->products, true) ?: [];

            foreach ($products as $product) {
                if ($product['code'] == $productCode) {
                    $history[] = [
                        'order_no' => $order->code,
                        'date' => $order->created_at,
                        'quantity' => $product['quantity'],
                        'status' => $order->status
                    ];
                }
            }
        }

        return $history;
    }
}
