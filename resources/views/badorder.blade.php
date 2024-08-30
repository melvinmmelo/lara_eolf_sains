@extends('layouts.app')

@section('contents')
    <!-- Content Header (Page header) -->
    <section class="content-header">
        <div class="container-fluid">
            <div class="row mb-2">
                <div class="col-sm-6">
                    <h1>Bad Order Details</h1>
                </div>
                <div class="col-sm-6">
                    <ol class="breadcrumb float-sm-right">
                        <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Home</a></li>
                        <li class="breadcrumb-item active">Bad Orders</li>
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
                <div class="pb-2">
                    <a href="{{ route('newbo.deducted') }}"><button type="button" class="btn btn-primary">
                            Deducted BOs
                        </button></a>
                </div>

                <table id="example1" class="table table-bordered table-striped">
                    <thead>
                        <tr>
                            <th>Customer</th>
                            <th>Total Amount</th>
                            <th>BO %</th>
                            <th>Remarks</th>
                            <th>Created Date</th>
                            <th>Action</th>
                        </tr>
                    </thead>
                    <tbody>

                        @php $grandTotal = []; @endphp
                        @foreach ($badOrders as $badOrder)
                            <tr>
                                <td>{{ $badOrder->degic_code . " " . $badOrder->customer->fullName }}</td>
                                <td>{{ $badOrder->amount }}</td>
                                <td>{{ $badOrder->bo_percentage }}</td>
                                <td>{{ $badOrder->remarks }}</td>
                                <td>{{ $badOrder->created_at }}</td>
                                <td>
                                    <button type="submit" class="btn btn-danger"
                                        onclick="return deleteBO(`{{ $badOrder->id }}`);">Delete</button>
                                    <button type="button" class="btn btn-success btn-print"
                                        data-bo-id="{{ $badOrder->id }}" onclick="printPage(this)">
                                        <i class="fa-solid fa-print"></i> Print
                                    </button>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                    <tfoot>
                        <tr>
                            <th>Total:</th>
                            <th>{{ formatNumber(array_sum($grandTotal)) }}</th>
                            <th></th>
                            <th></th>
                            <th></th>
                            <th></th>
                        </tr>
                    </tfoot>
                </table>

            </div>
            <!-- /.card-body -->
            <div class="card-footer">
                <button type="button" class="btn btn-primary"
                    onclick="window.location.href='{{ route('newbo.create') }}'">
                    Add New
                </button>
            </div>
            <!-- /.card-footer-->
        </div>
        <!-- /.card -->


        <!-- /.modal-dialog -->
        <div class="modal fade" id="modal-badorder">
            <div class="modal-dialog">
                <form method="POST" action="#">
                    @csrf
                    <div class="modal-content">
                        <div class="modal-header">
                            <h4 class="modal-title">Add bad order</h4>
                            <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                <span aria-hidden="true">&times;</span>
                            </button>
                        </div>
                        <div class="modal-body">

                            <div class="form-group">
                                <div class="row mb-2">
                                    <div class="col-sm-12">
                                        <label class="form-label" for="code"><i style="color:red">*</i>Customer</label>
                                        <select class="form-control select2bs4" id="customer" name="customer">

                                        </select>
                                    </div>

                                    <div class="col-sm-12">
                                        <label class="form-label" for="name"><i style="color:red">*</i>Outbound</label>
                                        <select class="form-control select2bs4" id="outbound" name="outbound">

                                        </select>
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

    </section>


    <div class="modal fade" id="modal-delete">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header">
                    <h4 class="modal-title" id="deleteHeaderTitle">Delete bad order</h4>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <form action="{{ route('bo.destroy') }}" method="POST">
                        @csrf
                        @method('DELETE')

                        <div class="form-group">
                            <input type="text" name="bo_id" class="form-control mb-2 mt-2" required readonly>
                        </div>


                        <div class="modal-footer">
                            <button type="submit" class="btn btn-danger">Delete</button>
                        </div>

                    </form>
                </div>
                <!-- /.modal-content -->
            </div>
            <!-- /.modal-dialog -->
        </div>
        <!-- /.modal -->
    </div>
@endsection

