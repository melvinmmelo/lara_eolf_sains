@extends('layouts.app')

@section('custom_css')
<style>
    .new-row td { background-color: #f0fff4; }
    .qty-add-input { width: 90px; }
    .remove-row { cursor: pointer; }
</style>
@endsection

@section('contents')
<section class="content-header">
    <div class="container-fluid">
        <div class="row mb-2">
            <div class="col-sm-6">
                <h1><i class="fas fa-truck"></i> Receive Delivery</h1>
            </div>
            <div class="col-sm-6">
                <ol class="breadcrumb float-sm-right">
                    <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Home</a></li>
                    <li class="breadcrumb-item"><a href="{{ route('materialsInventory.index') }}">Materials Inventory</a></li>
                    <li class="breadcrumb-item active">Receive Delivery</li>
                </ol>
            </div>
        </div>
    </div>
</section>

<section class="content">
    @include('layouts.errors')

    <form action="{{ route('materialsInventory.bulkReceive') }}" method="POST" id="receiveForm">
        @csrf

        {{-- Existing Materials --}}
        <div class="card">
            <div class="card-header">
                <h3 class="card-title">Update Existing Stock</h3>
                <div class="card-tools">
                    <small class="text-muted">Leave "Add Qty" as 0 to skip updating that item.</small>
                </div>
            </div>
            <div class="card-body">
                <input type="text" id="searchExisting" class="form-control form-control-sm mb-2" placeholder="Search materials..." style="max-width:300px;">
            </div>
            <div class="card-body p-0">
                <table class="table table-bordered table-striped mb-0">
                    <thead>
                        <tr>
                            <th>Name</th>
                            <th>Unit</th>
                            <th class="text-center">Current Qty</th>
                            <th class="text-center" style="width:120px;">Add Qty</th>
                            <th class="text-center">New Total</th>
                        </tr>
                    </thead>
                    <tbody id="existingBody">
                        @foreach($materials as $i => $material)
                        <tr data-current="{{ $material->quantity }}">
                            <input type="hidden" name="existing[{{ $i }}][id]" value="{{ $material->id }}">
                            <td>{{ $material->name }}</td>
                            <td>{{ $material->unit }}</td>
                            <td class="text-center">{{ $material->quantity }}</td>
                            <td class="text-center">
                                <input type="number"
                                       class="form-control form-control-sm qty-add-input text-center add-qty"
                                       name="existing[{{ $i }}][add_qty]"
                                       value="0"
                                       min="0"
                                       step="any">
                            </td>
                            <td class="text-center new-total">{{ $material->quantity }}</td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>

        {{-- New Materials --}}
        <div class="card mt-3">
            <div class="card-header">
                <h3 class="card-title">Add New Materials</h3>
                <div class="card-tools">
                    <button type="button" class="btn btn-sm btn-success" id="addNewRow">
                        <i class="fas fa-plus"></i> Add Row
                    </button>
                </div>
            </div>
            <div class="card-body p-0">
                <table class="table table-bordered mb-0">
                    <thead>
                        <tr>
                            <th>Name</th>
                            <th style="width:120px;">Unit</th>
                            <th style="width:120px;" class="text-center">Quantity</th>
                            <th style="width:140px;" class="text-center">Unit Price (₱)</th>
                            <th style="width:50px;"></th>
                        </tr>
                    </thead>
                    <tbody id="newBody">
                        <tr id="noNewRows">
                            <td colspan="5" class="text-center text-muted py-3">
                                Click "Add Row" to add a new material.
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>

        <div class="d-flex gap-2 mb-4">
            <button type="submit" class="btn btn-primary mr-2">
                <i class="fas fa-save"></i> Save All
            </button>
            <a href="{{ route('materialsInventory.index') }}" class="btn btn-secondary">
                <i class="fas fa-times"></i> Cancel
            </a>
        </div>
    </form>
</section>
@endsection

@section('custom_js')
<script>
    // Search filter for existing materials
    $('#searchExisting').on('input', function () {
        var term = $(this).val().toLowerCase();
        $('#existingBody tr').each(function () {
            var name = $(this).find('td:first').text().toLowerCase();
            $(this).toggle(name.includes(term));
        });
    });

    // Live update "New Total" column
    $(document).on('input', '.add-qty', function () {
        var tr = $(this).closest('tr');
        var current = parseFloat(tr.data('current')) || 0;
        var add = parseFloat($(this).val()) || 0;
        tr.find('.new-total').text(current + add);
    });

    // Add new material row
    var newRowIndex = 0;
    $('#addNewRow').on('click', function () {
        $('#noNewRows').hide();
        newRowIndex++;
        var row = `<tr class="new-row">
            <td><input type="text" class="form-control form-control-sm" name="new[${newRowIndex}][name]" required placeholder="Material name"></td>
            <td><input type="text" class="form-control form-control-sm" name="new[${newRowIndex}][unit]" placeholder="pcs, kg, L..."></td>
            <td><input type="number" class="form-control form-control-sm text-center" name="new[${newRowIndex}][quantity]" min="1" step="any" required placeholder="0"></td>
            <td><input type="number" class="form-control form-control-sm text-center" name="new[${newRowIndex}][amount]" min="0" step="0.01" required placeholder="0.00"></td>
            <td class="text-center"><span class="remove-row text-danger" title="Remove"><i class="fas fa-times"></i></span></td>
        </tr>`;
        $('#newBody').append(row);
    });

    // Remove new row
    $(document).on('click', '.remove-row', function () {
        $(this).closest('tr').remove();
        if ($('#newBody tr:visible').length === 0) {
            $('#noNewRows').show();
        }
    });
</script>
@endsection
