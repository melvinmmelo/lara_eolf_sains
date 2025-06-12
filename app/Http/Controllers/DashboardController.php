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
        $yearlySales = Inbound::branch(session('branch_code'))
            ->whereNotNull('order_date')
            ->whereIn('status', ['Paid', 'Completed'])
            ->get()
            ->groupBy(function($item) {
                return Carbon::parse($item->order_date)->year;
            })
            ->map(function($items, $year) {
                return [
                    'year' => (int)$year,
                    'amount' => (float)$items->sum('grand_total'),
                    'count' => (int)$items->count()
                ];
            })
            ->sortByDesc('year')
            ->values();


        // Get monthly sales data for current year
        $monthlySales = collect();
        $currentYear = Carbon::now()->year;

        for ($month = 1; $month <= 12; $month++) {
            $date = Carbon::createFromDate($currentYear, $month, 1);
            $monthData = Inbound::branch(session('branch_code'))
                ->whereYear('order_date', $currentYear)
                ->whereMonth('order_date', $month)
                ->whereIn('status', ['Paid', 'Completed'])
                ->get();

            $monthlySales->push([
                'month' => $date->format('M'),
                'amount' => (float)$monthData->sum('grand_total')
            ]);
        }
 

        $todaysOrders = Inbound::branch(session('branch_code'))
            ->whereNotIn('status', ['Cancelled', 'Rejected', 'Deleted'])
            ->whereDate('order_date', $today)
            ->orderBy('order_slip_sno')->get();

        // Calculate today's order metrics
        $todaysOrdersCount = $todaysOrders->count();
        $todaysOrdersAmount = $todaysOrders->sum(function($order) {
            return $order->grand_total;
        });
        $todaysPaidOrders = $todaysOrders->where('status', 'Paid');
        $todaysPaidCount = $todaysPaidOrders->count();
        $todaysPaidAmount = $todaysPaidOrders->sum('delivered_amount');
        $todaysCompletedOrders = $todaysOrders->where('status', 'Completed');
        $todaysCompletedCount = $todaysCompletedOrders->count();
        $todaysCompletedAmount = $todaysCompletedOrders->sum('delivered_amount');

        // Get pending deliveries
        $pendingDeliveries = Inbound::branch(session('branch_code'))
            ->whereDoesntHave('deliveryReceipt')
            ->with(['customer', 'deliveryReceipt'])
            ->latest()
            ->take(5)
            ->get();

        // Get recent activities using Spatie's activity log
        $recentActivities = Activity::latest()
            ->take(10)
            ->get();

        // Get customers with no sales in last 2 months
        $twoMonthsAgo = Carbon::now()->subMonths(2);
        $inactiveCustomers = Customers::branch(session('branch_code'))
            ->whereDoesntHave('inbounds', function($query) use ($twoMonthsAgo) {
                $query->where('order_date', '>=', $twoMonthsAgo)
                    ->whereIn('status', ['Paid', 'Completed']);
            })
            ->with(['equipmentStores'])
            ->latest()
            ->get();

        $data = [
            'products_count' => Product::count(),
            'orders_count' => Inbound::branch(session('branch_code'))->count(),
            'customers_count' => Customers::branch(session('branch_code'))->count(),
            'todays_orders_count' => $todaysOrdersCount,
            'todays_orders_amount' => $todaysOrdersAmount,
            'todays_paid_count' => $todaysPaidCount,
            'todays_paid_amount' => $todaysPaidAmount,
            'todays_completed_count' => $todaysCompletedCount,
            'todays_completed_amount' => $todaysCompletedAmount,
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
            'recent_activities' => $recentActivities,
            'inactive_customers' => $inactiveCustomers
        ];

        return view('dashboard', $data);
    }
}
