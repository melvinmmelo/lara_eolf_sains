@extends('layouts.app')

@section('custom_css')
@endsection

@section('contents')
    <!-- Content Header (Page header) -->
    <section class="content-header">
        <div class="container-fluid">
            <div class="row mb-2">
                <div class="col-sm-6">
                    <h1>{{ $material->name }} Changes</h1>
                </div>
                <div class="col-sm-6">
                    <ol class="breadcrumb float-sm-right">
                        <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Home</a></li>
                        <li class="breadcrumb-item active">Materials Inventory</li>
                    </ol>
                </div>
            </div>
        </div>
    </section>

    <!-- Main content -->
    <section class="content">

        @include('layouts.errors')

        <div class="card">

            <div class="card-body table-responsive">
                <table id="example1" class="table table-bordered table-striped">
                    <thead>
                        <tr>
                            <th>Changes</th>
                            <th>Date</th>
                            <th></th>
                        </tr>
                    </thead>
                    <tbody>

                        @foreach ($activityLogs as $change)
                            <tr>
                                <td>{{ $change->properties }}</td>
                                <td>{{ $change->created_at }}</td>
                                <td></td>
                            </tr>
                        @endforeach

                    </tbody>
                    <tfoot>
                       <tr>
                            <th>Changes</th>
                            <th>Date</th>
                            <th></th>
                        </tr>
                    </tfoot>
                </table>
            </div>

        </div>
    </section>
@endsection

@section('custom_js')

@endsection
