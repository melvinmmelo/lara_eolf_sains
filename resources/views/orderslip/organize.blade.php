@extends('layouts.app')

@section('custom_css')

    <style>
        .order-list {
            list-style-type: none;
            margin: 0;
            padding: 0;
            width: 100%;
        }

        .order-item {
            margin: 10px 0;
            padding: 15px;
            border: 2px solid #e0e0e0;
            border-radius: 10px;
            background-color: #f8f9fa;
            transition: all 0.3s ease;
            display: flex;
            align-items: center;
            gap: 15px;
        }

        .order-item:hover {
            border-color: #51c3f0;
            background-color: #f0f8ff;
        }

        .order-item.checked {
            background-color: #e8f5e8;
            border-color: #28a745;
        }

        .order-checkbox {
            width: 20px;
            height: 20px;
            cursor: pointer;
        }

        .order-number {
            background-color: #007bff;
            color: white;
            border-radius: 50%;
            width: 30px;
            height: 30px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-weight: bold;
            font-size: 14px;
        }

        .order-number.hidden {
            visibility: hidden;
        }

        .order-content {
            flex-grow: 1;
        }

        .reset-btn {
            margin-bottom: 15px;
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
                    <p>Check items in the order you want them organized.</p>
                    <button type="button" class="btn btn-secondary btn-sm reset-btn" id="resetOrder">
                        Reset All
                    </button>
                    <ul class="order-list">
                        @foreach ($inbounds as $inbound)
                            <li class="order-item" data-id="{{ $inbound->id }}">
                                <input type="checkbox" class="order-checkbox" id="checkbox_{{ $inbound->id }}"
                                       data-inbound-id="{{ $inbound->id }}">
                                <div class="order-number hidden" id="number_{{ $inbound->id }}"></div>
                                <div class="order-content">
                                    <label for="checkbox_{{ $inbound->id }}" style="cursor: pointer; margin: 0;">
                                        <h6>{{ $inbound->degic_no . ' ' . $inbound->customer_name }}</h6>
                                    </label>
                                </div>
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


    <script>
        $(document).ready(function() {
            let orderCounter = 0;
            let checkedItems = [];

            // open modal
            $('#modal-print').on('show.bs.modal', function(e) {
                var deliveryPerson = $('#deliveryPerson').val();
                $('#delivery_person').val(deliveryPerson);
            });

            // Handle checkbox changes
            $('.order-checkbox').on('change', function() {
                const inboundId = $(this).data('inbound-id');
                const isChecked = $(this).is(':checked');
                const orderItem = $(this).closest('.order-item');
                const orderNumber = $('#number_' + inboundId);

                if (isChecked) {
                    // Add to checked items
                    orderCounter++;
                    checkedItems.push({
                        id: inboundId,
                        order: orderCounter
                    });

                    // Update visual elements
                    orderItem.addClass('checked');
                    orderNumber.removeClass('hidden').text(orderCounter);
                } else {
                    // Remove from checked items
                    const itemIndex = checkedItems.findIndex(item => item.id == inboundId);
                    if (itemIndex > -1) {
                        const removedOrder = checkedItems[itemIndex].order;
                        checkedItems.splice(itemIndex, 1);

                        // Update order numbers for remaining items
                        checkedItems.forEach((item, index) => {
                            if (item.order > removedOrder) {
                                item.order--;
                                $('#number_' + item.id).text(item.order);
                            }
                        });
                        orderCounter--;
                    }

                    // Update visual elements
                    orderItem.removeClass('checked');
                    orderNumber.addClass('hidden').text('');
                }

                // Send update to server
                updateOrder();
            });

            // Reset all selections
            $('#resetOrder').on('click', function() {
                $('.order-checkbox').prop('checked', false);
                $('.order-item').removeClass('checked');
                $('.order-number').addClass('hidden').text('');
                checkedItems = [];
                orderCounter = 0;
                updateOrder();
            });

            function updateOrder() {
                // Sort checked items by order
                const sortedItems = checkedItems.sort((a, b) => a.order - b.order);
                const itemArray = sortedItems.map(item => item.id);

                // Only send request if there are checked items
                if (itemArray.length > 0) {
                    $.ajax({
                        type: "GET",
                        url: "/organize-update",
                        data: { item: itemArray },
                        success: function(response) {
                            console.log("Order updated successfully");
                        },
                        error: function(xhr, status, error) {
                            console.error("Error updating order:", error);
                        }
                    });
                }
            }
        });
    </script>
@endsection
