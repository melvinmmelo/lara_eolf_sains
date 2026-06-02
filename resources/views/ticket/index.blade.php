@extends('layouts.app')

@section('custom_css')
@endsection

@section('contents')
    <!-- Content Header (Page header) -->
    <section class="content-header">
        <div class="container-fluid">
            <div class="row mb-2">
                <div class="col-sm-6">
                    <h1>Loading Tickets</h1>
                </div>
                <div class="col-sm-6">
                    <ol class="breadcrumb float-sm-right">
                        <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Home</a></li>
                        <li class="breadcrumb-item active">Loading Tickets</li>
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
                    <div class="">
                        <a href="{{ route('generate-ticket') }}" class="btn btn-primary">Generate Ticket</a>
                    </div>

                    <div class="tbContainer">

                        <table id="example3" class="table table-bordered table-striped">
                            <thead>
                                <tr>
                                    <th>Group Ticket No</th>
                                    <th>Date</th>
                                    <th></th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($loadingTickets as $loadingTicket)
                                    <tr>
                                        <td>{{ $loadingTicket->ticket_no }}</td>
                                        <td>{{ optional($loadingTicket->created_at)->format('M d, Y h:i A') }}</td>
                                        <td>
                                            <a href="{{ route('inbounds-ticket', ['grp' => $loadingTicket->ticket_no]) }}"
                                                class="btn btn-primary">Show</a>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                            <tfoot>
                                <tr>
                                    <th>Group Ticket No</th>
                                    <th>Date</th>
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
