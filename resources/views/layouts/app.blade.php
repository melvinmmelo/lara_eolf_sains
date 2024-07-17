<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>EOLF</title>

    <link rel="stylesheet"
        href="https://fonts.googleapis.com/css?family=Source+Sans+Pro:300,400,400i,700&display=fallback">
    <link rel="stylesheet" href="{{ asset('plugins/fontawesome-free/css/all.min.css') }} ">
    <script src="https://kit.fontawesome.com/133d51430d.js" crossorigin="anonymous"></script>
    <link rel="stylesheet" href="{{ asset('vendor/adminlte/dist/css/adminlte.min.css') }}">
    <link rel="stylesheet" href="{{ asset('plugins/datatables-bs4/css/dataTables.bootstrap4.min.css') }}">
    <link rel="stylesheet" href="{{ asset('plugins/datatables-responsive/css/responsive.bootstrap4.min.css') }}">
    <link rel="stylesheet" href="{{ asset('plugins/datatables-buttons/css/buttons.bootstrap4.min.css') }}">
    <link rel="stylesheet" href="{{ asset('plugins/bootstrap4-duallistbox/bootstrap-duallistbox.min.css') }}">
    <link rel="stylesheet" href="{{ asset('plugins/select2/css/select2.min.css') }}">
    <link rel="stylesheet" href="{{ asset('plugins/select2-bootstrap4-theme/select2-bootstrap4.min.css') }}">
    <!-- Include SweetAlert and Toast CSS -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/limonte-sweetalert2/11.1.2/sweetalert2.min.css">
    <!-- Include AdminLTE JavaScript -->

    <link rel="stylesheet" href="{{ asset('plugins/icheck-bootstrap/icheck-bootstrap.min.css') }}">

    <link rel="stylesheet" href="https://unpkg.com/leaflet/dist/leaflet.css" />

    <script src="https://unpkg.com/leaflet/dist/leaflet.js"></script>

    <!-- Forms label CSS Style -->
    <style>

        .form-label {
            font-weight: normal !important;
            color: #5a5a5a;
            transition: color 0.3s;
            /* Adding transition for smooth color change */
        }

        .form-control:hover,
        {
        background-color: #f0f0f0;
        }

        /* Add custom CSS to control modal content height and scrollbar */
        .modal-body {
            max-height: calc(100vh - 200px);
            /* Adjust as needed */
            overflow-y: auto;
        }


        /* Add custom CSS to control modal content height and scrollbar */
        .modal-body {
            max-height: calc(100vh - 200px);
            /* Adjust as needed */
            overflow-y: auto;
        }

        .modal-backdrop {
            background-color: rgba(255, 255, 255, 0.5) !important;
            /* Set the background color to white with some transparency */
            backdrop-filter: blur(10px);
            /* Apply a blur effect to the backdrop */
            /* 5% opacity black */
        }

        /* Style for dotted horizontal rule */
        .dotted-hr {
            border-top: 2px dotted #ccc;
            /* Change color and thickness as needed */
            margin: 20px 0;
            /* Adjust spacing as needed */
        }

        .label-input {
            width: 100%;
            border: none;
            background-color: transparent;
            font-size: 1rem;
            font-family: inherit;
            padding: 0;
            margin: 0;
            color: inherit;
        }

        .label-input:focus {
            outline: none;
        }

        /* Change the background color of the export buttons */
        .dataTables_wrapper .dt-buttons .btn {
            background-color: #ffffff;
            color: #292b2c;
            /* Text color */
            border-color: #e7e7e7;
            /* Border color */
            border-radius: 0px;
        }

        /* Change the background color of the export buttons on hover */
        .dataTables_wrapper .dt-buttons .btn:hover {
            background-color: #0275d8;
            border-color: #0275d8;
            color: #fff;
        }

        .tbContainer {
            width: 100%;
            height: 600px;
            overflow: auto;
        }
    </style>

    @yield('custom_css')

</head>

