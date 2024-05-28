<div class="modal fade" id="modalAddAmountDelivered">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h4 class="modal-title">Enter amount</h4>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">

                <form action="#" method="post">
                    @csrf

                    <div class="form-group">
                        <div class="row">
                            <div class="col-sm-12">
                                <label class="form-label" for="branch_code">Branch Code</label>
                                <input type="text" class="form-control" name="branch_code" id="branch_code"
                                    value="{{ session('branch_code') }}" required readonly>

                            </div>
                        </div>
                    </div>

                    <div class="form-group">
                        <div class="row">
                            <div class="col-sm-12">
                                <label class="form-label" for="pricelevel_id"><i style="color:red">*</i>Amount</label>
                                <input type="amount" class="form-control">
                            </div>
                        </div>
                    </div>

                    <div class="modal-footer">
                        <button type="submit" class="btn btn-success">Next</button>
                    </div>

                </form>
            </div>
            <!-- /.modal-content -->
        </div>
        <!-- /.modal-dialog -->
    </div>
    <!-- /.modal -->
</div>
