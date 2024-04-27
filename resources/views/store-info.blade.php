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
                                        <label class="form-label" for="cust_region">Region</label>
                                        <select class="form-control" id="cust_region" name="region">
                                            <!-- <option>option 1</option>
                                            <option>option 2</option>
                                            <option>option 3</option>
                                            <option>option 4</option>
                                            <option>option 5</option> -->
                                        </select>
                                    </div>
                                    <div class="col-sm-6">
                                        <label class="form-label" for="cust_prov">Province</label>
                                        <select class="form-control" id="cust_prov" name="province">
                                            <!-- <option>option 1</option>
                                            <option>option 2</option>
                                            <option>option 3</option>
                                            <option>option 4</option>
                                            <option>option 5</option> -->
                                        </select>
                                    </div>
                                    <div class="col-sm-3">
                                        <label class="form-label" for="cust_city">City</label>
                                        <select class="form-control" id="cust_city" name="city">
                                            <!-- <option>option 1</option>
                                            <option>option 2</option>
                                            <option>option 3</option>
                                            <option>option 4</option>
                                            <option>option 5</option> -->
                                        </select>
                                    </div>
                                </div>

                                <div class="form-group">
                                    <div class="row mb-2">
                                        <div class="col-sm-6">
                                            <label class="form-label" for="cust_brgy">Barangay</label>
                                            <select class="form-control" id="cust_brgy" name="brgy">
                                                <!-- <option>option 1</option>
                                                <option>option 2</option>
                                                <option>option 3</option>
                                                <option>option 4</option>
                                                <option>option 5</option> -->
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
                                        <label class="form-label" for="region">Region</label>
                                        <select class="form-control" id="region" name="region">
                                            <!-- <option>option 1</option>
                                            <option>option 2</option>
                                            <option>option 3</option>
                                            <option>option 4</option>
                                            <option>option 5</option> -->
                                        </select>
                                    </div>
                                    <div class="col-sm-6">
                                        <label class="form-label" for="province">Province</label>
                                        <select class="form-control" id="province" name="province">
                                            <!-- <option>option 1</option>
                                            <option>option 2</option>
                                            <option>option 3</option>
                                            <option>option 4</option>
                                            <option>option 5</option> -->
                                        </select>
                                    </div>
                                    <div class="col-sm-3">
                                        <label class="form-label" for="city">City</label>
                                        <select class="form-control" id="city" name="city">
                                            <!-- <option>option 1</option>
                                            <option>option 2</option>
                                            <option>option 3</option>
                                            <option>option 4</option>
                                            <option>option 5</option> -->
                                        </select>
                                    </div>
                                </div>

                                <div class="form-group">
                                    <div class="row mb-2">
                                        <div class="col-sm-6">
                                            <label class="form-label" for="brgy">Barangay</label>
                                            <select class="form-control" id="brgy" name="brgy">
                                                <!-- <option>option 1</option>
                                                <option>option 2</option>
                                                <option>option 3</option>
                                                <option>option 4</option>
                                                <option>option 5</option> -->
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
            document.getElementById("region").value = reg;
            document.getElementById("province").value = prov;
            document.getElementById("city").value = city;
            document.getElementById("brgy").value = brgy;
            document.getElementById("subdivision2").value = subv;
            document.getElementById("longitude2").value = long;
            document.getElementById("latitude2").value = lat;
            document.getElementById("listype2").value = lis;
            document.getElementById("length_stay2").value = leng;
            document.getElementById("remarks2").value = rem2;
        }

    </script>


