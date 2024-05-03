<meta charset="utf-8">
<meta name="viewport" content="width=device-width, initial-scale=1">
<title>EOLF</title>

<head>
    <link rel="stylesheet"
        href="https://fonts.googleapis.com/css?family=Source+Sans+Pro:300,400,400i,700&display=fallback">

    <link rel="stylesheet" href="{{ asset('plugins/fontawesome-free/css/all.min.css') }} ">
    <script src="https://kit.fontawesome.com/133d51430d.js" crossorigin="anonymous"></script>

    <link rel="stylesheet" href="{{ asset('vendor/adminlte/dist/css/adminlte.min.css') }}">
    <style>
        .card-body {
            background: #f3f7fd;
        }

        @media print {
            .btn-print {
                display: none;
            }
        }
    </style>
</head>

<!-- Content Header (Page header) -->


<!-- Main content -->
<section class="content">

    <!-- Default box -->
    <div class="card">
        <div class="card-header">
            <h3 class="card-title">Loading Ticket</h3>


        </div>
        <div class="card-body">
            <div>Sequence No: </div>
            <div>Date: </div>
            <div>Delivery Person: </div>
            <div>Customer Data: </div>
            <div>Encoded By: </div>
            <hr style="border-top: 1px dashed">

            <div class="d-flex">
                <div class="p-2 w-50">Product</div>
                <div class="p-2 w-50">Quantity</div>

            </div>

            <div class="d-flex">
                <div class="p-2 w-50">SC</div>
                <div class="p-2 w-50">5</div>

            </div>
            <div class="d-flex">
                <div class="p-2 w-50"></div>
                <div class="p-2 w-50">5</div>

            </div>
            <hr style="border-top: 1px dashed">
            <div> CUT FOR SPOON </div>
            <div>Sequence No: </div>
            <div>Customer:</div>
            <div>Total Spoon Count: </div>
            <div>Spoon Set: </div>

        </div>
        <!-- /.card-body -->
        <div class="card-footer">
            <div class="d-flex">
                <div class="p-2"><button type="submit" class="btn btn-success btn-print" onclick="printPage()"><i
                            class="fa-solid fa-print"></i> Print</button></div>
                <div class="p-2"><a href="/orders" class="btn btn-danger btn-print"><i class="fa-solid fa-xmark"></i>
                        Close</a></div>

            </div>

        </div>
        <!-- /.card-footer-->
    </div>
    <!-- /.card -->

    <script>
        function printPage() {
            // Hide the print button before printing
            var printButton = document.querySelector('.btn-print');
            if (printButton) {
                printButton.style.display = 'none';
            }
            // Trigger the print dialog
            window.print();
            // Show the print button after printing is done (optional)
            if (printButton) {
                printButton.style.display = 'block';
            }
        }
    </script>


</section>
<!-- /.content -->
