<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>EOLF</title>

    <!-- Theme style -->

    <link rel="stylesheet"
        href="https://fonts.googleapis.com/css?family=Source+Sans+Pro:300,400,400i,700&display=fallback">
    <link rel="stylesheet" href="{{ asset('plugins/fontawesome-free/css/all.min.css') }} ">
    <link rel="stylesheet" href="{{ asset('plugins/icheck-bootstrap/icheck-bootstrap.min.css') }} ">
    <link rel="stylesheet" href="{{ asset('vendor/adminlte/dist/css/adminlte.min.css') }}">

    <!-- Include SweetAlert and Toast CSS -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/limonte-sweetalert2/11.1.2/sweetalert2.min.css">
    <!-- Include AdminLTE JavaScript -->
    <script src="https://cdnjs.cloudflare.com/ajax/libs/admin-lte/3.2.0/js/adminlte.min.js"></script>
    <!-- Include SweetAlert and Toast JavaScript -->

    <style>
        .login-page {
            background-image: url("{{ asset('img/eolfbg.png') }}");
            background-size: cover;
            background-position: center;

        }
    </style>

</head>


<body class="hold-transition login-page">
    <div class="login-box">
        <!-- /.login-logo -->
        <div class="card card-outline card-primary">
            <div class="card-header text-center">
                <img src="{{ asset('img/eolf_logo.jpg') }}" alt="EOLF Logo" class="img-fluid"
                    style="max-width: 100%; height: auto; ">
            </div>
            <div class="card-body">
                <p class="login-box-msg">Welcome! <strong>Username</strong></p>
                <p class="login-box-msg">Select branch</p>
                <hr>



                <form method="POST" action="#">
                    @csrf


                    <div class="form-group clearfix">
                        <div class="icheck-primary d-inline">
                            <input type="radio" id="radioPrimary1" name="r1" checked>
                            <label for="radioPrimary1">
                                EOLF Food Trading OPC - Cagayan
                            </label>
                        </div>
                        <hr>
                        <div class="icheck-primary d-inline">
                            <input type="radio" id="radioPrimary2" name="r1" checked>
                            <label for="radioPrimary2">
                                EOLF Food Trading OPC - Tarlac
                            </label>
                        </div>
                    </div>



                    <div class="row">

                        <!-- /.col -->
                        <div class="col-12">
                            <button type="submit" class="btn btn-primary btn-block">Submit</button>
                        </div>
                        <!-- /.col -->
                    </div>
                </form>
            </div>
            <!-- /.card-body -->
        </div>
        <!-- /.card -->
    </div>
    <!-- /.login-box -->
    <!-- jQuery -->
    <script src="{{ asset('plugins/jquery/jquery.min.js') }}"></script>
    <!-- Bootstrap 4 -->
    <script src="{{ asset('plugins/bootstrap/js/bootstrap.bundle.min.js') }}"></script>

    <script src="{{ asset('vendor/adminlte/dist/js/adminlte.min.js') }}"></script>
    <!-- SweetAlert2 -->


</body>

</html>
