@extends('layouts.app')

@section('contents')
    <section class="content-header">
        <div class="container-fluid">
            <div class="row mb-2">
                <div class="col-sm-6">
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
                                <th>User Name</th>
                                <th>Date Pulled Out</th>
                                <th>User Name</th>
                                <th>Remarks</th>
                                <th>Updated date</th>
                                <th>User Name</th>
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
                                    <td>{{ $equipment->updated_at }}</td>
                                    <td>{{ $equipment->current_user_name }}</td>
                                </tr>
                            @endforeach
                        </tbody>
                        <tfoot>
                            <tr>
                                <th></th>
                                <th>Customer Name</th>
                                <th>Date Assigned</th>
                                <th>User Name</th>
                                <th>Date Pulled Out</th>
                                <th>User Name</th>
                                <th>Remarks</th>
                                <th>Updated date</th>
                                <th>User Name</th>
                            </tr>
                        </tfoot>
                    </table>
                </div>
            </div>
    </section>
@endsection