<body class="hold-transition sidebar-mini layout-fixed">
    <!-- Site wrapper -->
    <div class="wrapper">
        <!-- Navbar -->
        @include('layouts.nav')
        <!-- /.navbar -->

        <!-- Main Sidebar Container -->
        @include('layouts.aside')
        <!-- Main Sidebar Container -->

        <!-- Content Wrapper. Contains page content -->
        <div class="content-wrapper">
            @yield('contents')
            {{-- <div class="preloader flex-column justify-content-center align-items-center">
                <img class="animation__shake" src="{{ asset('img/preloader.jpg') }}" alt="AdminLTELogo">
            </div> --}}
        </div>
        <!-- /.content-wrapper -->

        @include('layouts.footer')

        <!-- Control Sidebar -->
        <aside class="control-sidebar control-sidebar-dark">
            <!-- Control sidebar content goes here -->
        </aside>
        <!-- /.control-sidebar -->
    </div>
    <!-- ./wrapper -->

    <!-- jQuery -->
    <script src="https://cdnjs.cloudflare.com/ajax/libs/limonte-sweetalert2/11.1.2/sweetalert2.min.js"></script>
    <script src="{{ asset('plugins/jquery/jquery.min.js') }}"></script>
    <script src="{{ asset('plugins/bootstrap/js/bootstrap.bundle.min.js') }}"></script>
    <script src="{{ asset('plugins/sweetalert2/sweetalert2.min.js') }}"></script>
    <script src="{{ asset('plugins/datatables/jquery.dataTables.min.js') }}"></script>
    <script src="{{ asset('plugins/datatables-bs4/js/dataTables.bootstrap4.min.js') }}"></script>
    <script src="{{ asset('plugins/datatables-responsive/js/dataTables.responsive.min.js') }}"></script>
    <script src="{{ asset('plugins/datatables-responsive/js/responsive.bootstrap4.min.js') }}"></script>
    <script src="{{ asset('plugins/datatables-buttons/js/dataTables.buttons.min.js') }}"></script>
    <script src="{{ asset('plugins/datatables-buttons/js/buttons.bootstrap4.min.js') }}"></script>
    <script src="{{ asset('plugins/jszip/jszip.min.js') }}"></script>
    <script src="{{ asset('plugins/pdfmake/pdfmake.min.js') }}"></script>
    <script src="{{ asset('plugins/pdfmake/vfs_fonts.js') }}"></script>
    <script src="{{ asset('plugins/datatables-buttons/js/buttons.html5.min.js') }}"></script>
    <script src="{{ asset('plugins/datatables-buttons/js/buttons.print.min.js') }}"></script>
    <script src="{{ asset('plugins/datatables-buttons/js/buttons.colVis.min.js') }}"></script>
    <script src="{{ asset('plugins/bootstrap-switch/js/bootstrap-switch.min.js') }}"></script>
    <script src="{{ asset('plugins/bootstrap4-duallistbox/jquery.bootstrap-duallistbox.js') }}"></script>
    <script src="{{ asset('plugins/select2/js/select2.full.min.js') }}"></script>
    <script src="{{ asset('vendor/adminlte/dist/js/adminlte.min.js') }}"></script>

    <script>
        $(function() {

            if (document.getElementById('example1')) {
                $("#example1").DataTable({
                    "order": [],
                    "paging": false,
                    "scrollY": "1000px",
                    "scrollCollapse": true,
                    "responsive": true,
                    "lengthChange": false,
                    "autoWidth": false,
                    "buttons": ["copy", "csv", "excel", "pdf", "print", "colvis"]
                }).buttons().container().appendTo('#example1_wrapper .col-md-6:eq(0)');
            }


            if (document.getElementById('customer_tb')) {
                $("#customer_tb").DataTable({
                    "order": [0, 'asc'],
                    "paging": false,
                    "scrollY": "1000px",
                    "scrollCollapse": true,
                    "responsive": true,
                    "lengthChange": false,
                    "autoWidth": false,
                    "columnDefs": [
                        { "width": "10%", "targets": 0 },
                        { "width": "10%", "targets": 1 },
                        { "width": "10%", "targets": 2 },
                        { "width": "10%", "targets": 3 },
                        { "width": "10%", "targets": 4 },
                        { "width": "19%", "targets": 5 },
                        { "width": "10%", "targets": 6 },
                        { "width": "10%", "targets": 7 },
                        { "width": "10%", "targets": 8 },
                    ],
                    "buttons": ["copy", "csv", "excel", "pdf", "print", "colvis"]
                }).buttons().container().appendTo('#customer_tb_wrapper .col-md-6:eq(0)');
            }

            if (document.getElementById('example1e')) {
                $("#example1e").DataTable({
                    "order": [3, 'asc'],
                    "paging": false,
                    "scrollY": "1000px",
                    "scrollCollapse": true,
                    "responsive": true,
                    "lengthChange": false,
                    "autoWidth": false,
                    "buttons": ["copy", "csv", "excel", "pdf", "print", "colvis"]
                }).buttons().container().appendTo('#example1e_wrapper .col-md-6:eq(0)');
            }

            if (document.getElementById('example2')) {
                $('#example2').DataTable({
                    "paging": true,
                    "lengthChange": false,
                    "searching": false,
                    "ordering": true,
                    "info": true,
                    "autoWidth": false,
                    "responsive": true,
                });
            }

            if (document.getElementById('example3')) {
                $('#example3').DataTable({
                    "paging": false,
                    "lengthChange": false,
                    "searching": true,
                    "ordering": true,
                    "info": true,
                    "autoWidth": true,
                    "responsive": true,
                });
            }

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

            $('.swalDefaultSuccess').click(function() {
                Toast.fire({
                    icon: 'success',
                    title: 'Data Added'
                })
            });

            $("[data-bootstrap-switch]").bootstrapSwitch();

            $('.duallistbox').bootstrapDualListbox()

            $('.select2').select2()

            $('.select2bs4').select2({
                theme: 'bootstrap4'
            })

        });
    </script>

    @yield('custom_js')

    <script src="{{ asset('js/editcustomeraddress.js') }}"></script>

</body>

</html>