@section('custom_js')
    <script>
        function confirmSetInactive() {
            return confirm("Are you sure you want to update the product status?")
        }

        function deleteBO(obId) {
            var boId = obId;
            $('#modal-delete').modal('show');
            $('#modal-delete').find('input[name="bo_id"]').val(boId);

        }
    </script>
    <script>
        function printPage(button) {
            var boId = button.getAttribute('data-bo-id');

            // AJAX request to fetch BO details
            fetch(`/getBoDetails/${boId}`)
                .then(response => response.json())
                .then(data => {

                    badOrder = data["badOrder"];
                    dbProducts = data["products"];

                    // Assuming data is an array of items
                    var products = dbProducts.map(item => `
                            <tr>
                                <td>${item.ptype_code}</td>
                                <td>${item.quantity}</td>
                                <td align="right">${formatNumber(item.quantity * item.price)}</td>
                            </tr>
                        `).join('');

                    var totalAmount = data["amount"];
                    var created = badOrder["created_at"];
                    // Create a Date object from the string
                    var dateObj = new Date(created);

                    // Extract the date components
                    var month = dateObj.getMonth() + 1; // Month is zero-based, so we add 1
                    var day = dateObj.getDate();
                    var year = dateObj.getFullYear();

                    // Format the date as MM-DD-YYYY
                    var formattedDate =
                        `${month.toString().padStart(2, '0')}-${day.toString().padStart(2, '0')}-${year}`;
                    var boperc = badOrder["bo_percentage"];
                    var lessamt = totalAmount * (boperc / 100);
                    var netamt = totalAmount - lessamt;

                    var fname = badOrder.customer.firstname;
                    var lname = badOrder.customer.lastname;
                    // Create a new window for printing
                    var mywindow = window.open('', 'PRINT', 'height=900,width=1000');
                    mywindow.document.write('<html><head><title>BAD ORDER SLIP</title>');
                    mywindow.document.write('<style>');
                    mywindow.document.write(
                        'body{ font-family:"Arial",Helvetica,sans-serif;font-size: 9pt;word-wrap: break-word; }');
                    mywindow.document.write('hr { border: 0; border-top: 1px solid #000; margin: 10px 0; }');
                    mywindow.document.write(
                        'td { font-family:"Arial",Helvetica,sans-serif;font-size: 9pt;word-wrap: break-word; }');
                    mywindow.document.write('</style>');
                    mywindow.document.write('</head><body>');
                    mywindow.document.write('<center>EOLF FOOD TRADING OPC</center><br>');
                    mywindow.document.write('<center>BAD ORDER SLIP</center><br>');
                    mywindow.document.write('<br><br>');
                    mywindow.document.write('BO no.:  ' + boId + '<br>');
                    mywindow.document.write('Date:  ' + formattedDate + '<br>');
                    mywindow.document.write('Customer: ' + lname + ', ' + fname + '<br>');
                    mywindow.document.write('<table width="100%">');
                    mywindow.document.write('<tr><td>Items</td><td>Pcs</td><td align="right">Amount</td></tr>');
                    mywindow.document.write(products);
                    mywindow.document.write('</table>');
                    mywindow.document.write('<hr>');
                    mywindow.document.write('<table width="100%">');
                    mywindow.document.write('<tr><td align="left">Sub-total:</td><td align="right">' + formatNumber(
                        totalAmount) + '</td></tr>');
                    mywindow.document.write('<tr><td align="left">BO Amount Due:</td><td align="right">' + formatNumber(
                        lessamt) + '</td></tr>');
                    mywindow.document.write('<tr><td align="left">BO (%):</td><td align="right">' + boperc +
                        '%</td></tr>');
                    mywindow.document.write('<tr><td align="left">Total Amount:</td><td align="right">' + formatNumber(
                        netamt) + '</td></tr>');
                    mywindow.document.write('</table>');
                    mywindow.document.write('Received By:<br>');
                    mywindow.document.write('<br><br><br>');
                    mywindow.document.write('---------------------------------<br>');
                    mywindow.document.write('Signature over printed name<br>');
                    mywindow.document.write('</body></html>');

                    mywindow.document.close(); // Necessary for IE >= 10
                    mywindow.focus(); // Necessary for IE >= 10

                    mywindow.print();
                    mywindow.close();
                })
                .catch(error => console.error('Error:', error));


        }

        function formatNumber(amount) {
            // Ensure amount is a number and convert it to a string with fixed two decimal places
            var formattedAmount = parseFloat(amount).toFixed(2);

            // Split the number into integer and decimal parts
            var parts = formattedAmount.split('.');
            var integerPart = parts[0];
            var decimalPart = parts.length > 1 ? '.' + parts[1] : '';

            // Add commas for thousands in the integer part
            integerPart = integerPart.replace(/\B(?=(\d{3})+(?!\d))/g, ',');

            // Combine integer part and decimal part
            return integerPart + decimalPart;
        }
    </script>
@endsection
