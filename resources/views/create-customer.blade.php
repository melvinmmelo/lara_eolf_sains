<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>DataTables Example</title>
    <!-- DataTables CSS -->
    <link rel="stylesheet" type="text/css" href="https://cdn.datatables.net/1.11.5/css/jquery.dataTables.min.css">
</head>

@extends('layouts.app')

@section('contents')
    <!-- Content Header (Page header) -->
    <section class="content-header">
        <div class="container-fluid">
            <div class="row mb-2">
                <div class="col-sm-6">
                    <h1>Blank Page</h1>
                </div>
                <div class="col-sm-6">
                    <ol class="breadcrumb float-sm-right">
                        <li class="breadcrumb-item"><a href="#">Home</a></li>
                        <li class="breadcrumb-item active">Blank Page</li>
                    </ol>
                </div>
            </div>
        </div><!-- /.container-fluid -->
    </section>

    <!-- Main content -->
    <section class="content">


@if(session('success'))
    <script>
        // JavaScript code to trigger SweetAlert pop-up message
        document.addEventListener('DOMContentLoaded', function () {
            // Set default icon
            let icon = 'success';
            
            // Check if success message is "Customer deleted successfully!"
            @if(session('success') == 'Customer deleted successfully!')
                icon = 'error'; // Set icon to 'error' if message is for deletion
            @elseif(session('success') == 'Customer updated successfully!')
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


<!-- @if(session('delete'))
    <script>
        // JavaScript code to trigger SweetAlert pop-up message for deletion
        document.addEventListener('DOMContentLoaded', function () {
            Swal.fire({
                icon: 'delete',
                title: '{{ session('delete') }}',
                showConfirmButton: false,
                timer: 2000
            });
        });
    </script>
@endif -->
<section class="content-header">
    <div class="container-fluid">
        <div class="row mb-2">
            <div class="col-sm-6">
                <h1>Customers</h1>

            </div>
            <div class="card-body">

                <table id="example1" class="table table-bordered table-striped">
                    <thead>
                        <tr>
                            <th>Distributor</th>
                            <th>Last Name</th>
                            <th>First Name</th>
                            <th>Contact No.</th>
                            <th>Address</th>
                            <th>Store Name</th>
                            <th>Store Address</th>







                        </tr>
                    </thead>
                    <tbody>
                        <tr>
                            <td>aaa</td>
                            <td>aaa</td>
                            <td>aaa</td>
                            <td>aaa</td>
                            <td>aaa</td>
                            <td>aaa</td>
                            <td>aaa</td>


                        </tr>

                        <tr>
                            <td>bbb</td>
                            <td>bbb</td>
                            <td>bbb</td>
                            <td>bbb</td>
                            <td>bbb</td>
                            <td>bbb</td>
                            <td>bbb</td>

                        </tr>
                    </tbody>
                    <tfoot>
                        <tr>
                            <th>Distributor</th>
                            <th>Last Name</th>
                            <th>First Name</th>
                            <th>Contact No.</th>
                            <th>Address</th>
                            <th>Store Name</th>
                            <th>Store Address</th>
                        </tr>
                    </tfoot>
                </table>


            </div>




            <div class="card-footer">
                <button type="button" class="btn btn-primary" data-toggle="modal" data-target="#customerModal">
                    Add New
                </button>
            </div>
            <!-- /.card-footer-->

        </div>
        <!-- /.card -->

    </section>
    <!-- /.content -->



<!-- Main content -->
<section class="content">
<!-- <table id="customerTable" class="table table-bordered table-striped" >
    <thead>
        <tr>
            <th>Name</th>
            <th>Email</th>
            <th>Actions</th>
        </tr>
    </thead>
    <tbody>
      <tr>
        <td>JR Andal</td>
        <td>mario.andal.jr@ub.edu.ph</td>
        <td>&nbsp;</td>
      </tr>
    </tbody>
</table> -->
<table id="customerTable" class="table table-bordered table-striped" >
    <thead>
        <tr>
            <th>Lastname</th>
            <th>Firstname</th>
            <th>Middlename</th>
            <th>Company Name</th>
            <th>Actions</th>
        </tr>
    </thead>
    <tbody>
        @foreach($customers as $customer)
            <tr>
                <td>{{ $customer->lastname }}</td>
                <td>{{ $customer->firstname }}</td>
                <td>{{ $customer->middlename }}</td>
                <td>{{ $customer->companyname }}</td>
                <td>
                    <a class="btn btn-success btn-sm" href="{{ route('customer.edit', $customer->id) }}">Edit</a>
                    <form method="POST" action="{{ route('customer.destroy', $customer->id) }}" style="display: inline;">
                        @csrf
                        @method('DELETE')
                        <button type="submit" class="btn btn-danger btn-sm">Delete</button>
                    </form>
                </td>
            </tr>
        @endforeach
    </tbody>
</table>


    <!-- Customer Entry Modal -->
    <div class="modal fade" id="customerModal" tabindex="-1" aria-labelledby="customerModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="customerModalLabel">Customer Info</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <!-- Your existing content goes here -->
                    <div class="content-header">
                        <!-- Content Header (Page header) -->
                        <div class="container-fluid">
                            <!-- Your existing form -->
                            <div class="form-group">




                            
<form method="POST" action="/customers/store">
@csrf
        <div class="row mb-3">
                  <div class="col-sm-6">
            <label class="form-label" for="cust_dist">Distributor</label>
            <select name="distributor" class="form-control d-block" id="cust_dist" onfocus="changeColor('cust_dist')" onblur="resetColor('cust_dist')">
                <option>option 1</option>
                <option>option 2</option>
                <option>option 3</option>
                <option>option 4</option>
                <option>option 5</option>
            </select>
        </div>
        </div>


      <div class="form-group">
        <div class="row mb-3">
          <div class="col-sm-4">
            <label class="form-label" for="cust_lname">Last Name:</label>
            <input type="text" class="form-control" id="cust_lname" name="lastname">
          </div>
          <div class="col-sm-4">
            <label class="form-label" for="cust_fname">First Name:</label>
            <input type="text" class="form-control" id="cust_fname" name="firstname">
          </div>
          <div class="col-sm-4">
            <label class="form-label" for="cust_mname">Middle Name:</label>
            <input type="text" class="form-control" id="cust_mname" name="middlename">
          </div>
        </div>
      </div>


      <div class="form-group">
        <div class="row mb-3">
        <div class="col-sm-4">
            <label class="form-label" for="cust_contact">Contact No.:</label>
            <input type="text" class="form-control" id="cust_contact" name="contact_no">
          </div>
          <div class="col-sm-4">
            <label class="form-label" for="cust_comp">Company Name:</label>
            <input type="text" class="form-control" id="cust_comp" name="companyname">
          </div>
          <div class="col-sm-4">
            <label class="form-label" for="cust_tin">TIN:</label>
            <input type="text" class="form-control" id="cust_tin" name="tin">
          </div>
        </div>
      </div>

      <h6>Residential Address</h6>
      <hr class="dotted-hr">


      <div class="form-group">
        <div class="row mb-2">
          <div class="col-sm-3">
            <label class="form-label" for="cust_region">Region</label>
            <select class="form-control" id="cust_region" name="region">
              <option>option 1</option>
              <option>option 2</option>
              <option>option 3</option>
              <option>option 4</option>
              <option>option 5</option>
            </select>
          </div>
          <div class="col-sm-6">
            <label class="form-label" for="cust_prov">Province</label>
            <select class="form-control" id="cust_prov" name="province">
              <option>option 1</option>
              <option>option 2</option>
              <option>option 3</option>
              <option>option 4</option>
              <option>option 5</option>
            </select>
          </div>
          <div class="col-sm-3">
            <label class="form-label" for="cust_city">City</label>
            <select class="form-control" id="cust_city" name="city">
              <option>option 1</option>
              <option>option 2</option>
              <option>option 3</option>
              <option>option 4</option>
              <option>option 5</option>
            </select>
          </div>
        </div>



                                <div class="form-group">
                                    <div class="row mb-3">
                                        <div class="col-sm-4">
                                            <label class="form-label" for="cust_lname">Last Name:</label>
                                            <input type="text" class="form-control" id="cust_lname">
                                        </div>
                                        <div class="col-sm-4">
                                            <label class="form-label" for="cust_fname">First Name:</label>
                                            <input type="text" class="form-control" id="cust_fname">
                                        </div>
                                        <div class="col-sm-4">
                                            <label class="form-label" for="cust_mname">Middle Name:</label>
                                            <input type="text" class="form-control" id="cust_mname">
                                        </div>
                                    </div>
                                </div>


                                <div class="form-group">
                                    <div class="row mb-3">
                                        <div class="col-sm-4">
                                            <label class="form-label" for="cust_contact">Contact No.:</label>
                                            <input type="text" class="form-control" id="cust_contact">
                                        </div>
                                        <div class="col-sm-4">
                                            <label class="form-label" for="cust_comp">Company Name:</label>
                                            <input type="text" class="form-control" id="cust_comp">
                                        </div>
                                        <div class="col-sm-4">
                                            <label class="form-label" for="cust_tin">TIN:</label>
                                            <input type="text" class="form-control" id="cust_tin">
                                        </div>
                                    </div>
                                </div>

                                <h6>Residential Address</h6>
                                <hr class="dotted-hr">


                                <div class="form-group">
                                    <div class="row mb-2">
                                        <div class="col-sm-3">
                                            <label class="form-label" for="cust_region">Region</label>
                                            <select class="form-control" id="cust_region">
                                                <option>option 1</option>
                                                <option>option 2</option>
                                                <option>option 3</option>
                                                <option>option 4</option>
                                                <option>option 5</option>
                                            </select>
                                        </div>
                                        <div class="col-sm-6">
                                            <label class="form-label" for="cust_prov">Province</label>
                                            <select class="form-control" id="cust_prov">
                                                <option>option 1</option>
                                                <option>option 2</option>
                                                <option>option 3</option>
                                                <option>option 4</option>
                                                <option>option 5</option>
                                            </select>
                                        </div>
                                        <div class="col-sm-3">
                                            <label class="form-label" for="cust_city">City</label>
                                            <select class="form-control" id="cust_city">
                                                <option>option 1</option>
                                                <option>option 2</option>
                                                <option>option 3</option>
                                                <option>option 4</option>
                                                <option>option 5</option>
                                            </select>
                                        </div>
                                    </div>


      <div class="form-group">
        <div class="row mb-2">
        <div class="col-sm-6">
            <label class="form-label" for="cust_brgy">Barangay</label>
            <select class="form-control" id="cust_brgy" name="brgy">
              <option>option 1</option>
              <option>option 2</option>
              <option>option 3</option>
              <option>option 4</option>
              <option>option 5</option>
            </select>
          </div>
          <div class="col-sm-6">
            <label class="form-label" for="cust_subd">Subdivision:</label>
            <input type="text" class="form-control" id="cust_subd" name="subdivision">
          </div>
          <div class="col-sm-6">
            <label class="form-label" for="cust_lat">Latitude:</label>
            <input type="text" class="form-control" id="cust_lat" name="latitude">
          </div>
          <div class="col-sm-6">
            <label class="form-label" for="cust_long">Longitude:</label>
            <input type="text" class="form-control" id="cust_long" name="longitude">
          </div>
        </div>


                    <button type="button" class="btn btn-primary" onclick="toggleModal('storeModal')">Store Info</button>
                    <!-- <input type="submit" name="submit" value="submit"> -->
                    <button type="submit" class="btn btn-success">Save changes</button>


                </div>
            </div>
        </div>
    </div>

    <!-- Store Entry Modal -->
    <div class="modal fade" id="storeModal" tabindex="-1" aria-labelledby="storeModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="storeModalLabel">Store Info</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <div class="container-fluid">

                        <div class="form-group">
                            <div class="row mb-2">
                                <div class="col-sm-8">
                                    <label class="form-label" for="cust_lname">Store Name:</label>
                                    <input type="text" class="form-control" id="cust_lname">
                                </div>
                                <div class="col-sm-4">
                                    <label class="form-label" for="cust_contact">Contact No.:</label>
                                    <input type="text" class="form-control" id="cust_contact">
                                </div>
                            </div>
                        </div>



                        <h6>Store Address</h6>
                        <hr class="dotted-hr">

                        <div class="form-group">
                            <div class="row mb-2">
                                <div class="col-sm-3">
                                    <label class="form-label" for="cust_region">Region</label>
                                    <select class="form-control" id="cust_region">
                                        <option>option 1</option>
                                        <option>option 2</option>
                                        <option>option 3</option>
                                        <option>option 4</option>
                                        <option>option 5</option>
                                    </select>
                                </div>
                                <div class="col-sm-6">
                                    <label class="form-label" for="cust_prov">Province</label>
                                    <select class="form-control" id="cust_prov">
                                        <option>option 1</option>
                                        <option>option 2</option>
                                        <option>option 3</option>
                                        <option>option 4</option>
                                        <option>option 5</option>
                                    </select>
                                </div>
                                <div class="col-sm-3">
                                    <label class="form-label" for="cust_city">City</label>
                                    <select class="form-control" id="cust_city">
                                        <option>option 1</option>
                                        <option>option 2</option>
                                        <option>option 3</option>
                                        <option>option 4</option>
                                        <option>option 5</option>
                                    </select>
                                </div>
                            </div>

                            <div class="form-group">
                                <div class="row mb-2">
                                    <div class="col-sm-6">
                                        <label class="form-label" for="cust_brgy">Barangay</label>
                                        <select class="form-control" id="cust_brgy">
                                            <option>option 1</option>
                                            <option>option 2</option>
                                            <option>option 3</option>
                                            <option>option 4</option>
                                            <option>option 5</option>
                                        </select>
                                    </div>
                                    <div class="col-sm-6">
                                        <label class="form-label" for="cust_subd">Subdivision:</label>
                                        <input type="text" class="form-control" id="cust_subd">
                                    </div>
                                    <div class="col-sm-6">
                                        <label class="form-label" for="cust_lat">Latitude:</label>
                                        <input type="text" class="form-control" id="cust_lat">
                                    </div>
                                    <div class="col-sm-6">
                                        <label class="form-label" for="cust_long">Longitude:</label>
                                        <input type="text" class="form-control" id="cust_long">
                                    </div>
                                </div>
                            </div>

                            <div class="form-group">
                                <div class="row mb-2">
                                    <div class="col-sm-6">
                                        <label for="store_list">List Type:</label>
                                        <select class="form-control" id="store_region">
                                            <option>option 1</option>
                                            <option>option 2</option>
                                            <option>option 3</option>
                                            <option>option 4</option>
                                            <option>option 5</option>
                                        </select>
                                    </div>
                                    <div class="col-sm-6">
                                        <label for="store_length">Length of Stay:</label>
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
                                <div class="row mb-2">
                                    <div class="col-sm-12">
                                        <label for="store_remarks">Remarks</label>
                                        <textarea class="form-control" rows="3" id="store_remarks"></textarea>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-success swalDefaultSuccess">Save
                                changes</button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>


</section>



@endsection
<!-- jQuery -->
<script src="https://ajax.googleapis.com/ajax/libs/jquery/3.6.0/jquery.min.js"></script>
<!-- DataTables JavaScript -->
<script src="https://cdn.datatables.net/1.11.5/js/jquery.dataTables.min.js"></script>

<script>
    $(document).ready(function() {
        $('#customerTable').DataTable();
    });
</script>

<script>
    function toggleModal(modalId) {
        // Close the existing modal
        $('#customerModal').modal('hide');
        // Open another modal
        $('#' + modalId).modal('show');
    }
</script>
