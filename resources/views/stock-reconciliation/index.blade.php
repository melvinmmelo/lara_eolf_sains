@extends('layouts.app')


@section('contents')
<div class="container-fluid">
    <div class="row justify-content-center">
        <div class="col-md-12">
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title">
                        Stock Reconciliation Tool
                      </h3>

                    <div class="card-tools">
                        <a href="{{ route('stock-reconciliation.reconcile-all') }}" class="btn btn-warning" 
                        onclick="return confirm('This will recalculate all product stocks based on DPRs and orders. Continue?')">
                        <i class="fas fa-sync-alt"></i> Reconcile All Products
                        </a>
                    </div>
                </div>

                <div class="card-body">
                    @if(session('success'))
                        <div class="alert alert-success">
                            {{ session('success') }}
                        </div>
                    @endif

                    @if(session('error'))
                        <div class="alert alert-danger">
                            {{ session('error') }}
                        </div>
                    @endif

                    <div class="row mb-3">
                        <div class="col-md-6">
                            <form action="{{ route('stock-reconciliation.index') }}" method="GET">
                                <div class="input-group">
                                    <input type="text" class="form-control" placeholder="Search by product code or description" 
                                           name="search" value="{{ $searchTerm }}">
                                    <div class="input-group-append">
                                        <button class="btn btn-outline-secondary" type="submit">Search</button>
                                    </div>
                                </div>
                            </form>
                        </div>
                    </div>

                    <div class="table-responsive">
                        <table class="table table-bordered table-striped">
                            <thead class="thead-dark">
                                <tr>
                                    <th>Product Code</th>
                                    <th>Description</th>
                                    <th>Master Stock</th>
                                    <th>Transactions Based Stock</th>
                                    <th>Status</th>
                                    <th>Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($items as $item)
                                    <tr class="{{ $item->stocks < $item->orders->sum('quantity') ? 'table-danger' : '' }}">
                                        <td>{{ $item->product_code }}</td>
                                        <td>{{ $item->product_description }}</td>
                                        <td>{{ $item->stocks }}</td>
                                        <td>{{ $item->orders->sum('quantity') }}</td>
                                        <td>
                                            @if($item->stocks < $item->orders->sum('quantity'))
                                                <span class="badge badge-danger">Negative Stock</span>
                                            @elseif($item->stocks > $item->orders->sum('quantity'))
                                                <span class="badge badge-warning">Stock > Orders</span>
                                            @else
                                                <span class="badge badge-success">OK</span>
                                            @endif
                                        </td>
                                        <td>
                                            <a href="{{ route('stock-reconciliation.product', $item->product_code) }}" 
                                               class="btn btn-sm btn-info">
                                                <i class="fas fa-search"></i> Details
                                            </a>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="7" class="text-center">No items found</td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>

                    <div class="d-flex justify-content-center mt-3">
                        {{ $items->links('pagination::bootstrap-4') }}
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
