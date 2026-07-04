<div class="eForm-layouts">
    <form method="POST" enctype="multipart/form-data" class="d-block ajaxForm" action="{{ route('superadmin.faq_update', ['id' => $faq->id]) }}">
        @csrf
        <div class="form-row">
            <div class="fpb-7">
                <label for="title" class="eForm-label">{{ get_phrase('Question Title') }}</label>
                <input type="text" class="form-control eForm-control" id="title" name = "title" value="{{ $faq->title }}" required>
            </div>
            <div class="fpb-7">
                <label for="description" class="eForm-label">{{ get_phrase('Question Description') }}</label>
                <textarea class="form-control eForm-control" id="description" name = "description" rows="6" required>{{ $faq->description }}</textarea>
            </div>
            <div class="fpb-7 pt-2">
                <button class="btn-form" type="submit">{{ get_phrase('Update') }}</button>
            </div>
        </div>
    </form>
</div>