<form method="POST" enctype="multipart/form-data" class="d-block ajaxForm" action="{{ route('admin.examination.admit_card_upload') }}">
    @csrf 
    <div class="fpb-7">
        <label for="template" class="eForm-label">{{ get_phrase('Template *') }}</label>
        <input type="text" class="form-control eForm-control" id="template" name = "template" required>
    </div>

    <div class="fpb-7">
        <label for="heading" class="eForm-label">{{ get_phrase('Heading') }}</label>
        <input type="text" class="form-control eForm-control" id="heading" name = "heading" required>
    </div>

    <div class="fpb-7">
        <label for="title" class="eForm-label">{{ get_phrase('Title') }}</label>
        <input type="text" class="form-control eForm-control" id="title" name = "title" required>
    </div>

    <div class="fpb-7">
        <label for="exam_name" class="eForm-label">{{ get_phrase('Exam Name') }}</label>
        <input type="text" class="form-control eForm-control" id="exam_name" name = "exam_name" required>
    </div>
    <div class="fpb-7">
        <label for="exam_center" class="eForm-label">{{ get_phrase('Exam Center') }}</label>
        <input type="text" class="form-control eForm-control" id="exam_center" name = "exam_center" required>
    </div>
    <div class="fpb-7">
        <label for="footer_text" class="eForm-label">{{ get_phrase('Footer Text') }}</label>
        <input type="text" class="form-control eForm-control" id="footer_text" name = "footer_text" required>
    </div>

    <div class="fpb-7">
        <label for="sign" class="eForm-label">{{ get_phrase('Signature') }}</label>
            <input class="form-control eForm-control-file" id="sign" name="sign" accept="image/*" type="file"
            />
    </div>

    <div class="fpb-7 pt-2">
        <button type="submit" class="btn-form">{{ get_phrase('Create') }}</button>
    </div>
</form>

