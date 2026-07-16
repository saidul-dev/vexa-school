<form method="POST" enctype="multipart/form-data" class="d-block ajaxForm" action="{{ route('admin.account_heads.update', ['id' => $account_head->id]) }}">
    @csrf
    <div class="row">
        <div class="col-md-12 mb-3">
            <label class="eForm-label">{{ get_phrase('Name') }}</label>
            <input type="text" class="form-control eForm-control" name="name" value="{{ $account_head->name }}" required>
        </div>
        <div class="col-md-12 mb-3">
            <label class="eForm-label">{{ get_phrase('Type') }}</label>
            <select class="form-select eForm-select" name="type" {{ $account_head->is_system ? 'disabled' : '' }} required>
                @foreach(['asset', 'liability', 'income', 'expense', 'equity'] as $type)
                    <option value="{{ $type }}" {{ $account_head->type == $type ? 'selected' : '' }}>{{ ucfirst($type) }}</option>
                @endforeach
            </select>
            @if($account_head->is_system)
                <input type="hidden" name="type" value="{{ $account_head->type }}">
            @endif
        </div>
        <div class="col-md-6 mb-3">
            <label class="eForm-label">{{ get_phrase('Opening balance') }}</label>
            <input type="number" step="0.01" class="form-control eForm-control" name="opening_balance" value="{{ $account_head->opening_balance }}">
        </div>
        <div class="col-md-6 mb-3">
            <label class="eForm-label">{{ get_phrase('Opening balance type') }}</label>
            <select class="form-select eForm-select" name="opening_balance_type">
                <option value="debit" {{ $account_head->opening_balance_type == 'debit' ? 'selected' : '' }}>{{ get_phrase('Debit') }}</option>
                <option value="credit" {{ $account_head->opening_balance_type == 'credit' ? 'selected' : '' }}>{{ get_phrase('Credit') }}</option>
            </select>
        </div>
        <div class="col-md-12">
            <button type="submit" class="eBtn eBtn-secondary form-control">{{ get_phrase('Update') }}</button>
        </div>
    </div>
</form>
