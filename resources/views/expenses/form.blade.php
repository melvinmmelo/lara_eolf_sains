@php($prefix = $prefix ?? '')
<div class="form-group row">
    <div class="col-sm-6">
        <label><i style="color:red">*</i> Date</label>
        <input type="date" class="form-control" name="expense_date" id="{{ $prefix }}expense_date"
            value="{{ $prefix === '' ? date('Y-m-d') : '' }}" required>
    </div>
    <div class="col-sm-6">
        <label><i style="color:red">*</i> Expenses Account</label>
        <select class="form-control" name="category" id="{{ $prefix }}category" required>
            <option value="">-- Select --</option>
            @foreach (\App\Models\Expenses::CATEGORIES as $cat)
                <option value="{{ $cat }}">{{ $cat }}</option>
            @endforeach
        </select>
    </div>
</div>

<div class="form-group">
    <label><i style="color:red">*</i> Particulars</label>
    <input type="text" class="form-control" name="particulars" id="{{ $prefix }}particulars"
        placeholder="What was this expense for?" required>
</div>

<div class="form-group row">
    <div class="col-sm-6">
        <label>Payee</label>
        <select class="form-control" id="{{ $prefix }}payee_select"
            onchange="togglePayeeOther('{{ $prefix }}')">
            <option value="">-- Select --</option>
            @foreach (($payees ?? collect()) as $p)
                <option value="{{ $p }}">{{ $p }}</option>
            @endforeach
            <option value="__other__">Other (specify)…</option>
        </select>
        <input type="text" class="form-control mt-2 d-none" name="payee" id="{{ $prefix }}payee"
            placeholder="Enter payee name">
    </div>
    <div class="col-sm-6">
        <label><i style="color:red">*</i> Amount</label>
        <div class="input-group">
            <div class="input-group-prepend">
                <span class="input-group-text">₱</span>
            </div>
            <input type="number" step="0.01" min="0.01" class="form-control" name="amount"
                id="{{ $prefix }}amount" required>
        </div>
    </div>
</div>

<div class="form-group row">
    <div class="col-sm-6">
        <label>Payee Address</label>
        <input type="text" class="form-control" name="payee_address" id="{{ $prefix }}payee_address"
            placeholder="Payee's registered address">
    </div>
    <div class="col-sm-3">
        <label>TIN</label>
        <input type="text" class="form-control" name="tin" id="{{ $prefix }}tin"
            placeholder="000-000-000-0000" maxlength="191">
    </div>
    <div class="col-sm-3">
        <label>Taxpayer Type</label>
        <select class="form-control" name="taxpayer_type" id="{{ $prefix }}taxpayer_type">
            <option value="">-- Select --</option>
            @foreach (\App\Models\Expenses::TAXPAYER_TYPES as $t)
                <option value="{{ $t }}">{{ $t }}</option>
            @endforeach
        </select>
    </div>
</div>

<div class="form-group row">
    <div class="col-sm-4">
        <label>Payment Method</label>
        <select class="form-control" name="payment_method" id="{{ $prefix }}payment_method">
            <option value="">-- Select --</option>
            @foreach (\App\Models\Expenses::PAYMENT_METHODS as $method)
                <option value="{{ $method }}">{{ $method }}</option>
            @endforeach
        </select>
    </div>
    <div class="col-sm-4">
        <label>Reference No.</label>
        <input type="text" class="form-control" name="reference_no" id="{{ $prefix }}reference_no"
            placeholder="OR / Check / Txn no.">
    </div>
    <div class="col-sm-4">
        <label>Petty Cash No.</label>
        <input type="text" class="form-control" name="petty_cash_no" id="{{ $prefix }}petty_cash_no"
            placeholder="PCV no.">
    </div>
</div>

<div class="form-group">
    <label>Remarks</label>
    <textarea class="form-control" name="remarks" id="{{ $prefix }}remarks" rows="2"></textarea>
</div>
