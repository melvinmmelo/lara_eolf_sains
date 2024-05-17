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
                        <li class="breadcrumb-item"><a href="#">Home</a></li>
                        <li class="breadcrumb-item active">Item Master Data</li>
                    </ol>
                </div>
            </div>
        </div>
    </section>

    <!-- Main content -->
    <section class="content">

        <!-- Default box -->
        <div class="card">

            <div class="card-body">

                @include('layouts.errors')

                <table id="example1" class="table table-bordered table-striped">
                    <thead>
                        <tr>
                            <th>Branch</th>
                            <th>Product</th>
                            <th>Quantity</th>
                            <th>Date</th>

                        </tr>
                    </thead>
                    <tbody>

                            <tr>
                                <td></td>
                                <td></td>
                                <td></td>
                                <td></td>


                            </tr>
                    </tbody>
                    <tfoot>
                        <tr>
                            <th>Branch</th>
                            <th>Product</th>
                            <th>Quantity</th>
                            <th>Date</th>

                        </tr>

                    </tfoot>
                </table>


            </div>
            <!-- /.card-body -->
            <div class="card-footer">
                {{-- <button type="button" class="btn btn-primary" data-toggle="modal" data-target="#modal-devpersons">
                    Add New
                </button> --}}
            </div>
            <!-- /.card-footer-->
        </div>
        <!-- /.card -->




    </section>



    <!-- /.content -->
@endsection
