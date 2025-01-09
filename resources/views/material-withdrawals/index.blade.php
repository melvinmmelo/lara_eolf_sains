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
                    <h1>Material Withdrawal</h1>
                </div>
                <div class="col-sm-6">
                    <ol class="breadcrumb float-sm-right">
                        <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Home</a></li>
                        <li class="breadcrumb-item active">Material Withdrawal</li>
                    </ol>
                </div>
            </div>
        </div>
    </section>

    <section class="content">
        @include('layouts.errors')

        <form id="withdrawalForm" action="{{ route('material-withdrawals.store') }}" method="POST">
            @csrf
            <div class="row">
                <div class="col-md-4">
                    <div class="card">
                        <div class="card-header">
                            <h3 class="card-title">Search Items</h3>
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
                    </div>
                </div>
                <div class="col-md-5">
                <div class="card">
                        <div class="card-header">
                            <h3 class="card-title">Selected Items</h3>
                        </div>
                        <div class="card-body">
                            <div class="selected-items" id="selectedItems">
                                <!-- Selected items will be shown here -->
                            </div>
                        </div>
                    </div>
                </div>

                <div class="col-md-3">
                    <div class="card">
                        <div class="card-header">
                            <h3 class="card-title">Withdrawal Form</h3>
                        </div>
                        <div class="card-body">
                            <div class="form-group">
                                <label for="requested_by">Requested By</label>
                                <input type="text" class="form-control" name="requested_by" id="requested_by" value="{{ auth()->user()->fullName }}" required>
                            </div>
                            <div class="form-group">
                                <label for="issued_by">Issued By</label>
                                <input type="text" class="form-control" name="issued_by" id="issued_by" value="{{ auth()->user()->fullName }}" required>
                            </div>
                            <div class="form-group">
                                <label for="withdrawal_date">Withdrawal Date</label>
                                <input type="date" class="form-control" name="withdrawal_date" id="withdrawal_date" value="{{ date('Y-m-d') }}" required>
                            </div>
                            <button type="submit" class="btn btn-primary" id="submitBtn">Submit Withdrawal</button>
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
                url: '{{ route("material-withdrawals.search") }}',
                data: { query: query },
                beforeSend: function() {
                    showLoading();
                },
                success: function(data) {
                    hideLoading();
                    let html = '';
                    if (data && data.length > 0) {
                        data.forEach(function(item) {
                            if (!selectedItems.has(item.id)) {
                                html += `
                                    <div class="card mb-2">
                                        <div class="card-body p-2">
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
                                                    Add
                                                </button>
                                            </div>
                                        </div>
                                    </div>`;
                            }
                        });
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
            $(this).closest('.card').remove();
        }
    });

    function updateSelectedItemsView() {
        let html = '';
        selectedItems.forEach((item, id) => {
            html += `
                <div class="card mb-2">
                    <div class="card-body p-2">
                        <div class="d-flex justify-content-between align-items-center">
                            <div>
                                <h5 class="mb-1">${item.name}</h5>
                                <p class="mb-0">Available: ${item.available} ${item.unit}</p>
                            </div>
                            <div class="d-flex align-items-center">
                                <input type="number" class="form-control form-control-sm mr-2" 
                                    style="width: 100px"
                                    name="items[${id}][quantity]"
                                    min="1"
                                    max="${item.available}"
                                    required
                                    placeholder="Qty">
                                <input type="hidden" name="items[${id}][id]" value="${id}">
                                <button type="button" class="btn btn-sm btn-danger remove-item" data-id="${id}">
                                    Remove
                                </button>
                            </div>
                        </div>
                    </div>
                </div>`;
        });
        $('#selectedItems').html(html || '<p class="text-muted p-2">No items selected</p>');
    }

    $(document).on('click', '.remove-item', function() {
        const id = $(this).data('id');
        selectedItems.delete(id);
        updateSelectedItemsView();
    });

    $('#withdrawalForm').on('submit', function(e) {
        e.preventDefault();
        if (selectedItems.size === 0) {
            alert('Please select at least one item');
            return;
        }

        let valid = true;
        $(this).find('input[type="number"]').each(function() {
            if (!this.checkValidity()) {
                valid = false;
                $(this).addClass('is-invalid');
                return false;
            } else {
                $(this).removeClass('is-invalid');
            }
        });

        if (!valid) {
            alert('Please check the quantities entered');
            return;
        }

        if (confirm('Are you sure you want to process this withdrawal?')) {
            const submitBtn = $(this).find('button[type="submit"]');
            submitBtn.prop('disabled', true)
                    .html('<i class="fas fa-spinner fa-spin"></i> Processing...');

            $.ajax({
                url: $(this).attr('action'),
                method: 'POST',
                data: $(this).serialize(),
                success: function(response) {
                    window.location.reload();
                },
                error: function(xhr) {
                    submitBtn.prop('disabled', false).text('Submit Withdrawal');
                    let errorMessage = 'An error occurred while processing the withdrawal';
                    if (xhr.responseJSON && xhr.responseJSON.message) {
                        errorMessage = xhr.responseJSON.message;
                    }
                    alert(errorMessage);
                }
            });
        }
    });
});
</script>
@endsection
