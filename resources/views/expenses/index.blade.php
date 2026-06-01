@extends('layouts.app')

@section('contents')
    <!-- Content Header (Page header) -->
    <section class="content-header">
        <div class="container-fluid">
            <div class="row mb-2">
                <div class="col-sm-6">
                    <h1>Expenses<small type="button" class="btn btn-default" data-toggle="modal" data-target="#modal-expense">Add New</small></h1>
                </div>
                <div class="col-sm-6">
                    <ol class="breadcrumb float-sm-right">
                        <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Home</a></li>
                        <li class="breadcrumb-item active">Expenses</li>
                    </ol>
                </div>
            </div>
        </div>
    </section>

    <!-- Main content -->
    <section class="content">

        @if (session('success'))
            <script>
                document.addEventListener('DOMContentLoaded', function() {
                    let icon = 'success';
                    @if (session('success') == 'Expense deleted successfully!')
                        icon = 'error';
                    @endif
                    Swal.fire({
                        icon: icon,
                        title: '{{ session('success') }}',
                        showConfirmButton: false,
                        timer: 2000
                    });
                });
            </script>
        @endif

        @include('layouts.errors')

        <div class="row">
            <div class="col-lg-3 col-6">
                <div class="small-box bg-info">
                    <div class="inner">
                        <h3>₱{{ formatNumber($total) }}</h3>
                        <p>Total (current filter)</p>
                    </div>
                    <div class="icon">
                        <i class="fas fa-wallet"></i>
                    </div>
                </div>
            </div>
        </div>

        <!-- Filters -->
        <div class="card card-outline card-primary collapsed-card">
            <div class="card-header">
                <h3 class="card-title"><i class="fas fa-filter mr-1"></i> Filters</h3>
                <div class="card-tools">
                    <button type="button" class="btn btn-tool" data-card-widget="collapse">
                        <i class="fas fa-plus"></i>
                    </button>
                </div>
            </div>
            <div class="card-body">
                <form method="GET" action="{{ route('expenses.index') }}" class="row">
                    <div class="col-md-3 mb-2">
                        <label>Category</label>
                        <select name="category" class="form-control">
                            <option value="">All</option>
                            @foreach ($categories as $cat)
                                <option value="{{ $cat }}" {{ request('category') == $cat ? 'selected' : '' }}>{{ $cat }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-md-2 mb-2">
                        <label>From</label>
                        <input type="date" name="date_from" class="form-control" value="{{ request('date_from') }}">
                    </div>
                    <div class="col-md-2 mb-2">
                        <label>To</label>
                        <input type="date" name="date_to" class="form-control" value="{{ request('date_to') }}">
                    </div>
                    <div class="col-md-3 mb-2">
                        <label>Search</label>
                        <input type="text" name="search" class="form-control" placeholder="Particulars / payee / ref no."
                            value="{{ request('search') }}">
                    </div>
                    <div class="col-md-2 mb-2 d-flex align-items-end">
                        <button type="submit" class="btn btn-primary mr-2">Filter</button>
                        <a href="{{ route('expenses.index') }}" class="btn btn-default">Reset</a>
                    </div>
                    
                </form>
            </div>
        </div>

        <!-- Default box -->
        <div class="card">
            <div class="card-body">
                <table class="table table-bordered table-striped">
                    <thead>
                        <tr>
                            <th>Date</th>
                            <th>Category</th>
                            <th>Particulars</th>
                            <th>Payee</th>
                            <th class="text-right">Amount</th>
                            <th>Payment</th>
                            <th>Ref No.</th>
                            <th>Recorded By</th>
                            <th></th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($expenses as $expense)
                            <tr>
                                <td>{{ optional($expense->expense_date)->format('m-d-Y') }}</td>
                                <td>{{ $expense->category }}</td>
                                <td>{{ $expense->particulars }}</td>
                                <td>{{ $expense->payee }}</td>
                                <td class="text-right">₱{{ formatNumber($expense->amount) }}</td>
                                <td>{{ $expense->payment_method }}</td>
                                <td>{{ $expense->reference_no }}</td>
                                <td>{{ optional($expense->creator)->fullName ?? '—' }}</td>
                                <td class="text-nowrap">
                                    <button class="btn btn-primary btn-sm" data-toggle="modal" data-target="#editExpense"
                                        onclick='setToUpdateExpense(@json($expense))'>Edit</button>
                                    <form method="POST" action="{{ route('expenses.destroy', $expense->id) }}"
                                        style="display:inline;" onsubmit="return confirm('Delete this expense?');">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="btn btn-danger btn-sm">Delete</button>
                                    </form>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="9" class="text-center text-muted">No expenses found.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>

                {{ $expenses->links() }}
            </div>
            <!-- /.card-body -->
            <div class="card-footer">
                <a href="{{ route('expenses.export', request()->query()) }}" class="btn btn-success">
                    <i class="fas fa-file-excel mr-1"></i> Export to Excel
                </a>
                <small class="text-muted ml-2">Exports all rows matching the current filters.</small>
            </div>
            <!-- /.card-footer-->
        </div>
        <!-- /.card -->

        <!-- Add Expense Modal -->
        <div class="modal fade" id="modal-expense">
            <div class="modal-dialog">
                <form method="POST" action="{{ route('expenses.store') }}">
                    @csrf
                    <div class="modal-content">
                        <div class="modal-header">
                            <h4 class="modal-title">Add Expense</h4>
                            <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                <span aria-hidden="true">&times;</span>
                            </button>
                        </div>
                        <div class="modal-body">
                            @include('expenses.form', ['prefix' => ''])
                        </div>
                        <div class="modal-footer">
                            <button type="submit" class="btn btn-success">Save</button>
                        </div>
                    </div>
                </form>
            </div>
        </div>

        <!-- Edit Expense Modal -->
        <div class="modal fade" id="editExpense" tabindex="-1" aria-hidden="true">
            <div class="modal-dialog">
                <form id="editExpenseForm" method="POST" action="">
                    @csrf
                    @method('PUT')
                    <div class="modal-content">
                        <div class="modal-header">
                            <h4 class="modal-title">Edit Expense</h4>
                            <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                <span aria-hidden="true">&times;</span>
                            </button>
                        </div>
                        <div class="modal-body">
                            @include('expenses.form', ['prefix' => 'e_'])
                        </div>
                        <div class="modal-footer">
                            <button type="submit" class="btn btn-success">Save changes</button>
                        </div>
                    </div>
                </form>
            </div>
        </div>

    </section>
    <!-- /.content -->
@endsection

@section('custom_js')
    <script>
        // Sync the hidden payee text field with the dropdown; reveal it when "Other" is chosen.
        function togglePayeeOther(prefix) {
            const select = document.getElementById(prefix + 'payee_select');
            const input = document.getElementById(prefix + 'payee');
            if (select.value === '__other__') {
                input.classList.remove('d-none');
                input.value = '';
                input.focus();
            } else {
                input.classList.add('d-none');
                input.value = select.value; // '' or the chosen payee
            }
        }

        function setPayee(prefix, payee) {
            const select = document.getElementById(prefix + 'payee_select');
            const input = document.getElementById(prefix + 'payee');
            payee = payee || '';
            const known = Array.from(select.options).some(o => o.value === payee && payee !== '');
            if (payee && known) {
                select.value = payee;
                input.value = payee;
                input.classList.add('d-none');
            } else if (payee) {
                select.value = '__other__';
                input.value = payee;
                input.classList.remove('d-none');
            } else {
                select.value = '';
                input.value = '';
                input.classList.add('d-none');
            }
        }

        function setToUpdateExpense(expense) {
            const form = document.getElementById('editExpenseForm');
            form.action = '{{ url('expenses') }}/' + expense.id;
            document.getElementById('e_expense_date').value = expense.expense_date ? expense.expense_date.substring(0, 10) : '';
            document.getElementById('e_category').value = expense.category || '';
            document.getElementById('e_particulars').value = expense.particulars || '';
            setPayee('e_', expense.payee);
            document.getElementById('e_amount').value = expense.amount || '';
            document.getElementById('e_payment_method').value = expense.payment_method || '';
            document.getElementById('e_reference_no').value = expense.reference_no || '';
            document.getElementById('e_remarks').value = expense.remarks || '';
        }
    </script>
@endsection
