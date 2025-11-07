@extends('layouts.app')

@section('custom_css')
    <style>
        .sales-invoice-input {
            max-width: 200px;
        }
        .filter-section {
            background-color: #f8f9fa;
            padding: 20px;
            border-radius: 5px;
            margin-bottom: 20px;
        }
        .table-actions {
            white-space: nowrap;
        }
    </style>
@endsection

@section('contents')
    <!-- Content Header (Page header) -->
    <section class="content-header">
        <div class="container-fluid">
            <div class="row mb-2">
                <div class="col-sm-6">
                    <h1>Sales Invoice Management</h1>
                </div>
                <div class="col-sm-6">
                    <ol class="breadcrumb float-sm-right">
                        <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Home</a></li>
                        <li class="breadcrumb-item active">Sales Invoices</li>
                    </ol>
                </div>
            </div>
        </div>
    </section>

    <!-- Main content -->
    <section class="content">
        @include('layouts.errors')

        @if(session('success'))
            <div class="alert alert-success alert-dismissible fade show" role="alert">
                {{ session('success') }}
                <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
        @endif

        <!-- Filter Section -->
        <div class="card">
            <div class="card-body filter-section">
                <form action="{{ route('sales-invoices.index') }}" method="GET">
                    <div class="row">
                        <div class="col-md-3">
                            <div class="form-group">
                                <label for="date_from">Date From</label>
                                <input type="date" class="form-control" id="date_from" name="date_from"
                                       value="{{ request('date_from', date('Y-m-01')) }}">
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="form-group">
                                <label for="date_to">Date To</label>
                                <input type="date" class="form-control" id="date_to" name="date_to"
                                       value="{{ request('date_to', date('Y-m-d')) }}">
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="form-group">
                                <label for="status">Status</label>
                                <select class="form-control" id="status" name="status">
                                    <option value="">All Statuses</option>
                                    <option value="Completed" {{ request('status') == 'Completed' ? 'selected' : '' }}>Completed</option>
                                    <option value="Paid" {{ request('status') == 'Paid' ? 'selected' : '' }}>Paid</option>
                                    <option value="Free" {{ request('status') == 'Free' ? 'selected' : '' }}>Free</option>
                                </select>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="form-group">
                                <label>&nbsp;</label>
                                <div>
                                    <button type="submit" class="btn btn-primary btn-block">
                                        <i class="fas fa-filter"></i> Filter
                                    </button>
                                </div>
                            </div>
                        </div>
                    </div>
                </form>
            </div>
        </div>

        <!-- Orders Table -->
        <div class="card">
            <div class="card-header">
                <h3 class="card-title">Orders with Invoice ({{ $inbounds->count() }} records)</h3>
                <div class="card-tools">
                    <button type="button" class="btn btn-success" onclick="saveAllInvoices()">
                        <i class="fas fa-save"></i> Save All Changes
                    </button>
                </div>
            </div>
            <div class="card-body">
                <form id="salesInvoiceForm">
                    @csrf
                    <div class="table-responsive">
                        <table class="table table-bordered table-striped table-hover">
                            <thead>
                                <tr>
                                    <th style="width: 100px;">Order Date</th>
                                    <th style="width: 120px;">Order No.</th>
                                    <th style="width: 100px;">Degic No.</th>
                                    <th>Customer</th>
                                    <th>Store</th>
                                    <th style="width: 120px;">Amount</th>
                                    <th style="width: 80px;">Status</th>
                                    <th style="width: 250px;">Sales Invoice No.</th>
                                    <th style="width: 100px;" class="table-actions">Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                @php
                                    $totalAmount = 0;
                                @endphp
                                @forelse($inbounds as $inbound)
                                    @php
                                        $totalAmount += $inbound->grandTotal;
                                    @endphp
                                    <tr id="row_{{ $inbound->id }}">
                                        <td>{{ $inbound->f_created_at }}</td>
                                        <td>{{ $inbound->code }}</td>
                                        <td>{{ $inbound->degic_no }}</td>
                                        <td>{{ $inbound->customer->fullName ?? 'N/A' }}</td>
                                        <td>{{ $inbound->store_name }}</td>
                                        <td class="text-right">{{ number_format($inbound->grandTotal, 2) }}</td>
                                        <td>
                                            <span class="badge badge-{{ $inbound->status == 'Paid' ? 'success' : ($inbound->status == 'Completed' ? 'primary' : 'secondary') }}">
                                                {{ $inbound->status }}
                                            </span>
                                        </td>
                                        <td>
                                            <input type="text"
                                                   class="form-control sales-invoice-input"
                                                   id="invoice_{{ $inbound->id }}"
                                                   data-inbound-id="{{ $inbound->id }}"
                                                   data-original-value="{{ $inbound->sales_invoice_no ?? '' }}"
                                                   value="{{ $inbound->sales_invoice_no ?? '' }}"
                                                   placeholder="Enter invoice number"
                                                   maxlength="50">
                                        </td>
                                        <td class="table-actions">
                                            <button type="button"
                                                    class="btn btn-sm btn-primary"
                                                    onclick="saveSingleInvoice({{ $inbound->id }})">
                                                <i class="fas fa-save"></i> Save
                                            </button>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="9" class="text-center">No orders with invoice found for the selected date range.</td>
                                    </tr>
                                @endforelse
                            </tbody>
                            @if($inbounds->count() > 0)
                                <tfoot>
                                    <tr class="bg-light font-weight-bold">
                                        <td colspan="5" class="text-right">Total:</td>
                                        <td class="text-right">{{ number_format($totalAmount, 2) }}</td>
                                        <td colspan="3"></td>
                                    </tr>
                                </tfoot>
                            @endif
                        </table>
                    </div>
                </form>
            </div>
        </div>
    </section>
