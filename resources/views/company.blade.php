@extends('layouts.app')

@section('contents')
    <!-- Content Header (Page header) -->
    <section class="content-header">
        <div class="container-fluid">
            <div class="row mb-2">
                <div class="col-sm-6">
                    <h1>Company</h1>
                </div>
                <div class="col-sm-6">
                    <ol class="breadcrumb float-sm-right">
                        <li class="breadcrumb-item"><a href="#">Home</a></li>
                        <li class="breadcrumb-item active">Company</li>
                    </ol>
                </div>
            </div>
        </div><!-- /.container-fluid -->
    </section>

    <!-- Main content -->
    <section class="content">




        <!-- Default box -->
        <div class="card">

            <div class="card-body">

                @include('layouts.errors')


                {{-- Sample displaying data --}}


                <div class="card">

                    <!-- /.card-header -->
                    <div class="card-body">

                        <div align="center"><img src="{{ asset('img/eolf_logo_trans.png') }}" alt="AdminLTELogo"
                                height="120" width="200"></div>

                        <hr>
                        <strong><i class="fas fa-book mr-1"></i> Name</strong>

                        <p class="text-muted">
                            {{ $company->name }}
                        </p>

                        <hr>

                        <strong><i class="fas fa-map-marker-alt mr-1"></i>Address</strong>

                        <p class="text-muted">
                            {{ $company->address }}
                        </p>

                        <hr>

                        <strong><i class="fas fa-envelope mr-1"></i>Email Address</strong>

                        <p class="text-muted">
                            {{ $company->email }}

                        </p>

                        <hr>

                        <strong><i class="fas fa-phone mr-1"></i> Contact No</strong>

                        <p class="text-muted">
                            {{ $company->contact }}


                        </p>
                    </div>
                    <!-- /.card-body -->
                </div>

                <div class="modal fade" id="modal-company">
                    <form action="{{ route('company.update', ['companyDetails' => $company->id]) }}" method="POST"
                        enctype="multipart/form-data">
                        @csrf
                        @method('PUT')
                        <div class="modal-dialog">
                            <div class="modal-content">
                                <div class="modal-header">
                                    <h4 class="modal-title">Update Company</h4>
                                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                        <span aria-hidden="true">&times;</span>
                                    </button>
                                </div>
                                <div class="modal-body">

                                    <div class="form-group">
                                        <div class="row mb-2">
                                            <div class="col-sm-12">
                                                <label class="form-label" for="name"><i style="color:red">*</i>Company
                                                    Name</label>
                                                <input type="text" class="form-control" name="name" value=" {{ $company->name }}">
                                            </div>
                                        </div>
                                    </div>
                                    <div class="form-group">
                                        <div class="row">
                                            <div class="col-sm-12">
                                                <label class="form-label" for="address"><i
                                                        style="color:red">*</i>Address</label>
                                                <textarea class="form-control" rows="3" name="address"> {{ $company->address }}</textarea>
                                            </div>
                                        </div>
                                    </div>

                                    <div class="form-group">
                                        <div class="row mb-2">
                                            <div class="col-sm-6">
                                                <label class="form-label" for="email">Email</label>
                                                <input type="text" class="form-control" name="email" value=" {{ $company->email }}">
                                            </div>
                                            <div class="col-sm-6">
                                                <label class="form-label" for="contact_no">Contact No</label>
                                                <input type="text" class="form-control" name="contact_no" value=" {{ $company->contact_no }}">
                                            </div>
                                        </div>

                                        <div class="form-group">
                                            <div class="row mb-2">
                                                <div class="col-sm-12">
                                                    <label class="form-label" for="logo">Logo</label>
                                                    <div class="input-group">
                                                        <div class="custom-file">
                                                            <input type="file" class="custom-file-input" id="logo"
                                                                name="logo">
                                                            <label class="custom-file-label" for="exampleInputFile">Choose
                                                                file</label>
                                                        </div>
                                                        <div class="input-group-append">
                                                            <span class="input-group-text">Upload</span>
                                                        </div>
                                                    </div>

                                                </div>
                                            </div>
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
                    </form>
                </div>
                <!-- /.modal -->


            </div>
        </div>
        <!-- /.card-body -->
        <div class="card-footer">
            <button type="button" class="btn btn-primary" data-toggle="modal" data-target="#modal-company">
                Update
            </button>
        </div>

        <!-- /.card-footer-->
        </div>
        <!-- /.card -->

    </section>
    <!-- /.content -->
@endsection
