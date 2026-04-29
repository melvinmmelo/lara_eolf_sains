@extends('layouts.app')

@section('custom_css')
<style>
    .search-results {
        max-height: 300px;
        overflow-y: auto;
    }
    .selected-items {
        max-height: 300px;
        overflow-y: auto;
    }
    .loading-spinner {
        display: none;
        margin-top: 10px;
    }
    .search-error {
        display: none;
        color: #dc3545;
        margin-top: 10px;
    }
    .search-wrapper {
        position: relative;
    }
    .search-wrapper .loading-spinner {
        position: absolute;
        right: 10px;
        top: 50%;
        transform: translateY(-50%);
    }
</style>
@endsection

@section('contents')
    <section class="content-header">
        <div class="container-fluid">
            <div class="row mb-2">
                <div class="col-sm-6">
                    <h1>Withdraw Materials</h1>
                </div>
                <div class="col-sm-6">
                    <ol class="breadcrumb float-sm-right">
                        <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Home</a></li>
                        <li class="breadcrumb-item active">Withdraw Materials</li>
                    </ol>
                </div>
            </div>
            <div class="row">
                <div class="col-sm-12">
                    <a href="{{ route('material-withdrawals.list') }}" class="btn btn-info btn-sm">
                        <i class="fas fa-list"></i> View Withdrawal History
                    </a>
                </div>
            </div>
        </div>
    </section>

    <section class="content">
        @include('layouts.errors')

        <form id="withdrawalForm" action="{{ route('material-withdrawals.review') }}" method="POST">
            @csrf
            <div class="row">

            <div class="col-md-3">
                    <div class="card">
                        <div class="card-header">
                            <h3 class="card-title text-blue">Details</h3>
                        </div>
                        <div class="card-body">
                            <div class="form-group">
                                <label for="requested_by">Requested By</label>
                                <select class="form-control select2bs4" name="requested_by" id="requested_by" required>
                                    <option value="">-- Select Customer --</option>
                                    @foreach ($customers as $customer)
                                        <option value="{{ $customer->lastname }}, {{ $customer->firstname }} {{ $customer->middlename }}">
                                            {{ $customer->lastname }}, {{ $customer->firstname }} {{ $customer->middlename }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="form-group">
                                <label for="issued_by">Issued By</label>
                                <select class="form-control select2bs4" name="issued_by" id="issued_by" required>
                                    <option value="">-- Select Issuer --</option>
                                    @if ($employees->isNotEmpty())
                                        <optgroup label="Employees">
                                            @foreach ($employees as $employee)
                                                <option value="{{ $employee->fullName }}">{{ $employee->fullName }}</option>
                                            @endforeach
                                        </optgroup>
                                    @endif
                                    @if ($drivers->isNotEmpty())
                                        <optgroup label="Drivers">
                                            @foreach ($drivers as $driver)
                                                <option value="{{ $driver->name }}">{{ $driver->name }}</option>
                                            @endforeach
                                        </optgroup>
                                    @endif
                                </select>
                            </div>
                            <div class="form-group">
                                <label for="withdrawal_date">Withdrawal Date</label>
                                <input type="date" class="form-control" name="withdrawal_date" id="withdrawal_date" value="{{ date('Y-m-d') }}" required>
                            </div>
                        </div>
                    </div>
                </div>


                <div class="col-md-9">
                    <div class="card">
                        <div class="card-header">
                            <h3 class="card-title text-blue">Items</h3>
                        </div>
                        <div class="card-body">
                            <div class="form-group">
                                <div class="search-wrapper">
                                    <input type="text" class="form-control" id="searchInput" placeholder="Search materials...">
                                    <div class="loading-spinner">
                                        <i class="fas fa-spinner fa-spin"></i>
                                    </div>
                                </div>
                                <div class="search-error">
                                    <i class="fas fa-exclamation-circle"></i>
                                    <span class="error-message">Error occurred while searching</span>
                                </div>
                            </div>
                            <div class="search-results" id="searchResults">
                                <!-- Search results will be populated here -->
                            </div>
                        </div>


                        <div class="selected-items" id="selectedItems">
                            <!-- Selected items will be shown here -->
                        </div>

                        <div class="text-right m-2">
                            <button type="submit" class="btn btn-primary" id="reviewBtn">
                                <i class="fas fa-arrow-right"></i> Review & Continue
                            </button>
                        </div>


                    </div>

                </div>
            </div>


        </form>
    </section>
@endsection

@section('custom_js')
<script>
$(document).ready(function() {
    let selectedItems = new Map();
    let searchTimeout;
    let currentRequest = null;

    function showLoading() {
        $('.loading-spinner').show();
        $('.search-error').hide();
    }

    function hideLoading() {
        $('.loading-spinner').hide();
    }

    function showError(message) {
        $('.search-error .error-message').text(message);
        $('.search-error').show();
        $('#searchResults').html('<p class="text-muted p-2">No items found</p>');
    }

    $('#searchInput').on('input', function() {
        clearTimeout(searchTimeout);
        let query = $(this).val();

        if (query.length < 2) {
            $('#searchResults').empty();
            hideLoading();
            return;
        }

        showLoading();

        // Abort previous request if it exists
        if (currentRequest) {
            currentRequest.abort();
        }

        searchTimeout = setTimeout(function() {
            currentRequest = $.ajax({
                url: '{{ config('app.url') }}/material-withdrawals/search',
                data: { query: query },
                beforeSend: function() {
                    showLoading();
                },
                success: function(data) {
                    hideLoading();
                    let html = '';
                    if (data && data.length > 0) {
                        html += `<h5 class="card-header text-blue">Search Results <small> Click Plus Icon to Add</small></h5>`;

                        html += `<div class="row g-2 p-2">`;

                        data.forEach(function(item) {
                            if (!selectedItems.has(item.id)) {
                                html += `
                                    <div class="col-md-6 col-lg-4">
                                        <div class="card h-100">
                                            <div class="card-body p-3">
                                                <div class="d-flex justify-content-between align-items-center">
                                                    <div>
                                                        <h6 class="mb-1">${item.name}</h6>
                                                        <p class="mb-0">Available: ${item.quantity} ${item.unit || ''}</p>
                                                    </div>
                                                    <button type="button" class="btn btn-sm btn-primary add-item"
                                                        data-id="${item.id}"
                                                        data-name="${item.name}"
                                                        data-unit="${item.unit || ''}"
                                                        data-available="${item.quantity}">
                                                        <i class="fas fa-plus"></i>
                                                    </button>
                                                </div>
                                            </div>
                                        </div>
                                    </div>`;
                            }
                        });

                        html += `</div>`;
                    } else {
                        html = '<p class="text-muted p-2">No items found</p>';
                    }
                    $('#searchResults').html(html);
                },
                error: function(xhr, status, error) {
                    hideLoading();
                    if (status !== 'abort') {
                        let errorMessage = 'An error occurred while searching';
                        if (xhr.responseJSON && xhr.responseJSON.message) {
                            errorMessage = xhr.responseJSON.message;
                        }
                        showError(errorMessage);
                    }
                }
            });
        }, 500); // Increased debounce time for better performance
    });

    $(document).on('click', '.add-item', function() {
        const id = $(this).data('id');
        const name = $(this).data('name');
        const unit = $(this).data('unit');
        const available = $(this).data('available');

        if (!selectedItems.has(id)) {
            selectedItems.set(id, { name, unit, available });
            updateSelectedItemsView();
            $(this).closest('tr').remove();
        }
    });

    function updateSelectedItemsView() {
        let html = '';

        if (selectedItems.size > 0) {
            html += `
                <table class="table table-bordered table-hover m-2">
                    <thead class="table-dark">
                        <tr>
                            <th>Item</th>
                            <th>Available</th>
                            <th>Quantity</th>
                            <th>Action</th>
                        </tr>
                    </thead>
                    <tbody>`;

            selectedItems.forEach((item, id) => {
                html += `
                    <tr>
                        <td>${item.name}</td>
                        <td>${item.available} ${item.unit}</td>
                        <td>
                            <input type="number" class="form-control form-control-sm"
                                style="width: 100px"
                                name="items[${id}][quantity]"
                                min="1"
                                max="${item.available}"
                                required
                                placeholder="Qty"
                                value="1">
                            <input type="hidden" name="items[${id}][id]" value="${id}">
                            <input type="hidden" name="items[${id}][name]" value="${item.name}">
                            <input type="hidden" name="items[${id}][unit]" value="${item.unit}">
                            <input type="hidden" name="items[${id}][available]" value="${item.available}">
                        </td>
                        <td>
                            <button type="button" class="btn btn-sm btn-danger remove-item" data-id="${id}">
                                <i class="fas fa-times"></i>
                            </button>
                        </td>
                    </tr>`;
            });

            html += `
                    </tbody>
                </table>`;
        } else {
            html = '<p class="text-muted p-2 m-2">No items selected</p>';
        }

        $('#selectedItems').html(html);
    }

    $(document).on('click', '.remove-item', function() {
        const id = $(this).data('id');
        selectedItems.delete(id);
        updateSelectedItemsView();
    });

    $('#withdrawalForm').on('submit', function(e) {
        if (selectedItems.size === 0) {
            e.preventDefault();
            alert('Please select at least one item');
            return false;
        }

        let valid = true;
        let errorMsg = 'Please check the quantities entered';
        $(this).find('input[name$="[quantity]"]').each(function() {
            const qty = parseFloat($(this).val());
            if (isNaN(qty) || qty < 1) {
                valid = false;
                errorMsg = 'Quantity must be at least 1.';
                $(this).addClass('is-invalid');
                return false;
            }
            if (!this.checkValidity()) {
                valid = false;
                $(this).addClass('is-invalid');
                return false;
            }
            $(this).removeClass('is-invalid');
        });

        if (!valid) {
            e.preventDefault();
            alert(errorMsg);
            return false;
        }

        // Form will submit normally to review page
        return true;
    });
});
</script>
@endsection
