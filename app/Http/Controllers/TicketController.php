<?php

namespace App\Http\Controllers;

use App\Models\Inbound;
use Illuminate\Http\Request;

class TicketController extends Controller
{
    //

    public function generate()
    {
        $inbounds = Inbound::branch(session('branch_code'))->forLoading()->get();
        return view('ticket.index', compact('inbounds'));
    }


    public function print(Request $request)
    {

        $request->validate([
            'inboundIds' => 'required',
        ]);

        $grpPrintTicketNo = date('YmdHis') . Inbound::max('id') + 1;

        $inbounds = Inbound::whereIn('id', $request->inboundIds)->get();
        $cnt = 1;

        foreach ($inbounds as $inbound) {
            $inbound->grp_print_ticket_no = $grpPrintTicketNo;
            $inbound->ticket_sequence_no = $cnt++;
            $inbound->save();
        }

        dd("generated");

        return redirect()->route('ticket.form', compact('inbounds', 'grpPrintTicketNo'));
    }
}
