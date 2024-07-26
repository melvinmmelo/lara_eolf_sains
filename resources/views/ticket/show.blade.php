@extends('layouts.app')

@section('custom_css')
@endsection

@section('contents')
    <!-- Content Header (Page header) -->
    <section class="content-header">
        <div class="container-fluid">
            <div class="row mb-2">
                <div class="col-sm-6">
                    <h1>Loading  Ticket {{ $grp }}</h1>
                </div>
                <div class="col-sm-6">
                    <ol class="breadcrumb float-sm-right">
                        <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Home</a></li>
                        <li class="breadcrumb-item active">Loading Ticket {{ $grp }}</li>
                    </ol>
                </div>
            </div>
        </div>
    </section>

    <!-- Main content -->
    <section class="content">

        @include('layouts.errors')

        <form action="{{ route('reprint-ticket') }}" method="POST">
            @csrf
            <input type="hidden" name="grp" value="{{ $grp }}">
            <div class="card">
                <div class="card-body">
                    <div class="pb-2">
                        <button type="submit" class="btn btn-primary">
                            Reprint
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
                                        <td>{{ $inbound->degic_no }}</td>
                                        <td>{{ $inbound->customer_name }}</td>
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
                <div class="card-footer">
                    <button type="submit" class="btn btn-primary">
                        Reprint
                    </button>
                </div>
            </div>
        </form>
        <!-- /.card -->
    </section>
    <!-- /.content -->
@endsection
