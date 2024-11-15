<?php

namespace App\Http\Controllers;

use App\Models\Inbound;
use App\Models\LoadingTicket;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Session;

class TicketController extends Controller
{
    //

    public function index()
    {
        $loadingTickets = LoadingTicket::where('branch_code', session('branch_code'))->get();
        return view('ticket.index', compact('loadingTickets'));
    }

    public function show($grp)
    {
        $inbounds = Inbound::where('grp_print_ticket_no', $grp)->get();
        return view('ticket.show', compact('inbounds', 'grp'));
    }

    public function generate()
    {

        $ticketdetails = "";
        $sorted_product_codes = DB::table('product_types')->orderBy('sequence_no', 'asc')->select('code','spoon_pcs_per_bag')->get();
        $inbounds = Inbound::branch(session('branch_code'))->forLoading()->WithProducts()->activeOrders()->get();

        $print = false;

        return view('ticket.generate', compact('inbounds','ticketdetails','sorted_product_codes', 'print'));

    }

    public function print(Request $request)
    {

        $sorted_product_codes = DB::table('product_types')->orderBy('sequence_no', 'asc')->select('code', 'spoon_pcs_per_bag')->get();

        $request->validate([
            'inboundIds' => 'required',
        ]);

        $ticketNo = LoadingTicket::generateTicketNo(session('branch_code'));

        LoadingTicket::create([
            'ticket_no' => $ticketNo,
            'branch_code' => session('branch_code'),
            'user_name' => auth()->user()->fullName,
        ]);

        $inbounds = Inbound::whereIn('id', $request->inboundIds)->get();

        $cnt = 1;
        foreach ($inbounds as $inbound) {
            $inbound->grp_print_ticket_no = $ticketNo;
            $inbound->ticket_sequence_no = $cnt++;
            $inbound->save();
        }

        $ticketdetails = Inbound::where('grp_print_ticket_no', $ticketNo)->get();

        session()->put('ticketnum', $ticketNo);

        $print = true;

        return view('ticket.generate', compact('inbounds', 'ticketdetails', 'sorted_product_codes', 'print'));
    }

    public function reprint(Request $request)
    {
        session()->put('ticketnum', $request->grp);
        return redirect()->route('generate-ticket');
    }
}
