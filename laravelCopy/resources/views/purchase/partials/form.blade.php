<div class="row mt-4" style="display: block">
    <div class="row mb-4">
        <div class="col-2 text-right">
            <label class="col-form-label text-right colorLabel">Product:</label>
        </div>
        <div class="col-4">
            <div class="form-group">
                <select class="form-control select2 select2-secondary" name="product" data-dropdown-css-class="select2-secondary" id="product" style="width: 100%;">
                    <option selected="selected" disabled>Select</option>
                    @foreach ($products as $product)
                        <option value="{{$product->id}}">{{ $product->description }}</option>
                    @endforeach
                </select>
            </div>
        </div>
        <div class="col-2 text-right">
            <label class="col-form-label text-right colorLabel">Provider:</label>
        </div>
        <div class="col-3">
            <div class="form-group">
                <select class="form-control select2 select2-secondary" name="provider" data-dropdown-css-class="select2-secondary" id="provider" style="width: 100%;">
                    <option selected="selected" disabled>Select</option>
                    @foreach ($providers as $provider)
                        <option value="{{$provider->id}}">{{ $provider->first_name.', '.$provider->last_name }}</option>
                    @endforeach
                </select>
            </div>
        </div>
    </div>
    <div class="row mb-4">
        <div class="col-2 text-right">
            <label class="col-form-label text-right colorLabel">Cost Price:</label>
        </div>
        <div class="col-4">
            <input type="text" class="form-control" name='cost_price' maxlength="10" size="10" required>
        </div>
        <div class="col-2 text-right">
            <label class="col-form-label text-right colorLabel">Increase:</label>
        </div>
        <div class="col-3">
            <input type="text" class="form-control" name='increase' maxlength="2" size="2" required>
        </div>
    </div>
    <div class="row mb-4">
        <div class="col-2 text-right">
            <label class="col-form-label text-right colorLabel">Count:</label>
        </div>
        <div class="col-4">
            <input type="text" class="form-control" name='count' maxlength="4" size="4" required>
        </div>
    </div>

</div>
