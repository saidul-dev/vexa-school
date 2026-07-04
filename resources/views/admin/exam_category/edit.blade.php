<form method="POST" enctype="multipart/form-data" class="d-block ajaxForm" action="{{ route('admin.exam_category.update', ['id' => $exam_category->id]) }}">
    @csrf 
    <div class="form-row">
        <div class="fpb-7">
            <label for="name" class="eForm-label">{{ get_phrase('Name') }}</label>
            <input type="text" class="form-control eForm-control" id="name" name = "name" value="{{ $exam_category->name }}" required>
        </div>
        
        <div class="fpb-7 pt-2">
            <button class="btn-form" type="submit">{{ get_phrase('Update category') }}</button>
        </div>

    </div>
</form>