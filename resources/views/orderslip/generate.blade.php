@extends('layouts.app')

@section('custom_css')
@endsection

@section('contents')
    <!-- Content Header (Page header) -->
    <section class="content-header">
        <div class="container-fluid">
            <div class="row mb-2">
                <div class="col-sm-6">
                    <h1>Generate Order Slip</h1>

                    <div>

                        <select class="form-control" name="driver_id" id="deliveryPerson" required>
                            <option value="">--Select Delivery Person--</option>
                            @foreach ($deliveryPersons as $dperson)
                                <option value="{{ $dperson->name }}">{{ $dperson->name }}</option>
                            @endforeach
                        </select>
                    </div>
                </div>
                <div class="col-sm-6">
                    <ol class="breadcrumb float-sm-right">
                        <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Home</a></li>
                        <li class="breadcrumb-item active">Generate Order Slip</li>
                    </ol>
                </div>
            </div>
        </div>
    </section>

    <!-- Main content -->
    <section class="content">

        @include('layouts.errors')

        <form action="{{ route('print-order-slip') }}" method="POST">
            @csrf
            <div class="card">
                <div class="card-body">
                    <div class="pb2">
                        <button type="button" class="btn btn-primary" data-toggle="modal" data-target="#modal-print">
                            Print
                        </button>
                        <a href="{{ route('order-slips') }}"><button type="button" class="btn btn-default">
                                Order Slips
                            </button></a>
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
                                    <th>Delivery Person</th>
                                    <th>Invoice Amount</th>
                                    <th>Balance Due</th>
                                    <th>Status</th>
                                    <th>Days Overdue</th>
                                    <th>Ticket No</th>
                                </tr>
                            </thead>
                            <tbody>
                                @php
                                    $grandTotal = [];
                                    $grandTotalBDue = [];
                                @endphp
                                @foreach ($inbounds as $inbound)
                                    @php
                                        $total = $inbound->totalAmount;
                                        $grandTotal[] = $total;
                                        $grandTotalBDue[] = $total - $inbound->delivered_amount;
                                    @endphp

                                    <tr>
                                        <td>
                                            <input type="checkbox" name="inboundIds[]" value="{{ $inbound->id }}"
                                                id="inboundIds{{ $inbound->id }}">
                                        </td>
                                        <td>{{ $inbound->f_created_at }}</td>
                                        <td>{{ $inbound->id }}</td>
                                        <td>{{ $inbound->equipment->code ?? '' }}</td>
                                        <td>{{ $inbound->customer->fullName }}</td>
                                        <td>{{ $inbound->delivery_person }}</td>
                                        <td><span class="label label-primary">{{ formatNumber($total) }}</span></td>
                                        <td>{{ formatNumber($total - $inbound->delivered_amount) }}</td>
                                        <td>{{ $inbound->status }}</td>
                                        <td>{{ number_format($inbound->created_at->diffInDays(now()), 0) }}</td>
                                        <td>{{ $inbound->grp_print_ticket_no }}</td>
                                    </tr>
                                @endforeach
                            </tbody>
                            <tfoot>
                                <tr>
                                    <th></th>
                                    <th></th>
                                    <th></th>
                                    <th></th>
                                    <th></th>
                                    <th>Total:</th>
                                    <th><span class="label label-primary">{{ formatNumber(array_sum($grandTotal)) }}</span>
                                    </th>
                                    <th>{{ formatNumber(array_sum($grandTotalBDue)) }}</th>
                                    <th></th>
                                    <th></th>
                                    <th></th>

                                </tr>
                            </tfoot>
                        </table>
                    </div>
                </div>
                <!-- /.card-body -->
                <div class="card-footer">
                    <button type="button" class="btn btn-primary" data-toggle="modal" data-target="#modal-print">
                        Print
                    </button>

                    <a href="{{ route('order-slips') }}"><button type="button" class="btn btn-default">
                            Order Slips
                        </button></a>
                </div>

                <!-- /.card-footer-->
            </div>


            <div class="modal fade" id="modal-print">
                <div class="modal-dialog">
                    <div class="modal-content">
                        <div class="modal-header">
                            <h4 class="modal-title">Encode details to print order slip</h4>
                            <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                <span aria-hidden="true">&times;</span>
                            </button>
                        </div>
                        <div class="modal-body">
                            <div class="form-group">
                                <label class="form-label" for="delivery_person">Delivery Person</label>
                                <input type="text" class="form-control" name="delivery_person" id="delivery_person"
                                    value="" required readonly>
                            </div>

                            <div class="form-group">
                                <label class="form-label" for="driver_name">Driver name</label>
                                <select class="form-control" name="driver_name" id="driver_name" required>
                                    <option value="">--Select--</option>
                                    @foreach ($drivers as $driver)
                                        <option value="{{ $driver->name }}">{{ $driver->name }}</option>
                                    @endforeach
                                </select>
                            </div>

                            <div class="form-group">
                                <label class="form-label" for="checked_by">Checked by</label>
                                <input type="text" class="form-control" name="checked_by" id="checked_by" value="">
                            </div>

                            <div class="form-group">
                                <label class="form-label" for="remarks">Remarks</label>
                                <input type="text" class="form-control" name="remarks" id="remarks" value="">
                            </div>

                            <div class="form-group">
                                <label class="form-label" for="generated_by">Generated by</label>
                                <input type="text" class="form-control" name="generated_by" id="generated_by"
                                    value=" {{ auth()->user()->fullName }}" required readonly>
                            </div>


                            <div class="form-group">
                                <label class="form-label" for="date_today">Date</label>
                                <input type="text" class="form-control" name="date_today" id="date_today"
                                    value=" {{ date('Y-m-d') }}" required readonly>
                            </div>

                            <div class="modal-footer">
                                <button type="submit" class="btn btn-success" name="submit_form"
                                    value="print">Print</button>
                            </div>

                        </div>
                    </div>
                </div>
            </div>
        </form>
        <!-- /.card -->
    </section>
    <!-- /.content -->
@endsection

@section('custom_js')
    <script>
        $(document).ready(function() {
            $('#deliveryPerson').on('change', function() {
                var value = $(this).val();
                $('#example3_filter input').val(value).trigger('keyup');
                document.getElementById('delivery_person').value = value;
            });
        });
    </script>
@endsection