@endsection

@section('custom_js')
<script src="https://cdn.jsdelivr.net/npm/axios/dist/axios.min.js"></script>
<script>
    // Save single invoice
    function saveSingleInvoice(inboundId) {
        const inputField = document.getElementById(`invoice_${inboundId}`);
        const invoiceNo = inputField.value.trim();

        // Show loading state
        const btn = event.target.closest('button');
        const originalHtml = btn.innerHTML;
        btn.disabled = true;
        btn.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Saving...';

        axios.post('{{ route("sales-invoices.update") }}', {
            _token: '{{ csrf_token() }}',
            inbound_id: inboundId,
            sales_invoice_no: invoiceNo
        })
        .then(response => {
            if (response.data.success) {
                // Update original value
                inputField.setAttribute('data-original-value', invoiceNo);

                // Show success feedback
                btn.innerHTML = '<i class="fas fa-check"></i> Saved';
                btn.classList.remove('btn-primary');
                btn.classList.add('btn-success');

                // Reset button after 2 seconds
                setTimeout(() => {
                    btn.innerHTML = originalHtml;
                    btn.classList.remove('btn-success');
                    btn.classList.add('btn-primary');
                    btn.disabled = false;
                }, 2000);

                // Show success message
                showAlert('success', response.data.message);
            }
        })
        .catch(error => {
            btn.innerHTML = originalHtml;
            btn.disabled = false;

            if (error.response && error.response.data.message) {
                showAlert('error', error.response.data.message);
            } else {
                showAlert('error', 'Failed to save sales invoice number.');
            }
        });
    }

    // Save all invoices
    function saveAllInvoices() {
        const inputs = document.querySelectorAll('.sales-invoice-input');
        const updates = [];

        inputs.forEach(input => {
            const inboundId = input.getAttribute('data-inbound-id');
            const originalValue = input.getAttribute('data-original-value');
            const currentValue = input.value.trim();

            // Only include changed values
            if (currentValue !== originalValue) {
                updates.push({
                    inbound_id: inboundId,
                    sales_invoice_no: currentValue
                });
            }
        });

        if (updates.length === 0) {
            showAlert('info', 'No changes detected.');
            return;
        }

        if (!confirm(`Save ${updates.length} invoice number(s)?`)) {
            return;
        }

        // Show loading state
        const btn = event.target;
        const originalHtml = btn.innerHTML;
        btn.disabled = true;
        btn.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Saving...';

        axios.post('{{ route("sales-invoices.bulkUpdate") }}', {
            _token: '{{ csrf_token() }}',
            updates: updates
        })
        .then(response => {
            if (response.data.success) {
                // Update all original values
                updates.forEach(update => {
                    const input = document.querySelector(`[data-inbound-id="${update.inbound_id}"]`);
                    if (input) {
                        input.setAttribute('data-original-value', update.sales_invoice_no);
                    }
                });

                btn.innerHTML = originalHtml;
                btn.disabled = false;
                showAlert('success', response.data.message);
            }
        })
        .catch(error => {
            btn.innerHTML = originalHtml;
            btn.disabled = false;

            if (error.response && error.response.data.message) {
                showAlert('error', error.response.data.message);
            } else {
                showAlert('error', 'Failed to save sales invoice numbers.');
            }
        });
    }

    // Show alert message
    function showAlert(type, message) {
        const alertClass = type === 'success' ? 'alert-success' : (type === 'error' ? 'alert-danger' : 'alert-info');
        const alertHtml = `
            <div class="alert ${alertClass} alert-dismissible fade show" role="alert">
                ${message}
                <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
        `;

        // Insert alert at the top of content section
        const contentSection = document.querySelector('.content');
        const existingAlerts = contentSection.querySelectorAll('.alert');
        existingAlerts.forEach(alert => alert.remove());

        contentSection.insertAdjacentHTML('afterbegin', alertHtml);

        // Auto-dismiss after 5 seconds
        setTimeout(() => {
            const alerts = contentSection.querySelectorAll('.alert');
            alerts.forEach(alert => {
                alert.classList.remove('show');
                setTimeout(() => alert.remove(), 150);
            });
        }, 5000);
    }

    // Highlight changed fields
    document.querySelectorAll('.sales-invoice-input').forEach(input => {
        input.addEventListener('input', function() {
            const originalValue = this.getAttribute('data-original-value');
            const currentValue = this.value.trim();

            if (currentValue !== originalValue) {
                this.style.backgroundColor = '#fff3cd'; // Light yellow
                this.style.borderColor = '#ffc107'; // Warning color
            } else {
                this.style.backgroundColor = '';
                this.style.borderColor = '';
            }
        });
    });
</script>
@endsection
