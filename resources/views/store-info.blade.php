@extends('layouts.app')

@section('contents')
    <!-- Content Header (Page header) -->
    @if (session('success'))
            <script>
                // JavaScript code to trigger SweetAlert pop-up message
                document.addEventListener('DOMContentLoaded', function() {
                    // Set default icon
                    let icon = 'success';

                    // Check if success message is "Customer deleted successfully!"
                    @if (session('success') == 'Store Info deleted successfully!')
                        icon = 'error'; // Set icon to 'error' if message is for deletion
                    @elseif (session('success') == 'Store Info updated successfully!')
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
    <section class="content-header">
        <div class="container-fluid">
            <div class="row mb-2">
                <div class="col-sm-6">
                <h1>Store Info ({{ request()->query('customer_name') }})</h1>
                </div>
                <div class="col-sm-6">
                    <ol class="breadcrumb float-sm-right">
                        <li class="breadcrumb-item"><a href="#">Home</a></li>
                        <li class="breadcrumb-item active">Store Info</li>
                    </ol>
                </div>
            </div>
        </div><!-- /.container-fluid -->
    </section>

    <!-- Main content -->
    <section class="content">

        <!-- Default box -->
        <div class="card">
            <div class="card-header">
                <h3 class="card-title">Store List</h3>

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
                            <th>ID</th>
                            <th>Store Name</th>
                            <th>Contact No.</th>
                            <th>Address</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($storeinfos as $storeinfo)
                            <tr>
                                <td>{{ $storeinfo->id }}</td>
                                <td>{{ $storeinfo->storename }}</td>
                                <td>{{ $storeinfo->contactno }}</td>
                                <!-- {{ $storeinfo->firstname }} -->
                                <td></td> 


                                <td>


                                <button class="btn btn-primary btn-sm edit-btn" data-toggle="modal"
                                    data-target="#editStoreModal"
                                    onclick="setToUpdatestoreinfo('{{ $storeinfo->id }}', '{{ $storeinfo->customer_id }}', '{{ $storeinfo->storename }}', '{{ $storeinfo->contactno }}', '{{ $storeinfo->region }}', '{{ $storeinfo->province }}', '{{ $storeinfo->city }}', '{{ $storeinfo->brgy }}', '{{ $storeinfo->subdivision }}', '{{ $storeinfo->longitude }}', '{{ $storeinfo->latitude }}', '{{ $storeinfo->listype }}', '{{ $storeinfo->length_stay }}', '{{ $storeinfo->remarks }}')">Edit</button>



                                    <form method="POST" action="{{ route('store-info.destroy', $storeinfo->id) }}" style="display: inline;">
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
            <!-- /.card-body -->
            <div class="card-footer">
                <button type="button" class="btn btn-primary" data-toggle="modal" data-target="#storeModal">Add Store
                    Info</button>
            </div>

            <!-- /.card-footer-->
        </div>
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
                        <form method="POST" action="/store-info/store">
                            @csrf
                            Customer ID:
                            <input type="type" class="form-control" name="customer_id" value="{{ request()->query('customer_id') }}" readonly required>
                            <input type="hidden" name="customer_name" value="{{ request()->query('customer_name') }}">
                        <div class="container-fluid">

                            <div class="form-group">
                                <div class="row mb-2">
                                    <div class="col-sm-8">
                                        <label class="form-label" for="storename">Store Name:</label>
                                        <input type="text" class="form-control" id="storename" name="storename">
                                    </div>
                                    <div class="col-sm-4">
                                        <label class="form-label" for="cust_contact">Contact No.:</label>
                                        <input type="text" class="form-control" id="contactno" name="contactno">
                                    </div>
                                </div>
                            </div>



                            <h6>Store Address</h6>
                            <hr class="dotted-hr">

                            <div class="form-group">
                                <div class="row mb-2">
                                    <div class="col-sm-3">
                                        <label class="form-label" for="region">Region</label>
                                        <select class="form-control" id="region" name="region">
                                            <option>option 1</option>
                                            <option>option 2</option>
                                            <option>option 3</option>
                                            <option>option 4</option>
                                            <option>option 5</option>
                                        </select>
                                    </div>
                                    <div class="col-sm-6">
                                        <label class="form-label" for="province">Province</label>
                                        <select class="form-control" id="province" name="province">
                                            <option>option 1</option>
                                            <option>option 2</option>
                                            <option>option 3</option>
                                            <option>option 4</option>
                                            <option>option 5</option>
                                        </select>
                                    </div>
                                    <div class="col-sm-3">
                                        <label class="form-label" for="city">City</label>
                                        <select class="form-control" id="city" name="city">
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
                                            <label class="form-label" for="brgy">Barangay</label>
                                            <select class="form-control" id="brgy" name="brgy">
                                                <option>option 1</option>
                                                <option>option 2</option>
                                                <option>option 3</option>
                                                <option>option 4</option>
                                                <option>option 5</option>
                                            </select>
                                        </div>
                                        <div class="col-sm-6">
                                            <label class="form-label" for="subdivision">Subdivision:</label>
                                            <input type="text" class="form-control" id="subdivision" name="subdivision">
                                        </div>
                                        <div class="col-sm-6">
                                            <label class="form-label" for="latitude">Latitude:</label>
                                            <input type="text" class="form-control" id="latitude" name="latitude">
                                        </div>
                                        <div class="col-sm-6">
                                            <label class="form-label" for="longitude">Longitude:</label>
                                            <input type="text" class="form-control" id="longitude" name="longitude">
                                        </div>
                                    </div>
                                </div>

                                <div class="form-group">
                                    <div class="row mb-2">
                                        <div class="col-sm-6">
                                            <label for="listype">List Type:</label>
                                            <select class="form-control" id="listype" name="listype" >
                                                <option>option 1</option>
                                                <option>option 2</option>
                                                <option>option 3</option>
                                                <option>option 4</option>
                                                <option>option 5</option>
                                            </select>
                                        </div>
                                        <div class="col-sm-6">
                                            <label for="length_stay">Length of Stay:</label>
                                            <select class="form-control" id="length_stay" name="length_stay">
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
                                            <label for="remarks">Remarks</label>
                                            <!-- <textarea class="form-control" rows="3" id="remarks" name="remarks"></textarea> -->
                                            <input type="type" class="form-control" name="remarks" id="remarks" >
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="modal-footer">
                                <!-- <button type="submit" class="btn btn-success swalDefaultSuccess">Save
                                    changes</button> -->
                                    <button type="submit" class="btn btn-success">Save changes</button>
                            </div>
                        </div></form>
                    </div>
                </div>
            </div>
        </div>


        <div class="modal fade" id="editStoreModal" tabindex="-1" aria-labelledby="storeModalLabel" aria-hidden="true">
            <div class="modal-dialog modal-dialog-centered modal-lg">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="storeModalLabel">Store Info</h5>
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                    <div class="modal-body">
                        <form method="POST" action="{{ route('store-info.update') }}">
                        

                        @csrf
                        @method('PATCH')
                            Customer ID:
                            <input type="type" class="form-control" name="customer_id" id="customer_id"  readonly required>
                            Store ID:
                            <input type="type" class="form-control" name="id" id="id" readonly required>
                                
                            <input type="hidden" name="customer_name" value="{{ request()->query('customer_name') }}">
                        <div class="container-fluid">

                            <div class="form-group">
                                <div class="row mb-2">
                                    <div class="col-sm-8">
                                        <label class="form-label" for="storename2">Store Name:</label>
                                        <input type="text" class="form-control" id="storename2" name="storename">
                                    </div>
                                    <div class="col-sm-4">
                                        <label class="form-label" for="contactno2">Contact No.:</label>
                                        <input type="text" class="form-control" id="contactno2" name="contactno">
                                    </div>
                                </div>
                            </div>



                            <h6>Store Address</h6>
                            <hr class="dotted-hr">

                            <div class="form-group">
                                <div class="row mb-2">
                                    <div class="col-sm-3">
                                        <label class="form-label" for="region2">Region</label>
                                        <select class="form-control" id="region2" name="region">
                                            <option>option 1</option>
                                            <option>option 2</option>
                                            <option>option 3</option>
                                            <option>option 4</option>
                                            <option>option 5</option>
                                        </select>
                                    </div>
                                    <div class="col-sm-6">
                                        <label class="form-label" for="province2">Province</label>
                                        <select class="form-control" id="province2" name="province">
                                            <option>option 1</option>
                                            <option>option 2</option>
                                            <option>option 3</option>
                                            <option>option 4</option>
                                            <option>option 5</option>
                                        </select>
                                    </div>
                                    <div class="col-sm-3">
                                        <label class="form-label" for="city2">City</label>
                                        <select class="form-control" id="city2" name="city">
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
                                            <label class="form-label" for="brgy2">Barangay</label>
                                            <select class="form-control" id="brgy2" name="brgy">
                                                <option>option 1</option>
                                                <option>option 2</option>
                                                <option>option 3</option>
                                                <option>option 4</option>
                                                <option>option 5</option>
                                            </select>
                                        </div>
                                        <div class="col-sm-6">
                                            <label class="form-label" for="subdivision2">Subdivision:</label>
                                            <input type="text" class="form-control" id="subdivision2" name="subdivision">
                                        </div>
                                        <div class="col-sm-6">
                                            <label class="form-label" for="latitude2">Latitude:</label>
                                            <input type="text" class="form-control" id="latitude2" name="latitude">
                                        </div>
                                        <div class="col-sm-6">
                                            <label class="form-label" for="longitude2">Longitude:</label>
                                            <input type="text" class="form-control" id="longitude2" name="longitude">
                                        </div>
                                    </div>
                                </div>

                                <div class="form-group">
                                    <div class="row mb-2">
                                        <div class="col-sm-6">
                                            <label for="listype2">List Type:</label>
                                            <select class="form-control" id="listype2" name="listype" >
                                                <option>option 1</option>
                                                <option>option 2</option>
                                                <option>option 3</option>
                                                <option>option 4</option>
                                                <option>option 5</option>
                                            </select>
                                        </div>
                                        <div class="col-sm-6">
                                            <label for="length_stay2">Length of Stay:</label>
                                            <select class="form-control" id="length_stay2" name="length_stay">
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
                                            <label for="remarks2">Remarks</label>
                                            <!-- <textarea class="form-control" rows="3" id="remarks2" name="remarks"></textarea> -->
                                            <input type="type" class="form-control" name="remarks" id="remarks2" >
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="modal-footer">
                                <!-- <button type="submit" class="btn btn-success swalDefaultSuccess">Save
                                    changes</button> -->
                                    <button type="submit" class="btn btn-success">Save changes</button>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

    </section>
    <!-- /.content -->
@endsection


@section('custom_js')
    <script>
        function setToUpdatestoreinfo(uid, cusid, storename2, contactno2, reg, prov, city, brgy, subv, long, lat, lis, leng, rem2) {
            document.getElementById("id").value = uid;
            document.getElementById("customer_id").value = cusid;
            document.getElementById("storename2").value = storename2;
            document.getElementById("contactno2").value = contactno2;
            document.getElementById("region2").value = reg;
            document.getElementById("province2").value = prov;
            document.getElementById("city2").value = city;
            document.getElementById("brgy2").value = brgy;
            document.getElementById("subdivision2").value = subv;
            document.getElementById("longitude2").value = long;
            document.getElementById("latitude2").value = lat;
            document.getElementById("listype2").value = lis;
            document.getElementById("length_stay2").value = leng;
            document.getElementById("remarks2").value = rem2;
        }

    </script>
@endsection