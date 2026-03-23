<?php

namespace App\Http\Controllers;

use App\Models\Inbound;
use App\Models\Expense;
use App\Models\Equipment;
use App\Models\DeliveryReceipt;
use App\Models\InventoryBadOrder;
use Illuminate\Http\Request;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

class ReportController extends Controller
{
    /**
     * Show reports menu/index
     */
    public function index()
    {
        return view('reports.index');
    }

    /**
     * Report #31: Payment Report
     * Group by customer, show payments with date filters
     */
    public function paymentReport(Request $request)
    {
        $branchCode = session('branch_code');
        $startDate = $request->input('start_date', now()->startOfMonth()->format('Y-m-d'));
        $endDate = $request->input('end_date', now()->endOfMonth()->format('Y-m-d'));

        $data = Inbound::where('branch_code', $branchCode)
            ->whereBetween('order_date', [$startDate, $endDate])
            ->whereNotIn('status', ['Cancelled', 'Deleted'])
            ->where('delivered_amount', '>', 0)
            ->selectRaw('customer_name, payment_type, SUM(delivered_amount) as total_paid, COUNT(*) as orders_count')
            ->groupBy('customer_name', 'payment_type')
            ->orderBy('customer_name')
            ->get();

        $grandTotal = $data->sum('total_paid');

        return view('reports.payment-report', compact('data', 'grandTotal', 'startDate', 'endDate'));
    }

    /**
     * Report #32: Sales by Product Type
     * Parse products JSON and group by type
     */
    public function salesByProductType(Request $request)
    {
        $branchCode = session('branch_code');
        $startDate = $request->input('start_date', now()->startOfMonth()->format('Y-m-d'));
        $endDate = $request->input('end_date', now()->endOfMonth()->format('Y-m-d'));

        $inbounds = Inbound::where('branch_code', $branchCode)
            ->whereBetween('order_date', [$startDate, $endDate])
            ->whereNotIn('status', ['Cancelled', 'Deleted'])
            ->get();

        // Parse products JSON and aggregate
        $productSummary = [];
        foreach ($inbounds as $inbound) {
            $products = json_decode($inbound->products, true);
            if (is_array($products)) {
                foreach ($products as $product) {
                    $ptypeCode = $product['ptype_code'] ?? 'Unknown';
                    $quantity = $product['quantity'] ?? 0;
                    $price = $product['price'] ?? 0;
                    $amount = $quantity * $price;

                    if (!isset($productSummary[$ptypeCode])) {
                        $productSummary[$ptypeCode] = [
                            'product_type' => $ptypeCode,
                            'quantity' => 0,
                            'amount' => 0,
                        ];
                    }

                    $productSummary[$ptypeCode]['quantity'] += $quantity;
                    $productSummary[$ptypeCode]['amount'] += $amount;
                }
            }
        }

        $data = collect($productSummary)->values();
        $totalQuantity = $data->sum('quantity');
        $totalAmount = $data->sum('amount');

        return view('reports.sales-by-product-type', compact('data', 'totalQuantity', 'totalAmount', 'startDate', 'endDate'));
    }

    /**
     * Report #33: Inbound Summary per Flavor
     * Group by product variant/flavor
     */
    public function inboundSummaryPerFlavor(Request $request)
    {
        $branchCode = session('branch_code');
        $startDate = $request->input('start_date', now()->startOfMonth()->format('Y-m-d'));
        $endDate = $request->input('end_date', now()->endOfMonth()->format('Y-m-d'));

        $inbounds = Inbound::where('branch_code', $branchCode)
            ->whereBetween('order_date', [$startDate, $endDate])
            ->whereNotIn('status', ['Cancelled', 'Deleted'])
            ->get();

        // Parse products and group by variant (assuming variant is in product code or separate field)
        $flavorSummary = [];
        foreach ($inbounds as $inbound) {
            $products = json_decode($inbound->products, true);
            if (is_array($products)) {
                foreach ($products as $product) {
                    // Extract variant/flavor from product structure
                    $flavor = $product['ptype_code'] ?? 'Unknown'; // Adjust based on actual structure
                    $quantity = $product['quantity'] ?? 0;

                    if (!isset($flavorSummary[$flavor])) {
                        $flavorSummary[$flavor] = [
                            'flavor' => $flavor,
                            'quantity' => 0,
                            'orders_count' => 0,
                        ];
                    }

                    $flavorSummary[$flavor]['quantity'] += $quantity;
                    $flavorSummary[$flavor]['orders_count']++;
                }
            }
        }

        $data = collect($flavorSummary)->values();
        $totalQuantity = $data->sum('quantity');
        $totalOrders = $data->sum('orders_count');

        return view('reports.inbound-summary-per-flavor', compact('data', 'totalQuantity', 'totalOrders', 'startDate', 'endDate'));
    }

