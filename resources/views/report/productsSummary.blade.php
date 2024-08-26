@extends('layouts.app')

@section('custom_css')
@endsection

@section('contents')
    <!-- Content Header (Page header) -->
    <section class="content-header">
        <div class="container-fluid">
            <div class="row mb-2">
                <div class="col-sm-6">
                    <h1>{{ $title }} Products Summary</h1>
                </div>
                <div class="col-sm-6">
                    <ol class="breadcrumb float-sm-right">
                        <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Home</a></li>
                        <li class="breadcrumb-item active">Products Summary</li>
                    </ol>
                </div>
            </div>
        </div>
    </section>

    <!-- Main content -->
    <section class="content">

        @include('layouts.errors')

        <!-- Default box -->
        <div class="card">
            <div class="card-body table-responsive">

                <form action="{{ route('report.productsSummaryFiltered') }}" method="GET">
                    @csrf
                    <div class="form-group">
                        <div class="row">
                            <div class="col-md-3">
                                <label class="form-label" for="from_date">From</label>
                                <input type="date" class="form-control" name="from_date" required
                                    value="{{ request('from_date') }}">
                            </div>

                            <div class="col-md-3">
                                <label class="form-label" for="to_date">To</label>
                                <input type="date" class="form-control" name="to_date" required
                                    value="{{ request('to_date') }}">
                            </div>

                            <div class="col-md-2 mt-4">
                                <button type="submit" class="btn btn-primary">Filter</button>
                            </div>
                        </div>
                    </div>
                </form>

                <table id="example1" class="table table-bordered table-striped">
                    <thead>
                        <tr>
                            <th>Product code</th>
                            <th>Total</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($products as $product)
                            <tr>
                                <td>{{ $product['code'] }}</td>
                                <td>{{ $product['quantity'] }}</td>
                            </tr>
                        @endforeach
                    </tbody>

                    <tfoot>
                        <tr>
                            <th></th>
                            <th></th>
                        </tr>
                    </tfoot>
                </table>
            </div>

            <!-- /.card-footer-->
        </div>
        <!-- /.card -->
    </section>
    <!-- /.content -->
@endsection
