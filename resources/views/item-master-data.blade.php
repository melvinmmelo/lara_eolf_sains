@extends('layouts.app')

@section('contents')
    <!-- Content Header (Page header) -->
    <section class="content-header">
        <div class="container-fluid">
            <div class="row mb-2">
                <div class="col-sm-6">
                    <h1>Item Master Data</h1>
                </div>
                <div class="col-sm-6">
                    <ol class="breadcrumb float-sm-right">
                        <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Home</a></li>
                        <li class="breadcrumb-item active">Item Master Data</li>
                    </ol>
                </div>
            </div>
        </div>
    </section>

    <section class="content">
        <div class="card">

            <div class="card-body">
                <h4 class="mt-3">Summary</h4>
                <div class="d-flex flex-row mb-2">
                    @foreach ($productsSumm as $productSummary)
                        <div class="btn btn-default mr-2">
                            {{ $productSummary['code'] . ': ' . $productSummary['quantity'] }}
                        </div>
                    @endforeach
                </div>

                <h4> Total</h4>

                <div class="d-flex flex-row mb-2">
                    <div class="btn btn-default mr-2">
                        Total Stocks: {{ $products->sum('stocks') }}
                    </div>
                    <div class="btn btn-default mr-2">
                        Total Reserved: {{ $products->sum('reserved') }}
                    </div>
                    <div class="btn btn-default mr-2">
                        Total Hold: {{ $products->sum('hold_quantity') }}
                    </div>
                </div>

                <table id="example1" class="table table-bordered table-striped">
                    <thead>
                        <tr>
                            <th>Product</th>
                            <th>Reserved</th>
                            <th>Hold</th>
                            <th>Stocks</th>
                            <th>Date</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($products as $product)
                            <tr>
                                <td>{{ $product->product_code . ' ' . $product->product_description }}</td>
                                <td>{{ $product->reserved }}</td>
                                <td>
                                    @if ($product->hold_quantity > 0)
                                        <a href="#"
                                            onclick="unholdProduct(`{{ $product->id }}`, `{{ $product->hold_quantity }}`);"
                                            class="btn btn-danger btn-sm">{{ $product->hold_quantity }}</a>
                                    @endif
                                </td>
                                <td>{{ $product->stocks }}</td>
                                <td>{{ $product->updated_at }}</td>
                            </tr>
                        @endforeach
                    </tbody>
                    <tfoot>
                        <tr>
                            <th>Total</th>
                            <th>{{ $products->sum('reserved') }}</th>
                            <th>{{ $products->sum('hold_quantity') }}</th>
                            <th>{{ $products->sum('stocks') }}</th>
                            <th></th>
                        </tr>
                    </tfoot>
                </table>
            </div>
            <div class="card-footer">
            </div>
        </div>
    </section>

    <div class="modal fade" id="modal-unhold-product">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <form action="{{ route('imd.addQtyFromHold') }}" method="post">
                    <div class="modal-body">
                        @csrf

                        <input type="hidden" class="form-control" name="imd_id" id="imd_id" value=""
                            requiredreadonly>

                        <div class="form-group">
                            <label class="form-label" for="quantity"><i style="color:red">*</i>Quantity to Add</label>
                            <input type="number" class="form-control" name="quantity" id="quantity" value=""
                                required>
                        </div>
                    </div>

                    <div class="modal-footer">
                        <button type="submit" class="btn btn-success">Save</button>
                    </div>

                </form>
                <!-- /.modal-content -->
            </div>
            <!-- /.modal-dialog -->
        </div>
        <!-- /.modal -->
    </div>
    <!-- /.content -->
@endsection

@section('custom_js')
    <script>
        function unholdProduct(id, hold_quantity) {

            $('#imd_id').val(id);
            $('#imd_id').attr('max', hold_quantity);

            $('#modal-unhold-product').modal('show');
        }
    </script>
@endsection
