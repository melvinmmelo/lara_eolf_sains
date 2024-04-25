<div class="form-group">
    <div class="row">
        <div class="col-sm-12">
            <label class="form-label" for="button types">Products</label>
            <div class="buttontypes">
                @foreach ($products as $product)
                    @php // convert to object
                        $product = (object) $product;
                    @endphp
                    <button type="button" class="btn btn-primary" onclick="addProduct('{{ $product->code }}')">{{ $product->code }} ({{ $product->qty}})</button>
                @endforeach
            </div>
        </div>
    </div>
</div>
