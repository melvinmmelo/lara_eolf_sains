<?php

namespace App\Http\Controllers;

use App\Models\Inbound;
use App\Models\OrderSlip;
use Illuminate\Http\Request;

class ReportGeneratorController extends Controller
{
    //

    public function orderSlip($code)
    {

        $orderSlip = OrderSlip::where('code', $code)->first();
        $inbounds = Inbound::where('order_slip_code', $code)->get();
        $totalInbounds = count($inbounds);
        $totalFitProducts = 22;

        $j = 0; // counter of the inbounds
        $deno = 7;
        $totalPages = 1;
        $remainder = 0;

        if($totalInbounds > $deno){
            $totalPages = ceil($totalInbounds / $deno);
            $remainder = $totalInbounds % $deno;
            // dd("total pages: " . $totalPages);
        }

        if($totalPages === 1){
            $deno = $totalInbounds % $deno;
        }

        $grandTotal = 0;

        // iterate through the products and get the total quantity and price
        foreach ($inbounds as $inbound) {
            $products = json_decode($inbound->products, true);
            $grandTotal += $inbound->totalAmount;
        }


        // dd($inbounds[0]);
        // dd($remainder);
        // dd($totalPages);
        // dd($totalInbounds);


        return view('report.orderSlip', compact('inbounds', 'code', 'orderSlip', 'grandTotal', 'totalInbounds', 'totalPages', 'deno', 'remainder', 'j', 'totalFitProducts'));
    }
}
