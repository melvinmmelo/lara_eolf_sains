@extends('layouts.app')

@section('custom_css')
<style>
    .activity-badge {
        font-size: 0.85rem;
        padding: 0.35rem 0.65rem;
    }
    .change-detail {
        margin-bottom: 0.5rem;
    }
    .change-label {
        font-weight: 600;
        color: #495057;
        min-width: 120px;
        display: inline-block;
    }
    .change-value {
        color: #6c757d;
    }
    .old-value {
        text-decoration: line-through;
        color: #dc3545;
    }
    .new-value {
        color: #28a745;
        font-weight: 500;
    }
    .change-arrow {
        margin: 0 0.5rem;
        color: #6c757d;
    }
    .no-changes {
        color: #6c757d;
        font-style: italic;
    }
</style>
@endsection

@section('contents')
    <!-- Content Header (Page header) -->
    <section class="content-header">
        <div class="container-fluid">
            <div class="row mb-2">
                <div class="col-sm-6">
                    <h1>
                        <i class="fas fa-history"></i> {{ $material->name }} - Change History
                    </h1>
                    <p class="text-muted mb-0">
                        <small>Material Code: {{ $material->code ?? 'N/A' }}</small>
                    </p>
                </div>
                <div class="col-sm-6">
                    <ol class="breadcrumb float-sm-right">
                        <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Home</a></li>
                        <li class="breadcrumb-item"><a href="{{ route('materialsInventory.index') }}">Materials Inventory</a></li>
                        <li class="breadcrumb-item active">Change History</li>
                    </ol>
                </div>
            </div>
        </div>
    </section>

    <!-- Main content -->
    <section class="content">

        @include('layouts.errors')

        <div class="card">
            <div class="card-header">
                <h3 class="card-title">
                    <i class="fas fa-list"></i> Activity Log
                </h3>
                <div class="card-tools">
                    <span class="badge badge-info">{{ $activityLogs->count() }} Records</span>
                </div>
            </div>

            <div class="card-body table-responsive">
                @if($activityLogs->isEmpty())
                    <div class="alert alert-info">
                        <i class="fas fa-info-circle"></i> No change history available for this material.
                    </div>
                @else
                    <table id="historyTable" class="table table-bordered table-striped table-hover">
                        <thead>
                            <tr>
                                <th width="10%">Action</th>
                                <th width="50%">Changes</th>
                                <th width="15%">User</th>
                                <th width="15%">Date</th>
                                <th width="10%">Details</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($activityLogs as $log)
                                <tr>
                                    <td>
                                        @if($log->event === 'created')
                                            <span class="badge badge-success activity-badge">
                                                <i class="fas fa-plus"></i> Created
                                            </span>
                                        @elseif($log->event === 'updated')
                                            <span class="badge badge-primary activity-badge">
                                                <i class="fas fa-edit"></i> Updated
                                            </span>
                                        @elseif($log->event === 'deleted')
                                            <span class="badge badge-danger activity-badge">
                                                <i class="fas fa-trash"></i> Deleted
                                            </span>
                                        @else
                                            <span class="badge badge-secondary activity-badge">
                                                <i class="fas fa-question"></i> {{ ucfirst($log->event) }}
                                            </span>
                                        @endif
                                    </td>
                                    <td>
                                        @php
                                            $properties = $log->properties;
                                            $attributes = $properties['attributes'] ?? [];
                                            $old = $properties['old'] ?? [];
                                        @endphp

                                        @if($log->event === 'created')
                                            <div class="change-detail">
                                                <span class="change-label">Initial Values:</span>
                                            </div>
                                            @foreach($attributes as $key => $value)
                                                @if(!in_array($key, ['id', 'created_at', 'updated_at', 'deleted_at']))
                                                    <div class="change-detail ml-3">
                                                        <strong>{{ ucfirst(str_replace('_', ' ', $key)) }}:</strong>
                                                        <span class="new-value">{{ $value ?? 'N/A' }}</span>
                                                    </div>
                                                @endif
                                            @endforeach
                                        @elseif($log->event === 'updated' && !empty($old))
                                            @foreach($attributes as $key => $value)
                                                @if(isset($old[$key]) && $old[$key] != $value && !in_array($key, ['updated_at']))
                                                    <div class="change-detail">
                                                        <strong>{{ ucfirst(str_replace('_', ' ', $key)) }}:</strong>
                                                        <span class="old-value">{{ $old[$key] ?? 'N/A' }}</span>
                                                        <span class="change-arrow">→</span>
                                                        <span class="new-value">{{ $value ?? 'N/A' }}</span>
                                                    </div>
                                                @endif
                                            @endforeach
                                        @elseif($log->event === 'deleted')
                                            <div class="change-detail">
                                                <span class="text-danger">
                                                    <i class="fas fa-exclamation-triangle"></i> Material record deleted
                                                </span>
                                            </div>
                                        @else
                                            <span class="no-changes">No detailed changes recorded</span>
                                        @endif
                                    </td>
                                    <td>
                                        @if($log->causer)
                                            <i class="fas fa-user"></i> {{ $log->causer->name ?? 'Unknown' }}
                                            <br>
                                            <small class="text-muted">{{ $log->causer->email ?? '' }}</small>
                                        @else
                                            <span class="text-muted">System</span>
                                        @endif
                                    </td>
                                    <td>
                                        <i class="far fa-clock"></i> {{ $log->created_at->format('M d, Y') }}
                                        <br>
                                        <small class="text-muted">{{ $log->created_at->format('h:i A') }}</small>
                                        <br>
                                        <small class="text-muted">({{ $log->created_at->diffForHumans() }})</small>
                                    </td>
                                    <td class="text-center">
                                        <button type="button" class="btn btn-sm btn-outline-info"
                                                data-toggle="modal"
                                                data-target="#detailModal{{ $log->id }}"
                                                title="View full details">
                                            <i class="fas fa-eye"></i>
                                        </button>
                                    </td>
                                </tr>

                                <!-- Detail Modal -->
                                <div class="modal fade" id="detailModal{{ $log->id }}" tabindex="-1">
                                    <div class="modal-dialog modal-lg">
                                        <div class="modal-content">
                                            <div class="modal-header bg-info">
                                                <h5 class="modal-title">
                                                    <i class="fas fa-info-circle"></i> Change Details
                                                </h5>
                                                <button type="button" class="close text-white" data-dismiss="modal">
                                                    <span>&times;</span>
                                                </button>
                                            </div>
                                            <div class="modal-body">
                                                <dl class="row">
                                                    <dt class="col-sm-3">Action:</dt>
                                                    <dd class="col-sm-9">{{ ucfirst($log->event) }}</dd>

                                                    <dt class="col-sm-3">User:</dt>
                                                    <dd class="col-sm-9">
                                                        {{ $log->causer ? $log->causer->name : 'System' }}
                                                    </dd>

                                                    <dt class="col-sm-3">Date & Time:</dt>
                                                    <dd class="col-sm-9">
                                                        {{ $log->created_at->format('F d, Y h:i:s A') }}
                                                    </dd>

                                                    <dt class="col-sm-3">Full Properties:</dt>
                                                    <dd class="col-sm-9">
                                                        <pre class="bg-light p-3 rounded" style="max-height: 400px; overflow-y: auto;">{{ json_encode($log->properties, JSON_PRETTY_PRINT) }}</pre>
                                                    </dd>
                                                </dl>
                                            </div>
                                            <div class="modal-footer">
                                                <button type="button" class="btn btn-secondary" data-dismiss="modal">
                                                    Close
                                                </button>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            @endforeach
                        </tbody>
                        <tfoot>
                            <tr>
                                <th>Action</th>
                                <th>Changes</th>
                                <th>User</th>
                                <th>Date</th>
                                <th>Details</th>
                            </tr>
                        </tfoot>
                    </table>
                @endif
            </div>

            @if($activityLogs->isNotEmpty())
                <div class="card-footer">
                    <a href="{{ route('materialsInventory.index') }}" class="btn btn-default">
                        <i class="fas fa-arrow-left"></i> Back to Materials
                    </a>
                </div>
            @endif
        </div>
    </section>
@endsection

@section('custom_js')
<script>
    $(function () {
        $("#historyTable").DataTable({
            "responsive": true,
            "lengthChange": true,
            "autoWidth": false,
            "order": [[3, "desc"]], // Sort by date column (newest first)
            "pageLength": 25,
            "buttons": [
                {
                    extend: 'excel',
                    text: '<i class="fas fa-file-excel"></i> Export Excel',
                    className: 'btn-success',
                    exportOptions: {
                        columns: [0, 1, 2, 3] // Export all columns except Details
                    }
                },
                {
                    extend: 'pdf',
                    text: '<i class="fas fa-file-pdf"></i> Export PDF',
                    className: 'btn-danger',
                    exportOptions: {
                        columns: [0, 1, 2, 3]
                    }
                },
                {
                    extend: 'print',
                    text: '<i class="fas fa-print"></i> Print',
                    className: 'btn-info',
                    exportOptions: {
                        columns: [0, 1, 2, 3]
                    }
                }
            ],
            "language": {
                "emptyTable": "No change history available"
            }
        }).buttons().container().appendTo('#historyTable_wrapper .col-md-6:eq(0)');
    });
</script>
@endsection
