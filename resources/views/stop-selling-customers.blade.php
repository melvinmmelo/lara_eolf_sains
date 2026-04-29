@extends('layouts.app')

@section('contents')
    <section class="content-header">
        <div class="container-fluid">
            <div class="row mb-2">
                <div class="col-sm-6">
                    <h1>Stop Selling Customers</h1>
                </div>
                <div class="col-sm-6">
                    <ol class="breadcrumb float-sm-right">
                        <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Home</a></li>
                        <li class="breadcrumb-item"><a href="{{ route('customers') }}">Customers</a></li>
                        <li class="breadcrumb-item active">Stop Selling</li>
                    </ol>
                </div>
            </div>
        </div>
    </section>

    <section class="content">
        <div class="card">
            <div class="card-body">
                @include('layouts.errors')
                <div class="pb-2">
                    <a href="{{ route('customers') }}" class="btn btn-secondary">
                        <i class="fas fa-arrow-left"></i> Back to Customers
                    </a>
                </div>
                <table id="example1" class="table table-bordered table-striped">
                    <thead>
                        <tr>
                            <th>Name</th>
                            <th>Company</th>
                            <th>Contact No.</th>
                            <th>Tin no.</th>
                            <th>Stores</th>
                            <th>Equipments</th>
                            <th>Status</th>
                            <th>Created at</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($customers as $customer)
                            <tr>
                                <td>{{ $customer->lastname }}, {{ $customer->firstname }} {{ $customer->middlename }}</td>
                                <td>{{ $customer->companyname }}</td>
                                <td>{{ $customer->contact_no }}</td>
                                <td>{{ $customer->tin }}</td>
                                <td>
                                    @forelse ($customer->stores as $store)
                                        <div>
                                            <strong>{{ $store->storename ?? '' }}</strong>
                                            <div class="text-muted small">{{ $store->brgy }}, {{ $store->subdivision }}, {{ $store->city }}</div>
                                        </div>
                                    @empty
                                        <span class="text-muted">No stores</span>
                                    @endforelse
                                </td>
                                <td>
                                    @php
                                        $codes = collect();
                                        foreach ($customer->stores as $store) {
                                            foreach ($store->equipmentStores as $es) {
                                                if ($es->equipment) {
                                                    $codes->push($es->equipment->code);
                                                }
                                            }
                                        }
                                    @endphp
                                    {{ $codes->isNotEmpty() ? $codes->implode(', ') : 'No Equipment Assigned' }}
                                </td>
                                <td>{!! statusBadge($customer->status) !!}</td>
                                <td>{{ $customer->date_created }}</td>
                                <td>
                                    <form action="{{ route('customer.reactivate', $customer->id) }}" method="POST"
                                        onsubmit="return confirm('Reactivate this customer and set status to Active?');">
                                        @csrf
                                        @method('PATCH')
                                        <button type="submit" class="btn btn-success btn-sm">
                                            <i class="fas fa-undo"></i> 
                                        </button>
                                    </form>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </section>
@endsection
