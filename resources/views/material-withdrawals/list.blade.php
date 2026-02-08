@extends('layouts.app')

@section('custom_css')
<style>
    .materials-table {
        font-size: 0.9rem;
    }

</style>
@endsection

@section('contents')
    <section class="content-header">
        <div class="container-fluid">
            <div class="row mb-2">
                <div class="col-sm-6">
                    <h1>
                        <i class="fas fa-clipboard-list"></i> Material Withdrawals History
                    </h1>
                </div>
                <div class="col-sm-6">
                    <ol class="breadcrumb float-sm-right">
                        <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Home</a></li>
                        <li class="breadcrumb-item"><a href="{{ route('material-withdrawals.index') }}">Withdraw Materials</a></li>
                        <li class="breadcrumb-item active">Withdrawals History</li>
                    </ol>
                </div>
            </div>
        </div>
    </section>

    <section class="content">
        @if (session('success'))
            <script>
                document.addEventListener('DOMContentLoaded', function() {
                    Swal.fire({
                        icon: 'success',
                        title: '{{ session('success') }}',
                        showConfirmButton: false,
                        timer: 2000
                    });
                });
            </script>
        @endif

        <div class="card">
            <div class="card-header">
                <h3 class="card-title">Material Withdrawals History</h3>
            </div>

            <div class="card-body">
                @include('layouts.errors')
                <table id="withdrawalsTable" class="table table-bordered table-striped">
                    <thead>
                        <tr>
                            <th>Code</th>
                            <th>Date</th>
                            <th>Requested By</th>
                            <th>Issued By</th>
                            <th>Items Count</th>
                            <th>Created</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($withdrawals as $withdrawal)
                            <tr>
                                <td>{{ $withdrawal->code }}</td>
                                <td>{{ $withdrawal->withdrawal_date ? $withdrawal->withdrawal_date->format('M d, Y') : 'N/A' }}</td>
                                <td>{{ $withdrawal->requested_by }}</td>
                                <td>{{ $withdrawal->issued_by }}</td>
                                <td>{{ $withdrawal->materials->count() }}</td>
                                <td>{{ $withdrawal->created_at->format('M d, Y h:i A') }}</td>
                                <td>
                                    <button type="button"
                                            class="btn btn-sm btn-info"
                                            data-toggle="modal"
                                            data-target="#detailModal{{ $withdrawal->id }}"
                                            title="View Details">
                                        <i class="fas fa-eye"></i> View
                                    </button>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
            <!-- Detail Modals (outside table for proper rendering) -->
            @foreach($withdrawals as $withdrawal)
                <div class="modal fade" id="detailModal{{ $withdrawal->id }}" tabindex="-1" role="dialog" aria-labelledby="detailModalLabel{{ $withdrawal->id }}" aria-hidden="true">
                    <div class="modal-dialog modal-xl" role="document">
                        <div class="modal-content">
                            <div class="modal-header">
                                <h5 class="modal-title" id="detailModalLabel{{ $withdrawal->id }}">
                                    Withdrawal Details - {{ $withdrawal->code }}
                                </h5>
                                <button type="button" class="close" data-dismiss="modal">
                                    <span>&times;</span>
                                </button>
                            </div>
                            <div class="modal-body">
                                <div class="row mb-3">
                                    <div class="col-md-6">
                                        <strong>Code:</strong> {{ $withdrawal->code }}<br>
                                        <strong>Date:</strong> {{ $withdrawal->withdrawal_date ? $withdrawal->withdrawal_date->format('M d, Y') : 'N/A' }}<br>
                                        <strong>Created:</strong> {{ $withdrawal->created_at->format('M d, Y h:i A') }}
                                    </div>
                                    <div class="col-md-6">
                                        <strong>Requested By:</strong> {{ $withdrawal->requested_by }}<br>
                                        <strong>Issued By:</strong> {{ $withdrawal->issued_by }}
                                    </div>
                                </div>

                                <h6>Withdrawn Materials ({{ $withdrawal->materials->count() }} items)</h6>
                                <table class="table table-bordered table-sm">
                                    <thead>
                                        <tr>
                                            <th>#</th>
                                            <th>Material Name</th>
                                            <th>Unit</th>
                                            <th>Quantity</th>
                                            <th>Amount</th>
                                            <th>Location</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach($withdrawal->materials as $index => $material)
                                            <tr>
                                                <td>{{ $index + 1 }}</td>
                                                <td>{{ $material->name }}</td>
                                                <td>{{ $material->unit }}</td>
                                                <td>{{ $material->quantity }}</td>
                                                <td>₱{{ number_format($material->amount, 2) }}</td>
                                                <td>{{ $material->location ?? 'N/A' }}</td>
                                            </tr>
                                        @endforeach
                                    </tbody>
                                    <tfoot>
                                        <tr>
                                            <th colspan="4" class="text-right">Total:</th>
                                            <th>₱{{ number_format($withdrawal->materials->sum('amount'), 2) }}</th>
                                            <th></th>
                                        </tr>
                                    </tfoot>
                                </table>
                            </div>
                            <div class="modal-footer">
                                <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                            </div>
                        </div>
                    </div>
                </div>
            @endforeach

            <div class="card-footer">
                <a href="{{ route('material-withdrawals.index') }}" class="btn btn-primary">
                    <i class="fas fa-plus"></i> New Withdrawal
                </a>
            </div>
        </div>
    </section>
@endsection

@section('custom_js')
<script>
    $(function () {
        $("#withdrawalsTable").DataTable({
            "responsive": true,
            "lengthChange": true,
            "autoWidth": false,
            "order": [[1, "desc"]]
        });
    });
</script>
@endsection
