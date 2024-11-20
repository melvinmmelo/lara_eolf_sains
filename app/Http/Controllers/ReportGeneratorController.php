<?php

namespace App\Http\Controllers;

use App\Models\Inbound;
use App\Models\ItemMasterData;
use App\Models\OrderSlip;
use App\Models\ProductVariant;
use App\Services\InboundService;
use Illuminate\Http\Request;

use function Ramsey\Uuid\v1;

class ReportGeneratorController extends Controller
{


    public function productsSummaryv2()
    {
        $branchCode = session('branch_code');

        $products = InboundService::getTotalOfAllInboundProductsv2($branchCode); // ! for all outbound naman ito

        return view('report.productsSummaryv2', compact('products'));
    }


    public function productsSummary(Request $request)
    {
        $branchCode = session('branch_code');

        if ($request->filled('from_date') && $request->filled('to_date')) {
            $products = InboundService::getTotalOfAllInboundProducts($branchCode, $request->from_date, $request->to_date); // ! you can add 2nd parameter for date sample "2024-08-08"
            $title = "From: " . $request->from_date . " To: " . $request->to_date;
        } else {
            $products = InboundService::getTotalOfAllInboundProducts($branchCode); // ! you can add 2nd parameter for date sample "2024-08-08"
            $title = "Today";
        }


        return view('report.productsSummary', compact('products', 'title'));
    }
    public function orderSlip($code)
    {
        $orderSlip = OrderSlip::where('code', $code)->first();
        $inbounds = Inbound::whereNotIn('status', ['Cancelled', 'Rejected', 'Deleted'])->where('order_slip_code', $code)->orderBy('order_slip_sno')->get();
        $totalInbounds = $inbounds->count();

        $pagesData = $this->distributeInboundsToPages($inbounds);
        $totalPages = count($pagesData);
        $grandTotal = $inbounds->sum('grandTotal');


        return view('report.orderSlip', compact('inbounds', 'code', 'orderSlip', 'grandTotal', 'totalInbounds', 'totalPages', 'pagesData'));
    }

    private function distributeInboundsToPages($inbounds)
    {
        $pagesData = [];
        $currentPage = 1;
        $currentPageRows = 0;

        foreach ($inbounds as $inbound) {
            $productsCount = count(json_decode($inbound->products, true));
            $rowsNeeded = $this->calculateRowsNeeded($productsCount);

            if ($currentPageRows + $rowsNeeded > 9) {
                $currentPage++;
                $currentPageRows = 0;
            }

            $pagesData[$currentPage][] = $inbound;
            $currentPageRows += $rowsNeeded;
        }

        return $pagesData;
    }

    private function calculateRowsNeeded($productsCount)
    {
        if ($productsCount <= 23) {
            return 1;
        } elseif ($productsCount <= 45) {
            return 2;
        } else {
            return 3;
        }
    }


    public function availableStocks()
    {
        // First, get all unique variant codes
        $variantCodes = ItemMasterData::branch(session('branch_code'))
            ->get()
            ->pluck('product_code')
            ->map(function ($code) {
                $parts = explode('_', $code);
                return end($parts); // Get the last part after underscore
            })
            ->unique();

        // Preload all needed variants
        $variants = ProductVariant::whereIn('code', $variantCodes)
            ->get()
            ->keyBy('code');

        $products = ItemMasterData::branch(session('branch_code'))
            ->with(['product.productType'])
            ->select('id', 'product_code', 'product_description', 'reserved', 'stocks')
            ->get()
            ->map(function ($product) use ($variants) {
                $parts = explode('_', $product->product_code);
                $variantCode = end($parts); // Get the last part after underscore

                $product->available_stocks = $product->stocks - $product->reserved;
                $product->variant_name = $variants->get($variantCode)?->name ?? 'N/A';
                return $product;
            })
            ->groupBy(function ($product) {
                return $product->product->productType->name;
            })
            ->sortBy(function ($products, $key) {
                return $products->first()->product->productType->sequence_no;
            });

        return view('available-stocks', compact('products'));
    }
}
