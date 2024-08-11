<?php

namespace App\Http\Controllers;

use App\Models\Inbound;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Session;

class TicketController extends Controller
{
    //

    public function index()
    {

        $inbounds = Inbound::select('grp_print_ticket_no')->whereNotNull('grp_print_ticket_no')->groupBy('grp_print_ticket_no')->get();
        return view('ticket.index', compact('inbounds'));
    }

    public function show($grp)
    {
        $inbounds = Inbound::where('grp_print_ticket_no', $grp)->get();
        return view('ticket.show', compact('inbounds', 'grp'));
    }

    public function generate()
    {
        $ticketdetails="";
        $sorted_product_codes = DB::table('product_types')->orderBy('sequence_no', 'asc')->select('code','spoon_pcs_per_bag')->get();
        //dd($sorted_product_codes);
        $inbounds = Inbound::branch(session('branch_code'))->forLoading()->WithProducts()->activeOrders()->get();
        if(Session::has('ticketnum')){
            $ticketdetails = Inbound::where('grp_print_ticket_no', session()->get('ticketnum'))->get();
        }
        return view('ticket.generate', compact('inbounds','ticketdetails','sorted_product_codes'));
    }


    public function print(Request $request)
    {

        $request->validate([
            'inboundIds' => 'required',
        ]);

        $grpPrintTicketNo = "LT" .date('y') . "-" .str_pad(Inbound::max('id') + 1, 5, '0', STR_PAD_LEFT);

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

    public function reprint(Request $request)
    {
        session()->put('ticketnum', $request->grp);
        return redirect()->route('generate-ticket');
    }
}