    /**
     * Report #34: Expenses Report
     * Use Expense model from Phase 1
     */
    public function expensesReport(Request $request)
    {
        $branchCode = session('branch_code');
        $startDate = $request->input('start_date', now()->startOfMonth()->format('Y-m-d'));
        $endDate = $request->input('end_date', now()->endOfMonth()->format('Y-m-d'));
        $category = $request->input('category');

        $query = Expense::where('branch_code', $branchCode)
            ->whereBetween('date', [$startDate, $endDate]);

        if ($category) {
            $query->where('category', $category);
        }

        $data = $query->selectRaw('category, SUM(amount) as total_amount, COUNT(*) as count')
            ->groupBy('category')
            ->orderBy('category')
            ->get();

        $grandTotal = $data->sum('total_amount');
        $categories = Expense::distinct()->pluck('category');

        return view('reports.expenses-report', compact('data', 'grandTotal', 'startDate', 'endDate', 'categories', 'category'));
    }

    /**
     * Report #37: Sales by Freezer Report
     * Join with equipment table
     */
    public function salesByFreezer(Request $request)
    {
        $branchCode = session('branch_code');
        $startDate = $request->input('start_date', now()->startOfMonth()->format('Y-m-d'));
        $endDate = $request->input('end_date', now()->endOfMonth()->format('Y-m-d'));

        $data = DB::table('inbounds')
            ->join('equipment', 'inbounds.equipment_id', '=', 'equipment.id')
            ->where('inbounds.branch_code', $branchCode)
            ->whereBetween('inbounds.order_date', [$startDate, $endDate])
            ->whereNotIn('inbounds.status', ['Cancelled', 'Deleted'])
            ->selectRaw('
                equipment.name as equipment_name,
                equipment.code as equipment_code,
                inbounds.customer_name,
                COUNT(inbounds.id) as orders_count,
                SUM(CASE 
                    WHEN inbounds.is_with_sf = 1 
                    THEN JSON_EXTRACT(inbounds.products, "$[*].quantity * $[*].price") + 1000 
                    ELSE JSON_EXTRACT(inbounds.products, "$[*].quantity * $[*].price") 
                END) as total_sales
            ')
            ->groupBy('equipment.id', 'equipment.name', 'equipment.code', 'inbounds.customer_name')
            ->orderBy('total_sales', 'desc')
            ->get();

        $grandTotal = $data->sum('total_sales');

        return view('reports.sales-by-freezer', compact('data', 'grandTotal', 'startDate', 'endDate'));
    }

    /**
     * Report #38: Bad Order Report
     * Track damaged/returned products
     */
    public function badOrderReport(Request $request)
    {
        $branchCode = session('branch_code');
        $period = $request->input('period', 'monthly'); // daily, weekly, monthly, yearly
        
        $now = Carbon::now();
        
        switch ($period) {
            case 'daily':
                $startDate = $now->startOfDay();
                $endDate = $now->endOfDay();
                break;
            case 'weekly':
                $startDate = $now->startOfWeek();
                $endDate = $now->endOfWeek();
                break;
            case 'yearly':
                $startDate = $now->startOfYear();
                $endDate = $now->endOfYear();
                break;
            case 'monthly':
            default:
                $startDate = $now->startOfMonth();
                $endDate = $now->endOfMonth();
                break;
        }

        $data = Inbound::where('branch_code', $branchCode)
            ->whereBetween('order_date', [$startDate, $endDate])
            ->where('bo_amount', '>', 0)
            ->select('id', 'customer_name', 'bo_amount', 'order_date', 'products')
            ->orderBy('order_date', 'desc')
            ->get();

        $grandTotal = $data->sum('bo_amount');

        return view('reports.bad-order-report', compact('data', 'grandTotal', 'startDate', 'endDate', 'period'));
    }

    /**
     * Report #39: Delivery Receipt Report
     * List DRs with totals
     */
    public function deliveryReceiptReport(Request $request)
    {
        $branchCode = session('branch_code');
        $startDate = $request->input('start_date', now()->startOfMonth()->format('Y-m-d'));
        $endDate = $request->input('end_date', now()->endOfMonth()->format('Y-m-d'));
        $drSearch = $request->input('dr_search');

        $query = Inbound::where('branch_code', $branchCode)
            ->whereBetween('order_date', [$startDate, $endDate])
            ->whereNotNull('degic_no')
            ->whereNotIn('status', ['Cancelled', 'Deleted']);

        if ($drSearch) {
            $query->where('degic_no', 'LIKE', "%{$drSearch}%");
        }

        $data = $query->select('id', 'degic_no', 'customer_name', 'order_date', 'products')
            ->orderBy('order_date', 'desc')
            ->get();

        // Calculate totals
        $data->each(function ($record) {
            $products = json_decode($record->products, true);
            $total = 0;
            if (is_array($products)) {
                foreach ($products as $product) {
                    $total += ($product['quantity'] ?? 0) * ($product['price'] ?? 0);
                }
            }
            $record->total_amount = $total;
        });

        $grandTotal = $data->sum('total_amount');

        return view('reports.delivery-receipt-report', compact('data', 'grandTotal', 'startDate', 'endDate', 'drSearch'));
    }

    /**
     * Report #40: Sales by Flavor Report
     * Similar to product type but focus on variant/flavor
     */
    public function salesByFlavor(Request $request)
    {
        $branchCode = session('branch_code');
        $startDate = $request->input('start_date', now()->startOfMonth()->format('Y-m-d'));
        $endDate = $request->input('end_date', now()->endOfMonth()->format('Y-m-d'));

        $inbounds = Inbound::where('branch_code', $branchCode)
            ->whereBetween('order_date', [$startDate, $endDate])
            ->whereNotIn('status', ['Cancelled', 'Deleted'])
            ->get();

        // Parse products and aggregate by flavor
        $flavorSummary = [];
        foreach ($inbounds as $inbound) {
            $products = json_decode($inbound->products, true);
            if (is_array($products)) {
                foreach ($products as $product) {
                    $flavor = $product['ptype_code'] ?? 'Unknown';
                    $quantity = $product['quantity'] ?? 0;
                    $price = $product['price'] ?? 0;
                    $amount = $quantity * $price;

                    if (!isset($flavorSummary[$flavor])) {
                        $flavorSummary[$flavor] = [
                            'flavor' => $flavor,
                            'quantity' => 0,
                            'amount' => 0,
                            'orders_count' => 0,
                        ];
                    }

                    $flavorSummary[$flavor]['quantity'] += $quantity;
                    $flavorSummary[$flavor]['amount'] += $amount;
                    $flavorSummary[$flavor]['orders_count']++;
                }
            }
        }

        $data = collect($flavorSummary)->values();
        $totalQuantity = $data->sum('quantity');
        $totalAmount = $data->sum('amount');
        $totalOrders = $data->sum('orders_count');

        return view('reports.sales-by-flavor', compact('data', 'totalQuantity', 'totalAmount', 'totalOrders', 'startDate', 'endDate'));
    }
}
