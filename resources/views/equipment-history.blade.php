@extends('layouts.app')

@section('contents')
    <section class="content-header">
        <div class="container-fluid">
            <div class="row mb-2">
                <div class="col-sm-6">
                    <h1>{{ $dno }}</h1>
                </div>
                <div class="col-sm-6">
                    <ol class="breadcrumb float-sm-right">
                        <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Home</a></li>
                        <li class="breadcrumb-item active">Equipment History</li>
                    </ol>
                </div>
            </div>
        </div>
    </section>

    <section class="content">
        <div class="card">
            @include('layouts.errors')
            <div class="card">
                <div class="card-body">
                    <table id="example1e" class="table table-bordered table-striped">
                        <thead>
                            <tr>
                                <th></th>
                                <th>Customer Name</th>
                                <th>Date Assigned</th>
                                <th>Assigned by</th>
                                <th>Date Pulled Out</th>
                                <th>Pulled Out by</th>
                                <th>Remarks</th>
                                <th>Date</th>
                                <th></th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($equipments as $equipment)
                                <tr>
                                    <td></td>
                                    <td>{{ $equipment->customer_name }}</td>
                                    <td>{{ $equipment->date_assigned }}</td>
                                    <td>{{ $equipment->user_name_assigned }}</td>
                                    <td>{{ $equipment->date_pulled_out }}</td>
                                    <td>{{ $equipment->user_name_pulled_out }}</td>
                                    <td>{{ $equipment->pull_out_reason }}</td>
                                    <td>{{ $equipment->created_at->format('Y-m-d h:i A') }}</td>
                                    <td>
                                        <a href="{{ route('report.pullout-replaced-form', ['degic_no' => $equipment->degic_no, 'customer_id' => $equipment->customer_id]) }}"
                                            class="btn btn-default">
                                            <i class="fas fa-print"></i>
                                        </a>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                        <tfoot>
                            <tr>
                                <th></th>
                                <th>Customer Name</th>
                                <th>Date Assigned</th>
                                <th>Assigned by</th>
                                <th>Date Pulled Out</th>
                                <th>Pulled Out by</th>
                                <th>Remarks</th>
                                <th>Updated date</th>
                                <th></th>
                            </tr>
                        </tfoot>
                    </table>
                </div>
            </div>
    </section>
@endsection
