<form method="POST" class="d-block ajaxForm" enctype="multipart/form-data" action="{{ route('admin.syllabus.syllabus_add') }}">
    @csrf 
    <div class="form-row">
        <div class="fpb-7">
            <label for="title" class="eForm-label">{{ get_phrase('Tittle') }}</label>
            <input type="text" class="form-control eForm-control" id="title" name = "title" required>
        </div>

        <div class="fpb-7">
            <label for="class_id_on_syllabus_creation" class="eForm-label">{{ get_phrase('Class') }}</label>
            <select name="class_id" id="class_id_on_syllabus_creation" class="form-select eForm-select eChoice-multiple-with-remove"  required onchange="classWiseSectionForSyllabusCreate(this.value)">
                <option value="">{{ get_phrase('Select a class') }}</option>
                <?php foreach($classes as $class): ?>
                    <option value="{{ $class['id'] }}">{{ $class['name'] }}</option>
                <?php endforeach; ?>
            </select>
        </div>

        <div class="fpb-7">
            <label for="section_id_on_syllabus_creation" class="eForm-label">{{ get_phrase('Section') }}</label>
            <select name="section_id" id = "section_id_on_syllabus_creation" class="form-select eForm-select eChoice-multiple-with-remove"  required>
                <option value="">{{ get_phrase('Select section') }}</option>
            </select>
        </div>

        <div class="fpb-7">
            <label for="subject_id_on_syllabus_creation" class="eForm-label">{{ get_phrase('Subject') }}</label>
            <select name="subject_id" id = "subject_id_on_syllabus_creation" class="form-select eForm-select eChoice-multiple-with-remove"  required>
                <option value="">{{ get_phrase('Select subject') }}</option>
            </select>
        </div>

        <div class="fpb-7">
            <label for="syllabus_file" class="eForm-label">{{ get_phrase('Upload syllabus') }}</label>
            <input type="file" class="form-control eForm-control-file" id="syllabus_file" name = "syllabus_file" required>
        </div>

        <div class="fpb-7 pt-2">
            <button class="btn-form" type="submit">{{ get_phrase('Add syllabus') }}</button>
        </div>
    </div>
</form>


<script type="text/javascript">

    "use strict";

    function classWiseSectionForSyllabusCreate(classId) {
        let url = "{{ route('admin.class_wise_sections', ['id' => ":classId"]) }}";
        url = url.replace(":classId", classId);
        $.ajax({
            url: url,
            success: function(response){
                $('#section_id_on_syllabus_creation').html(response);
                classWiseSubjectForSyllabusCreate(classId);
            }
        });
    }

    function classWiseSubjectForSyllabusCreate(classId) {
        let url = "{{ route('admin.class_wise_subject', ['id' => ":classId"]) }}";
        url = url.replace(":classId", classId);
        $.ajax({
            url: url,
            success: function(response){
                $('#subject_id_on_syllabus_creation').html(response);
            }
        });
    }

    $(document).ready(function () {
      $(".eChoice-multiple-with-remove").select2();
    });

</script>
