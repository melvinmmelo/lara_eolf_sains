<?php

namespace App\Http\Controllers;

use App\Models\Inbound;
use App\Models\OrderSlip;
use App\Services\InboundService;
use Illuminate\Http\Request;

use function Ramsey\Uuid\v1;

class ReportGeneratorController extends Controller
{

    public function productsSummary()
    {
        $branchCode = session('branch_code');

        $products = InboundService::getTotalOfAllInboundProducts($branchCode); // ! you can add 2nd parameter for date sample "2024-08-08"

        return view('report.productsSummary', compact('products'));
    }
    public function orderSlip($code)
    {
        $orderSlip = OrderSlip::where('code', $code)->first();
        $inbounds = Inbound::where('order_slip_code', $code)->get();
        $totalInbounds = $inbounds->count();

        $pagesData = $this->distributeInboundsToPages($inbounds);
        $totalPages = count($pagesData);
        $grandTotal = $inbounds->sum('totalAmount');


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
}
