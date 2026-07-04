<div class="eForm-layouts">
    <form method="POST" enctype="multipart/form-data" class="d-block ajaxForm"
        action="{{ route('admin.disable_reason.update', ['id' => $reason->id]) }}">
        @csrf
        <input type="hidden" name="id" value="{{ $reason->id }}" />
        <div class="form-row">
            <div class="fpb-7">
                <label for="disable_reason" class="eForm-label">{{ get_phrase('Reason') }}</label>
                <input type="text" class="form-control eForm-control" value="{{ $reason->disable_reason }}"
                    id="disable_reason" name = "disable_reason" required>
            </div>
            <div class="fpb-7 pt-2">
                <button class="btn-form" type="submit">{{ get_phrase('Update') }}</button>
            </div>
        </div>
    </form>
</div>
