<?php

namespace App\Http\Controllers;

use App\Models\Product;
use App\Models\ProductType;
use App\Models\Inbound;
use App\Models\Customers;
use App\Models\Delivery;
use App\Models\ItemMasterData;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;
use Spatie\Activitylog\Models\Activity;

class DashboardController extends Controller
{
    public function index()
    {
        $today = Carbon::today();
        $currentYear = Carbon::now()->year;
        $branchCode = session('branch_code');

        // Cache heavy analytics per branch — single pass over all inbounds
        $analytics = Cache::remember("dashboard_analytics:{$branchCode}", now()->addMinutes(10), function () use ($branchCode, $currentYear) {
            $productLookup = Product::with(['productType', 'productVariant'])->get()->keyBy('code');
            $productTypeNames = ProductType::all()->keyBy('code');

            $salesByProductType = [];
            $salesByVariant = [];
            $yearlySalesAcc = [];
            $monthlySalesAcc = [];

            // Single chunk pass replaces 3 separate heavy queries
            Inbound::branch($branchCode)
                ->whereIn('status', ['Paid', 'Completed'])
                ->whereNotNull('products')
                ->select('id', 'order_date', 'products', 'is_with_sf')
                ->chunkById(500, function ($inbounds) use (&$salesByProductType, &$salesByVariant, &$yearlySalesAcc, &$monthlySalesAcc, $productLookup, $currentYear) {
                    foreach ($inbounds as $inbound) {
                        $products = json_decode($inbound->products, true);

                        if (is_array($products)) {
                            foreach ($products as $product) {
                                $ptypeCode = $product['ptype_code'] ?? null;
                                $code = $product['code'] ?? null;
                                $quantity = (float)($product['quantity'] ?? 0);

                                if ($ptypeCode) {
                                    $salesByProductType[$ptypeCode] = ($salesByProductType[$ptypeCode] ?? 0) + $quantity;
                                }

                                if ($code && isset($productLookup[$code])) {
                                    $variant = $productLookup[$code]->productVariant;
                                    if ($variant) {
                                        $variantName = $variant->name;
                                        $salesByVariant[$variantName] = ($salesByVariant[$variantName] ?? 0) + $quantity;
                                    }
                                }
                            }
                        }

                        if ($inbound->order_date) {
                            $date = Carbon::parse($inbound->order_date);
                            $year = $date->year;
                            $grandTotal = $inbound->grand_total;

                            if (!isset($yearlySalesAcc[$year])) {
                                $yearlySalesAcc[$year] = ['amount' => 0.0, 'count' => 0];
                            }
                            $yearlySalesAcc[$year]['amount'] += $grandTotal;
                            $yearlySalesAcc[$year]['count']++;

                            if ($year === $currentYear) {
                                $monthNum = $date->month;
                                $monthlySalesAcc[$monthNum] = ($monthlySalesAcc[$monthNum] ?? 0.0) + $grandTotal;
                            }
                        }
                    }
                });

            $salesVolumeByType = collect($salesByProductType)
                ->map(fn($qty, $code) => ['name' => isset($productTypeNames[$code]) ? $productTypeNames[$code]->name : $code, 'quantity' => (int)$qty])
                ->sortByDesc('quantity')
                ->values();

            $salesVolumeByFlavor = collect($salesByVariant)
                ->map(fn($qty, $name) => ['name' => $name, 'quantity' => (int)$qty])
                ->sortByDesc('quantity')
                ->values();

            $yearlySales = collect($yearlySalesAcc)
                ->map(fn($data, $year) => ['year' => (int)$year, 'amount' => (float)$data['amount'], 'count' => (int)$data['count']])
                ->sortByDesc('year')
                ->values();

            $monthlySales = collect();
            for ($month = 1; $month <= 12; $month++) {
                $date = Carbon::createFromDate($currentYear, $month, 1);
                $monthlySales->push([
                    'month' => $date->format('M'),
                    'amount' => (float)($monthlySalesAcc[$month] ?? 0.0),
                ]);
            }

            return compact('salesVolumeByType', 'salesVolumeByFlavor', 'yearlySales', 'monthlySales');
        });

        // Today's orders — not cached, must be fresh; select only needed columns
        $todaysOrders = Inbound::branch($branchCode)
            ->whereNotIn('status', ['Cancelled', 'Rejected', 'Deleted'])
            ->whereDate('order_date', $today)
            ->select('id', 'status', 'products', 'is_with_sf', 'delivered_amount', 'order_slip_sno')
            ->orderBy('order_slip_sno')
            ->get();

        $todaysOrdersCount = $todaysOrders->count();
        $todaysOrdersAmount = $todaysOrders->sum(fn($order) => $order->grand_total);
        $todaysPaidOrders = $todaysOrders->where('status', 'Paid');
        $todaysPaidCount = $todaysPaidOrders->count();
        $todaysPaidAmount = $todaysPaidOrders->sum('delivered_amount');
        $todaysCompletedOrders = $todaysOrders->where('status', 'Completed');
        $todaysCompletedCount = $todaysCompletedOrders->count();
        $todaysCompletedAmount = $todaysCompletedOrders->sum('delivered_amount');

        $pendingDeliveries = Inbound::branch($branchCode)
            ->whereDoesntHave('deliveryReceipt')
            ->with(['customer'])
            ->latest()
            ->take(5)
            ->get();

        $recentActivities = Activity::latest()->take(10)->get();

        $data = [
            'products_count' => Product::count(),
            'orders_count' => Inbound::branch($branchCode)->count(),
            'customers_count' => Customers::branch($branchCode)->count(),
            'todays_orders_count' => $todaysOrdersCount,
            'todays_orders_amount' => $todaysOrdersAmount,
            'todays_paid_count' => $todaysPaidCount,
            'todays_paid_amount' => $todaysPaidAmount,
            'todays_completed_count' => $todaysCompletedCount,
            'todays_completed_amount' => $todaysCompletedAmount,
            'yearly_sales' => $analytics['yearlySales'],
            'monthly_sales' => $analytics['monthlySales'],
            'pending_deliveries' => $pendingDeliveries,
            'recent_orders' => Inbound::branch($branchCode)
                ->with(['customer', 'deliveryReceipt'])
                ->latest()
                ->take(5)
                ->get(),
            'inventory_status' => Product::with(['productType', 'productVariant'])
                ->take(5)
                ->get(),
            'recent_activities' => $recentActivities,
            'sales_volume_by_type' => $analytics['salesVolumeByType'],
            'sales_volume_by_flavor' => $analytics['salesVolumeByFlavor'],
        ];

        return view('dashboard', $data);
    }
}
