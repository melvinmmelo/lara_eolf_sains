@extends('layouts.app')

@section('custom_css')
    <link rel="stylesheet" href="{{ asset('css/themes_base_jquery-ui.css') }}" />

    <style>
        #sortable {
            list-style-type: none;
            margin: 0;
            padding: 0;
            width: 100%;

        }

        #sortable li {
            margin: 0 3px 3px 3px;
            padding: 0.4em;
            padding-left: 1.5em;
            font-size: 1em;
        }

        #sortable li span {
            position: absolute;
            margin-left: -1.3em;
        }

        #sortable li.ui-state-default {
            background-color: #f0f0f0;
            /* Default background color */
            cursor: pointer;
            transition: background-color 0.3s ease;
        }

        #sortable li.ui-state-default:hover {
            background-color: #51c3f0;
            /* Background color on hover */
        }
    </style>
@endsection

@section('contents')
    <!-- Content Header (Page header) -->
    <section class="content-header">
        <div class="container-fluid">
            <div class="row mb-2">
                <div class="col-sm-6">
                    <h1>Organize Order Slip Orders</h1>
                </div>
                <div class="col-sm-6">
                    <ol class="breadcrumb float-sm-right">
                        <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Home</a></li>
                        <li class="breadcrumb-item active">Organize Order Slip Orders</li>
                    </ol>
                </div>
            </div>
        </div>
    </section>

    <!-- Main content -->
    <section class="content">

        @include('layouts.errors')

        <form>
            <div class="card">
                <div class="card-body">
                    <p>Drag and drop to reorder.</p>
                    <ul id="sortable">
                        @foreach ($inbounds as $inbound)
                            <li style="margin: 10px; width: 100%; border-radius: 10px" id="item_{{ $inbound->id }}"
                                class='ui-state-default'><span class="ui-icon ui-icon-arrowthick-2-n-s"></span>
                                <h6>{{ $inbound->degic_no . ' ' . $inbound->customer_name }}</h6>
                            </li>
                        @endforeach
                    </ul>

                    <div class="">
                        <a href="{{ route('report.orderSlip', ['code' => $orderSlip->code]) }}"><button type="button"
                                class="btn btn-primary float-right">
                                Print
                            </button></a>
                    </div>
                </div>
                <!-- /.card-body -->

                <!-- /.card-footer-->
            </div>
        </form>
        <!-- /.card -->
    </section>
    <!-- /.content -->
@endsection

@section('custom_js')
    {{-- <script src="https://code.jquery.com/jquery-3.7.1.js"></script> --}}
    <script src="https://code.jquery.com/ui/1.14.0/jquery-ui.js"></script>


    <script>
        $(document).ready(function() {
            // open modal
            $('#modal-print').on('show.bs.modal', function(e) {
                var deliveryPerson = $('#deliveryPerson').val();
                $('#delivery_person').val(deliveryPerson);
            });

            $(function() {
                $("#sortable").sortable({
                    placeholder: 'ui-state-highlight',
                    stop: function(event, ui) {
                        const sortedData = $("#sortable").sortable("serialize");

                        $.ajax({
                            type: "GET",
                            url: "/organize-update",
                            data: sortedData,
                            success: function(response) {
                                console.log("Sorting order updated successfully");
                            },
                            error: function(xhr, status, error) {
                                console.error("Error updating sorting order:",
                                    error);
                                // Optionally revert the sorting if the server update fails
                                // $("#sortable").sortable("cancel");
                            }
                        });
                    }
                }).disableSelection();
            });
        });
    </script>
@endsection
