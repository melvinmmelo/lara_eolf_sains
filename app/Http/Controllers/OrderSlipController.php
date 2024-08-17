<?php

namespace App\Http\Controllers;

use App\Models\Delivery;
use App\Models\Drivers;
use App\Models\Inbound;
use App\Models\OrderSlip;
use Illuminate\Http\Request;

class OrderSlipController extends Controller
{

    public function organizeUpdate(Request $request)
    {
        $request->validate([
            'item' => 'required|array',
        ]);

        $cnt = 1;
        foreach ($request->item as $inboundId) {
            $inbound = Inbound::find($inboundId);
            $inbound->order_slip_sno = $cnt++;
            $inbound->save();
        }

        return response()->json(['success' => 'success']);
    }

    public function index()
    {
        $orderSlips = OrderSlip::branchCode(session('branch_code'))->get();
        return view('orderslip.index', compact('orderSlips'));
    }

    public function organize(Request $request)
    {

        $orderSlip = OrderSlip::where('code', $request->code)->first();
        $inbounds = Inbound::where('order_slip_code', $request->code)->orderBy('order_slip_sno')->get();

        return view('orderslip.organize', compact('orderSlip', 'inbounds'));
    }

    public function generate()
    {

        $drivers = Drivers::active()->perDesignation('Driver')->get();
        $deliveryPersons = Drivers::active()->perDesignation('Salesman')->get();

        $inbounds = Inbound::branch(session('branch_code'))->forOrderSlip()->WithProducts()->get();

        return view('orderslip.generate', compact('inbounds', 'drivers', 'deliveryPersons'));
    }

    public function print(Request $request)
    {
        $request->validate([
            'inboundIds' => 'required|array',
        ]);

        $validated = $request->validate([
            'delivery_person' => 'required',
            'driver_name' => 'required',
            'checked_by' => 'nullable',
            'remarks' => 'nullable',
        ]);

        $validated['branch_code'] = session('branch_code');

        $validated['total_amount'] = 0;

        $validated['code'] = date('y') . "-" . str_pad(OrderSlip::count() + 1, 5, "0", STR_PAD_LEFT);

        if(OrderSlip::where('code', $validated['code'])->exists()){
            $validated['code'] = date('y') . "-" . str_pad(OrderSlip::count() + 2, 5, "0", STR_PAD_LEFT);
        }

        $validated['generated_by'] = auth()->user()->fullName;

        // $check = OrderSlip::where('delivery_person', $validated['delivery_person'])->whereDate('created_at', now()->toDateString())->first();
        // if ($check) {
        //     return redirect()->route('report.orderSlip', ['code' => $check->code])->with('success', 'Order Slip created successfully.');
        // }

        $cnt = 1;

        foreach ($request->inboundIds as $inboundId) {

            $inbound = Inbound::find($inboundId);

            $inbound->order_slip_code = $validated['code'];

            $inbound->order_slip_sno = $cnt++;

            $products = json_decode($inbound->products, true);

            $validated['total_amount'] += getTotalOfProducts($products);

            $inbound->save();
        }

        OrderSlip::create($validated);

        return redirect()->route('orderSlip.organize', ['code' => $validated['code']])->with('success', 'Order Slip created successfully.');

        // return redirect()->route('report.orderSlip', ['code' => $validated['code']])->with('success', 'Order Slip created successfully.');
    }
    public function show($code)
    {
        return redirect()->route('report.orderSlip', ['code' => $code])->with('success', 'Order Slip created successfully.');
    }
}
