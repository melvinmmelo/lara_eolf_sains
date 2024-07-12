@extends('layouts.app')

@section('custom_css')
@endsection

@section('contents')
    <!-- Content Header (Page header) -->
    <section class="content-header">
        <div class="container-fluid">
            <div class="row mb-2">
                <div class="col-sm-6">
                    <h1>Generate Loading  Ticket</h1>
                </div>
                <div class="col-sm-6">
                    <ol class="breadcrumb float-sm-right">
                        <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Home</a></li>
                        <li class="breadcrumb-item active">Generate Loading Ticket</li>
                    </ol>
                </div>
            </div>
        </div>
    </section>

    <!-- Main content -->
    <section class="content">

        @include('layouts.errors')

        <form action="{{ route('print-ticket') }}" method="POST">
            @csrf
            <div class="card">
                <div class="card-body">
                    <div class="pb-2">
                        <button type="submit" class="btn btn-primary">
                            Print
                        </button>
                    </div>
                    <div class="tbContainer">

                        <table id="example3" class="table table-bordered table-striped">
                            <thead>
                                <tr>
                                    <th></th>
                                    <th>Date created</th>
                                    <th>Order No.</th>
                                    <th>Degic No.</th>
                                    <th>Customer</th>
                                    <th>Invoice Amount</th>
                                    <th>Balance Due</th>
                                    <th>Status</th>
                                    <th>Days Overdue</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($inbounds as $inbound)
                                    @php
                                        $total = $inbound->totalAmount;
                                    @endphp

                                    <tr>
                                        <td>
                                            <input type="checkbox" name="inboundIds[]" value="{{ $inbound->id }}" id="inboundIds{{ $inbound->id }}">
                                        </td>
                                        <td>{{ $inbound->f_created_at }}</td>
                                        <td>{{ $inbound->id }}</td>
                                        <td>{{ $inbound->equipment->serial_no }}</td>
                                        <td>{{ $inbound->customer->fullName }}</td>
                                        <td><span class="label label-primary">{{ $total }}</span></td>
                                        <td>{{ $total - $inbound->delivered_amount }}</td>
                                        <td>{{ $inbound->status }}</td>
                                        <td>{{ number_format($inbound->created_at->diffInDays(now()), 0) }}</td>
                                    </tr>
                                @endforeach
                            </tbody>
                            <tfoot>
                                <tr>
                                    <th></th>
                                    <th>Date created</th>
                                    <th>Order No.</th>
                                    <th>Degic No.</th>
                                    <th>Customer</th>
                                    <th>Invoice Amount</th>
                                    <th>Balance Due</th>
                                    <th>Status</th>
                                    <th>Days Overdue</th>
                                </tr>
                            </tfoot>
                        </table>
                    </div>
                </div>
                <!-- /.card-body -->
                <div class="card-footer">
                    <button type="submit" class="btn btn-primary">
                        Print
                    </button>
                </div>

                <!-- /.card-footer-->
            </div>
        </form>
        <!-- /.card -->
    </section>
    <!-- /.content -->
@endsection
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script>
    $(document).ready(function() {
        try {
            console.log("Document is ready");
            var curr_total=0;
            var last=0;
            var sp_count=0;
            var sp_set=0;
            @php
            if (Session::has('ticketnum')) {
                $prod = json_decode($ticketdetails[0]->products);
                //dd($prod);
            @endphp

            // Create a new window for printing
            var mywindow = window.open('', 'PRINT', 'height=600,width=600');
            if (!mywindow) {
                console.log("Failed to open print window. Pop-up blocked?");
                return;
            }

            mywindow.document.write('<html><head><title>LOADING TICKET</title>');
            mywindow.document.write('<style>');
            mywindow.document.write('body { font-family: "Arial", Helvetica, sans-serif; font-size: 9pt; word-wrap: break-word; }');
            mywindow.document.write('hr { border: none; border-top: 1px dotted black; }');
            mywindow.document.write('td { font-family: "Arial", Helvetica, sans-serif; font-size: 9pt; word-wrap: break-word; }');
            mywindow.document.write('</style>');
            mywindow.document.write('</head><body>');
            mywindow.document.write('<center>EOLF FOOD TRADING OPC</center><br>');
            mywindow.document.write('<center>LOADING TICKET</center><br><br><br>');
            mywindow.document.write('Sequence No: {{ $ticketdetails[0]->ticket_sequence_no }}<br>');
            mywindow.document.write('Date: {{ $ticketdetails[0]->updated_at }}<br>');
            mywindow.document.write('Delivery Person: (ID {{ $ticketdetails[0]->driver_id }}) {{ $ticketdetails[0]->driver->name }}<br>');
            mywindow.document.write('Customer: {{ $ticketdetails[0]->customer->fullName }} ({{ $ticketdetails[0]->store->storename }})<br>');
            mywindow.document.write('Encoded By: <br>');
            mywindow.document.write('<table width="100%">');
            mywindow.document.write('<tr><td>PRODUCT</td><td></td><td><center>QUANTITY</center></td><td align="right"></td></tr>');

            @foreach ($sorted_product_codes as $sorted_code)
            curr_total = 0;
            last = 0;
                @foreach ($prod as $product)

                    @if ($product->ptype_code == $sorted_code->code)

                        mywindow.document.write('<tr><td>{{ $product->code }}</td><td></td><td style="text-align: center;">{{ $product->quantity }}</td><td style="text-align: right;"></td></tr>');
                        curr_total += {{ $product->quantity }};
                        sp_count += {{ $sorted_code->spoon_pcs_per_bag }} * {{ $product->quantity }};
                        last=1;

                    @else
                        if(last==1){
                            last=1;
                        }
                        else{
                            last=0;
                        }

                    @endif
                @endforeach
                    if(last==0){

                    }
                    else{
                        mywindow.document.write('<tr><td></td><td></td><td></td><td align="left">[ '+curr_total +']</td></tr>');
                mywindow.document.write('<tr><td colspan=4><hr></td></tr>');
                    }
                    sp_set=sp_count/12;

            @endforeach


            mywindow.document.write('</table>');

            mywindow.document.write('<center>CUT FOR SPOON</center><br>');
            mywindow.document.write('Sequence No: {{ $ticketdetails[0]->ticket_sequence_no }}<br>');
            mywindow.document.write('Customer: {{ $ticketdetails[0]->customer->fullName }} ({{ $ticketdetails[0]->store->storename }})<br>');
            mywindow.document.write('Total Spoon Count: '+sp_count+'<br>');
            mywindow.document.write('Spoon Set: '+sp_set+'<br>');
            mywindow.document.write('<hr>');
            mywindow.document.write('</body></html>');

            mywindow.document.close(); // Necessary for IE >= 10
            mywindow.focus(); // Necessary for IE >= 10

            console.log("Printing...");
            mywindow.print();
            mywindow.close();

            @php
             Session::forget('ticketnum');
            }
            @endphp

            console.log("Print window closed");
        } catch (e) {
            console.error("Error in print script", e);
        }
    });
</script>