<script>
    $(document).ready(function(){
        
        // Function to populate the Region dropdown
        function populateRegionDropdown() {
    // Clear existing options
    $('#cust_region').empty();

    // Add a blank option as the first option
    $('#cust_region').append('<option value="">Please select</option>');
            $.ajax({
                type: "GET",
                url: "/get-regions", // Route to fetch regions from your server
                success: function(response) {
                    // $('#cust_region').empty(); // Clear existing options
                    $.each(response, function(key, value) {
                        $('#cust_region').append('<option value="' + value.code + '">' + value.name + '</option>');
                    });
                }
            });
        }
        function populateRegionDropdown2() {
    // Clear existing options

        $('#region').empty();

// Add a blank option as the first option
        $('#region').append('<option value="">Please select</option>');
            $.ajax({
                type: "GET",
                url: "/get-regions", // Route to fetch regions from your server
                success: function(response) {
                    // $('#cust_region').empty(); // Clear existing options
                    $.each(response, function(key, value) {
                        $('#region').append('<option value="' + value.code + '">' + value.name + '</option>');
                    });
                }
            });
        }
        // Function to populate the Province dropdown based on the selected region
        function populateProvinceDropdown(regionId) {
            $('#cust_prov').empty();

// Add a blank option as the first option
            $('#cust_prov').append('<option value="">Please select</option>');
            $.ajax({
                type: "GET",
                url: "/get-provinces/" + regionId, // Route to fetch provinces based on region
                success: function(response) {
                    // $('#cust_prov').empty(); // Clear existing options
                    $.each(response, function(key, value) {
                        $('#cust_prov').append('<option value="' + value.code + '">' + value.name + '</option>');
                    });
                }
            });
        }
        function populateProvinceDropdown2(regionId) {
            $('#province').empty();

// Add a blank option as the first option
            $('#province').append('<option value="">Please select</option>');
            $.ajax({
                type: "GET",
                url: "/get-provinces/" + regionId, // Route to fetch provinces based on region
                success: function(response) {
                    // $('#cust_prov').empty(); // Clear existing options
                    $.each(response, function(key, value) {
                        $('#province').append('<option value="' + value.code + '">' + value.name + '</option>');
                    });
                }
            });
        }

        // Function to populate the City dropdown based on the selected province
        function populateCityDropdown(provinceId) {
            $('#cust_city').empty();

// Add a blank option as the first option
            $('#cust_city').append('<option value="">Please select</option>');
            $.ajax({
                type: "GET",
                url: "/get-cities/" + provinceId, // Route to fetch cities based on province
                success: function(response) {
                    // $('#cust_city').empty(); // Clear existing options
                    $.each(response, function(key, value) {
                        $('#cust_city').append('<option value="' + value.code + '">' + value.name + '</option>');
                    });
                }
            });
        }
        function populateCityDropdown2(provinceId) {
            $('#city').empty();

// Add a blank option as the first option
            $('#city').append('<option value="">Please select</option>');
            $.ajax({
                type: "GET",
                url: "/get-cities/" + provinceId, // Route to fetch cities based on province
                success: function(response) {
                    // $('#cust_city').empty(); // Clear existing options
                    $.each(response, function(key, value) {
                        $('#city').append('<option value="' + value.code + '">' + value.name + '</option>');
                    });
                }
            });
        }

        function populateBrgyDropdown(cityId) {
            $('#cust_brgy').empty();

// Add a blank option as the first option
            $('#cust_brgy').append('<option value="">Please select</option>');
            $.ajax({
                type: "GET",
                url: "/get-brgy/" + cityId, // Route to fetch cities based on city/mun
                success: function(response) {
                    // $('#cust_brgy').empty(); // Clear existing options
                    $.each(response, function(key, value) {
                        $('#cust_brgy').append('<option value="' + value.code + '">' + value.name + '</option>');
                    });
                }
            });
        }
        function populateBrgyDropdown2(cityId) {
            $('#brgy').empty();

// Add a blank option as the first option
            $('#brgy').append('<option value="">Please select</option>');
            $.ajax({
                type: "GET",
                url: "/get-brgy/" + cityId, // Route to fetch cities based on city/mun
                success: function(response) {
                    // $('#cust_brgy').empty(); // Clear existing options
                    $.each(response, function(key, value) {
                        $('#brgy').append('<option value="' + value.code + '">' + value.name + '</option>');
                    });
                }
            });
        }
        // Initial population of Region dropdown
        populateRegionDropdown();
        populateRegionDropdown2();
        // Event listener for Region dropdown change
        $('#cust_region').change(function(){
            var regionId = $(this).val();
            if(regionId){
                // Populate Province dropdown based on selected region
                populateProvinceDropdown(regionId);
            }
        });
        $('#region').change(function(){
            var regionId = $(this).val();
            if(regionId){
                // Populate Province dropdown based on selected region
                populateProvinceDropdown2(regionId);
            }
        });

        // Event listener for Province dropdown change
        $('#cust_prov').change(function(){
            var provinceId = $(this).val();
            if(provinceId){
                // Populate City dropdown based on selected province
                populateCityDropdown(provinceId);
            }
        });
        $('#province').change(function(){
            var provinceId = $(this).val();
            if(provinceId){
                // Populate City dropdown based on selected province
                populateCityDropdown2(provinceId);
            }
        });
        // Event listener for brgy dropdown change
        $('#cust_city').change(function(){
            var cityId = $(this).val();
            if(cityId){
                // Populate brgy dropdown based on selected province
                populateBrgyDropdown(cityId);
            }
        });
        $('#city').change(function(){
            var cityId = $(this).val();
            if(cityId){
                // Populate brgy dropdown based on selected province
                populateBrgyDropdown2(cityId);
            }
        });
    });
</script>


@endsection