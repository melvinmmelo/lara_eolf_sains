@extends('layouts.app')

@section('custom_css')
@endsection

@section('contents')
    <!-- Content Header (Page header) -->
    <section class="content-header">
        <div class="container-fluid">
            <div class="row mb-2">
                <div class="col-sm-6">
                    <h1>Price Level</h1>
                </div>
                <div class="col-sm-6">
                    <ol class="breadcrumb float-sm-right">
                        <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Home</a></li>
                        <li class="breadcrumb-item active">Price Level</li>
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
                            {{-- <th>Branch</th> --}}
                            <th>Name</th>
                            <th>Description</th>
                            <th>Status</th>
                            <th>Created at</th>
                            <th></th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($pricelevels as $pl)
                            <tr>
                                {{-- <td>{{ $pl->branch_code }}</td> --}}
                                <td>{{ $pl->pl_name }}</td>
                                <td>{{ $pl->pl_desc }}</td>
                                <td>{{ $pl->pl_status }}</td>
                                <td>{{ $pl->date_created }}</td>
                                <td>
                                    <a href="#" data-toggle="modal" data-target="#modalEdit"
                                        class="btn btn-primary btn-sm"
                                        onclick="setToUpdate('{{ $pl->id }}','{{ $pl->pl_name }}','{{ $pl->pl_desc }}','{{ $pl->status }}')">Edit</a>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                    <tfoot>
                        <tr>
                            {{-- <th>Branch</th> --}}
                            <th>Name</th>
                            <th>Description</th>
                            <th>Status</th>
                            <th>Created at</th>
                            <th></th>
                        </tr>
                    </tfoot>
                </table>

            </div>
            <!-- /.card-body -->
            <div class="card-footer">
                <button type="button" class="btn btn-primary" data-toggle="modal" data-target="#modal-price-level">
                    Add New
                </button>
            </div>
            <!-- /.card-footer-->
        </div>
        <!-- /.card -->
        <div class="modal fade" id="modal-price-level">
            <div class="modal-dialog">
                <form method="POST" action="/pricing-level/store">
                    @csrf
                    <div class="modal-content">
                        <div class="modal-header">
                            <h4 class="modal-title">Add Price Level</h4>
                            <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                <span aria-hidden="true">&times;</span>
                            </button>
                        </div>
                        <div class="modal-body">

                            <div id="branchCodeCon" class="form-group">
                                <div class="row">
                                    <div class="col-sm-12">
                                        <label class="form-label" for="branch_code">Branch Code</label>
                                        <input type="text" class="form-control" name="branch_code" id="branch_code"
                                            value="{{ session('branch_code') }}" required readonly>
                                    </div>
                                </div>
                            </div>

                            <div class="form-group">
                                <div class="row">
                                    <div class="col-sm-12">
                                        <label class="form-label" for="code"><i style="color:red">*</i>Name</label>
                                        <input type="text" class="form-control" name="name">
                                    </div>

                                </div>
                            </div>
                            <div class="form-group">
                                <div class="row">
                                    <div class="col-sm-12">
                                        <label class="form-label" for="address"><i
                                                style="color:red">*</i>Description</label>
                                        <textarea class="form-control" rows="3" name="Description"></textarea>
                                    </div>
                                </div>
                            </div>

                            <div class="form-group">
                                <h6><i style="color:red">*</i>Price type</h6>
                                <div class="form-group clearfix">
                                    <div class="icheck-primary d-inline">
                                        <input type="radio" id="isForCustomer" name="priceType" value="CUSTOMER" checked>
                                        <label for="isForCustomer">For customers
                                        </label>
                                    </div>

                                    <div class="icheck-primary d-inline">
                                        <input type="radio" id="isFactoryPrice" name="priceType" value="FACTORY PRICE">
                                        <label for="isFactoryPrice">Factory Price
                                        </label>
                                    </div>
                                    <div class="icheck-primary d-inline">
                                        <input type="radio" id="isBadPricing" name="priceType" value="BAD PRICING">
                                        <label for="isBadPricing"> Bad pricing
                                        </label>
                                    </div>
                                </div>
                            </div>

                            {{-- <div class="form-group">
                                <div class="row">
                                    <div class="col-sm-12">
                                        <label class="form-label" for="address">Active</label>
                                        <br>
                                        <input type="checkbox" id="mySwitch" data-bootstrap-switch data-on-text="On"
                                            data-off-text="Off" data-on-color="success" data-off-color="danger"
                                            name="status">

                                        <div style="margin-bottom: 20px"></div>
                                    </div>
                                </div>
                            </div> --}}

                            <div class="modal-footer">
                                <button type="submit" class="btn btn-success">Save changes</button>
                            </div>
                        </div>
                        <!-- /.modal-content -->
                    </div>
                    <!-- /.modal-dialog -->
            </div>
            <!-- /.modal -->
            </form>
        </div>

        <div class="modal fade" id="modalEdit">
            <div class="modal-dialog">
                <form method="POST" action="{{ route('pricing-level.update') }}">
                    @csrf
                    @method('PUT')

                    <div class="modal-content">
                        <div class="modal-header">
                            <h4 class="modal-title">Edit Price Level</h4>
                            <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                <span aria-hidden="true">&times;</span>
                            </button>
                        </div>
                        <div class="modal-body">
                            <div class="form-group">
                                <div class="row">
                                    <div class="col-sm-12">
                                        <label class="form-label" for="code"><i style="color:red">*</i>Name</label>
                                        <input type="hidden" class="form-control" name="e_pricelevel_id" required
                                            readonly>
                                        <input type="text" class="form-control" name="e_name" required>
                                    </div>

                                </div>
                            </div>
                            <div class="form-group">
                                <div class="row">
                                    <div class="col-sm-12">
                                        <label class="form-label" for="address"><i
                                                style="color:red">*</i>Description</label>
                                        <textarea class="form-control" rows="3" name="e_description" required></textarea>
                                    </div>
                                </div>
                            </div>

                            <div class="form-group">
                                <h6><i style="color:red">*</i>Price type</h6>
                                <div class="form-group clearfix">
                                    <div class="icheck-primary d-inline">
                                        <input type="radio" name="e_priceType" id="e_isForCustomer" value="CUSTOMER"
                                            checked>
                                        <label for="e_isForCustomer">For customers
                                        </label>
                                    </div>
                                    <div class="icheck-primary d-inline">
                                        <input type="radio" name="e_priceType" id="e_isFactoryPrice"
                                            value="FACTORY PRICE">
                                        <label for="e_isFactoryPrice">Factory Price
                                        </label>
                                    </div>
                                    <div class="icheck-primary d-inline">
                                        <input type="radio" name="e_priceType" id="e_isBadPricing"
                                            value="BAD PRICING">
                                        <label for="e_isBadPricing"> Bad pricing
                                        </label>
                                    </div>
                                </div>
                            </div>

                            <div class="form-group">
                                <div class="row">
                                    <div class="col-sm-12">
                                        <label class="form-label" for="e_status">Active</label>
                                        <br>
                                        <input type="checkbox" id="mySwitch" data-bootstrap-switch data-on-text="On"
                                            data-off-text="Off" data-on-color="success" data-off-color="danger"
                                            name="e_status">

                                        <div style="margin-bottom: 20px"></div>
                                    </div>
                                </div>
                            </div>


                            <div class="modal-footer">
                                <button type="submit" class="btn btn-success">Save changes</button>
                            </div>
                        </div>
                        <!-- /.modal-content -->
                    </div>
                    <!-- /.modal-dialog -->
            </div>
            <!-- /.modal -->
            </form>
        </div>

    </section>
    <!-- /.content -->
