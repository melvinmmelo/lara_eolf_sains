<?php

namespace App\Http\Controllers;

use App\Models\Inbound;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Session;

class TicketController extends Controller
{
    //

    public function generate()
    {
        $ticketdetails="";
        $sorted_product_codes = DB::table('product_types')->orderBy('sequence_no', 'asc')->select('code','spoon_pcs_per_bag')->get();
        //dd($sorted_product_codes);
        $inbounds = Inbound::branch(session('branch_code'))->forLoading()->get();
        if(Session::has('ticketnum')){
            $ticketdetails = Inbound::where('grp_print_ticket_no', session()->get('ticketnum'))->get();
        }
        return view('ticket.index', compact('inbounds','ticketdetails','sorted_product_codes'));
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
        session()->put('ticketnum', $grpPrintTicketNo);

        return redirect()->route('generate-ticket');
    }
}
