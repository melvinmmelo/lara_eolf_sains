@extends('layouts.app')

@section('contents')
    <section class="content-header">
        <div class="container-fluid">
            <div class="row mb-2">
                <div class="col-sm-6">
                    <h1>Activity Log</h1>
                </div>
                <div class="col-sm-6">
                    <ol class="breadcrumb float-sm-right">
                        <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Home</a></li>
                        <li class="breadcrumb-item active">Activity Log</li>
                    </ol>
                </div>
            </div>
        </div>
    </section>

    <section class="content">
        <div class="card">
            <div class="card-body">
                <form method="GET" action="{{ route('activity-log.index') }}" class="row g-2 mb-3">
                    <div class="col-md-2">
                        <label class="small mb-1">User</label>
                        <select name="user_id" class="form-control form-control-sm">
                            <option value="">All users</option>
                            @foreach ($users as $u)
                                <option value="{{ $u->id }}" @selected(request('user_id') == $u->id)>
                                    {{ $u->first_name }} {{ $u->last_name }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-md-2">
                        <label class="small mb-1">Category</label>
                        <select name="log_name" class="form-control form-control-sm">
                            <option value="">All</option>
                            @foreach ($logNames as $name)
                                <option value="{{ $name }}" @selected(request('log_name') === $name)>{{ $name }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-md-2">
                        <label class="small mb-1">From</label>
                        <input type="date" name="from" value="{{ request('from') }}" class="form-control form-control-sm">
                    </div>
                    <div class="col-md-2">
                        <label class="small mb-1">To</label>
                        <input type="date" name="to" value="{{ request('to') }}" class="form-control form-control-sm">
                    </div>
                    <div class="col-md-3">
                        <label class="small mb-1">Search</label>
                        <input type="text" name="q" value="{{ request('q') }}" placeholder="description, subject, properties..." class="form-control form-control-sm">
                    </div>
                    <div class="col-md-1 d-flex align-items-end">
                        <button class="btn btn-primary btn-sm w-100">Filter</button>
                    </div>
                </form>

                <div class="table-responsive">
                    <table class="table table-sm table-bordered table-striped align-middle">
                        <thead>
                            <tr>
                                <th style="width: 150px;">When</th>
                                <th style="width: 100px;">Category</th>
                                <th style="width: 160px;">User</th>
                                <th>Description</th>
                                <th style="width: 200px;">Subject</th>
                                <th style="width: 110px;">IP</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse ($logs as $log)
                                <tr>
                                    <td>
                                        <div>{{ $log->created_at->format('Y-m-d H:i:s') }}</div>
                                        <small class="text-muted">{{ $log->created_at->diffForHumans() }}</small>
                                    </td>
                                    <td><span class="badge badge-secondary">{{ $log->log_name }}</span></td>
                                    <td>
                                        @if ($log->causer)
                                            {{ $log->causer->first_name ?? '' }} {{ $log->causer->last_name ?? '' }}
                                        @else
                                            <span class="text-muted">—</span>
                                        @endif
                                    </td>
                                    <td>
                                        <div>{{ $log->description }}</div>
                                        @php
                                            $props = $log->properties->toArray();
                                        @endphp
                                        @if (isset($props['url']))
                                            <small class="text-muted d-block">{{ $props['method'] ?? '' }} {{ $props['route'] ?? $props['path'] ?? $props['url'] }}</small>
                                        @endif
                                        @if (!empty($props['attributes']) || !empty($props['old']))
                                            <details class="mt-1">
                                                <summary class="small text-primary" style="cursor: pointer;">changes</summary>
                                                <pre class="small mb-0" style="white-space: pre-wrap;">{{ json_encode(array_intersect_key($props, array_flip(['attributes','old'])), JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE) }}</pre>
                                            </details>
                                        @endif
                                    </td>
                                    <td>
                                        @if ($log->subject_type)
                                            <small>{{ class_basename($log->subject_type) }} #{{ $log->subject_id }}</small>
                                        @else
                                            <span class="text-muted">—</span>
                                        @endif
                                    </td>
                                    <td><small>{{ $props['ip'] ?? '—' }}</small></td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="6" class="text-center text-muted py-4">No activity found.</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

                {{ $logs->links() }}
            </div>
        </div>
    </section>
@endsection
