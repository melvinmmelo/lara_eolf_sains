@extends('layouts.app')
<style>
    .input-number {
        text-align: center;
    }

    @media (max-width: 767px) {

        .table-responsive table td,
        .table-responsive table th {
            display: block;
            width: 100%;
        }

        .table-responsive table th {
            display: none;
        }

        .align-middle {
            text-align: left;
            padding: 8px;
        }

        .d-md-table-header {
            display: table-header-group !important;
            border: none !important;
        }

        .d-md-none {
            display: none;
        }


        .desktop-view {
            display: none;
        }
    }

    .buttontypes {
        margin: 5px;
    }

    .product-list {
        max-height: 250px;
        overflow: auto;
    }
</style>

@section('contents')
    <!-- Content Header (Page header) -->
    <section class="content-header">
        <div class="container-fluid">
            <div class="row mb-2">
                <div class="col-sm-6">
                    <h1>Inventory</h1>
                </div>
                <div class="col-sm-6">
                    <ol class="breadcrumb float-sm-right">
                        <li class="breadcrumb-item"><a href="#">Home</a></li>
                        <li class="breadcrumb-item active">Inventory</li>
                    </ol>
                </div>
            </div>
        </div><!-- /.container-fluid -->
    </section>

    <!-- Main content -->
    <section class="content">

        <!-- Default box -->
        <div class="card">

            <div class="card-body">

                <div>
                    <div id="inboundList">
                        <div class="row">
                            <div class="col-sm-6">
                                <div class="form-group">
                                    <label class="form-label">DR. No.</label>
                                    <input type="text" class="form-control" id="#" readonly>
                                </div>
                            </div>
                            <div class="col-sm-6">
                                <div class="form-group">
                                    <label class="form-label">Issued date</label>
                                    <input type="text" class="form-control" id="#" readonly>
                                </div>
                            </div>


                        </div>


                        <div class="row">
                            <div class="col-sm-8">
                                <div class="form-group">
                                    <label class="form-label">Items</label>
                                    <select class="form-control select2bs4" style="width: 100%;">
                                        <option selected="selected">Alabama</option>
                                        <option>Alaska</option>
                                        <option>California</option>
                                        <option>Delaware</option>
                                        <option>Tennessee</option>
                                        <option>Texas</option>
                                        <option>Washington</option>
                                    </select>
                                </div>
                            </div>

                            <div class="col-sm-2">
                                <div class="form-group">
                                    <label class="form-label">Quantity</label>
                                    <input type="text" class="form-control" id="#">
                                </div>
                            </div>

                            <div class="col-sm-2">
                                <div class="form-group">
                                    <label class="form-label">&nbsp;</label>
                                    <div><button type="button" class="btn btn-primary" style="width: 100%">
                                            Add
                                        </button></div>
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-sm-12">
                                <div class="table-responsive">
                                    <table class="table table-bordered table-striped">
                                        <thead>
                                            <tr>
                                                <th>Item</th>
                                                <th>Quantity</th>

                                                <th>Unit Price</th>
                                                <th>Total</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <tr>
                                                <td colspan="5" class="d-md-none"><strong>Items</strong></td>
                                            </tr>
                                            <tr>
                                                <td class="align-middle text-center" colspan="4">No data
                                                    available.
                                                </td>
                                            </tr>

                                            <!-- Additional rows here -->
                                            <tr>
                                                <td colspan="5" class="d-md-none"><strong>Total</strong></td>
                                            </tr>
                                        </tbody>
                                        <tfoot class="desktop-view">
                                            <tr>
                                                <td colspan="3"></td>
                                                <td>Total:</td>

                                            </tr>
                                        </tfoot>
                                    </table>
                                </div>
                            </div>
                        </div>
                        <!-- /.card-body -->
                        <div class="card-footer">
                            Footer
                        </div>
                        <!-- /.card-footer-->
                    </div>
                    <!-- /.card -->

    </section>
    <!-- /.content -->
@endsection
