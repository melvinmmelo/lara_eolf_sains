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
                    <li class="breadcrumb-item active">Products</li>
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
            <h3 class="card-title">Products</h3>

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
                        <th>Code</th>
                        <th>Name</th>
                        <th>Flavor</th>


                    </tr>
                </thead>
                <tbody>
                    <tr>
                        <td>aaa</td>
                        <td>aaa</td>
                        <td>aaa</td>


                    </tr>

                    <tr>
                        <td>aaa</td>
                        <td>aaa</td>
                        <td>aaa</td>


                    </tr>
                </tbody>
                <tfoot>
                    <tr>
                        <th>Code</th>
                        <th>Name</th>
                        <th>Flavor</th>


                    </tr>
                </tfoot>
            </table>


        </div>
        <!-- /.card-body -->
        <div class="card-footer">
            <button type="button" class="btn btn-primary" data-toggle="modal" data-target="#modal-products">
                Add New
            </button>
        </div>
        <!-- /.card-footer-->
    </div>
    <!-- /.card -->


    <div class="modal fade" id="modal-products">
        <div class="modal-dialog">
            <form method="POST" action="{{ route('#') }}">
                @csrf
                <div class="modal-content">
                    <div class="modal-header">
                        <h4 class="modal-title">Add Products</h4>
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                    <div class="modal-body">

                        <div class="form-group">
                            <div class="row mb-2">
                                <div class="col-sm-6">
                                    <label class="form-label" for="name">Types</label>
                                    <select class="form-control" id="store_prov">
                                        <option>option 1</option>
                                        <option>option 2</option>
                                        <option>option 3</option>
                                        <option>option 4</option>
                                        <option>option 5</option>
                                    </select>
                                </div>

                                <div class="col-sm-6">
                                    <label class="form-label" for="name">Variants</label>
                                    <select class="form-control" id="store_prov">
                                        <option>option 1</option>
                                        <option>option 2</option>
                                        <option>option 3</option>
                                        <option>option 4</option>
                                        <option>option 5</option>
                                    </select>
                                </div>
                            </div>
                        </div>


                        <div class="form-group">
                            <div class="row">
                                <div class="col-sm-12">
                                    <label class="form-label" for="email">Type</label>
                                    <textarea class="form-control" rows="3" id="store_remarks"></textarea>
                                </div>
                            </div>
                        </div>
                        <div class="form-group">
                            <div class="row">
                                <div class="col-sm-12">
                                    <label class="form-label" for="email">Flavor</label>
                                    <input type="text" class="form-control" name="email">
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="modal-footer">
                        <button type="submit" class="btn btn-success">Save
                            changes</button>
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