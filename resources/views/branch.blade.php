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
                        <li class="breadcrumb-item"><a href="#">Home</a></li>
                        <li class="breadcrumb-item active">Users</li>
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
                <table id="example1" class="table table-bordered table-striped">
                    <thead>
                        <tr>
                            <th>Code</th>
                            <th>Branch</th>
                            <th>Contact No.</th>
                            <th>Address</th>


                        </tr>
                    </thead>
                    <tbody>

                        @foreach ($branches as $branch)
                            <tr>
                                <td>{{ $branch->code }}</td>
                                <td>{{ $branch->name }}</td>
                                <td>{{ $branch->office_no }}</td>
                                <td>{{ $branch->address }}</td>
                            </tr>
                            @endforeach
                    </tbody>
                    <tfoot>
                        <tr>
                            <th>Code</th>
                            <th>Branch</th>
                            <th>Contact No.</th>
                            <th>Address</th>


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
                                        <label class="form-label" for="code">Code</label>
                                        <input type="text" class="form-control" name="code" required>
                                    </div>

                                    <div class="col-sm-8">
                                        <label class="form-label" for="name">Branch Name</label>
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
                                <button type="submit" class="btn btn-success swalDefaultSuccess">Save changes</button>
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
