@extends('layouts.app')

@section('custom_css')
@endsection

@section('contents')
    <!-- Content Header (Page header) -->
    <section class="content-header">
        <div class="container-fluid">
            <div class="row mb-2">
                <div class="col-sm-6">
                    <h1>Deducted BOs</h1>
                </div>
                <div class="col-sm-6">
                    <ol class="breadcrumb float-sm-right">
                        <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Home</a></li>
                        <li class="breadcrumb-item active">Deducted BOs</li>
                    </ol>
                </div>
            </div>
        </div>
    </section>

    <!-- Main content -->
    <section class="content">

        @include('layouts.errors')



        <form>
            <div class="card">
                <div class="card-body">

                    <div class="tbContainer">
                        <table id="example1" class="table table-bordered table-striped">
                            <thead>
                                <tr>
                                    <th>Customer</th>
                                    <th>Total Amount</th>
                                    <th>BO %</th>
                                    <th>Remarks</th>
                                    <th>Created Date</th>
                                    <th>Action</th>
                                </tr>
                            </thead>
                            <tbody>

                                @php $grandTotal = []; @endphp
                                @foreach ($badOrders as $badOrder)
                                    <tr>
                                        <td>
                                            {{ optional($badOrder['customer'])->firstname }}
                                            {{ optional($badOrder['customer'])->lastname }}
                                            ({{ optional($badOrder['storeinfo'])->storename }})
                                        </td>
                                        <td>{{ formatNumber($badOrder['amount']) }}</td>
                                        <td>{{ $badOrder['bo_percentage'] }}</td>
                                        <td>{{ $badOrder['remarks'] }}</td>
                                        <td>{{ $badOrder['updated_at'] }}</td>
                                        <td>
                                            <button type="button" class="btn btn-outline-primary btn-print"
                                        data-bo-id="{{ $badOrder->id }}" onclick="printPage(this)">
                                        <i class="fa-solid fa-print"></i> Print
                                    </button>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                            <tfoot>
                                <tr>
                                    <th></th>
                                    <th>Total:</th>
                                    <th>{{ formatNumber(array_sum($grandTotal)) }}</th>
                                    <th></th>
                                    <th></th>
                                    <th></th>
                                </tr>
                            </tfoot>
                        </table>
                    </div>
                </div>
            </div>


        </form>
        <!-- /.card -->
    </section>
    <!-- /.content -->
@endsection

@section('custom_js')
    @include('bad_order_js')
@endsection