@endsection


@section('custom_js')
    <script>
        window.onload = function() {

            var branchCode = document.getElementById('branchCodeCon');

            var forCustomerRadio = document.getElementById('isForCustomer');
            var factoryPriceRadio = document.getElementById('isFactoryPrice');
            var badPricingRadio = document.getElementById('isBadPricing');
            var inputName = document.querySelector('input[name="name"]');

            forCustomerRadio.addEventListener('change', function() {
                if (this.checked) {
                    branchCode.style.display = 'block';
                    inputName.value = '';
                    inputName.readOnly = false;
                }
            });

            factoryPriceRadio.addEventListener('change', function() {
                if (this.checked) {
                    branchCode.style.display = 'none';
                    inputName.value = 'FACTORY PRICE';
                    inputName.readOnly = true;
                }
            });

            badPricingRadio.addEventListener('change', function() {
                if (this.checked) {
                    branchCode.style.display = 'block';
                    inputName.value = 'BAD PRICING';
                    inputName.readOnly = true;
                }
            });


            // ! FOR EDITING

            var e_forCustomerRadio = document.getElementById('e_isForCustomer');
            var e_factoryPriceRadio = document.getElementById('e_isFactoryPrice');
            var e_badPricingRadio = document.getElementById('e_isBadPricing');
            var e_inputName = document.querySelector('input[name="e_name"]');


            e_forCustomerRadio.addEventListener('change', function() {
                if (this.checked) {
                    branchCode.style.display = 'block';
                    e_inputName.readOnly = false;
                }
            });

            e_factoryPriceRadio.addEventListener('change', function() {
                if (this.checked) {
                    branchCode.style.display = 'none';
                    e_inputName.value = 'FACTORY PRICE';
                    e_inputName.readOnly = true;
                }
            });

            e_badPricingRadio.addEventListener('change', function() {
                if (this.checked) {
                    branchCode.style.display = 'block';
                    e_inputName.value = 'BAD PRICING';
                    e_inputName.readOnly = true;
                }
            });

            // END FOR EDITING



        }

        function setToUpdate(id, name, description, status) {
            var inputId = document.querySelector('input[name="e_pricelevel_id"]');
            var inputName = document.querySelector('input[name="e_name"]');
            var inputDescription = document.querySelector('textarea[name="e_description"]');
            var inputStatus = document.querySelector('input[name="e_status"]');

            if (name == 'FACTORY PRICE') {
                // disable input name
                inputName.readOnly = true;
                inputName.value = "FACTORY PRICE";
                document.getElementById('e_isFactoryPrice').checked = true;
                document.getElementById('e_isBadPricing').checked = false;
                document.getElementById('e_isForCustomer').checked = false;

            } else if (name == 'BAD PRICING') {
                // disable input name
                inputName.readOnly = true;
                inputName.value = name;
                document.getElementById('e_isFactoryPrice').checked = false;
                document.getElementById('e_isBadPricing').checked = true;
                document.getElementById('e_isForCustomer').checked = false;
            } else {
                // enable input name
                inputName.readOnly = false;
                inputName.value = name;
                document.getElementById('e_isFactoryPrice').checked = false;
                document.getElementById('e_isBadPricing').checked = false;
                document.getElementById('e_isForCustomer').checked = true;
            }

            inputId.value = id;
            inputDescription.value = description;
            inputStatus.checked = status == 'ACTIVE' ? true : false;
        }
    </script>
@endsection
