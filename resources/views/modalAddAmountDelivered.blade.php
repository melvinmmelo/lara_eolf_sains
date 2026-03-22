<div class="modal fade" id="modalAddAmountDelivered">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h4 class="modal-title">Enter payment</h4>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">

                <form action="{{ route('inbound.addPayment') }}" method="POST">
                    @csrf
                    @method('PATCH')

                    <div class="form-group">
                        <label class="form-label" for="payment_type"><i style="color:red">*</i>Payment Type</label>
                        <input type="hidden" class="form-control" name="ob_id" id="ob_id" value="" required
                            readonly>
                        <select name="payment_type" id="payment_type" class="form-control" required>
                            <option value="">--Select--</option>
                            <option value="Cash">Cash</option>
                            <option value="Cheque">Cheque</option>
                            <option value="Bank Transfer">Bank Transfer</option>
                        </select>
                    </div>

                    <div class="form-group">
                        <label class="form-label" for="payment_date"><i style="color:red">*</i>Payment Date</label>
                        <input type="date" class="form-control" name="payment_date" id="payment_date" value="{{ now()->format('Y-m-d') }}" required>
                    </div>

                    <div class="form-group">
                        <label class="form-label" for="ref_no">Reference No.</label>
                        <input type="text" class="form-control" name="ref_no" id="ref_no" value="">
                    </div>

                    <div class="form-group">
                        <div class="row">
                            <div class="col-sm-12">
                                <label class="form-label" for="delivered_amount"><i style="color:red">*</i>Amount</label>
                                <input type="number" step="0.01" min="0.01" class="form-control" name="delivered_amount" id="delivered_amount" required>
                            </div>
                        </div>
                    </div>

                    <div class="form-group">
                        <label class="form-label" for="remarks">Remarks</label>
                        <textarea class="form-control" name="remarks" id="remarks" rows="2" placeholder="Optional notes"></textarea>
                    </div>

                    {{-- <div class="form-group">
                        <div class="row">
                            <div class="col-sm-12">
                                <label class="form-label" for="status">Status</label>
                                <select name="status" id="status" class="form-control">
                                    <option value="">--Select--</option>
                                    <option value="Paid">Paid</option>
                                    <option value="Unpaid">Unpaid</option>
                                </select>
                            </div>
                        </div>
                    </div> --}}

                    <div class="modal-footer">
                        <button type="submit" class="btn btn-success">Save changes</button>
                    </div>

                </form>
            </div>
            <!-- /.modal-content -->
        </div>
        <!-- /.modal-dialog -->
    </div>
    <!-- /.modal -->
</div>
