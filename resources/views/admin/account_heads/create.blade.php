<form method="POST" enctype="multipart/form-data" class="d-block ajaxForm" action="{{ route('admin.create.account_heads') }}">
    @csrf
    <div class="row">
        <div class="col-md-12 mb-3">
            <label class="eForm-label">{{ get_phrase('Name') }}</label>
            <input type="text" class="form-control eForm-control" name="name" required>
        </div>
        <div class="col-md-12 mb-3">
            <label class="eForm-label">{{ get_phrase('Type') }}</label>
            <select class="form-select eForm-select" name="type" required>
                <option value="asset">{{ get_phrase('Asset') }}</option>
                <option value="liability">{{ get_phrase('Liability') }}</option>
                <option value="income">{{ get_phrase('Income') }}</option>
                <option value="expense">{{ get_phrase('Expense') }}</option>
                <option value="equity">{{ get_phrase('Equity') }}</option>
            </select>
        </div>
        <div class="col-md-6 mb-3">
            <label class="eForm-label">{{ get_phrase('Opening balance') }}</label>
            <input type="number" step="0.01" class="form-control eForm-control" name="opening_balance" value="0">
        </div>
        <div class="col-md-6 mb-3">
            <label class="eForm-label">{{ get_phrase('Opening balance type') }}</label>
            <select class="form-select eForm-select" name="opening_balance_type">
                <option value="debit">{{ get_phrase('Debit') }}</option>
                <option value="credit">{{ get_phrase('Credit') }}</option>
            </select>
        </div>
        <div class="col-md-12">
            <button type="submit" class="eBtn eBtn-secondary form-control">{{ get_phrase('Save') }}</button>
        </div>
    </div>
</form>
