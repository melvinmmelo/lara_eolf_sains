@extends('layouts.app')

@section('contents')
    <!-- Content Header (Page header) -->
    <section class="content-header">
        <div class="container-fluid">
            <div class="row mb-2">
                <div class="col-sm-6">
                    <h1>Customers</h1>
                </div>
                <div class="col-sm-6">
                    <ol class="breadcrumb float-sm-right">
                        <li class="breadcrumb-item"><a href="#">Home</a></li>
                        <li class="breadcrumb-item active">Customers</li>
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
        <!-- Default box -->
        <div class="card">
            <div class="card-header">
                <h3 class="card-title">Customers Info</h3>

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
                <table id="example1" class="table table-bordered table-striped">
                    <thead>
                        <tr>
                            <th>ID</th>
                            <th>Distributor</th>
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
                            <td>{{ $customer->id }}</td>
                                <td>{{ $customer->distributor }}</td>
                                <td>{{ $customer->lastname }}</td>
                                <td>{{ $customer->firstname }}</td>
                                <td>{{ $customer->middlename }}</td>
                                <td>{{ $customer->companyname }}</td>
                                
                                <td>
                                    <!-- <button class="btn btn-primary btn-sm edit-btn" data-toggle="modal" data-target="#editCustomerModal" data-id="{{ $customer->id }}">Edit</button> -->
                                   
                                    <button class="btn btn-primary btn-sm edit-btn" data-toggle="modal" data-target="#editCustomerModal" onclick="setToUpdatecustomer('{{ $customer->id }}','{{ $customer->distributor }}','{{ $customer->lastname }}','{{ $customer->firstname }}','{{ $customer->middlename }}','{{ $customer->contact_no }}','{{ $customer->companyname }}','{{ $customer->tin }}','{{ $customer->longitude }}','{{ $customer->latitude }}')">Edit</button>
                                    <!-- <a class="dropdown-item" href="#" data-toggle="modal" data-target="#editCustomerModal" onclick="setToUpdatecustomer('{{ $customer->id }}',{{ $customer->lastname }}','{{ $customer->firstname }}','{{ $customer->middlename }}','{{ $customer->contact_no }}')">Edit</a> -->
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
            </div>
            <div class="card-footer">
                <button type="button" class="btn btn-primary" data-toggle="modal" data-target="#customerModal">Add New</button>
            </div>
        </div>
        <!-- /.card -->

        </section>
    <!-- /.content -->

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
                        <form method="POST" action="/customers/store">
                        @csrf
                        <div class="container-fluid">
                            <!-- Your existing form -->
                            <div class="form-group">

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

                                    </div><!-- /.container-fluid -->
                                </div><!-- /.content-header -->

                            </div>
                        
                        </div><!-- /.container-fluid -->
                    </div><!-- /.content-header -->
                </div>
                <div class="modal-footer">
                    <!-- Button to open another modal -->
                    <!-- <button type="button" class="btn btn-primary" onclick="toggleModal('storeModal')">Store
                        Info</button>
                    <button type="button" class="btn btn-success swalDefaultSuccess">Save changes</button> -->
                    <button type="button" class="btn btn-primary" onclick="toggleModal('storeModal')">Store Info</button>
                    <!-- <button type="button" class="btn btn-primary" data-toggle="modal" data-target="#storeModal">Store Info</button> -->
                    <!-- <input type="submit" name="submit" value="submit"> -->
                    <button type="submit" class="btn btn-success">Save changes</button>
                </div>
            </div></form>
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

<!-- Edit Customer Modal -->
<div class="modal fade" id="editCustomerModal" tabindex="-1" aria-labelledby="editCustomerModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="editCustomerModalLabel">Edit Customer</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <form id="editCustomerForm" method="POST" action="{{ route('customer.update') }}">
                @csrf
                @method('PATCH')

                
                <div class="modal-body">
                <input type="text" class="form-control" name="id" id="id" required readonly>

                    <div class="container-fluid">
                        <div class="form-group">
                            <label for="distributor">Distributor:</label>
                            <input type="text" class="form-control" id="distributor" name="distributor" >
                        </div>
                        <div class="form-group">
                            <label class="form-label" for="lastname">Last Name:</label>
                            <input type="text" class="form-control" id="lastname" name="lastname">
                        </div>
                        <div class="form-group">
                            <label class="form-label" for="firstname">First Name:</label>
                            <input type="text" class="form-control" id="firstname" name="firstname">
                        </div>
                        <div class="form-group">
                            <label class="form-label" for="middlename">Middle Name:</label>
                            <input type="text" class="form-control" id="middlename" name="middlename">
                        </div>
                        <div class="form-group">
                            <label class="form-label" for="contact_no">Contact No.:</label>
                            <input type="text" class="form-control" id="contact_no" name="contact_no">
                        </div>
                        <div class="form-group">
                            <label class="form-label" for="companyname">Company Name:</label>
                            <input type="text" class="form-control" id="companyname" name="companyname" >
                        </div>
                        <div class="form-group">
                            <label class="form-label" for="tin">TIN:</label>
                            <input type="text" class="form-control" id="tin" name="tin">
                        </div>
                        <div class="form-group">
                            <label class="form-label" for="longitude">LONGITUDE:</label>
                            <input type="text" class="form-control" id="longitude" name="longitude">
                        </div>
                        <div class="form-group">
                            <label class="form-label" for="latitude">LATITUDE:</label>
                            <input type="text" class="form-control" id="latitude" name="latitude">
                        </div>
                        <!-- Add more input fields for other customer attributes -->
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                    <button type="submit" class="btn btn-primary">Save changes</button>
                </div>
            </form>
        </div>
    </div>
</div>


<!-- JavaScript to handle edit button click and populate modal -->
<script>
    $('.edit-btn').click(function() {
        var id = $(this).data('id');
        $.ajax({
            url: '/customers/' + id + '/edit',
            method: 'GET',
            success: function(data) {
                $('#edit_distributor').val(data.distributor);
                $('#edit_lastname').val(data.lastname);
                $('#edit_firstname').val(data.firstname);
                $('#edit_middlename').val(data.middlename);
                $('#edit_companyname').val(data.companyname);
                // Update other modal fields with data from 'data' object
            },
            error: function(xhr, status, error) {
                console.error(xhr.responseText);
            }
        });
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

    <script>
        // Initialize SweetAlert
        const Toast = Swal.mixin({
            toast: true,
            position: 'top-end',
            showConfirmButton: false,
            timer: 3000,
            timerProgressBar: true,
            didOpen: (toast) => {
                toast.addEventListener('mouseenter', Swal.stopTimer)
                toast.addEventListener('mouseleave', Swal.resumeTimer)
            }
        });

        // Click event handler for the button
        $('.swalDefaultSuccess').click(function() {
            Toast.fire({
                icon: 'success',
                title: 'Customers Added'
            })
        });
    </script>
    <!-- Include any additional scripts you may need -->




@endsection


@section('custom_js')
    <script>
        function setToUpdatecustomer(uid,dist, ln, fn, mn, con,cm,tin,long,lat) {
            document.getElementById("id").value = uid;
            document.getElementById("distributor").value = dist;
            document.getElementById("lastname").value = ln;
            document.getElementById("firstname").value = fn;
            document.getElementById("middlename").value = mn;
            document.getElementById("contact_no").value = con;
            document.getElementById("companyname").value = cm;
            document.getElementById("tin").value = tin;
            document.getElementById("longitude").value = long;
            document.getElementById("latitude").value = lat;
            
        }
    </script>
@endsection