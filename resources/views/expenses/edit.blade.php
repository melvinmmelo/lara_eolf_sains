@extends('layouts.app')

@section('custom_css')
@endsection

@section('contents')
    <!-- Content Header (Page header) -->
    <section class="content-header">
        <div class="container-fluid">
            <div class="row mb-2">
                <div class="col-sm-6">
                    <h1>Edit Expense</h1>
                </div>
                <div class="col-sm-6">
                    <ol class="breadcrumb float-sm-right">
                        <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Home</a></li>
                        <li class="breadcrumb-item"><a href="{{ route('expenses.index') }}">Expenses</a></li>
                        <li class="breadcrumb-item active">Edit</li>
                    </ol>
                </div>
            </div>
        </div>
    </section>

    <!-- Main content -->
    <section class="content">

        @include('layouts.errors')

        <div class="card">
            <form action="{{ route('expenses.update', $expense) }}" method="POST" enctype="multipart/form-data">
                @csrf
                @method('PUT')

                <div class="card-body">
                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="date">Date <span class="text-danger">*</span></label>
                                <input type="date" 
                                       name="date" 
                                       id="date" 
                                       class="form-control @error('date') is-invalid @enderror" 
                                       value="{{ old('date', $expense->date->format('Y-m-d')) }}" 
                                       required>
                                @error('date')
                                    <span class="invalid-feedback">{{ $message }}</span>
                                @enderror
                            </div>
                        </div>

                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="category">Category <span class="text-danger">*</span></label>
                                <select name="category" 
                                        id="category" 
                                        class="form-control @error('category') is-invalid @enderror" 
                                        required>
                                    <option value="">Select Category</option>
                                    @foreach($categories as $cat)
                                        <option value="{{ $cat }}" {{ old('category', $expense->category) == $cat ? 'selected' : '' }}>
                                            {{ $cat }}
                                        </option>
                                    @endforeach
                                </select>
                                @error('category')
                                    <span class="invalid-feedback">{{ $message }}</span>
                                @enderror
                            </div>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="amount">Amount <span class="text-danger">*</span></label>
                                <input type="number" 
                                       name="amount" 
                                       id="amount" 
                                       step="0.01" 
                                       min="0"
                                       class="form-control @error('amount') is-invalid @enderror" 
                                       value="{{ old('amount', $expense->amount) }}" 
                                       required>
                                @error('amount')
                                    <span class="invalid-feedback">{{ $message }}</span>
                                @enderror
                            </div>
                        </div>

                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="receipt">Receipt (Optional)</label>
                                @if($expense->receipt_path)
                                    <div class="mb-2">
                                        <small class="text-muted">Current: 
                                            <a href="{{ asset('storage/' . $expense->receipt_path) }}" target="_blank">
                                                View Receipt
                                            </a>
                                        </small>
                                    </div>
                                @endif
                                <input type="file" 
                                       name="receipt" 
                                       id="receipt" 
                                       class="form-control-file @error('receipt') is-invalid @enderror"
                                       accept=".jpg,.jpeg,.png,.pdf">
                                <small class="form-text text-muted">Upload new to replace. Accepted: JPG, PNG, PDF (Max 2MB)</small>
                                @error('receipt')
                                    <span class="invalid-feedback d-block">{{ $message }}</span>
                                @enderror
                            </div>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-12">
                            <div class="form-group">
                                <label for="description">Description (Optional)</label>
                                <textarea name="description" 
                                          id="description" 
                                          rows="3" 
                                          class="form-control @error('description') is-invalid @enderror">{{ old('description', $expense->description) }}</textarea>
                                @error('description')
                                    <span class="invalid-feedback">{{ $message }}</span>
                                @enderror
                            </div>
                        </div>
                    </div>
                </div>

                <div class="card-footer">
                    <button type="submit" class="btn btn-primary">
                        <i class="fas fa-save"></i> Update Expense
                    </button>
                    <a href="{{ route('expenses.index') }}" class="btn btn-secondary">
                        <i class="fas fa-times"></i> Cancel
                    </a>
                </div>
            </form>
        </div>

    </section>
@endsection

@section('custom_js')
@endsection
