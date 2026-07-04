<form method="POST" enctype="multipart/form-data" class="d-block ajaxForm" action="{{ route('admin.examination.admit_card_update', ['id' => $admitCardEdit->id]) }}">
    @csrf 

    <div class="fpb-7">
        <label for="template" class="eForm-label">{{ get_phrase('Template *') }}</label>
        <input type="text" class="form-control eForm-control" id="template" name = "template" value="{{ $admitCardEdit->template }}" required>
    </div>

    <div class="fpb-7">
        <label for="heading" class="eForm-label">{{ get_phrase('Heading') }}</label>
        <input type="text" class="form-control eForm-control" id="heading" name = "heading" value="{{ $admitCardEdit->heading }}" required>
    </div>

    <div class="fpb-7">
        <label for="title" class="eForm-label">{{ get_phrase('Title') }}</label>
        <input type="text" class="form-control eForm-control" id="title" name = "title" value="{{ $admitCardEdit->title }}" required>
    </div>

    <div class="fpb-7">
        <label for="exam_center" class="eForm-label">{{ get_phrase('Exam Center') }}</label>
        <input type="text" class="form-control eForm-control" id="exam_center" name = "exam_center"value="{{ $admitCardEdit->exam_center }}" required>
    </div>
    <div class="fpb-7">
        <label for="footer_text" class="eForm-label">{{ get_phrase('Footer Text') }}</label>
        <input type="text" class="form-control eForm-control" id="footer_text" name = "footer_text" value="{{ $admitCardEdit->footer_text }}" required>
    </div>

    <div class="fpb-7">
        <label for="sign" class="eForm-label">{{ get_phrase('Signature') }}</label>
            <input class="form-control eForm-control-file" id="sign" name="sign" accept="image/*" type="file"
            />
    </div>


    <div class="fpb-7 pt-2">
        <button type="submit" class="btn-form">{{ get_phrase('Update') }}</button>
    </div>
</form>