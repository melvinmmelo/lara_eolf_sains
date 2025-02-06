<?php

namespace App\Http\Controllers;

use App\Models\Product;
use App\Models\Inbound;
use App\Models\Customers;
use App\Models\Delivery;
use App\Models\ItemMasterData;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;
use Spatie\Activitylog\Models\Activity;

class DashboardController extends Controller
{
    public function index()
    {
        $today = Carbon::today();
        $startOfYear = Carbon::now()->startOfYear();
        $threeYearsAgo = Carbon::now()->subYears(3)->startOfYear();

        // Debug: Check raw data first
        $rawData = Inbound::select('id', 'branch_code','order_date', 'status', 'delivered_amount')
            ->where('branch_code', '=', session()->get('branch_code'))
            ->whereIn('status', ['Paid', 'Completed'])
            ->orderBy('order_date', 'desc')
            ->limit(10)
            ->get();


        // Get low stock items
        $lowStockItems = ItemMasterData::branch(session('branch_code'))
            ->with(['product.productType', 'product.productVariant'])
            ->where('branch_code', '=', session()->get('branch_code'))
            ->whereRaw('stocks - reserved <= ?', [10])
            ->orderBy('stocks')
            ->get()
            ->map(function($item) {
                return [
                    'product_name' => $item->product_name,
                    'stocks' => $item->stocks,
                    'reserved' => $item->reserved,
                    'available' => $item->available_stocks,
                    'branch_code' => $item->branch_code
                ];
            });

        // Get yearly sales data
        $yearlySales = Inbound::select([
            DB::raw('YEAR(order_date) as year'),
            DB::raw('COUNT(*) as order_count'),
            DB::raw('SUM(COALESCE(delivered_amount, 0)) as total')
        ])
        ->where('branch_code', '=', session()->get('branch_code'))
        ->whereNotNull('order_date')
        ->whereNotNull('delivered_amount')
        ->whereIn('status', ['Paid', 'Completed'])
        ->groupBy(DB::raw('YEAR(order_date)'))
        ->orderBy('year', 'desc')
        ->get()
        ->map(function($item) {
            return [
                'year' => (int)$item->year,
                'amount' => (float)$item->total,
                'count' => (int)$item->order_count
            ];
        });


        // Get monthly sales data for current year
        $monthlySales = collect();
        $currentYear = Carbon::now()->year;

        for ($month = 1; $month <= 12; $month++) {
            $date = Carbon::createFromDate($currentYear, $month, 1);
            $monthData = Inbound::select([
                DB::raw('COUNT(*) as order_count'),
                DB::raw('SUM(COALESCE(delivered_amount, 0)) as total')
            ])
            ->where('branch_code', '=', session()->get('branch_code'))
            ->whereYear('order_date', $currentYear)
            ->whereMonth('order_date', $month)
            ->whereIn('status', ['Paid', 'Completed'])
            ->first();

            $monthlySales->push([
                'month' => $date->format('M'),
                'amount' => (float)($monthData->total ?? 0)
            ]);
        }

        // Get today's orders metrics
        $todaysOrders = Inbound::whereDate('order_date', $today)
            ->where('branch_code', '=', session()->get('branch_code'))
            ->select([
                DB::raw('COUNT(*) as count'),
                DB::raw('SUM(
                    CASE
                        WHEN products IS NOT NULL AND JSON_LENGTH(products) > 0 THEN (
                            SELECT SUM(
                                CAST(JSON_UNQUOTE(JSON_EXTRACT(products, CONCAT("$[", numbers.n, "].quantity"))) AS DECIMAL) *
                                CAST(JSON_UNQUOTE(JSON_EXTRACT(products, CONCAT("$[", numbers.n, "].price"))) AS DECIMAL)
                            )
                            FROM (
                                SELECT 0 AS n UNION SELECT 1 UNION SELECT 2 UNION SELECT 3 UNION SELECT 4 UNION
                                SELECT 5 UNION SELECT 6 UNION SELECT 7 UNION SELECT 8 UNION SELECT 9
                            ) numbers
                            WHERE numbers.n < JSON_LENGTH(products)
                        ) +
                        CASE WHEN is_with_sf = 1 THEN 1000 ELSE 0 END -
                        COALESCE(bo_amount, 0) -
                        COALESCE(discount, 0)
                        ELSE 0
                    END
                ) as total'),
                DB::raw('SUM(CASE
                    WHEN status = "Paid" THEN (
                        CASE
                            WHEN products IS NOT NULL AND JSON_LENGTH(products) > 0 THEN (
                                SELECT SUM(
                                    CAST(JSON_UNQUOTE(JSON_EXTRACT(products, CONCAT("$[", numbers.n, "].quantity"))) AS DECIMAL) *
                                    CAST(JSON_UNQUOTE(JSON_EXTRACT(products, CONCAT("$[", numbers.n, "].price"))) AS DECIMAL)
                                )
                                FROM (
                                    SELECT 0 AS n UNION SELECT 1 UNION SELECT 2 UNION SELECT 3 UNION SELECT 4 UNION
                                    SELECT 5 UNION SELECT 6 UNION SELECT 7 UNION SELECT 8 UNION SELECT 9
                                ) numbers
                                WHERE numbers.n < JSON_LENGTH(products)
                            ) +
                            CASE WHEN is_with_sf = 1 THEN 1000 ELSE 0 END -
                            COALESCE(bo_amount, 0) -
                            COALESCE(discount, 0)
                            ELSE 0
                        END
                    )
                    ELSE 0 END) as paid_amount'),
                DB::raw('COUNT(CASE WHEN status = "Paid" THEN 1 END) as paid_count'),
                DB::raw('SUM(CASE
                    WHEN status = "Completed" THEN (
                        CASE
                            WHEN products IS NOT NULL AND JSON_LENGTH(products) > 0 THEN (
                                SELECT SUM(
                                    CAST(JSON_UNQUOTE(JSON_EXTRACT(products, CONCAT("$[", numbers.n, "].quantity"))) AS DECIMAL) *
                                    CAST(JSON_UNQUOTE(JSON_EXTRACT(products, CONCAT("$[", numbers.n, "].price"))) AS DECIMAL)
                                )
                                FROM (
                                    SELECT 0 AS n UNION SELECT 1 UNION SELECT 2 UNION SELECT 3 UNION SELECT 4 UNION
                                    SELECT 5 UNION SELECT 6 UNION SELECT 7 UNION SELECT 8 UNION SELECT 9
                                ) numbers
                                WHERE numbers.n < JSON_LENGTH(products)
                            ) +
                            CASE WHEN is_with_sf = 1 THEN 1000 ELSE 0 END -
                            COALESCE(bo_amount, 0) -
                            COALESCE(discount, 0)
                            ELSE 0
                        END
                    )
                    ELSE 0 END) as completed_amount'),
                DB::raw('COUNT(CASE WHEN status = "Completed" THEN 1 END) as completed_count')
            ])
            ->first();

        // Get pending deliveries
        $pendingDeliveries = Inbound::branch(session('branch_code'))
            ->whereDoesntHave('deliveryReceipt')
            ->orWhereHas('deliveryReceipt', function($query) {
                $query->where('status', '!=', 'delivered');
            })
            ->with(['customer', 'deliveryReceipt'])
            ->latest()
            ->take(5)
            ->get();

        // Get recent activities using Spatie's activity log
        $recentActivities = Activity::latest()
            ->take(10)
            ->get();

        $data = [
            'products_count' => Product::count(),
            'orders_count' => Inbound::branch(session('branch_code'))->count(),
            'customers_count' => Customers::branch(session('branch_code'))->count(),
            'deliveries_count' => Delivery::count(),
            'todays_orders_count' => $todaysOrders->count ?? 0,
            'todays_orders_amount' => $todaysOrders->total ?? 0,
            'todays_paid_count' => $todaysOrders->paid_count ?? 0,
            'todays_paid_amount' => $todaysOrders->paid_amount ?? 0,
            'todays_completed_count' => $todaysOrders->completed_count ?? 0,
            'todays_completed_amount' => $todaysOrders->completed_amount ?? 0,
            'yearly_sales' => $yearlySales,
            'monthly_sales' => $monthlySales,
            'pending_deliveries' => $pendingDeliveries,
            'recent_orders' => Inbound::branch(session('branch_code'))
                ->with(['customer', 'deliveryReceipt'])
                ->latest()
                ->take(5)
                ->get(),
            'inventory_status' => Product::with(['productType', 'productVariant'])
                ->take(5)
                ->get(),
            'low_stock_items' => $lowStockItems,
            'recent_activities' => $recentActivities
        ];

        return view('dashboard', $data);
    }
}
