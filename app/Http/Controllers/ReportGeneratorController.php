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

        $grandTotal = 0;

        // iterate through the products and get the total quantity and price
        foreach ($inbounds as $inbound) {
            $products = json_decode($inbound->products, true);
            $grandTotal += getTotalOfProducts($products);
        }

        return view('report.orderSlip', compact('inbounds', 'code', 'orderSlip', 'grandTotal'));
    }
}
