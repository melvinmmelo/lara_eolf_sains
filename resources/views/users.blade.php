@extends('layouts.app')

@section('contents')
    <!-- Content Header (Page header) -->
    <section class="content-header">
        <div class="container-fluid">
            <div class="row mb-2">
                <div class="col-sm-6">
                </div>
                <div class="col-sm-6">
                    <ol class="breadcrumb float-sm-right">
                        <li class="breadcrumb-item"><a href="#">Home</a></li>
                        <li class="breadcrumb-item active">Users</li>
                    </ol>
                </div>
            </div>
        </div>
    </section>

    <!-- Main content -->
    <section class="content">

        <!-- Default box -->
        <div class="card">
            <div class="card-header">
                <h3 class="card-title">Users Info</h3>

                <div class="card-tools">
                    <button type="button" class="btn btn-tool" data-card-widget="collapse" title="Collapse">
                        <i class="fas fa-minus"></i>
                    </button>
                    <button type="button" class="btn btn-tool" data-card-widget="remove" title="Remove">
                        <i class="fas fa-times"></i>
                    </button>
                </div>
            </div>
            <div class="card-body">

                @include('layouts.errors')

                <table id="example1" class="table table-bordered table-striped">
                    <thead>
                        <tr>
                            <th>Last Name</th>
                            <th>First Name</th>
                            <th>Email</th>

                        </tr>
                    </thead>
                    <tbody>
                        <tr>
                            <td>Trident</td>
                            <td>Internet
                                Explorer 4.0
                            </td>
                            <td>Win 95+</td>

                        </tr>

                        <tr>
                            <td>Trident</td>
                            <td>Internet
                                Explorer 4.0
                            </td>
                            <td>Win 95+</td>

                        </tr>
                    </tbody>
                    <tfoot>
                        <tr>
                            <th>Last Name</th>
                            <th>First Name</th>
                            <th>Email</th>

                        </tr>
                    </tfoot>
                </table>


            </div>
            <!-- /.card-body -->
            <div class="card-footer">
                <button type="button" class="btn btn-primary" data-toggle="modal" data-target="#modal-users">
                    Add New
                </button>
            </div>
            <!-- /.card-footer-->
        </div>
        <!-- /.card -->


        <div class="modal fade" id="modal-users">
            <div class="modal-dialog">
                <form method="POST" action="{{ route('register') }}">
                    @csrf
                    <div class="modal-content">
                        <div class="modal-header">
                            <h4 class="modal-title">Add Users</h4>
                            <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                <span aria-hidden="true">&times;</span>
                            </button>
                        </div>
                        <div class="modal-body">

                            <div class="form-group">
                                <div class="row mb-2">
                                    <div class="col-sm-6">
                                        <label class="form-label" for="name">Last Name</label>
                                        <input type="text" class="form-control" name="last_name">
                                    </div>
                                    <div class="col-sm-6">
                                        <label class="form-label" for="name">First Name</label>
                                        <input type="text" class="form-control" name="first_name">
                                    </div>
                                </div>
                            </div>


                            <div class="form-group">
                                <div class="row">
                                    <div class="col-sm-12">
                                        <label class="form-label" for="email">E-mail</label>
                                        <input type="text" class="form-control" name="email">
                                    </div>
                                </div>
                            </div>

                            <div class="form-group">
                                <div class="row mb-2">
                                    <div class="col-sm-12">
                                        <label class="form-label" for="password">Password</label>
                                        <input type="password" class="form-control" name="password" value="Eolf@2024">
                                    </div>
                                </div>

                                <div class="row mb-2">
                                    <div class="col-sm-12">
                                        <label class="form-label" for="password">Confirm Password</label>
                                        <input type="password" name="password_confirmation" class="form-control"
                                            value="Eolf@2024">
                                    </div>
                                </div>



                                <div class="modal-footer">
                                    <button type="submit" class="btn btn-success">Save
                                        changes</button>
                                </div>
                            </div>
                            <!-- /.modal-content -->
                        </div>
                        <!-- /.modal-dialog -->
                    </div>
                    <!-- /.modal -->

                </form>
            </div>
        </div>

    </section>


    <!-- /.content -->
@endsection
