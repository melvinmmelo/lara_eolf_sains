@extends('layouts.app')

@section('contents')
    <!-- Content Header (Page header) -->
    <section class="content-header">
        <div class="container-fluid">
            <div class="row mb-2">
                <div class="col-sm-6">
                    <h1>Branch</h1>
                </div>
                <div class="col-sm-6">
                    <ol class="breadcrumb float-sm-right">
                        <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Home</a></li>
                        <li class="breadcrumb-item active">Branch</li>
                    </ol>
                </div>
            </div>
        </div>
    </section>

    <!-- Main content -->
    <section class="content">
        @if (session('success'))
            <script>
                // JavaScript code to trigger SweetAlert pop-up message
                document.addEventListener('DOMContentLoaded', function() {
                    // Set default icon
                    let icon = 'success';

                    // Check if success message is "Customer deleted successfully!"
                    @if (session('success') == 'Vehicle deleted successfully!')
                        icon = 'error'; // Set icon to 'error' if message is for deletion
                    @elseif (session('success') == 'Vehicle updated successfully!')
                        icon = 'success'; // Set icon to 'success' if message is for update
                    @endif

                    // Show SweetAlert pop-up message with the determined icon
                    Swal.fire({
                        icon: icon,
                        title: '{{ session('success') }}',
                        showConfirmButton: false,
                        timer: 2000
                    });
                });
            </script>
        @endif
        <!-- Default box -->
        <div class="card">

            <div class="card-body">

                @include('layouts.errors')


                <table id="example1" class="table table-bordered table-striped">
                    <thead>
                        <tr>
                            <th>Code</th>
                            <th>Branch</th>
                            <th>Contact No.</th>
                            <th>Address</th>
                            <th></th>
                        </tr>
                    </thead>
                    <tbody>

                        @foreach ($branches as $branch)
                            <tr>
                                <td>{{ $branch->code }}</td>
                                <td>{{ $branch->name }}</td>
                                <td>{{ $branch->office_no }}</td>
                                <td>{{ $branch->address }}</td>
                                <td>
                                    <a href="#" data-toggle="modal" data-target="#modalEdit"
                                        class="btn btn-primary btn-sm" onclick="setToUpdateBranch()">Edit</a>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                    <tfoot>
                        <tr>
                            <th>Code</th>
                            <th>Branch</th>
                            <th>Contact No.</th>
                            <th>Address</th>
                            <th></th>
                        </tr>
                    </tfoot>
                </table>

            </div>
            <!-- /.card-body -->
            <div class="card-footer">
                <button type="button" class="btn btn-primary" data-toggle="modal" data-target="#modal-branch">
                    Add New
                </button>
            </div>
            <!-- /.card-footer-->
        </div>

        <div class="modal fade" id="modalEdit">
            <div class="modal-dialog">
                <form method="POST" action="{{ route('branch.update')}}">
                    @csrf
                    @method('PUT')

                    <div class="modal-content">
                        <div class="modal-header">
                            <h4 class="modal-title">Edit Branch</h4>
                            <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                <span aria-hidden="true">&times;</span>
                            </button>
                        </div>
                        <div class="modal-body">
                            <div class="form-group">
                                <div class="row mb-3">
                                    <div class="col-sm-4">
                                        <label class="form-label" for="code"><i style="color:red">*</i>Code</label>
                                        <input type="text" class="form-control" name="e_id" required readonly>
                                        <input type="text" class="form-control" name="e_code" required readonly>
                                    </div>

                                    <div class="col-sm-8">
                                        <label class="form-label" for="name"><i style="color:red">*</i>Branch
                                            Name</label>
                                        <input type="text" class="form-control" name="e_name" required>
                                    </div>
                                </div>
                            </div>
                            <div class="form-group">
                                <div class="row">
                                    <div class="col-sm-12">
                                        <label class="form-label" for="address">Address</label>
                                        <textarea class="form-control" rows="3" name="e_address"></textarea>
                                    </div>
                                </div>
                            </div>

                            <div class="form-group">
                                <div class="row">
                                    <div class="col-sm-12">
                                        <label class="form-label" for="address">Contact No.</label>
                                        <input type="text" class="form-control" name="e_office_no">
                                    </div>
                                </div>
                            </div>

                            <div class="modal-footer">
                                <button type="submit" class="btn btn-success">Save changes</button>
                            </div>
                        </div>
                </form>
                <!-- /.modal-content -->
            </div>
            <!-- /.modal-dialog -->
        </div>
        <!-- /.modal -->

        <!-- /.card -->
        <div class="modal fade" id="modal-branch">
            <div class="modal-dialog">
                <form method="POST" action="/branch/store">
                    @csrf
                    <div class="modal-content">
                        <div class="modal-header">
                            <h4 class="modal-title">Add Branch</h4>
                            <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                <span aria-hidden="true">&times;</span>
                            </button>
                        </div>
                        <div class="modal-body">

                            <div class="form-group">
                                <div class="row mb-3">
                                    <div class="col-sm-4">
                                        <label class="form-label" for="code"><i style="color:red">*</i>Code</label>
                                        <input type="text" class="form-control" name="code" required>
                                    </div>

                                    <div class="col-sm-8">
                                        <label class="form-label" for="name"><i style="color:red">*</i>Branch
                                            Name</label>
                                        <input type="text" class="form-control" name="name" required>
                                    </div>
                                </div>
                            </div>
                            <div class="form-group">
                                <div class="row">
                                    <div class="col-sm-12">
                                        <label class="form-label" for="address">Address</label>
                                        <textarea class="form-control" rows="3" name="address"></textarea>
                                    </div>
                                </div>
                            </div>

                            <div class="form-group">
                                <div class="row">
                                    <div class="col-sm-12">
                                        <label class="form-label" for="address">Contact No.</label>
                                        <input type="text" class="form-control" name="office_no">
                                    </div>
                                </div>
                            </div>


                            <div class="modal-footer">
                                <button type="submit" class="btn btn-success">Save changes</button>
                            </div>
                        </div>
                </form>
                <!-- /.modal-content -->
            </div>
            <!-- /.modal-dialog -->
        </div>
        <!-- /.modal -->



        </div>
    </section>
    <!-- /.content -->
@endsection

@section('custom_js')
    <script>
        // function setToUpdateBranch() {
        //     // get datatable
        //     var table = $('#example1').DataTable();

        //     // get the data of clicked row
        //     var data = table.row($(this).parents('tr')).data();

        //     document.querySelector('input[name=e_code]').value = data[0];
        //     document.querySelector('input[name=e_name]').value = data[1];
        //     document.querySelector('textarea[name=e_address]').value = data[2];
        //     document.querySelector('input[name=e_office_no]').value = data[3];

        // }

        function setToUpdateBranch() {

            var table = $('#example1').DataTable();

            $('#example1 tbody').on('click', 'tr', function() {

                var data = table.row(this).data();

                document.querySelector('input[name=e_code]').value = data[0];
                document.querySelector('input[name=e_name]').value = data[1];
                document.querySelector('textarea[name=e_address]').value = data[2];
                document.querySelector('input[name=e_office_no]').value = data[3];


            });
        }
    </script>
@endsection
